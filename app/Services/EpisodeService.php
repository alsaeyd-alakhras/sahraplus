<?php

namespace App\Services;

use App\Models\Episode;
use App\Repositories\EpisodeRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EpisodeService
{
    public function __construct(private EpisodeRepository $repo) {}

    public function save(array $data): Episode
    {
        DB::beginTransaction();
        try {
            // علاقات قادمة من الفورم
            $video_files = $data['video_files'] ?? [];
            $subtitles   = $data['subtitles']   ?? [];
            unset($data['video_files'], $data['subtitles']);

            // صورة الثمبنيل (نفس منطقك)
            if (!empty($data['thumbnail_url_out'])) {
                $data['thumbnail_url'] = $data['thumbnail_url_out'];
            }
            unset($data['thumbnail_url_out']);

            $episode = $this->repo->save($data);

            $this->syncVideoFiles($episode, $video_files, false);
            $this->syncSubtitles($episode, $subtitles, false);

            DB::commit();
            return $episode;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(array $data, int $id): Episode
    {
        DB::beginTransaction();
        try {
            $episode = $this->repo->getById($id);

            $video_files = $data['video_files'] ?? [];
            $subtitles   = $data['subtitles']   ?? [];
            unset($data['video_files'], $data['subtitles']);

            if (!empty($data['thumbnail_url_out'])) {
                $data['thumbnail_url'] = $data['thumbnail_url_out'];
            } else {
                $data['thumbnail_url'] = $data['thumbnail_url'] ?? $episode->thumbnail_url;
            }
            unset($data['thumbnail_url_out']);

            $episode = $this->repo->update($data, $id);

            $this->syncVideoFiles($episode, $video_files, true);
            $this->syncSubtitles($episode, $subtitles, true);

            DB::commit();
            return $episode;
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

    /** -------- Helpers (مطابقة لمنطق MovieService) -------- */

    private function syncVideoFiles(Episode $episode, array $files, bool $replace = false): void
    {

        if ($replace) {
            $episode->videoFiles()->delete();
        }

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
                (!isset($f['file_url']) || empty($f['file_url']))
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
            $format  = $f['format']   ?? 'mp4';
            $size    = null;
            // لو رُفع ملف
            if ($sourceType == 'file') {
                if ($replace) {
                    $episode->videoFiles()->where('video_type', $type)->where('quality', $quality)->delete();
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
                'content_id'       => $episode->id,
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
                $episode->trailer_url = $fileUrl;
                $episode->save();
            }
        }
        if (empty($files)) {
            $episode->videoFiles()->delete();
        }

        if (!empty($payload)) {
            $episode->videoFiles()->createMany($payload); // morphMany يملأ content_type/id تلقائيًا
        }
    }

    private function syncSubtitles(Episode $episode, array $subs, bool $replace = false): void
    {
        if ($replace) {
            $episode->subtitles()->delete();
        }

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
                    $episode->subtitles()->where('language', $lang)->where('label', $label)->delete();
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
                'content_id'   => $episode->id,
                'language'   => $lang,
                'label'      => $label,
                'file_url'        => $url,
                'is_default' => !empty($s['is_default']),
            ];

            $seenLangs[]  = $lang;
            $seenLabels[] = $label;
        }

        if (empty($subs)) {
            $episode->subtitles()->delete();
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

            $episode->subtitles()->createMany($payload);
        }
    }
}
