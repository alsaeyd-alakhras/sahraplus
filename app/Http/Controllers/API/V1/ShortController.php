<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Short;
use Illuminate\Http\Request;
use App\Http\Resources\ShortResource;
use App\Models\ViewingHistory;
use App\Services\ProfileContextService;
use Illuminate\Support\Facades\Auth;

class ShortController extends Controller
{
    protected ProfileContextService $profileContextService;

    public function __construct(ProfileContextService $profileContextService)
    {
        $this->profileContextService = $profileContextService;
    }

    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);
        
        $query = Short::active();

        // تطبيق فلتر محتوى الأطفال إذا لزم الأمر
        $query = $this->profileContextService->applyKidsFilterIfNeeded($query, $request);

        $shorts = $query->orderByDesc('id')->paginate($perPage);
        
        return ShortResource::collection($shorts);
    }

    public function show(Request $request, $id)
    {
        $short = Short::find($id);

        if (!$short) {
            return response()->json([
                'message' => 'short not found'
            ], 404);
        }

        // التحقق من أن الشورت مناسب للبروفايل (لو كان طفل)
        $profile = $this->profileContextService->resolveProfile($request);
        if ($this->profileContextService->shouldApplyKidsFilter($profile)) {
            if (!$short->is_kids) {
                return response()->json(['message' => 'هذا المحتوى غير متاح لملفات الأطفال'], 403);
            }
        }

        return new ShortResource($short);
    }

    public function like($id)
    {
        $short = Short::findOrFail($id);
        
        // Toggle like logic - you can enhance this with user-specific likes later
        $short->increment('likes_count');
        
        return response()->json([
            'success' => true,
            'message' => 'Like toggled successfully',
            'likes_count' => $short->likes_count
        ]);
    }

    public function save($id)
    {
        $short = Short::findOrFail($id);
        
        // Toggle save logic - you can enhance this with user-specific saves later
        $short->increment('saves_count');
        
        return response()->json([
            'success' => true,
            'message' => 'Save toggled successfully',
            'saves_count' => $short->saves_count ?? 0
        ]);
    }

    public function share($id)
    {
        $short = Short::findOrFail($id);
        
        $short->increment('shares_count');
        
        return response()->json([
            'success' => true,
            'message' => 'Share count increased successfully',
            'shares_count' => $short->shares_count
        ]);
    }

    /**
     * Record a view for a Short into viewing history
     */
    public function view($id, Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }
        $profileId = session('active_profile_id');
        if (!$profileId) {
            return response()->json(['success' => false, 'message' => 'No active profile'], 400);
        }

        $short = Short::findOrFail($id);
        $watchDuration = (int) $request->input('watch_duration', 10);
        $completion = (int) $request->input('completion_percentage', 0);

        ViewingHistory::updateOrCreate(
            [
                'profile_id' => $profileId,
                'content_type' => 'short',
                'content_id' => $short->id,
            ],
            [
                'user_id' => Auth::id(),
                'watch_duration_seconds' => $watchDuration,
                'completion_percentage' => $completion,
                'device_type' => substr((string) $request->userAgent(), 0, 50),
                'quality_watched' => null,
                'watched_at' => now(),
            ]
        );

        return response()->json(['success' => true]);
    }
}
