<?php

namespace App\Helper\Series;

use App\Models\Category;
use App\Models\Series;
use App\Models\UserProfile;
use App\Models\WatchProgres;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class SeriesHelper
{
    public function addComment(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'يرجى تسجيل الدخول أولاً'
            ], 401);
        }

        // الحصول على البروفايل النشط
        $activeProfileId = session('active_profile_id');
        if (!$activeProfileId) {
            return response()->json([
                'success' => false,
                'message' => 'يرجى اختيار بروفايل أولاً'
            ], 400);
        }

        $movie = Series::findOrFail($id);
        $profile = UserProfile::find($activeProfileId);

        $comment = $movie->comments()->create([
            'user_id' => Auth::id(),
            'profile_id' => $activeProfileId,
            'content' => $request->get('content'),
            'status' => 'approved'
        ]);

        return response()->json([
            'success' => true,
            'comment' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'user_name' => $profile->name,
                'user_avatar' => $profile->avatar_full_url,
                'created_at' => $comment->created_at->diffForHumans()
            ]
        ]);
    }

    /**
     * إدارة قائمة المشاهدة - مربوطة بالبروفايل
     */
    public function toggleWatchlist(Request $request, $id)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'يرجى تسجيل الدخول أولاً'
            ], 401);
        }

        $activeProfileId = session('active_profile_id');
        if (!$activeProfileId) {
            return response()->json([
                'success' => false,
                'message' => 'يرجى اختيار بروفايل أولاً'
            ], 400);
        }

        $movie = Series::findOrFail($id);

        $exists = \App\Models\Watchlist::isInWatchlist($activeProfileId, Series::class, $movie->id);

        if ($exists) {
            \App\Models\Watchlist::removeFromWatchlist($activeProfileId, Series::class, $movie->id);
            return response()->json([
                'success' => true,
                'action' => 'removed',
                'message' => 'تم حذف الفيلم من قائمة المشاهدة'
            ]);
        } else {
            \App\Models\Watchlist::addToWatchlist($activeProfileId, Series::class, $movie->id);
            return response()->json([
                'success' => true,
                'action' => 'added',
                'message' => 'تم إضافة الفيلم لقائمة المشاهدة'
            ]);
        }
    }

    /**
     * تحديث عدد المشاهدات + إضافة تاريخ مشاهدة
     */
    public function incrementView(Request $request, $id)
    {
        $movie = Series::findOrFail($id);
        $movie->incrementViewCount();

        // إضافة لتاريخ المشاهدة إذا كان مسجل
        if (Auth::check()) {
            $activeProfileId = session('active_profile_id');
            if ($activeProfileId) {
                \App\Models\ViewingHistory::create([
                    'user_id' => Auth::id(),
                    'profile_id' => $activeProfileId,
                    'content_type' => Series::class,
                    'content_id' => $movie->id,
                    'watch_duration_seconds' => 30, // بداية المشاهدة
                    'completion_percentage' => 0,
                    'watched_at' => now()
                ]);
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * حفظ تقدم المشاهدة
     */
    public function updateWatchProgress(Request $request, $id)
    {
        $request->validate([
            'current_time' => 'required|numeric|min:0',
            'duration' => 'required|numeric|min:1'
        ]);

        if (!Auth::check()) {
            return response()->json(['success' => false], 401);
        }

        $activeProfileId = session('active_profile_id');
        if (!$activeProfileId) {
            return response()->json(['success' => false], 400);
        }

        $movie = Series::findOrFail($id);

        WatchProgres::updateProgress(
            $activeProfileId,
            Series::class,
            $movie->id,
            $request->current_time,
            $request->duration,
            Auth::id()
        );

        return response()->json(['success' => true]);
    }

    // في MovieController.php
    public function updateProgress(Request $request, Series $movie)
    {
        $request->validate([
            'current_time' => 'required|numeric|min:0',
            'duration' => 'required|numeric|min:0',
            'progress_percentage' => 'required|numeric|min:0|max:100'
        ]);

        $user = Auth::user();

        $watchProgress = WatchProgres::updateOrCreate(
            [
                'user_id' => $user->id,
                'movie_id' => $movie->id
            ],
            [
                'watched_seconds' => $request->current_time,
                'total_duration' => $request->duration,
                'progress_percentage' => $request->progress_percentage,
                'last_watched_at' => now()
            ]
        );

        return response()->json(['success' => true, 'data' => $watchProgress]);
    }

    public function markCompleted(Series $movie)
    {
        $user = Auth::user();

        WatchProgres::updateOrCreate(
            [
                'user_id' => $user->id,
                'movie_id' => $movie->id
            ],
            [
                'progress_percentage' => 100,
                'is_completed' => true,
                'completed_at' => now()
            ]
        );

        return response()->json(['success' => true]);
    }
}
