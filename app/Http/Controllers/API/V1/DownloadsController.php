<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Download;
use App\Traits\ApiResponse;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DownloadsController extends Controller
{
    use ApiResponse;
    // GET /api/v1/downloads
    public function index(Request $request)
    {
        $data = Download::where('user_id', $request->user()->id)
            ->whereIn('profile_id', $request->user()->profiles->pluck('id'))
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->latest()
            ->paginate(20);


        if ($data->count() > 0) {
            return $this->success([
                'items' => $data->items(),
                'meta' => [
                    'current_page' => $data->currentPage(),
                    'last_page' => $data->lastPage(),
                    'total' => $data->total(),
                    'per_page' => $data->perPage(),
                ]
            ], 'Get Data Successfully', 200);
        } else {
            return $this->error('Not Found Download', 409);
        }
    }

    // GET /api/v1/downloads/{id}
    public function show(Download $download)
    {
        $this->authorize('view', $download);

        if ($download && $download->user_id == auth()->id()) {
            return $this->success($download, 'Get Data Successfully', 201);
        } else {
            return $this->error('Not Found Download Or Not Unauthorized', 409);
        }
    }

    //  POST /api/v1/downloads/{type}/{id}
    public function store(Request $request, string $type, int $id)
    {
        $request->validate([
            'profile_id' => 'required|exists:user_profiles,id',
        ]);
        $user = $request->user();

        $map = [
            'movie'   => 'movie',
            'series'  => 'series',
            'episode' => 'episode',
            'short'   => 'short',
        ];
        abort_unless(isset($map[$type]), 404);

        $maxDownloads = 10;
        $this->authorize('create', [Download::class, $request->profile_id]);

        $activeDownloads = Download::where('user_id', $user->id)
            ->whereNull('completed_at')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->count();

        if ($activeDownloads >= $maxDownloads) {
            return $this->error('Download limit reached', 429);
        }

        $token = Str::random(40);
        $expiresAt = Carbon::now()->addDays(7);

        $download = Download::create([
            'user_id' => $user->id,
            'profile_id' => $request->input('profile_id'),
            'quality' => $request->input('quality'),
            'format' => $request->input('format'),
            'device_id' => $request->input('device_id', 'unknown'),

            'content_type' => $map[$type],
            'content_id' => $id,
            'status' => 'downloading',
            'progress_percentage' => 0,
            'download_token' => $token,
            'expires_at' => $expiresAt,
        ]);

        $downloadUrl = url("/api/v1/downloads/file/{$token}");

        return $this->success([
            'id' => $download->id,
            'download_token' => $token,
            'expires_at' => $expiresAt->format('d-m-Y'),
            'download_url' => $downloadUrl,
        ], 'Download created successfully', 201);
    }
}
