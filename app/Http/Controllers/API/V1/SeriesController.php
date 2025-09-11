<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\SeriesResource;
use App\Models\Series;
use Illuminate\Http\Request;

class SeriesController extends Controller
{
    public function index()
    {
        $series = Series::published()->all();
        return SeriesResource::collection($series);
    }

    public function show(Series $series)
    {
        return new SeriesResource($series);
    }
}
