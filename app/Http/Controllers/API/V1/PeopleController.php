<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PersonResource;
use App\Models\Person;
use Illuminate\Http\Request;

class PeopleController extends Controller
{
    public function index()
    {
        $people = Person::all();
        return PersonResource::collection($people);
    }

    public function show(Person $person)
    {
        return new PersonResource($person);
    }
}
