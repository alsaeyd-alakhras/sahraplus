<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\MovieResource;
use App\Models\Movie;
use Illuminate\Http\Request;

class MoviesController extends Controller
{
    public function index()
    {
        $movies = Movie::published()->all();
        return MovieResource::collection($movies);
    }

    public function show(Movie $movie)
    {
        return new MovieResource($movie);
    }
}
