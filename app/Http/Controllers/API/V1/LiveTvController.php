<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\LiveTvCategoryResource;
use App\Http\Resources\LiveTvChannelResource;
use App\Models\LiveTvCategory;
use App\Models\LiveTvChannel;
use Illuminate\Http\Request;

class LiveTvController extends Controller
{
    /**
     * GET /api/v1/live-tv/categories
     * عرض قائمة فئات التلفاز المباشر
     */
    public function categories()
    {
        $categories = LiveTvCategory::active()
            ->ordered()
            ->get();

        return response()->json([
            'success' => true,
            'data' => LiveTvCategoryResource::collection($categories)->resolve(),
        ]);
    }

    /**
     * GET /api/v1/live-tv/channels
     * عرض قائمة القنوات
     */
    public function channels(Request $request)
    {
        $categoryId = $request->query('category_id');
        $country = $request->query('country');
        $language = $request->query('language');
        $perPage = (int) $request->query('per_page', 20);

        $query = LiveTvChannel::with('category')
            ->where('is_active', true)
            ->ordered()
            ->when($categoryId, function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            })
            ->when($country, function ($q) use ($country) {
                $q->where('country', $country);
            })
            ->when($language, function ($q) use ($language) {
                $q->where('language', $language);
            });

        $channels = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => LiveTvChannelResource::collection($channels->items())->resolve(),
            'meta' => [
                'current_page' => $channels->currentPage(),
                'last_page' => $channels->lastPage(),
                'per_page' => $channels->perPage(),
                'total' => $channels->total(),
            ],
        ]);
    }

    /**
     * GET /api/v1/live-tv/categories/{id}/channels
     * عرض القنوات حسب الفئة
     */
    public function channelsByCategory($id, Request $request)
    {
        $category = LiveTvCategory::find($id);

        if (! $category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
            ], 404);
        }

        $perPage = (int) $request->query('per_page', 20);

        $channels = $category->channels()
            ->active()
            ->ordered()
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => LiveTvChannelResource::collection($channels->items())->resolve(),
            'meta' => [
                'current_page' => $channels->currentPage(),
                'last_page' => $channels->lastPage(),
                'per_page' => $channels->perPage(),
                'total' => $channels->total(),
            ],
        ]);
    }

    /**
     * GET /api/v1/live-tv/channels/{slug}
     * عرض تفاصيل قناة واحدة
     */
    public function showChannel($slug)
    {
        $channel = LiveTvChannel::where('slug', $slug)->first();

        if (! $channel) {
            return response()->json([
                'success' => false,
                'message' => 'Channel not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => (new LiveTvChannelResource($channel))->resolve(),
        ]);
    }

    /**
     * POST /api/v1/live-tv/channels/{id}/watch
     * تسجيل بدء مشاهدة القناة (زيادة viewer_count)
     */
    public function watch($id)
    {
        $channel = LiveTvChannel::find($id);

        if (! $channel) {
            return response()->json([
                'success' => false,
                'message' => 'Channel not found',
            ], 404);
        }

        $channel->increment('viewer_count');

        return response()->json([
            'success' => true,
            'message' => 'Viewer count updated successfully',
            'viewer_count' => $channel->viewer_count,
        ]);
    }

    /**
     * GET /api/v1/live-tv/channels/{id}/stream
     * بيانات البث لقناة محددة (مع توليد Secure Token من Flussonic)
     */
    public function stream(Request $request, $id)
    {
        $channel = LiveTvChannel::find($id);

        if (! $channel) {
            return response()->json([
                'success' => false,
                'message' => 'Channel not found',
            ], 404);
        }

        try {
            // Get authenticated user if available
            $user = $request->user();
            $userId = $user ? $user->id : null;

            // Get user IP (optional - we'll use 'no_check_ip' by default)
            $ipAddress = $request->ip();

            // Check if stream_url is already a full URL or just a stream name
            $streamUrl = $channel->stream_url;

            // If it's just a stream name (not a full URL), generate Flussonic URL
            if (!filter_var($streamUrl, FILTER_VALIDATE_URL)) {
                $flussonicService = app(\App\Services\FlussonicService::class);
                $streamData = $flussonicService->generateStreamUrl(
                    streamName: $channel->stream_url,
                    userId: $userId,
                    ipAddress: null,
                    protocol: $channel->stream_type
                );
                $streamUrl = $streamData['url'];
                $expiresAt = $streamData['expires_at'];
            } else {
                // Use the full URL as is
                $expiresAt = time() + 3600; // 1 hour default
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $channel->id,
                    'name_ar' => $channel->name_ar,
                    'name_en' => $channel->name_en,
                    'slug' => $channel->slug,
                    'stream_name' => $channel->stream_url,
                    'stream_url' => $streamUrl,
                    'stream_type' => $channel->stream_type,
                    'expires_at' => $expiresAt,
                    'expires_at_formatted' => date('Y-m-d H:i:s', $expiresAt),
                    'language' => $channel->language,
                    'country' => $channel->country,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate stream URL',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
