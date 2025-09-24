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
        $payload = [];
        $seen = [];

        foreach ($files as $f) {
            $type       = $f['video_type'] ?? null;
            $quality    = $f['quality']    ?? null;
            $sourceType = $f['source_type'] ?? 'url';

            if (!$type || !$quality) continue;

            if (empty($f['file']) && empty($f['file_url'])) continue;

            $key = $type.'_'.$quality;
            if (isset($seen[$key])) continue;
            $seen[$key] = true;

            $fileUrl = null;
            $format  = $f['format'] ?? null;
            $size    = null;

            if ($sourceType === 'file' && isset($f['file']) && $f['file'] instanceof \Illuminate\Http\UploadedFile) {
                if ($replace) {
                    $episode->videoFiles()->where('video_type', $type)->where('quality', $quality)->delete();
                }
                $path    = $f['file']->store('video_files/episodes', 'public');
                $fileUrl = Storage::url($path);
                $format  = $format ?: strtolower($f['file']->getClientOriginalExtension());
                $size    = $f['file']->getSize();
            } else {
                // URL فقط
                if ($replace) {
                    $episode->videoFiles()->where('video_type', $type)->where('quality', $quality)->delete();
                }
                $fileUrl = $f['file_url'] ?? null;
            }

            if (!$fileUrl) continue;

            // enum format عندك: ['mp4','hls','m3u8','webm'] — نزبطه لو ناقص
            if (!$format) {
                $ext = strtolower(pathinfo(parse_url($fileUrl, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
                $map = ['mp4'=>'mp4', 'webm'=>'webm', 'm3u8'=>'m3u8', 'hls'=>'hls'];
                $format = $map[$ext] ?? 'mp4';
            }

            $payload[] = [
                'content_type'     => 'episode',
                'content_id'       => $episode->id,
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

        if (empty($files)) {
            $episode->videoFiles()->delete();
        }

        if (!empty($payload)) {
            $episode->videoFiles()->createMany($payload);
        }
    }

    private function syncSubtitles(Episode $episode, array $subs, bool $replace = false): void
    {
        $payload = [];
        $seenPairs = [];

        foreach ($subs as $s) {
            $lang  = isset($s['language']) ? strtolower(trim($s['language'])) : null;
            $label = isset($s['label']) ? trim($s['label']) : null;
            $src   = $s['source_type'] ?? 'url';

            if (!$lang || !$label) continue;

            $pairKey = $lang.'|'.$label;
            if (isset($seenPairs[$pairKey])) continue;
            $seenPairs[$pairKey] = true;

            $fileUrl = null;

            if ($src === 'file' && isset($s['file']) && $s['file'] instanceof \Illuminate\Http\UploadedFile) {
                if ($replace) {
                    $episode->subtitles()->where('language', $lang)->where('label', $label)->delete();
                }
                $path    = $s['file']->store('subtitle_files/episodes', 'public');
                $fileUrl = Storage::url($path);
            } else {
                if ($replace) {
                    $episode->subtitles()->where('language', $lang)->where('label', $label)->delete();
                }
                // ملاحظة: عندك بالجدول اسم الحقل file_url — فلو الفورم يرسل 'url' نحوله
                $fileUrl = $s['url'] ?? $s['file_url'] ?? null;
            }

            if (!$fileUrl) continue;

            // format enum عندك: vtt|srt|ass
            $ext = strtolower(pathinfo(parse_url($fileUrl, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
            $fmt = in_array($ext, ['vtt','srt','ass']) ? $ext : 'vtt';

            $payload[] = [
                'content_type' => 'episode',
                'content_id'   => $episode->id,
                'language'     => $lang,
                'label'        => $label,
                'file_url'     => $fileUrl,  // 👈 مطابق لاسم الحقل في migration
                'format'       => $fmt,
                'is_default'   => !empty($s['is_default']),
                'is_forced'    => !empty($s['is_forced']),
                'is_active'    => true,
            ];
        }

        if (empty($subs)) {
            $episode->subtitles()->delete();
        }

        if (!empty($payload)) {
            // واحد فقط افتراضي
            $defaultSeen = false;
            foreach ($payload as &$row) {
                if ($row['is_default']) {
                    if ($defaultSeen) $row['is_default'] = false;
                    else $defaultSeen = true;
                }
            }
            unset($row);

            $episode->subtitles()->createMany($payload);
        }
    }
}
