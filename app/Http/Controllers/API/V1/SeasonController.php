<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Series;
use App\Models\Season;
use App\Http\Resources\SeasonResource;

class SeasonController extends Controller
{
    // GET /api/v1/series/{series}/seasons
    public function bySeries(Series $series)
    {
        $seasons = $series->seasons()->orderBy('number')->get();
        return SeasonResource::collection($seasons);
    }

    // GET /api/v1/seasons/{id}
    public function show(Season $season)
    {
        $season->load(['series','episodes']);
        return new SeasonResource($season);
    }
}
