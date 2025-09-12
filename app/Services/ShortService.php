<?php

namespace App\Services;

use App\Models\Short;
use App\Repositories\ShortRepository;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class ShortService
{
    public function __construct(private ShortRepository $repo) {}

    public function datatableIndex(Request $request)
    {
        $q = $this->repo->getQuery();

        if ($request->column_filters) {
            foreach ($request->column_filters as $field => $values) {
                $vals = array_values(array_filter((array)$values, fn($v) => !in_array($v, ['الكل','all','All'])));
                if (!$vals) continue;

                if ($field === 'is_featured') {
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
            ->addColumn('status_label', fn($m) => $m->status === 'active' ? 'نشط' : 'غير نشط')
            ->addColumn('edit', fn($m) => $m->id)
            ->make(true);
    }

    public function getFilterOptions(Request $request, string $column)
    {
        $q = $this->repo->getQuery();

        if ($request->active_filters) {
            foreach ($request->active_filters as $field => $values) {
                if ($field === $column) continue;
                $vals = array_values(array_filter((array)$values, fn($v) => !in_array($v, ['الكل','all','All'])));
                if (!$vals) continue;

                if ($field === 'is_featured') {
                    $map = ['مميز' => 1, '1' => 1, 1 => 1, true => 1, 'غير مميز' => 0, '0' => 0, 0 => 0, false => 0];
                    $q->whereIn('is_featured', array_map(fn($v) => $map[$v] ?? $v, $vals));
                } else {
                    $q->whereIn($field, $vals);
                }
            }
        }

        if ($column === 'status')   return response()->json(['active' => 'نشط', 'inactive' => 'غير نشط']);
        if ($column === 'is_featured') return response()->json(['مميز', 'غير مميز']);

        $unique = $q->whereNotNull($column)->where($column, '!=', '')
            ->distinct()->pluck($column)->filter()->values()->toArray();

        return response()->json($unique);
    }

    public function getById(int $id) { return $this->repo->getById($id); }

    public function save(array $data)
    {
        DB::beginTransaction();
        try {
            $data['created_by'] = $data['created_by'] ?? optional(Auth::guard('admin')->user())->id;

            $categoryIds = $data['category_ids'] ?? [];
            $videoFiles  = $data['video_files']  ?? [];
            unset($data['category_ids'], $data['video_files']);

            if (array_key_exists('poster_path_out', $data) && $data['poster_path_out'] !== null && $data['poster_path_out'] !== '') {
                $data['poster_path'] = $data['poster_path_out'];
            } else {
                $data['poster_path'] = $data['poster_path'] ?? null;
            }
            unset($data['poster_path_out']);
            $data['video_path'] = '-';

            $short = $this->repo->save($data);

            $this->syncCategories($short, $categoryIds);
            $this->syncVideoFiles($short, $videoFiles);

            DB::commit();
            return $short;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(array $data, int $id)
    {
        DB::beginTransaction();
        try {
            $short = $this->repo->getById($id);

            $categoryIds = $data['category_ids'] ?? [];
            $videoFiles  = $data['video_files']  ?? [];
            unset($data['category_ids'], $data['video_files']);

            if (array_key_exists('poster_path_out', $data) && $data['poster_path_out'] !== null && $data['poster_path_out'] !== '') {
                $data['poster_path'] = $data['poster_path_out'];
            } else {
                $data['poster_path'] = $data['poster_path'] ?? $short->poster_path;
            }
            unset($data['poster_path_out']);

            $data['video_path'] = '-';

            $short = $this->repo->update($data, $id);

            $this->syncCategories($short, $categoryIds);
            $this->syncVideoFiles($short, $videoFiles, replace: true);

            DB::commit();
            return $short;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
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

    /* ===== Helpers ===== */

    private function syncCategories(Short $short, array $categoryIds): void
    {
        $ids = array_filter(array_map('intval', $categoryIds)); // تنظيف
        $short->categories()->sync($ids);
    }



    private function syncVideoFiles(Short $short, array $files, bool $replace = false): void
    {

        // if ($replace) {
        //     $short->videoFiles()->delete();
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
            if (in_array($type, $usedTypes) || in_array($quality, $usedQualities)) {
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
                    $short->videoFiles()->where('video_type', $type)->where('quality', $quality)->delete();
                }
                if (isset($f['file']) && $f['file'] instanceof \Illuminate\Http\UploadedFile) {
                    $path    = $f['file']->store('video_files/shorts', 'public');
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
                'content_type'     => 'short',
                'content_id'       => $short->id,
                'video_type'       => $type,
                'quality'          => $quality,
                'format'           => $format,
                'file_url'         => $fileUrl,
                'file_size'        => $size,
                'duration_seconds' => null,      // ممكن نحسبها لاحقاً بـ ffmpeg
                'is_downloadable'  => false,
                'is_active'        => true,
            ];
            if ($type == 'main') {
                $short->video_path = $fileUrl;
                $short->save();
            }
        }

        if (!empty($payload)) {
            $short->videoFiles()->createMany($payload); // morphMany يملأ content_type/id تلقائيًا
        }
    }


    private function isExternalUrl(string $v): bool
    {
        return str_starts_with($v, 'http://') || str_starts_with($v, 'https://');
    }
}
