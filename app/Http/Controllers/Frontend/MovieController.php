<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MovieController extends Controller
{
    public function show($slug)
    {
        // جلب الفيلم مع العلاقات المطلوبة
        $movie = Movie::with([
            'categories',
            'cast.person',
            'videoFiles' => function($query) {
                $query->active()->orderBy('quality', 'desc');
            },
            'subtitles' => function($query) {
                $query->active();
            },
            'comments' => function($query) {
                $query->topLevel()->approved()->recent()->with('user')->limit(20);
            }
        ])
        ->published()
        ->where('slug', $slug)
        ->firstOrFail();

        // زيادة عدد المشاهدات
        $movie->incrementViewCount();

        // جلب الأفلام ذات الصلة بناء على التصنيفات المشتركة
        $relatedMovies = $this->getRelatedMovies($movie);

        return view('site.movie', compact('movie', 'relatedMovies'));
    }

    /**
     * جلب الأفلام ذات الصلة
     */
    private function getRelatedMovies(Movie $movie)
    {
        // الحصول على معرفات التصنيفات الخاصة بالفيلم
        $categoryIds = $movie->categories->pluck('id');

        if ($categoryIds->isEmpty()) {
            // إذا لم يكن للفيلم تصنيفات، احضر أفلام عشوائية
            return Movie::published()
                ->where('id', '!=', $movie->id)
                ->with(['categories'])
                ->inRandomOrder()
                ->limit(12)
                ->get();
        }

        return Movie::published()
            ->where('id', '!=', $movie->id)
            ->whereHas('categories', function ($query) use ($categoryIds) {
                $query->whereIn('movie_categories.id', $categoryIds);
            })
            ->with(['categories'])
            ->withCount(['categories' => function ($query) use ($categoryIds) {
                $query->whereIn('movie_categories.id', $categoryIds);
            }])
            ->orderBy('categories_count', 'desc') // ترتيب حسب عدد التصنيفات المشتركة
            ->orderBy('view_count', 'desc') // ثم حسب المشاهدات
            ->limit(12)
            ->get();
    }

    /**
     * إضافة تعليق جديد
     */
    public function addComment(Request $request, $slug)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $movie = Movie::where('slug', $slug)->firstOrFail();

        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => __('site.login_required')
            ], 401);
        }

        $comment = $movie->comments()->create([
            'user_id' => auth()->id(),
            'profile_id' => session('current_profile_id'), // إذا كنت تستخدم نظام الملفات الشخصية
            'content' => $request->content,
            'status' => 'approved' // أو 'pending' إذا كنت تريد مراجعة التعليقات
        ]);

        $comment->load('user');

        return response()->json([
            'success' => true,
            'comment' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'user' => [
                    'name' => $comment->user->full_name,
                    'avatar' => $comment->user->avatar_full_url
                ],
                'created_at' => $comment->created_at->diffForHumans()
            ]
        ]);
    }

    /**
     * إضافة/إزالة من قائمة المشاهدة
     */
    public function toggleWatchlist(Request $request, $slug)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => __('site.login_required')
            ], 401);
        }

        $movie = Movie::where('slug', $slug)->firstOrFail();
        $profileId = session('current_profile_id') ?? auth()->user()->profiles()->first()?->id;

        if (!$profileId) {
            return response()->json([
                'success' => false,
                'message' => __('site.profile_not_found')
            ], 400);
        }

        $exists = \App\Models\Watchlist::isInWatchlist($profileId, Movie::class, $movie->id);

        if ($exists) {
            \App\Models\Watchlist::removeFromWatchlist($profileId, Movie::class, $movie->id);
            return response()->json([
                'success' => true,
                'action' => 'removed',
                'message' => __('site.removed_from_watchlist')
            ]);
        } else {
            \App\Models\Watchlist::addToWatchlist($profileId, Movie::class, $movie->id);
            return response()->json([
                'success' => true,
                'action' => 'added',
                'message' => __('site.added_to_watchlist')
            ]);
        }
    }

    /**
     * تسجيل تقدم المشاهدة
     */
    public function updateWatchProgress(Request $request, $slug)
    {
        $request->validate([
            'current_time' => 'required|numeric|min:0',
            'duration' => 'required|numeric|min:1'
        ]);

        if (!auth()->check()) {
            return response()->json(['success' => false], 401);
        }

        $movie = Movie::where('slug', $slug)->firstOrFail();
        $profileId = session('current_profile_id') ?? auth()->user()->profiles()->first()?->id;

        if (!$profileId) {
            return response()->json(['success' => false], 400);
        }

        // حفظ التقدم
        \App\Models\WatchProgres::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'profile_id' => $profileId,
                'content_type' => Movie::class,
                'content_id' => $movie->id
            ],
            [
                'current_time' => $request->current_time,
                'duration' => $request->duration,
                'progress_percentage' => ($request->current_time / $request->duration) * 100,
                'last_watched' => now()
            ]
        );

        // إضافة لتاريخ المشاهدة
        \App\Models\ViewingHistory::create([
            'user_id' => auth()->id(),
            'profile_id' => $profileId,
            'content_type' => Movie::class,
            'content_id' => $movie->id,
            'watched_at' => now(),
            'watch_duration' => 30 // يمكن تخصيصها
        ]);

        return response()->json(['success' => true]);
    }
}
