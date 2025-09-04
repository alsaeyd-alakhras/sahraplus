<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CountryResource;
use App\Models\Country;

class CountryController extends Controller
{
    public function index()
    {
        $countries = Country::all();
        return CountryResource::collection($countries);
    }

    public function show(Country $country)
    {
        return new CountryResource($country);
    }
}
