<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Person;
use App\Http\Resources\PersonResource;
use App\Http\Resources\MovieResource;
use App\Http\Resources\EpisodeResource;

class PeopleController extends Controller
{
    // GET /api/v1/people/{id}
    public function show(Person $person)
    {
        $person->load(['movies','episodes']);

        return response()->json([
            'person'   => new PersonResource($person),
            'movies'   => MovieResource::collection($person->movies),
            'episodes' => EpisodeResource::collection($person->episodes),
        ]);
    }
}
