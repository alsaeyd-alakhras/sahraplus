<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Season;
use App\Models\Episode;
use App\Http\Resources\EpisodeResource;
use App\Services\ProfileContextService;
use Illuminate\Http\Request;

class EpisodeController extends Controller
{
    protected ProfileContextService $profileContextService;

    public function __construct(ProfileContextService $profileContextService)
    {
        $this->profileContextService = $profileContextService;
    }
    // GET /api/v1/seasons/{season}/episodes
    public function bySeason(Request $request, Season $season)
    {
        $profile = $this->profileContextService->resolveProfile($request);
        if ($this->profileContextService->shouldApplyKidsFilter($profile)) {
            $series = $season->series;
            if (!$series->is_kids) {
                return response()->json(['message' => 'هذا المسلسل غير متاح لملفات الأطفال'], 403);
            }
        }
        $episodes = $season->episodes()->orderBy('episode_number')->get();
        return EpisodeResource::collection($episodes);
    }

    // GET /api/v1/episodes/{id}
    public function show(Request $request, Episode $episode)
    {
        $profile = $this->profileContextService->resolveProfile($request);
        if ($this->profileContextService->shouldApplyKidsFilter($profile)) {
            $series = $episode->season->series;
            if (!$series->is_kids) {
                return response()->json(['message' => 'هذا المسلسل غير متاح لملفات الأطفال'], 403);
            }
        }
        $episode->load(['videoFiles','subtitles','comments.user']);
        return new EpisodeResource($episode);
    }
}
