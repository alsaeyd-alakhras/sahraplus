<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Series;
use App\Http\Resources\SeriesResource;
use Illuminate\Http\Request;

class SeriesController extends Controller
{
    // GET /api/v1/series
    public function index(Request $request)
    {
        $q = $request->query('q');

        $series = Series::with('categories')
            ->when($q, fn($qr)=>$qr->where('title_ar','like',"%$q%")->orWhere('title_en','like',"%$q%"))
            ->orderByDesc('created_at')
            ->paginate(20);

        return SeriesResource::collection($series);
    }

    // GET /api/v1/series/{id}
    public function show($id)
    {
        $series = Series::findOrFail($id);
        $series->load(['categories', 'seasons']);
        return new SeriesResource($series);
    }
}
