<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Short;
use Illuminate\Http\Request;
use App\Http\Resources\ShortResource;

class ShortController extends Controller
{
    public function index()
    {
        $shorts = Short::active()->paginate(20);
        return ShortResource::collection($shorts);
    }

    public function show(Short $short)
    {
        return new ShortResource($short);
    }
}
