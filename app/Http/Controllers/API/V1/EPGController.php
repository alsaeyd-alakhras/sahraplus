<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChannelProgramResource;
use App\Models\ChannelProgram;
use App\Models\LiveTvChannel;

class EPGController extends Controller
{
    /**
     * GET /api/v1/live-tv/channels/{id}/programs
     * عرض الجدول الكامل للقناة
     */
    public function programs($channelId)
    {
        $channel = LiveTvChannel::find($channelId);

        if (! $channel) {
            return response()->json([
                'success' => false,
                'message' => 'Channel not found',
            ], 404);
        }

        $programs = ChannelProgram::where('channel_id', $channel->id)
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'success' => true,
            'data' => ChannelProgramResource::collection($programs)->resolve(),
        ]);
    }

    /**
     * GET /api/v1/live-tv/channels/{id}/programs/current
     * عرض البرنامج الحالي
     */
    public function currentProgram($channelId)
    {
        $channel = LiveTvChannel::find($channelId);

        if (! $channel) {
            return response()->json([
                'success' => false,
                'message' => 'Channel not found',
            ], 404);
        }

        $program = ChannelProgram::current()
            ->where('channel_id', $channel->id)
            ->first();

        if (! $program) {
            return response()->json([
                'success' => false,
                'message' => 'No current program found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => (new ChannelProgramResource($program))->resolve(),
        ]);
    }

    /**
     * GET /api/v1/live-tv/channels/{id}/programs/upcoming
     * عرض البرامج القادمة
     */
    public function upcomingPrograms($channelId)
    {
        $channel = LiveTvChannel::find($channelId);

        if (! $channel) {
            return response()->json([
                'success' => false,
                'message' => 'Channel not found',
            ], 404);
        }

        $programs = ChannelProgram::upcoming()
            ->where('channel_id', $channel->id)
            ->get();

        return response()->json([
            'success' => true,
            'data' => ChannelProgramResource::collection($programs)->resolve(),
        ]);
    }
}


