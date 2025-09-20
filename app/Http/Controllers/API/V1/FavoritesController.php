<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Favorite;

class FavoritesController extends Controller
{
    // GET /api/v1/favorites
    public function index(Request $request)
    {
        $items = Favorite::where('user_id',$request->user()->id)
            ->with('favorable')
            ->latest()
            ->paginate(20);

        $data = $items->map(function ($f) {
            return [
                'type'  => strtolower(class_basename($f->favorable_type)),
                'id'    => $f->favorable_id,
                'at'    => $f->created_at->toIso8601String(),
                'title' => $f->favorable->title_ar
                            ?? $f->favorable->title_en
                            ?? $f->favorable->title
                            ?? null,
            ];
        });

        return response()->json($data);
    }

    // GET /api/v1/{type}/{id}/favorite/status
    public function status(Request $request, string $type, int $id)
    {
        $map = [
            'movie'   => \App\Models\Movie::class,
            'series'  => \App\Models\Series::class,
            'episode' => \App\Models\Episode::class,
            'short'   => \App\Models\Short::class,
        ];

        abort_unless(isset($map[$type]), 404);

        $exists = Favorite::where('user_id',$request->user()->id)
            ->where('favorable_type', $map[$type])
            ->where('favorable_id', $id)
            ->exists();

        return ['exists' => $exists];
    }
}
