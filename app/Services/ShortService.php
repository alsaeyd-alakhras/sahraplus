<?php

namespace App\Services;

use App\Models\Short;
use App\Models\VideoFiles;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Repositories\ShortRepository;

class ShortService
{
    public function __construct(private ShortRepository $repo) {}

    public function save(array $data)
    {
        DB::beginTransaction();
        try {
            $data['created_by'] = $data['created_by'] ?? optional(Auth::guard('admin')->user())->id;

            // ====== جهّز video_path من أول فيديو مُرسل ======
            $videoRows = $data['video_files'] ?? [];
            unset($data['video_files']);

            $primaryUrl = null;
            if (!empty($videoRows)) {
                // خُذ أول صف (ولو بدك خُذ أول main)
                $first = collect($videoRows)
                    ->sortBy('sort_order') // لو عندك ترتيب
                    ->first();

                if ($first) {
                    if (($first['source_type'] ?? 'url') === 'file' && !empty($first['file']) && $first['file'] instanceof UploadedFile) {
                        // ارفع الملف وطلع رابط التخزين
                        $stored = $first['file']->store('shorts/videos', 'public');
                        $primaryUrl = asset('storage/' . $stored);
                        // خزن هذا الرابط ضمن الصف نفسه ليستمر للحفظ في video_files
                        $first['file_url'] = $primaryUrl;
                    } else {
                        $primaryUrl = $first['file_url'] ?? null;
                    }
                    // أعِد حقن أول صف بعد التطبيع
                    $videoRows[0] = $first;
                }
            }

            // إجبار وجود قيمة بما أن العمود NOT NULL
            if (empty($primaryUrl)) {
                // ما في ولا فيديو = خلّي الفاليديشن يفشل لاحقًا أو ارمي إستثناء واضح
                throw new \RuntimeException(__('admin.video_path_required'));
            }
            $data['video_path'] = $primaryUrl;

            // بوستر (نفس منطقك)
            if (!empty($data['poster_path_out'])) {
                $data['poster_path'] = $data['poster_path_out'];
            }
            unset($data['poster_path_out']);

            // إنشاء الـ Short
            $short = $this->repo->save($data);

            // ====== حفظ بقية ملفات الفيديو في جدول video_files ======
            foreach ($videoRows as $row) {
                if (($row['source_type'] ?? 'url') === 'file' && !empty($row['file']) && $row['file'] instanceof UploadedFile) {
                    $stored = $row['file']->store('shorts/videos', 'public');
                    $row['file_url'] = asset('storage/' . $stored);
                }

                if (empty($row['file_url'])) continue;

                // استنتاج الـ format لو مش مرسل
                if (empty($row['format'])) {
                    $ext = strtolower(pathinfo(parse_url($row['file_url'], PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
                    $row['format'] = in_array($ext, ['mp4', 'webm', 'm3u8']) ? $ext : 'mp4';
                }
                $short->videoFiles()->create([
                    'video_type'       => $row['video_type'] ?? 'main',
                    'quality'          => $row['quality'] ?? '480p',
                    'format'           => $row['format'] ?? 'mp4',
                    'file_url'         => $row['file_url'],
                    'file_size'        => $row['file_size'] ?? null,
                    'duration_seconds' => $row['duration_seconds'] ?? null,
                    'is_downloadable'  => !empty($row['is_downloadable']),
                    'is_active'        => array_key_exists('is_active', $row) ? (bool)$row['is_active'] : true,
                ]);
            }

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

            $videoRows = $data['video_files'] ?? [];
            unset($data['video_files']);

            // بوستر
            if (!empty($data['poster_path_out'])) {
                $data['poster_path'] = $data['poster_path_out'];
            }
            unset($data['poster_path_out']);

            // لو وصل فيديوهات جديدة، حدّث video_path من أول صف
            if (!empty($videoRows)) {
                $first = collect($videoRows)->first();
                $primaryUrl = null;
                if ($first) {
                    if (($first['source_type'] ?? 'url') === 'file' && !empty($first['file']) && $first['file'] instanceof UploadedFile) {
                        $stored = $first['file']->store('shorts/videos', 'public');
                        $primaryUrl = asset('storage/' . $stored);
                        $first['file_url'] = $primaryUrl;
                        $videoRows[0] = $first;
                    } else {
                        $primaryUrl = $first['file_url'] ?? null;
                    }
                }
                if (!empty($primaryUrl)) {
                    $data['video_path'] = $primaryUrl;
                }
            }

            $short = $this->repo->update($data, $id);

            // لو بدك: امسح القديم ثم أعد الإدخال أو اعمل upsert حسب مفاتيحك
            foreach ($videoRows as $row) {
                if (($row['source_type'] ?? 'url') === 'file' && !empty($row['file']) && $row['file'] instanceof UploadedFile) {
                    $stored = $row['file']->store('shorts/videos', 'public');
                    $row['file_url'] = asset('storage/' . $stored);
                }
                if (empty($row['file_url'])) continue;

                if (empty($row['format'])) {
                    $ext = strtolower(pathinfo(parse_url($row['file_url'], PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
                    $row['format'] = in_array($ext, ['mp4', 'webm', 'm3u8']) ? $ext : 'mp4';
                }

                $short->videoFiles()->create([
                    'video_type'       => $row['video_type'] ?? 'main',
                    'quality'          => $row['quality'] ?? '480p',
                    'format'           => $row['format'] ?? 'mp4',
                    'file_url'         => $row['file_url'],
                    'file_size'        => $row['file_size'] ?? null,
                    'duration_seconds' => $row['duration_seconds'] ?? null,
                    'is_downloadable'  => !empty($row['is_downloadable']),
                    'is_active'        => array_key_exists('is_active', $row) ? (bool)$row['is_active'] : true,
                ]);
            }

            DB::commit();
            return $short;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
