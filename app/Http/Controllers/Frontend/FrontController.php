<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Category;
use App\Models\Series;
use App\Models\Person;
use App\Models\Short;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\WatchProgres;
use App\Models\Favorite;
use App\Models\Watchlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class FrontController extends Controller
{
    public function index()
    {
        // Hero: Top 4 overall by view_count (movies + series)
        $heroItems = Cache::remember('home_hero_items', 1800, function () {
            $movies = Movie::published()
                ->with('categories')
                ->orderBy('view_count', 'desc')
                ->limit(50)
                ->get();
            $series = Series::published()
                ->with('categories')
                ->orderBy('view_count', 'desc')
                ->limit(50)
                ->get();

            $mapMovie = function (Movie $m) {
                return [
                    'id' => $m->id,
                    'type' => 'movie',
                    'slug' => $m->slug,
                    'title' => $m->title,
                    'description' => $m->description,
                    'poster' => $m->poster_full_url ?? $m->backdrop_full_url,
                    'backdrop' => $m->backdrop_full_url ?? $m->poster_full_url,
                    'logo' => $m->poster_full_url ?? $m->backdrop_full_url,
                    'tags' => $m->categories?->pluck('name')->toArray() ?? [],
                   // 'url' => route('site.movie.show', $m->slug),
                ];
            };
            $mapSeries = function (Series $s) {
                return [
                    'id' => $s->id,
                    'type' => 'series',
                    'slug' => $s->slug,
                    'title' => $s->title,
                    'description' => $s->description,
                    'poster' => $s->poster_full_url ?? $s->backdrop_full_url,
                    'backdrop' => $s->backdrop_full_url ?? $s->poster_full_url,
                    'logo' => $s->poster_full_url ?? $s->backdrop_full_url,
                    'tags' => $s->categories?->pluck('name')->toArray() ?? [],
                    'url' => route('site.series.show', $s->slug),
                ];
            };

            $top = collect($movies)
                ->merge($series)
                ->sortByDesc('view_count')
                ->take(4);

            return $top->map(function ($item) use ($mapMovie, $mapSeries) {
                return $item instanceof Movie ? $mapMovie($item) : $mapSeries($item);
            })->values();
        });

        // Categories (Discover more tiles)
        $categoriesList = Cache::remember('home_categories_list', 3600, function () {
            return Category::active()->orderBy('sort_order')->limit(14)->get();
        });

        // Category sliders: pick first active category that has movies and first that has series
        $categoryMovies = null;
        $categorySeries = null;
        $activeCategories = Category::active()->orderBy('sort_order')->get();
        foreach ($activeCategories as $cat) {
            if (!$categoryMovies) {
                $movies = $cat->movies()
                    ->published()
                    ->with('categories')
                    ->latest()
                    ->limit(12)
                    ->get();
                if ($movies->count() > 0) {
                    $categoryMovies = [
                        'category' => $cat,
                        'items' => $movies->map(function (Movie $m) {
                            return [
                                'id' => $m->id,
                                'type' => 'movie',
                                'slug' => $m->slug,
                                'title' => $m->title,
                                'poster' => $m->poster_full_url ?? $m->backdrop_full_url,
                                'backdrop' => $m->backdrop_full_url ?? $m->poster_full_url,
                                'tags' => $m->categories?->pluck('name')->toArray() ?? [],
                                //'url' => route('site.movie.show', $m->slug),
                            ];
                        }),
                    ];
                }
            }
            if (!$categorySeries) {
                $series = $cat->series()
                    ->published()
                    ->with('categories')
                    ->latest()
                    ->limit(12)
                    ->get();
                if ($series->count() > 0) {
                    $categorySeries = [
                        'category' => $cat,
                        'items' => $series->map(function (Series $s) {
                            return [
                                'id' => $s->id,
                                'type' => 'series',
                                'slug' => $s->slug,
                                'title' => $s->title,
                                'poster' => $s->poster_full_url ?? $s->backdrop_full_url,
                                'backdrop' => $s->backdrop_full_url ?? $s->poster_full_url,
                                'tags' => $s->categories?->pluck('name')->toArray() ?? [],
                                'url' => route('site.series.show', $s->slug),
                            ];
                        }),
                    ];
                }
            }
            if ($categoryMovies && $categorySeries) break;
        }

        // Top viewed: merged movies + series
        $topViewed = Cache::remember('home_top_viewed', 1800, function () {
            $m = Movie::published()->orderBy('view_count', 'desc')->limit(50)->get();
            $s = Series::published()->orderBy('view_count', 'desc')->limit(50)->get();
            $mapMovie = fn(Movie $mv) => [
                'id' => $mv->id,
                'type' => 'movie',
                'slug' => $mv->slug,
                'title' => $mv->title,
                'poster' => $mv->poster_full_url ?? $mv->backdrop_full_url,
                'backdrop' => $mv->backdrop_full_url ?? $mv->poster_full_url,
                'view_count' => $mv->view_count,
               // 'url' => route('site.movie.show', $mv->slug),
            ];
            $mapSeries = fn(Series $sr) => [
                'id' => $sr->id,
                'type' => 'series',
                'slug' => $sr->slug,
                'title' => $sr->title,
                'poster' => $sr->poster_full_url ?? $sr->backdrop_full_url,
                'backdrop' => $sr->backdrop_full_url ?? $sr->poster_full_url,
                'view_count' => $sr->view_count,
                'url' => route('site.series.show', $sr->slug),
            ];
            return collect($m)->map($mapMovie)
                ->merge(collect($s)->map($mapSeries))
                ->sortByDesc('view_count')
                ->take(20)
                ->values();
        });
        $top10 = $topViewed->take(10)->values();

        // Auth only lists
        $continueWatching = collect();
        $favoritesList = collect();
        $watchlistList = collect();
        if (Auth::check()) {
            $activeProfileId = session('active_profile_id');
            $profile = null;
            if ($activeProfileId) {
                $profile = UserProfile::where('id', $activeProfileId)
                    ->where('user_id', Auth::id())
                    ->first();
                if (!$profile) {
                    session()->forget('active_profile_id');
                }
            }
            if (!$profile) {
                $profile = UserProfile::where('user_id', Auth::id())
                    ->orderByDesc('is_default')
                    ->orderBy('id')
                    ->first();
            }

            if ($profile) {
                $continueWatching = $profile->getContinueWatching()->map(function (WatchProgres $wp) {
                    $content = $wp->content;
                    if (!$content) return null;
                    $isMovie = $content instanceof Movie;
                    return [
                        'id' => $content->id,
                        'type' => $isMovie ? 'movie' : 'series',
                        'slug' => $content->slug,
                        'title' => $content->title,
                        'poster' => $content->poster_full_url ?? $content->backdrop_full_url,
                        'backdrop' => $content->backdrop_full_url ?? $content->poster_full_url,
                      //  'url' => $isMovie ? route('site.movie.show', $content->slug) : route('site.series.show', $content->slug),
                        'time' => gmdate('H:i:s', max(0, (int) $wp->total_seconds)),
                        'progress_pct' => (float) $wp->progress_percentage,
                    ];
                })->filter()->values();

                $favoritesList = $profile->favorites()->with('content')->recent()->limit(20)->get()->map(function (Favorite $fav) {
                    $content = $fav->content;
                    if (!$content) return null;
                    $isMovie = $content instanceof Movie;
                    return [
                        'id' => $content->id,
                        'type' => $isMovie ? 'movie' : 'series',
                        'slug' => $content->slug,
                        'title' => $content->title,
                        'poster' => $content->poster_full_url ?? $content->backdrop_full_url,
                        'backdrop' => $content->backdrop_full_url ?? $content->poster_full_url,
                       // 'url' => $isMovie ? route('site.movie.show', $content->slug) : route('site.series.show', $content->slug),
                    ];
                })->filter()->values();

                $watchlistList = $profile->watchlist()->with('content')->recent()->limit(20)->get()->map(function (Watchlist $wl) {
                    $content = $wl->content;
                    if (!$content) return null;
                    $isMovie = $content instanceof Movie;
                    return [
                        'id' => $content->id,
                        'type' => $isMovie ? 'movie' : 'series',
                        'slug' => $content->slug,
                        'title' => $content->title,
                        'poster' => $content->poster_full_url ?? $content->backdrop_full_url,
                        'backdrop' => $content->backdrop_full_url ?? $content->poster_full_url,
                      //  'url' => $isMovie ? route('site.movie.show', $content->slug) : route('site.series.show', $content->slug),
                    ];
                })->filter()->values();
            }
        }

        return view('site.index', [
            'heroItems' => $heroItems,
            'categoryMovies' => $categoryMovies,
            'categorySeries' => $categorySeries,
            'topViewed' => $topViewed,
            'categoriesList' => $categoriesList,
            'top10' => $top10,
            'continueWatching' => $continueWatching,
            'favoritesList' => $favoritesList,
            'watchlistList' => $watchlistList,
        ]);
    }

    public function shorts()
    {
        $profileId = session('active_profile_id');
        $shortsQuery = Short::query();
        if ($profileId) {
            $shortsQuery->whereNotExists(function ($q) use ($profileId) {
                $q->select(DB::raw(1))
                    ->from('viewing_histories as vh')
                    ->whereColumn('vh.content_id', 'shorts.id')
                    ->where('vh.profile_id', $profileId)
                    ->where('vh.content_type', 'short')
                    ->where('vh.watched_at', '>=', now()->subDays(30));
            });
        }
        $shorts = $shortsQuery->orderByDesc('id')->limit(10)->get();
        return view('site.shorts', compact('shorts'));
    }

    public function liveTv()
    {
        return view('site.live-tv');
    }

    public function cast($id)
    {
        $cast = Person::with(['movies', 'series'])->findOrFail($id);
        return view('site.cast', compact('cast'));
    }


    public function settings()
    {
        $user = Auth::user();
        $user = User::findOrFail($user->id)->with('sessions')->first();
        return view('site.settings', compact('user'));
    }

    public function updatePersonalInfo(Request $request)
    {
        $user = User::findOrFail(Auth::user()->id);
        $user->update($request->all());
        return back()->with('success', __('site.personal_info_updated_successfully'));
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required',
            'confirm_password' => 'required|same:new_password',
        ]);

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => __('site.password_does_not_match')]);
        }
        $user = User::findOrFail(Auth::user()->id);

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success', __('site.password_changed_successfully'));
    }


    public function categories()
    {
        $categories = Category::active()->orderBy('sort_order')->get();
        return view('site.categories.index', compact('categories'));
    }

    public function categoryShow(Category $category)
    {
        $category->load([
            'movies' => fn($q) => $q->published()->with('categories')->latest()->limit(50),
            'series' => fn($q) => $q->with('categories')->latest()->limit(50)
        ]);

        return view('site.categories.show', compact('category'));
    }
}
