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
        $shorts = Short::active()->get();
        return ShortResource::collection($shorts);
    }

    public function show($id)
    {
        $short = Short::find($id);

        if (!$short) {
            return response()->json([
                'message' => 'short not found'
            ], 404);
        }
        return new ShortResource($short);
    }
}
