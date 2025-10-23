<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Season;
use App\Models\Episode;
use App\Http\Resources\EpisodeResource;

class EpisodeController extends Controller
{
    // GET /api/v1/seasons/{season}/episodes
    public function bySeason(Season $season)
    {
        $episodes = $season->episodes()->orderBy('episode_number')->get();
        return EpisodeResource::collection($episodes);
    }

    // GET /api/v1/episodes/{id}
    public function show(Episode $episode)
    {
        $episode->load(['videoFiles','subtitles','comments.user']);
        return new EpisodeResource($episode);
    }
}
