<?php

namespace App\Jobs;

use App\Models\Season;
use App\Models\Series;
use App\Services\TMDBService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncSeriesSeasonsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $seriesId,
        public int $tmdbId
    ) {}

    public function handle()
    {
        $series = Series::find($this->seriesId);
        if (!$series) return;

        $tmdb = new TMDBService();
        $data = $tmdb->get("tv/{$this->tmdbId}");

        if (empty($data['seasons'])) return;

        foreach ($data['seasons'] as $season) {
            $seasonModel = Season::updateOrCreate(
                [
                    'series_id' => $series->id,
                    'season_number' => $season['season_number'],
                ],
                [
                    'title_ar' => $season['name'] ?? null,
                    'title_en' => $season['name'] ?? null,
                    'description_ar' => $season['overview'] ?? null,
                    'description_en' => $season['overview'] ?? null,
                    'poster_url' => !empty($season['poster_path'])
                        ? 'https://image.tmdb.org/t/p/w500' . $season['poster_path']
                        : null,
                    'air_date' => $season['air_date'] ?? null,
                    'episode_count' => $season['episode_count'] ?? 0,
                    'status' => 'draft',
                    'tmdb_id' => $season['id'] ?? null,
                ]
            );

            // SyncSeasonEpisodesJob::dispatch($seasonModel->id, $this->tmdbId, $season['season_number']);
        }
    }
}
