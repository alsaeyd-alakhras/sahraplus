<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Season;
use App\Models\Episode;
use App\Http\Resources\EpisodeResource;

class EpisodeController extends Controller
{
    // GET /api/v1/seasons/{season}/episodes
    public function bySeason(Season $season)
    {
        $episodes = $season->episodes()->orderBy('episode_number')->get();
        return EpisodeResource::collection($episodes);
    }

    // GET /api/v1/episodes/{id}
    public function show($id)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return response()->json([
                "success" => false,
                "message" => "User Not Auth"
            ], 401);
        }

        // جلب الحلقة + الموسم + المسلسل
        $episode = Episode::with(['videoFiles', 'season.series'])
            ->findOrFail($id);

           $series = $episode->season->series;

        // 1) فحص الاشتراك
          $subscription = $user->activeSubscription;
        $hasAccess = false;

        if ($subscription) {    
            $hasAccess = $subscription->plan
                ->contentAccess()
                ->where('content_type', 'series')
                ->where('content_id', $series->id)
                ->where('access_type', 'allow')
                ->exists();
        }

        // 2) لو ما عنده وصول → نحذف روابط الفيديو فقط
        if (!$hasAccess) {
            foreach ($episode->videoFiles as $video) {
               // unset($video->file_url);
                $video->file_url = $hasAccess ? $video->file_url : null;
            }
        }

        return response()->json([
            "success" => true,
            "status_subscription" => $hasAccess ? "Access granted" : "Upgrade your plan to watch this movie",
            "episode" => new EpisodeResource($episode),
        ]);
    }
}
