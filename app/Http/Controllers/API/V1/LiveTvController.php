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

        return LiveTvCategoryResource::collection($categories);
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
            ->active()
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

        return LiveTvChannelResource::collection($channels);
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
                'message' => 'Category not found',
            ], 404);
        }

        $perPage = (int) $request->query('per_page', 20);

        $channels = $category->channels()
            ->active()
            ->ordered()
            ->paginate($perPage);

        return LiveTvChannelResource::collection($channels);
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
                'message' => 'Channel not found',
            ], 404);
        }

        return new LiveTvChannelResource($channel);
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
     * بيانات البث لقناة محددة
     */
    public function stream($id)
    {
        $channel = LiveTvChannel::find($id);

        if (! $channel) {
            return response()->json([
                'message' => 'Channel not found',
            ], 404);
        }

        return response()->json([
            'id' => $channel->id,
            'name_ar' => $channel->name_ar,
            'name_en' => $channel->name_en,
            'slug' => $channel->slug,
            'stream_url' => $channel->stream_url,
            'stream_type' => $channel->stream_type,
            'language' => $channel->language,
            'country' => $channel->country,
        ]);
    }
}


