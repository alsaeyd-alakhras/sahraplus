<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Watchlist;
use App\Traits\ApiResponse;

class WatchlistsController extends Controller
{
    use ApiResponse;
    // GET /api/v1/watchlists
    public function index(Request $request)
    {

        $perPage=$request->get('per_page', 20);
         $items = Watchlist::where('user_id', $request->user()->id)
            ->with('content')
            ->latest()
            ->paginate($perPage);

        $data = $items->map(function ($item) {
            return [
                'type'     => strtolower($item->content_type),
                'content_id'       => $item->content_id,
                'added_at' => $item->created_at ? $item->created_at->format('d-m-Y') :' null',
                'title'    => optional($item->content)->title_ar
                    ?? optional($item->content)->title_en
                    ?? optional($item->content)->title
                    ?? null,
            ];
        });

        if ($data->isEmpty()) {
            return $this->error('Not Found watchlist', 404);
        }

        return $this->success($data, 'Get Data Successfully', 200);
    }


    // GET /api/v1/{type}/{id}/watchlist/status
    public function status(Request $request, string $type, int $id)
    {
        $map = [
            'movie'   => 'movie',
            'series'  => 'series',
            'episode' => 'episode',
            'short'   => 'short',
        ];

        abort_unless(isset($map[$type]), 404);

        $data = Watchlist::where('user_id', $request->user()->id)
            // ->where('watchlistable_type', $map[$type])
            ->where('content_type', $map[$type])
            ->where('content_id', $id)
            ->exists();

        if ($data) {
            return $this->success($data,'Get Data Successfully', 201);
        } else {
            return $this->error('Not Exists in watchlist', 409);
        }
    }

    // Post /api/v1/watchlist/store
    public function store(Request $request)
    {
        $data = $request->validate([
            'profile_id' => 'required|exists:user_profiles,id',
            'content_type' => ['required', 'in:movie,series,season,episode'],
            'content_id' => 'required|integer',
        ]);

        $exists = Watchlist::where('profile_id', $data['profile_id'])
            ->where('content_type', $data['content_type'])
            ->where('content_id', $data['content_id'])
            ->withTrashed(false)
            ->exists();

        if ($exists) {
            return $this->error('Already in watchlist', 409);
        }

        $watch = Watchlist::create(array_merge($data, ['user_id' => $request->user()->id, 'added_at' => now()]));

        return $this->success($watch, 'Added Successfully', 200);
    }

    // DELETE /api/v1/{id}/watchlist/delete
    public function destroy(Request $request, int $id)
    {
        $watchlist =Watchlist::findOrFail($id);
        // soft delete
        if ($watchlist) $watchlist->delete();

        return $this->success(null, 'Removed Successfully', 200);
    }
}
