<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Movie;
use App\Models\Series;
use App\Models\Episode;
use App\Http\Resources\MovieResource;
use App\Http\Resources\SeriesResource;
use App\Http\Resources\EpisodeResource;

class SearchController extends Controller
{
    // GET /api/v1/search?q=keyword&type=movie|series|episode
    public function index(Request $request)
    {
        $q = $request->query('q');
        $type = $request->query('type');

        abort_if(!$q, 422, 'Missing search keyword');

        if ($type === 'movie') {
            return MovieResource::collection(Movie::where('title_ar','like',"%$q%")
                ->orWhere('title_en','like',"%$q%")->limit(50)->get());
        }

        if ($type === 'series') {
            return SeriesResource::collection(Series::where('title_ar','like',"%$q%")
                ->orWhere('title_en','like',"%$q%")->limit(50)->get());
        }

        if ($type === 'episode') {
            return EpisodeResource::collection(Episode::where('title','like',"%$q%")->limit(50)->get());
        }

        // بحث عام
        return response()->json([
            'movies'   => MovieResource::collection(Movie::where('title_ar','like',"%$q%")
                ->orWhere('title_en','like',"%$q%")->limit(20)->get()),
            'series'   => SeriesResource::collection(Series::where('title_ar','like',"%$q%")
                ->orWhere('title_en','like',"%$q%")->limit(20)->get()),
            'episodes' => EpisodeResource::collection(Episode::where('title','like',"%$q%")->limit(20)->get()),
        ]);
    }
}
