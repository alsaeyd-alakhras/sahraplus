<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Person;
use App\Http\Resources\PersonResource;
use App\Http\Resources\MovieResource;
use App\Http\Resources\EpisodeResource;
use App\Http\Resources\SeriesResource;
use Illuminate\Support\Facades\Cache;

class PeopleController extends Controller
{
    public function index()
    {
        $people = Cache::remember('api:v1:people', 3600, function () {
            return Person::select('id','name_ar','name_en','bio_ar','bio_en')->orderBy('name_ar')->get();
        });

        return PersonResource::collection($people);
    }
    // GET /api/v1/people/{id}
    public function show(Person $person)
    {
        $person->load(['movies','series']);

        return response()->json([
            'person'   => new PersonResource($person),
            'movies'   => MovieResource::collection($person->movies),
            'series' => SeriesResource::collection($person->series),
        ]);
    }
}
