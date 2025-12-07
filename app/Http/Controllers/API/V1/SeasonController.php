<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Series;
use App\Models\Season;
use App\Http\Resources\SeasonResource;
use App\Services\ProfileContextService;
use Illuminate\Http\Request;

class SeasonController extends Controller
{
    protected ProfileContextService $profileContextService;

    public function __construct(ProfileContextService $profileContextService)
    {
        $this->profileContextService = $profileContextService;
    }
    // GET /api/v1/series/{series}/seasons
    public function bySeries(Request $request, Series $series)
    {
        $profile = $this->profileContextService->resolveProfile($request);
        if ($this->profileContextService->shouldApplyKidsFilter($profile)) {
            if (!$series->is_kids) {
                return response()->json(['message' => 'هذا المسلسل غير متاح لملفات الأطفال'], 403);
            }
        }
        $seasons = $series->seasons()->orderBy('season_number')->get();
        return SeasonResource::collection($seasons);
    }

    // GET /api/v1/seasons/{id}
    public function show(Request $request, Season $season)
    {
        $profile = $this->profileContextService->resolveProfile($request);
        if ($this->profileContextService->shouldApplyKidsFilter($profile)) {
            $series = $season->series;
            if (!$series->is_kids) {
                return response()->json(['message' => 'هذا المسلسل غير متاح لملفات الأطفال'], 403);
            }
        }
        $season->load(['series','episodes']);
        return new SeasonResource($season);
    }
}
