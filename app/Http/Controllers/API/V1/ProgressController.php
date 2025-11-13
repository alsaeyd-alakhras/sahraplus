<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\WatchProgres;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    use ApiResponse;
    // GET /api/v1/progress/{type}/{id}
    public function show(Request $request, string $type, int $id)
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

        $progress = WatchProgres::where('user_id', $request->user()->id)
            ->where('content_type', $map[$type])
            ->where('content_id', $id)
            ->latest('updated_at')
            ->first();

        $data = [
            'watched_seconds' => (int) ($progress->watched_seconds ?? 0),
            'total_seconds'   => (int) ($progress->total_seconds ?? 0),
            'updated_at'      => $progress?->updated_at?->format('d-m-Y'),
        ];
        if ($progress) {
            return $this->success($data, 'Get Data Successfully', 201);
        } else {
            return $this->error('Not Exists in Progress', 409);
        }
    }

    // put /api/v1/watch-progress-update/{type}/{id}
    public function updateProgress(Request $request, string $type, int $content_id)
    {
        $data = $request->validate([
            'profile_id' => 'required|exists:user_profiles,id',
            'watched_seconds' => 'required|numeric',
            'total_seconds' => 'required|numeric',
        ]);

        $map = [
            'movie'   => 'movie',
            'series'  => 'series',
            'episode' => 'episode',
            'short'   => 'short',
        ];

        abort_unless(isset($map[$type]), 404);
        $user_id = $request->user()->id;

        $progress = WatchProgres::updateProgress(
            $data['profile_id'],
            $map[$type],
            $content_id,
            $data['watched_seconds'],
            $data['total_seconds'],
            $user_id
        );

        if ($progress) {
            return $this->success($progress, 'Watch progress updated successfully', 201);
        }

        return $this->error('Error updating watch progress', 409);
    }


    //Endpoint /api/profiles/{id}/continue-watching
    public function continueWatching($profileId)
    {
        $items = WatchProgres::with('content')
            ->where('profile_id', $profileId)
            ->orderByDesc('last_watched_at')
            ->take(20)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title'    => optional($item->content)->title_ar
                        ?? optional($item->content)->title_en
                        ?? optional($item->content)->title
                        ?? null,
                    'progress' => $item->progress_percentage,
                ];
            });

        return $this->success($items, 'Get Data Successfully', 201);
    }
}
