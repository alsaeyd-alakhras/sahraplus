<?php

namespace App\Services;

use App\Models\Short;
use App\Repositories\ShortRepository;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class ShortService
{
    public function __construct(private ShortRepository $repo) {}

    public function datatableIndex(Request $request)
    {
        $query = $this->repo->getQuery();

        if ($request->column_filters) {
            foreach ($request->column_filters as $fieldName => $values) {
                if (! empty($values)) {
                    $filteredValues = array_filter($values, fn ($v) => ! in_array($v, ['الكل', 'all', 'All']));
                    if (! empty($filteredValues)) {
                        $query->whereIn($fieldName, $filteredValues);
                    }
                }
            }
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('edit', fn ($m) => $m->id)
            ->make(true);
    }

    public function getFilterOptions(Request $request, string $column)
    {
        $query = $this->repo->getQuery();

        if ($request->active_filters) {
            foreach ($request->active_filters as $fieldName => $values) {
                if (! empty($values) && $fieldName !== $column) {
                    $filteredValues = array_filter($values, fn ($v) => ! in_array($v, ['الكل', 'all', 'All']));
                    if (! empty($filteredValues)) {
                        $query->whereIn($fieldName, $filteredValues);
                    }
                }
            }
        }

        $uniqueValues = $query->whereNotNull($column)
            ->where($column, '!=', '')->distinct()->pluck($column)->filter()->values()->toArray();

        return response()->json($uniqueValues);
    }

    // private function getVideoInfo($fileOrUrl)
    // {
    //     $isUrl = filter_var($fileOrUrl, FILTER_VALIDATE_URL);

    //     if ($isUrl) {
    //         // رابط إنترنت → خزّنه مؤقتاً
    //         $tempDir = storage_path('app/temp_videos');
    //         if (!file_exists($tempDir)) mkdir($tempDir, 0755, true);
    //         $tempPath = $tempDir . '/video_' . uniqid() . '.' . pathinfo(parse_url($fileOrUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
    //         $content = @file_get_contents($fileOrUrl);
    //         if (!$content) throw new \RuntimeException("Unable to download video from URL: $fileOrUrl");
    //         file_put_contents($tempPath, $content);
    //         $filePath = $tempPath;
    //     } else {
    //         // ملف محلي → حوله لمسار كامل
    //         $filePath = $fileOrUrl;
    //         if (Str::startsWith($fileOrUrl, '/storage')) {
    //             $filePath = storage_path('app/public' . Str::after($fileOrUrl, '/storage'));
    //         }
    //         if (!file_exists($filePath)) {
    //             throw new \RuntimeException("Local file does not exist: $filePath");
    //         }
    //     }

    //     $ffprobe = \FFMpeg\FFProbe::create();
    //     $stream = $ffprobe->streams($filePath)->videos()->first();
    //     $height = $stream->get('height');
    //     $duration = $ffprobe->format($filePath)->get('duration');

    //     // حذف الملف المؤقت لو موجود
    //     if ($isUrl && file_exists($tempPath)) unlink($tempPath);

    //     $quality = ($height >= 1080) ? '1080p' : (($height >= 720) ? '720p' : '480p');
    //     $ext = pathinfo(parse_url($fileOrUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
    //     $format_video = $ext ?: 'mp4';
    //     return [
    //         'quality' => $quality,
    //         'format' => $format_video,
    //         'duration_seconds' => (int)$duration,
    //     ];
    // }

    public function save(array $data)
    {
        DB::beginTransaction();
        try {
            $data['created_by'] = $data['created_by'] ?? optional(Auth::guard('admin')->user())->id;

            $video_files = $data['video_files'] ?? [];
            $categoryIds = $data['category_ids'] ?? [];
            unset($data['video_files'], $data['category_ids']);
            $primaryUrl = null;
            if (! empty($video_files)) {
                // خُذ أول صف (ولو بدك خُذ أول main)
                $first = collect($video_files)
                    ->sortBy('sort_order') // لو عندك ترتيب
                    ->first();

                if ($first) {
                    if (($first['source_type'] ?? 'url') === 'file' && ! empty($first['file']) && $first['file'] instanceof UploadedFile) {
                        // ارفع الملف وطلع رابط التخزين
                        $stored = $first['file']->store('shorts/videos', 'public');
                        $primaryUrl = asset('storage/'.$stored);
                        // خزن هذا الرابط ضمن الصف نفسه ليستمر للحفظ في video_files
                        $first['file_url'] = $primaryUrl;
                    } else {
                        $primaryUrl = $first['file_url'] ?? null;
                    }
                    // أعِد حقن أول صف بعد التطبيع
                    $videoRows[0] = $first;
                }
            }

            // if (empty($primaryUrl)) {
            //     throw new \RuntimeException(__('admin.video_path_required'));
            // }
            $data['video_path'] = $primaryUrl;
            if (! empty($data['poster_path_out'])) {
                $data['poster_path'] = $data['poster_path_out'];
            }
            unset($data['poster_path_out']);

            $short = $this->repo->save($data);
            $short->update([
                'share_url' => route('site.shorts', ['short_id' => $short->id]),
            ]);
            $this->syncCategories($short, $categoryIds ?? []);
            $this->syncVideoFiles($short, $video_files ?? []);

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

            $video_files = $data['video_files'] ?? [];
            $categoryIds = $data['category_ids'] ?? [];
            unset($data['category_ids'], $data['video_files']);

            $primaryUrl = null;
            if (! empty($video_files)) {
                $first = collect($video_files)
                    ->sortBy('sort_order') // لو عندك ترتيب
                    ->first();

                if ($first) {
                    if (($first['source_type'] ?? 'url') === 'file' && ! empty($first['file']) && $first['file'] instanceof UploadedFile) {
                        $stored = $first['file']->store('shorts/videos', 'public');
                        $primaryUrl = asset('storage/'.$stored);
                        $first['file_url'] = $primaryUrl;
                    } else {
                        $primaryUrl = $first['file_url'] ?? null;
                    }
                    $videoRows[0] = $first;
                }
            }

            // if (empty($primaryUrl)) {
            //     throw new \RuntimeException(__('admin.video_path_required'));
            // }
            if (! empty($data['poster_path_out'])) {
                $data['poster_path'] = $data['poster_path_out'];
            }
            unset($data['poster_path_out']);

            $short = $this->repo->update($data, $id);
            $short->update([
                'share_url' => route('site.shorts', ['short_id' => $short->id]),
            ]);
            $this->syncCategories($short, $categoryIds ?? []);
            $this->syncVideoFiles($short, $video_files ?? [], true);
            DB::commit();

            return $short;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteById($id)
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

    private function syncCategories(Short $short, array $categoryIds): void
    {
        $ids = array_filter(array_map('intval', $categoryIds)); // تنظيف
        $short->categories()->sync($ids); // يضيف ويحذف حسب الحالة
    }

    private function syncVideoFiles(Short $short, array $files, bool $replace = false): void
    {

        if ($replace) {
            $short->videoFiles()->delete();
        }

        $payload = [];
        $usedTypes = [];
        $usedQualities = [];
        foreach ($files as $f) {
            $type = $f['video_type'] ?? null;
            $quality = $f['quality'] ?? null;
            $sourceType = $f['source_type'] ?? 'url';
            if (! $type || ! $quality) {
                continue;
            }

            // التحقق من وجود file أو file_url أولاً
            if ((! isset($f['file']) || empty($f['file'])) &&
                (! isset($f['file_url']) || empty($f['file_url']))
            ) {
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
            $format = $f['format'] ?? 'mp4';
            $size = null;
            // لو رُفع ملف
            if ($sourceType == 'file') {
                if ($replace) {
                    $short->videoFiles()->where('video_type', $type)->where('quality', $quality)->delete();
                }
                if (isset($f['file']) && $f['file'] instanceof \Illuminate\Http\UploadedFile) {
                    $path = $f['file']->store('video_files/shorts', 'public');
                    $fileUrl = Storage::url($path);
                    $format = $format ?: strtolower($f['file']->getClientOriginalExtension());
                    $size = $f['file']->getSize();
                } else {
                    // ما في ملف جديد؟ استخدم الرابط القديم إن وُجد
                    $fileUrl = $f['existing_url'] ?? null;
                }
            } else { // url
                $fileUrl = $f['file_url'] ?? null;
            }

            // لو ما في لا ملف ولا رابط → تجاهل هذا الصف
            if (! $fileUrl) {
                continue;
            }

            $payload[] = [
                'content_type' => 'movie',
                'content_id' => $short->id,
                'video_type' => $type,
                'quality' => $quality,
                'format' => $format,
                'file_url' => $fileUrl,
                'file_size' => $size,
                'duration_seconds' => null,      // ممكن نحسبها لاحقاً بـ ffmpeg
                'is_downloadable' => false,
                'is_active' => true,
            ];
            if ($type == 'trailer') {
                $short->trailer_url = $fileUrl;
                $short->save();
            }
        }
        if (empty($files)) {
            $short->videoFiles()->delete();
        }

        if (! empty($payload)) {
            $short->videoFiles()->createMany($payload); // morphMany يملأ content_type/id تلقائيًا
        }
    }
}
