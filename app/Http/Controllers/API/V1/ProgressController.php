<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\WatchProgres;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    // GET /api/v1/progress/{type}/{id}
    public function show(Request $request, string $type, int $id)
    {
        $map = [
            'movie'   => \App\Models\Movie::class,
            'series'  => \App\Models\Series::class,
            'episode' => \App\Models\Episode::class,
            'short'   => \App\Models\Short::class,
        ];

        abort_unless(isset($map[$type]), 404);

        $progress = WatchProgres::where('user_id', $request->user()->id)
            ->where('progressable_type', $map[$type])
            ->where('progressable_id', $id)
            ->latest('updated_at')
            ->first();

        return [
            'watched_seconds' => (int) ($progress->watched_seconds ?? 0),
            'total_seconds'   => (int) ($progress->total_seconds ?? 0),
            'updated_at'      => $progress?->updated_at?->toIso8601String(),
        ];
    }
}
