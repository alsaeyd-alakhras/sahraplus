<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\CommentResource;
use App\Models\Movie;
use App\Models\Series;
use App\Models\Episode;
use App\Models\Short;

class CommentController extends Controller
{
    // GET /api/v1/{type}/{id}/comments
    public function index(Request $request, string $type, int $id)
    {
        $map = [
            'movie'   => Movie::class,
            'series'  => Series::class,
            'episode' => Episode::class,
            'short'   => Short::class,
        ];

        abort_unless(isset($map[$type]), 404);

        $model = $map[$type];
        $item  = $model::findOrFail($id);

        $comments = $item->comments()->with('user')->latest()->get();

        return CommentResource::collection($comments);
    }
}
