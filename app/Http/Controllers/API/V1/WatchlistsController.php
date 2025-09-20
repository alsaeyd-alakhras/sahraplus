<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Watchlist;

class WatchlistsController extends Controller
{
    // GET /api/v1/watchlists
    public function index(Request $request)
    {
        $items = Watchlist::where('user_id', $request->user()->id)
            ->with('watchlistable') // علاقة morphTo
            ->latest()
            ->paginate(20);

        $data = $items->map(function ($item) {
            return [
                'type'     => strtolower(class_basename($item->watchlistable_type)),
                'id'       => $item->watchlistable_id,
                'added_at' => $item->created_at->toIso8601String(),
                'title'    => $item->watchlistable->title_ar
                              ?? $item->watchlistable->title_en
                              ?? $item->watchlistable->title
                              ?? null,
            ];
        });

        return response()->json($data);
    }

    // GET /api/v1/{type}/{id}/watchlist/status
    public function status(Request $request, string $type, int $id)
    {
        $map = [
            'movie'   => \App\Models\Movie::class,
            'series'  => \App\Models\Series::class,
            'episode' => \App\Models\Episode::class,
            'short'   => \App\Models\Short::class,
        ];

        abort_unless(isset($map[$type]), 404);

        $exists = Watchlist::where('user_id', $request->user()->id)
            ->where('watchlistable_type', $map[$type])
            ->where('watchlistable_id', $id)
            ->exists();

        return ['exists' => $exists];
    }
}
