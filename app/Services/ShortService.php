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

            // حسم مصادر البوستر/الفيديو
            $data = $this->resolvePosterAndVideo($data);

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

            $data = $this->resolvePosterAndVideo($data, $short->poster_path, $short->video_path);

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
        $ids = collect($categoryIds)->map(fn($v)=>(int)$v)->filter()->unique()->values()->toArray();
        $short->categories()->sync($ids);
    }

    // ملاحظة: ما منرسل content_type ولا content_id في الـpayload
    private function syncVideoFiles(Short $short, array $files, bool $replace = false): void
    {
        if ($replace) {
            $short->videoFiles()->delete();
        }

        $payload   = [];
        $usedPairs = []; // type|quality

        foreach ($files as $f) {
            $type       = $f['video_type'] ?? null;
            $quality    = $f['quality']    ?? null;
            $sourceType = $f['source_type'] ?? 'url';
            if (!$type || !$quality) continue;

            $pairKey = $type.'|'.$quality;
            if (isset($usedPairs[$pairKey])) continue;
            $usedPairs[$pairKey] = true;

            $fileUrl = $f['file_url'] ?? null;
            $format  = $f['format']   ?? null;
            $size    = null;

            if ($sourceType === 'file') {
                if (($f['file'] ?? null) instanceof \Illuminate\Http\UploadedFile) {
                    $path    = $f['file']->store('video_files/shorts', 'public');
                    $fileUrl = $path; // خزّن المسار (مو URL)
                    $format  = $format ?: strtolower($f['file']->getClientOriginalExtension());
                    $size    = $f['file']->getSize();
                } else {
                    $fileUrl = $f['existing_url'] ?? null;
                }
            } else {
                $fileUrl = $f['file_url'] ?? null;
            }

            if (!$fileUrl) continue;

            $payload[] = [
                'video_type'       => $type,
                'quality'          => $quality,
                'format'           => $format,
                'file_url'         => $fileUrl,
                'file_size'        => $size,
                'duration_seconds' => null,
                'is_downloadable'  => false,
                'is_active'        => true,
            ];
        }

        if (!empty($payload)) {
            // morphMany سيملأ content_type و content_id تلقائيًا (باستخدام morph map)
            $short->videoFiles()->createMany($payload);
        }
    }

    private function resolvePosterAndVideo(array $data, ?string $oldPoster = null, ?string $oldVideo = null): array
    {
        // Poster
        if (($data['posterUpload'] ?? null) instanceof UploadedFile) {
            if ($oldPoster && !$this->isExternalUrl($oldPoster)) {
                Storage::disk('public')->delete($oldPoster);
            }
            $path = $data['posterUpload']->store('shorts/posters', 'public');
            $data['poster_path'] = $path;
        } elseif (!empty($data['poster_path_out'])) {
            $data['poster_path'] = $data['poster_path_out'];
        } else {
            $data['poster_path'] = $data['poster_path'] ?? $oldPoster;
        }

        // Video
        if (($data['videoUpload'] ?? null) instanceof UploadedFile) {
            if ($oldVideo && !$this->isExternalUrl($oldVideo)) {
                Storage::disk('public')->delete($oldVideo);
            }
            $path = $data['videoUpload']->store('shorts/videos', 'public');
            $data['video_path'] = $path;
        } elseif (!empty($data['video_path_out'])) {
            $data['video_path'] = $data['video_path_out'];
        } else {
            $data['video_path'] = $data['video_path'] ?? $oldVideo;
        }

        unset($data['posterUpload'], $data['poster_path_out'], $data['videoUpload'], $data['video_path_out']);

        return $data;
    }

    private function isExternalUrl(string $v): bool
    {
        return str_starts_with($v, 'http://') || str_starts_with($v, 'https://');
    }
}
