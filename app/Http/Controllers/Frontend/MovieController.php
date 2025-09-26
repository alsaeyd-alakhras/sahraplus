<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\MovieCategory;
use App\Models\UserProfile;
use App\Models\WatchProgres;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class MovieController extends Controller
{
    public function index()
    {
        $moviesHero = Movie::published()->orderBy('view_count', 'desc')->limit(5)->get();
        $moviesHeroArray = Cache::remember('movies_hero', 3600, function () {
            return Movie::published()
            ->orderBy('view_count', 'desc')
            ->with('categories')
                ->limit(5)
                ->get()
                ->map(function ($movie) {
                    return [
                        'id' => $movie->id,
                        'title' => $movie->title_ar ?: $movie->title_en,
                        'logo' => $movie->poster_full_url,
                        'tags' => $movie->categories->pluck('name')->toArray(),
                    ];
                });
        });
        return view('site.movies', compact('moviesHero','moviesHeroArray'));
    }

    public function getHtmlSection(Request $request)
    {
        $title_section = $request->title_section;
        $items = $request->items;
        $display_type = $request->display_type;
        $index_section = $request->index_section;
        return view('site.partials.section_movies', compact('title_section', 'items', 'display_type','index_section'));
    }

    /**
     * جلب أقسام الأفلام عبر API
     */
    public function getSections(Request $request)
    {
        $loadedSections = $request->input('loaded', []);
        $sections = [];

        // قسم متابعة المشاهدة (للمستخدمين المسجلين فقط)
        if (!in_array('continue_watching', $loadedSections) && Auth::check()) {
            $continueWatching = $this->getContinueWatchingSection();
            if (!empty($continueWatching['movies'])) {
                $sections[] = $continueWatching;
            }
        }

        // قسم أفضل 10 أفلام
        if (!in_array('top10', $loadedSections)) {
            $sections[] = $this->getTop10Section();
        }

        // قسم الأكثر مشاهدة
        if (!in_array('most_viewed', $loadedSections)) {
            $sections[] = $this->getMostViewedSection();
        }

        // أقسام حسب التصنيفات
        $categorySection = $this->getCategorySection($loadedSections);
        if ($categorySection) {
            $sections[] = $categorySection;
        }

        return response()->json([
            'success' => true,
            'sections' => $sections
        ]);
    }

    /**
     * قسم متابعة المشاهدة
     */
    private function getContinueWatchingSection()
    {
        $activeProfileId = session('active_profile_id');

        if (!$activeProfileId) {
            return ['name' => 'continue_watching', 'title' => 'متابعة المشاهدة', 'movies' => []];
        }

        $watchProgress = WatchProgres::with(['content' => function ($query) {
            $query->select('id', 'title_ar', 'title_en', 'poster_url', 'imdb_rating', 'slug', 'poster_full_url', 'backdrop_full_url', 'duration_minutes', 'imdb_rating', 'language', 'slug', 'view_count', 'categories');
        }])
            ->forProfile($activeProfileId)
            ->inProgress()
            ->recent()
            ->limit(10)
            ->get();

        $movies = $watchProgress->map(function ($progress) {
            if ($progress->content) {
                return [
                    'progress_percentage' => $progress->progress_percentage,
                    'id' => $progress->content->id,
                    'title' => $progress->content->title_ar ?: $progress->content->title_en,
                    'poster_full_url' => $progress->content->poster_full_url,
                    'backdrop_full_url' => $progress->content->backdrop_full_url,
                    'duration_minutes' => $progress->content->duration_minutes,
                    'imdb_rating' => $progress->content->imdb_rating,
                    'language' => $progress->content->language,
                    'slug' => $progress->content->slug,
                    'view_count' => $progress->content->view_count,
                    'categories' => $progress->content->categories
                ];
            }
            return null;
        })->filter()->values();

        return [
            'name' => 'continue_watching',
            'title' => 'متابعة المشاهدة',
            'movies' => $movies
        ];
    }

    /**
     * قسم أفضل 10 أفلام
     */
    private function getTop10Section()
    {
        $movies = Cache::remember('movies_top10', 3600, function () {
            return Movie::published()
            ->orderBy('imdb_rating', 'desc')
            ->with('categories')
                ->limit(10)
                ->get()
                ->map(function ($movie) {
                    return [
                        'id' => $movie->id,
                        'title' => $movie->title_ar ?: $movie->title_en,
                        'backdrop_full_url' => $movie->backdrop_full_url,
                        'poster_full_url' => $movie->poster_full_url,
                        'view_count' => $movie->view_count,
                        'imdb_rating' => $movie->imdb_rating,
                        'language' => $movie->language,
                        'slug' => $movie->slug,
                        'duration_minutes' => $movie->duration_minutes,
                        'categories' => $movie->categories
                    ];
                });
        });
        return [
            'name' => 'top10',
            'title' => 'أفضل 10 أفلام',
            'movies' => $movies
        ];
    }

    /**
     * قسم الأكثر مشاهدة
     */
    private function getMostViewedSection()
    {
        $movies = Cache::remember('movies_most_viewed', 3600, function () {
            return Movie::published()
            ->orderBy('view_count', 'desc')
            ->with('categories')
                ->limit(12)
                ->get()
                ->map(function ($movie) {
                    return [
                        'id' => $movie->id,
                        'title' => $movie->title_ar ?: $movie->title_en,
                        'poster_full_url' => $movie->poster_full_url,
                        'backdrop_full_url' => $movie->backdrop_full_url,
                        'duration_minutes' => $movie->duration_minutes,
                        'imdb_rating' => $movie->imdb_rating,
                        'language' => $movie->language,
                        'slug' => $movie->slug,
                        'view_count' => $movie->view_count,
                        'categories' => $movie->categories
                    ];
                });
        });

        return [
            'name' => 'most_viewed',
            'title' => 'الأكثر مشاهدة',
            'display_type' => 'horizontal',
            'movies' => $movies
        ];
    }

    /**
     * قسم أحدث الأفلام
     */
    private function getLatestSection()
    {
        $movies = Movie::published()
            ->orderBy('created_at', 'desc')
            ->with('categories')
            ->limit(12)
            ->get()
            ->map(function ($movie) {
                return [
                    'id' => $movie->id,
                    'title' => $movie->title_ar ?: $movie->title_en,
                    'poster_full_url' => $movie->poster_full_url,
                    'backdrop_full_url' => $movie->backdrop_full_url,
                    'duration_minutes' => $movie->duration_minutes,
                    'imdb_rating' => $movie->imdb_rating,
                    'language' => $movie->language,
                    'slug' => $movie->slug,
                    'view_count' => $movie->view_count,
                    'categories' => $movie->categories
                ];
            });

        return [
            'name' => 'latest',
            'title' => 'أحدث الإضافات',
            'display_type' => 'horizontal',
            'movies' => $movies
        ];
    }

    /**
     * قسم حسب التصنيف
     */
    private function getCategorySection($loadedSections)
    {
        $categories = MovieCategory::active()
            ->orderBy('sort_order')
            ->get();

        foreach ($categories as $category) {
            $sectionName = 'category_' . $category->id;

            if (!in_array($sectionName, $loadedSections)) {
                $movies = Cache::remember("movies_category_{$category->id}", 3600, function () use ($category) {
                    return $category->movies()
                        ->published()
                        ->with('categories')
                        ->limit(12)
                        ->get()
                        ->map(function ($movie) {
                            return [
                                'id' => $movie->id,
                                'title' => $movie->title_ar ?: $movie->title_en,
                                'poster_full_url' => $movie->poster_full_url,
                                'backdrop_full_url' => $movie->backdrop_full_url,
                                'duration_minutes' => $movie->duration_minutes,
                                'imdb_rating' => $movie->imdb_rating,
                                'language' => $movie->language,
                                'slug' => $movie->slug,
                                'view_count' => $movie->view_count,
                                'categories' => $movie->categories
                            ];
                        });
                });

                if ($movies->isNotEmpty()) {
                    return [
                        'name' => $sectionName,
                        'title' => $category->name_ar ?: $category->name_en,
                        'movies' => $movies
                    ];
                }
            }
        }

        return null;
    }

    public function show($slug)
    {
        $movie = Movie::with([
            'categories',
            'cast.person',
            'videoFiles' => function ($query) {
                $query->orderBy('quality', 'desc');
            },
            'subtitles',
            'comments' => function ($query) {
                $query->topLevel()->approved()->recent()->with('user', 'profile')->limit(20);
            }
        ])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        // فحص تقدم المشاهدة فقط إذا كان المستخدم مسجل ولديه بروفايل
        $watchProgress = null;
        if (Auth::check()) {
            $activeProfileId = session('active_profile_id');

            // التأكد من وجود البروفايل وأنه ينتمي للمستخدم الحالي
            if ($activeProfileId) {
                $profile = UserProfile::where('id', $activeProfileId)
                    ->where('user_id', Auth::id())
                    ->first();

                if ($profile) {
                    $watchProgress = WatchProgres::where([
                        'profile_id' => $activeProfileId,
                        'content_type' => Movie::class,
                        'content_id' => $movie->id
                    ])->first();
                } else {
                    // إزالة البروفايل من الجلسة إذا لم يكن صحيحاً
                    session()->forget('active_profile_id');
                }
            }
        }

        return view('site.movie', compact('movie', 'watchProgress'));
    }

    /**
     * إضافة تعليق - مربوط بالبروفايل
     */
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

        $movie = Movie::findOrFail($id);
        $profile = UserProfile::find($activeProfileId);

        $comment = $movie->comments()->create([
            'user_id' => Auth::id(),
            'profile_id' => $activeProfileId,
            'content' => $request->content,
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

        $movie = Movie::findOrFail($id);

        $exists = \App\Models\Watchlist::isInWatchlist($activeProfileId, Movie::class, $movie->id);

        if ($exists) {
            \App\Models\Watchlist::removeFromWatchlist($activeProfileId, Movie::class, $movie->id);
            return response()->json([
                'success' => true,
                'action' => 'removed',
                'message' => 'تم حذف الفيلم من قائمة المشاهدة'
            ]);
        } else {
            \App\Models\Watchlist::addToWatchlist($activeProfileId, Movie::class, $movie->id);
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
        $movie = Movie::findOrFail($id);
        $movie->incrementViewCount();

        // إضافة لتاريخ المشاهدة إذا كان مسجل
        if (Auth::check()) {
            $activeProfileId = session('active_profile_id');
            if ($activeProfileId) {
                \App\Models\ViewingHistory::create([
                    'user_id' => Auth::id(),
                    'profile_id' => $activeProfileId,
                    'content_type' => Movie::class,
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

        $movie = Movie::findOrFail($id);

        WatchProgres::updateProgress(
            $activeProfileId,
            Movie::class,
            $movie->id,
            $request->current_time,
            $request->duration
        );

        return response()->json(['success' => true]);
    }

    // في MovieController.php
    public function updateProgress(Request $request, Movie $movie)
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

    public function markCompleted(Movie $movie)
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
