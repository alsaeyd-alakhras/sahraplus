<?php

namespace App\Http\Controllers\Frontend;

use App\Helper\Series\getSectionsSeries;
use App\Helper\Series\SeriesHelper;
use App\Http\Controllers\Controller;
use App\Models\Episode;
use App\Models\Series;
use App\Models\Category;
use App\Models\Season;
use App\Models\UserProfile;
use App\Models\WatchProgres;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class SeriesController extends Controller
{
    protected getSectionsSeries $getSectionsSeries;
    protected SeriesHelper $seriesHelper;

    public function __construct(getSectionsSeries $getSectionsSeries, SeriesHelper $seriesHelper)
    {
        $this->getSectionsSeries = $getSectionsSeries;
        $this->seriesHelper = $seriesHelper;
    }

    public function index()
    {
        $seriesHero = Series::published()->orderBy('view_count', 'desc')->limit(5)->get();
        $seriesHeroArray = Cache::remember('series_hero', 3600, function () {
            return Series::published()
            ->orderBy('view_count', 'desc')
            ->with('categories')
                ->limit(5)
                ->get()
                ->map(function ($series) {
                    return [
                        'id' => $series->id,
                        'title' => $series->title_ar ?: $series->title_en,
                        'logo' => $series->poster_full_url,
                        'tags' => $series->categories->pluck('name')->toArray(),
                    ];
                });
        });
        return view('site.series', compact('seriesHero','seriesHeroArray'));
    }

    public function getHtmlSection(Request $request)
    {
        $title_section = $request->title_section;
        $items = $request->items;
        $display_type = $request->display_type;
        $index_section = $request->index_section;
        return view('site.partials.section_series', compact('title_section', 'items', 'display_type', 'index_section'));
    }


    /**
     * جلب أقسام الأفلام عبر API
     */
    public function getSections(Request $request)
    {
        $loadedSections = $request->input('loaded', []);
        $sections = [];

        // // قسم متابعة المشاهدة (للمستخدمين المسجلين فقط)
        // if (!in_array('continue_watching', $loadedSections) && Auth::check()) {
        //     $continueWatching = $this->getContinueWatchingSection();
        //     if (!empty($continueWatching['series'])) {
        //         $sections[] = $continueWatching;
        //     }
        // }

        // قسم أفضل 10 أفلام
        if (!in_array('top10', $loadedSections)) {
            $sections[] = $this->getSectionsSeries->getTop10Section();
        }

        // قسم الأكثر مشاهدة
        if (!in_array('most_viewed', $loadedSections)) {
            $sections[] = $this->getSectionsSeries->getMostViewedSection();
        }

        // أقسام حسب التصنيفات
        $categorySection = $this->getSectionsSeries->getCategorySection($loadedSections);
        if ($categorySection) {
            $sections[] = $categorySection;
        }

        return response()->json([
            'success' => true,
            'sections' => $sections
        ]);
    }

    public function getSeasons(Request $request)
    {
        $loadedSections = $request->input('loaded', []);
        $sections = [];

        // قسم أفضل 10 أفلام
        if (!in_array('top10', $loadedSections)) {
            $sections[] = $this->getSectionsSeries->getTop10Section();
        }

        // قسم الأكثر مشاهدة
        if (!in_array('most_viewed', $loadedSections)) {
            $sections[] = $this->getSectionsSeries->getMostViewedSection();
        }

        // أقسام حسب التصنيفات
        $categorySection = $this->getSectionsSeries->getCategorySection($loadedSections);
        if ($categorySection) {
            $sections[] = $categorySection;
        }

        return response()->json([
            'success' => true,
            'sections' => $sections
        ]);
    }


    public function show($slug)
    {
        $series = Series::with([
            'categories',
            'cast.person',
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
                        'content_type' => Series::class,
                        'content_id' => $series->id
                    ])->first();
                } else {
                    // إزالة البروفايل من الجلسة إذا لم يكن صحيحاً
                    session()->forget('active_profile_id');
                }
            }
        }

        return view('site.series-single', compact('series', 'watchProgress'));
    }

    public function seasonShow($id)
    {
        $season = Season::published()->where('id', $id)->firstOrFail();
        return view('site.season-single', compact('season'));
    }

    public function episodeShow($id)
    {
        $episode = Episode::with([
            'series',
            'season',
            'videoFiles' => function ($query) {
                $query->orderBy('quality', 'desc');
            },
            'subtitles',
            'comments' => function ($query) {
                $query->topLevel()->approved()->recent()->with('user', 'profile')->limit(20);
            }
        ])
            ->published()
            ->where('id', $id)
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
                        'content_type' => Episode::class,
                        'content_id' => $episode->id
                    ])->first();
                } else {
                    // إزالة البروفايل من الجلسة إذا لم يكن صحيحاً
                    session()->forget('active_profile_id');
                }
            }
        }
        return view('site.episode-single', compact('episode', 'watchProgress'));
    }
    // public function show($slug)
    // {
    //     $series = Series::with([
    //         'categories',
    //         'cast.person',
    //     ])
    //         ->published()
    //         ->where('slug', $slug)
    //         ->firstOrFail();

    //     // فحص تقدم المشاهدة فقط إذا كان المستخدم مسجل ولديه بروفايل
    //     $watchProgress = null;
    //     if (Auth::check()) {
    //         $activeProfileId = session('active_profile_id');

    //         // التأكد من وجود البروفايل وأنه ينتمي للمستخدم الحالي
    //         if ($activeProfileId) {
    //             $profile = UserProfile::where('id', $activeProfileId)
    //                 ->where('user_id', Auth::id())
    //                 ->first();

    //             if ($profile) {
    //                 $watchProgress = WatchProgres::where([
    //                     'profile_id' => $activeProfileId,
    //                     'content_type' => Series::class,
    //                     'content_id' => $series->id
    //                 ])->first();
    //             } else {
    //                 // إزالة البروفايل من الجلسة إذا لم يكن صحيحاً
    //                 session()->forget('active_profile_id');
    //             }
    //         }
    //     }

    //     return view('site.series-single', compact('series', 'watchProgress'));
    // }

    /**
     * إضافة تعليق - مربوط بالبروفايل
     */

}