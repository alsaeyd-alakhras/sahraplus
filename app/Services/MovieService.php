<?php

namespace App\Services;

use App\Models\Movie;
use App\Repositories\MovieRepository;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class MovieService
{
    public function __construct(private MovieRepository $repo) {}

    public function datatableIndex(Request $request)
    {
        $q = $this->repo->getQuery();

        // فلاتر الأعمدة (حسب نمطك)
        if ($request->column_filters) {
            foreach ($request->column_filters as $field => $values) {
                $vals = array_values(array_filter((array)$values, fn($v) => !in_array($v, ['الكل', 'all', 'All'])));
                if (!$vals) continue;
                if ($field === 'status') {
                    $q->whereIn('status', $vals);
                } elseif ($field === 'is_featured') {
                    $map = ['مميز' => 1, '1' => 1, 1 => 1, true => 1, 'غير مميز' => 0, '0' => 0, 0 => 0, false => 0];
                    $q->whereIn('is_featured', array_map(fn($v) => $map[$v] ?? $v, $vals));
                } else {
                    $q->whereIn($field, $vals);
                }
            }
        }

        return DataTables::of($q)
            ->addIndexColumn()
            ->addColumn('is_featured', fn($m) => $m->is_featured ? 'مميز' : 'غير مميز')
            ->addColumn('status_label', fn($m) => match ($m->status) {
                'published' => 'منشور',
                'archived' => 'مؤرشف',
                default => 'مسودة'
            })
            ->addColumn('edit', fn($m) => $m->id)
            ->make(true);
    }

    public function getFilterOptions(Request $request, string $column)
    {
        $q = $this->repo->getQuery();

        if ($request->active_filters) {
            foreach ($request->active_filters as $field => $values) {
                if ($field === $column) continue;
                $vals = array_values(array_filter((array)$values, fn($v) => !in_array($v, ['الكل', 'all', 'All'])));
                if (!$vals) continue;
                if ($field === 'is_featured') {
                    $map = ['مميز' => 1, '1' => 1, 1 => 1, true => 1, 'غير مميز' => 0, '0' => 0, 0 => 0, false => 0];
                    $q->whereIn('is_featured', array_map(fn($v) => $map[$v] ?? $v, $vals));
                } else {
                    $q->whereIn($field, $vals);
                }
            }
        }

        if ($column === 'status') {
            return response()->json(['draft' => 'مسودة', 'published' => 'منشور', 'archived' => 'مؤرشف']);
        }
        if ($column === 'is_featured') {
            return response()->json(['مميز', 'غير مميز']);
        }

        $unique = $q->whereNotNull($column)->where($column, '!=', '')
            ->distinct()->pluck($column)->filter()->values()->toArray();
        return response()->json($unique);
    }

    public function save(array $data)
    {
        DB::beginTransaction();
        try {
            // من أنشأ؟
            $data['created_by'] = $data['created_by'] ?? optional(Auth::guard('admin')->user())->id;

            // التقط العلاقات
            $categoryIds = $data['category_ids'] ?? [];
            $cast   = $data['cast']   ?? [];
            $video_files   = $data['video_files']   ?? [];
            $subtitles   = $data['subtitles']   ?? [];
            unset($data['category_ids'], $data['cast'], $data['video_files'], $data['subtitles']);

            // الصور (نفضل *_out إن وُجدت وقيمتها غير فارغة)
            if (array_key_exists('poster_url_out', $data) && $data['poster_url_out'] !== null && $data['poster_url_out'] !== '') {
                $data['poster_url'] = $data['poster_url_out'];
            } else {
                $data['poster_url'] = $data['poster_url'] ?? null;
            }

            if (array_key_exists('backdrop_url_out', $data) && $data['backdrop_url_out'] !== null && $data['backdrop_url_out'] !== '') {
                $data['backdrop_url'] = $data['backdrop_url_out'];
            } else {
                $data['backdrop_url'] = $data['backdrop_url'] ?? null;
            }

            unset($data['poster_url_out'], $data['backdrop_url_out']);

            // slug
            $data['slug'] = Str::slug($data['title_en'] ?? $data['title_ar']);

            // إنشاء الفيلم
            $movie = $this->repo->save($data);

            // مزامنة العلاقات
            $this->syncCategories($movie, $categoryIds ?? []);
            $this->syncCast($movie, $cast ?? []);
            $this->syncVideoFiles($movie, $video_files ?? []);
            $this->syncSubtitles($movie, $subtitles ?? []);
            DB::commit();
            return $movie;
        } catch (\Throwable $e) {
            DB::rollBack();
            // throw $e;
            return back()->with('danger', $e->getMessage());
        }
    }

    public function update(array $data, int $id)
    {
        DB::beginTransaction();
        try {
            $movie = $this->repo->getById($id);

            // التقط العلاقات
            $categoryIds = $data['category_ids'] ?? [];
            $cast   = $data['cast']   ?? [];
            $video_files   = $data['video_files']   ?? [];
            $subtitles   = $data['subtitles']   ?? [];
            unset($data['category_ids'], $data['cast'], $data['video_files'], $data['subtitles']);
            // الصور: نفضل *_out إذا وُجدت وغير فارغة، وإلا نحافظ على القيمة السابقة
            if (array_key_exists('poster_url_out', $data) && $data['poster_url_out'] !== null && $data['poster_url_out'] !== '') {
                $data['poster_url'] = $data['poster_url_out'];
            } else {
                $data['poster_url'] = $data['poster_url'] ?? $movie->poster_url;
            }

            if (array_key_exists('backdrop_url_out', $data) && $data['backdrop_url_out'] !== null && $data['backdrop_url_out'] !== '') {
                $data['backdrop_url'] = $data['backdrop_url_out'];
            } else {
                $data['backdrop_url'] = $data['backdrop_url'] ?? $movie->backdrop_url;
            }

            unset($data['poster_url_out'], $data['backdrop_url_out']);

            // slug
            $data['slug'] = Str::slug($data['title_en'] ?? $data['title_ar']);

            // تحديث بيانات الفيلم
            $movie = $this->repo->update($data, $id);

            // مزامنة العلاقات
            $this->syncCategories($movie, $categoryIds ?? []);
            $this->syncCast($movie, $cast ?? []);
            $this->syncVideoFiles($movie, $video_files ?? [], true);
            $this->syncSubtitles($movie, $subtitles ?? []);

            DB::commit();
            return $movie;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
            return back()->with('danger', $e->getMessage());
        }
    }
    public function deleteById(int $id)
    {
        DB::beginTransaction();
        try {
            $deleted = $this->repo->delete($id);
            DB::commit();
            return $deleted;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
    /** --- Helpers --- */

    private function syncCategories(Movie $movie, array $categoryIds): void
    {
        $ids = array_filter(array_map('intval', $categoryIds)); // تنظيف
        $movie->categories()->sync($ids); // يضيف ويحذف حسب الحالة
    }

    private function syncCast(Movie $movie, array $castRows): void
    {
        // castRows: [ ['person_id'=>..,'role_type'=>..,'character_name'=>..,'sort_order'=>..], ... ]
        $pivotData = [];
        foreach ($castRows as $row) {
            if (!isset($row['person_id'])) continue;
            $pivotData[$row['person_id']] = [
                'role_type'           => $row['role_type'] ?? 'actor',
                'character_name' => $row['character_name'] ?? null,
                'sort_order'       => $row['sort_order']   ?? 0,
            ];
        }
        $movie->people()->sync($pivotData); // يحفظ ويحدث القيم الإضافية على الـpivot
    }

    private function syncVideoFiles(Movie $movie, array $files, bool $replace = false): void
    {

        // if ($replace) {
        //     $movie->videoFiles()->delete();
        // }

        $payload = [];
        $usedTypes = [];
        $usedQualities = [];
        foreach ($files as $f) {
            $type       = $f['video_type'] ?? null;
            $quality    = $f['quality']    ?? null;
            $sourceType = $f['source_type'] ?? 'url';
            if (!$type || !$quality) continue;

            // التحقق من وجود file أو file_url أولاً
            if ((!isset($f['file']) || empty($f['file'])) &&
                (!isset($f['file_url']) || empty($f['file_url']))) {
                continue;
            }

            // الآن نتحقق من التكرار (بعد التأكد من وجود بيانات صالحة)
            if (in_array($type, $usedTypes) && in_array($quality, $usedQualities)) {
                continue; // تجاهل المكرر
            }

            // إضافة للمصفوفات فقط إذا كان العنصر صالح للتخزين
            $usedTypes[] = $type;
            $usedQualities[] = $quality;
            $fileUrl = isset($f['file_url']) ? $f['file_url'] : null;
            $format  = $f['format']   ?? null;
            $size    = null;
            // لو رُفع ملف
            if ($sourceType == 'file') {
                if ($replace) {
                    $movie->videoFiles()->where('video_type', $type)->where('quality', $quality)->delete();
                }
                if (isset($f['file']) && $f['file'] instanceof \Illuminate\Http\UploadedFile) {
                    $path    = $f['file']->store('video_files/movies', 'public');
                    $fileUrl = Storage::url($path);
                    $format  = $format ?: strtolower($f['file']->getClientOriginalExtension());
                    $size    = $f['file']->getSize();
                } else {
                    // ما في ملف جديد؟ استخدم الرابط القديم إن وُجد
                    $fileUrl = $f['existing_url'] ?? null;
                }
            } else { // url
                $fileUrl = $f['file_url'] ?? null;
            }

            // لو ما في لا ملف ولا رابط → تجاهل هذا الصف
            if (!$fileUrl) {
                continue;
            }

            $payload[] = [
                'content_type'     => 'movie',
                'content_id'       => $movie->id,
                'video_type'       => $type,
                'quality'          => $quality,
                'format'           => $format,
                'file_url'         => $fileUrl,
                'file_size'        => $size,
                'duration_seconds' => null,      // ممكن نحسبها لاحقاً بـ ffmpeg
                'is_downloadable'  => false,
                'is_active'        => true,
            ];
            if ($type == 'trailer') {
                $movie->trailer_url = $fileUrl;
                $movie->save();
            }
        }
        if(empty($files)){
            $movie->videoFiles()->delete();
        }

        if (!empty($payload)) {
            $movie->videoFiles()->createMany($payload); // morphMany يملأ content_type/id تلقائيًا
        }
    }



    private function syncSubtitles(Movie $movie, array $subs, bool $replace = false): void
    {
        // if ($replace) {
        //     $movie->subtitles()->delete();
        // }

        $payload = [];
        $seenLangs  = [];
        $seenLabels = [];

        foreach ($subs as $s) {
            $lang  = isset($s['language']) ? strtolower(trim($s['language'])) : null;
            $label = isset($s['label'])    ? trim($s['label'])               : null;

            if (!$lang || !$label) continue;

            // منع تكرار اللغة / الليبل على مستوى السيرفر (حماية إضافية)
            if (in_array($lang, $seenLangs, true) && in_array($label, $seenLabels, true)) {
                continue;
            }

            $sourceType = $s['source_type'] ?? 'url';
            $url = null;

            // إذا مرفوع ملف
            if ($sourceType === 'file') {
                if ($replace) {
                    $movie->subtitles()->where('language', $lang)->where('label', $label)->delete();
                }
                if (isset($s['file']) && $s['file'] instanceof \Illuminate\Http\UploadedFile) {
                    $path = $s['file']->store('subtitle_files/movies', 'public');
                    $url  = Storage::url($path);
                } else {
                    $url  = $s['existing_url'] ?? null;
                }
            } else { // url
                $url = $s['url'] ?? null;
            }

            if (!$url) continue; // لا تضف صف فاضي

            $payload[] = [
                'content_type' => 'movie',
                'content_id'   => $movie->id,
                'language'   => $lang,
                'label'      => $label,
                'url'        => $url,
                'is_default' => !empty($s['is_default']),
            ];

            $seenLangs[]  = $lang;
            $seenLabels[] = $label;
        }

        if(empty($subs)){
            $movie->subtitles()->delete();
        }

        if (!empty($payload)) {
            // فرض "واحد فقط افتراضي": نخلي أول واحد true والباقي false إن وجد أكثر من واحد
            $defaultFound = false;
            foreach ($payload as &$row) {
                if ($row['is_default']) {
                    if ($defaultFound) {
                        $row['is_default'] = false;
                    } else {
                        $defaultFound = true;
                    }
                }
            }
            unset($row);

            $movie->subtitles()->createMany($payload);
        }
    }
}
