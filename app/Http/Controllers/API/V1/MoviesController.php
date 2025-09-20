<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Http\Resources\MovieResource;
use Illuminate\Http\Request;

class MoviesController extends Controller
{
    // GET /api/v1/movies
    public function index(Request $request)
    {
        $q = $request->query('q');
        $category = $request->query('category');
        $year = $request->query('year');

        $movies = Movie::with(['categories','cast'])
            ->when($q, fn($qr)=>$qr->where('title_ar','like',"%$q%")->orWhere('title_en','like',"%$q%"))
            ->when($category, fn($qr)=>$qr->whereHas('categories', fn($c)=>$c->where('slug',$category)))
            ->when($year, fn($qr)=>$qr->whereYear('release_date', $year))
            ->orderByDesc('release_date')
            ->paginate(20);

        return MovieResource::collection($movies);
    }

    // GET /api/v1/movies/{id}
    public function show(Movie $movie)
    {
        $movie->load(['categories','cast','videoFiles','subtitles','comments.user']);
        return new MovieResource($movie);
    }
}
