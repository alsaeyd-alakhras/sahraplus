<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Favorite;
use App\Traits\ApiResponse;

class FavoritesController extends Controller
{
    use ApiResponse;
    // GET /api/v1/favorites
    public function index(Request $request)
    {
        $items = Favorite::where('user_id', $request->user()->id)
            ->with('content')
            ->latest()
            ->paginate(20);

        $data = $items->map(function ($f) {
            return [
                'type'  => strtolower($f->content_type),
                'content_id'    => $f->content_id,
                'at'    => $f->created_at->toIso8601String(),
                'title' => optional($f->content)->title_ar
                    ?? optional($f->content)->title_en
                    ?? optional($f->content)->title
                    ?? null,
            ];
        });
        if ($data) {
            return $this->success($data, 'Data retrieved successfully', 200);
        }
        return $this->error('Not Found Data', 404);
    }


    // GET /api/v1/{type}/{id}/favorite/status
    public function status(Request $request, string $type, int $id)
    {
        // $map = [
        //     'movie'   => \App\Models\Movie::class,
        //     'series'  => \App\Models\Series::class,
        //     'episode' => \App\Models\Episode::class,
        //     'short'   => \App\Models\Short::class,
        // ];

        $map = [
            'movie'   => 'movie',
            'series'  => 'series',
            'episode' => 'episode',
            'short'   => 'short',
        ];
        abort_unless(isset($map[$type]), 404);

        $exists = Favorite::where('user_id', $request->user()->id)
            ->where('content_type', $map[$type])
            ->where('content_id', $id)
            ->exists();

        $message = __('api.ok');

        return response()->json([
            'status' => true,
            'exists' => $exists
        ]);
    }

    // POST /api/v1/favorite/toggle/{type}/{id}
    public function toggle(Request $request, string $type, int $id)
    {
        $data = $request->validate([
            'profile_id' => 'required|exists:user_profiles,id',
        ]);

        $map = [
            'movie'   => 'movie',
            'series'  => 'series',
            'episode' => 'episode',
            'short'   => 'short',
        ];
        abort_unless(isset($map[$type]), 404);
        $user_id = $request->user()->id;

        $favorite = Favorite::toggleFavorite(
            $data['profile_id'],
            $map[$type],
            $id,
            $user_id
        );

        if ($favorite === true) {
            return $this->success(null, 'Removed from favorites', 200);
        } elseif ($favorite instanceof Favorite) {
            return $this->success($favorite, 'Added to favorites', 201);
        }
        return $this->success(null, 'Removed from favorites', 200);
    }
}
