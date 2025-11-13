<?php

namespace App\Helper\Series;

use App\Models\Category;
use App\Models\Series;
use Illuminate\Support\Facades\Cache;

class getSectionsSeries
{

    // /**
    //  * قسم متابعة المشاهدة
    //  */
    // public function getContinueWatchingSection()
    // {
    //     $activeProfileId = session('active_profile_id');

    //     if (!$activeProfileId) {
    //         return ['name' => 'continue_watching', 'title' => 'متابعة المشاهدة', 'series' => []];
    //     }

    //     $watchProgress = WatchProgres::with(['content' => function ($query) {
    //         $query->select('id', 'title_ar', 'title_en', 'poster_url', 'imdb_rating', 'slug', 'poster_full_url', 'backdrop_full_url', 'duration_minutes', 'imdb_rating', 'language', 'slug', 'view_count', 'categories');
    //     }])
    //         ->forProfile($activeProfileId)
    //         ->inProgress()
    //         ->recent()
    //         ->limit(10)
    //         ->get();

    //     $series = $watchProgress->map(function ($progress) {
    //         if ($progress->content) {
    //             return [
    //                 'progress_percentage' => $progress->progress_percentage,
    //                 'id' => $progress->content->id,
    //                 'title' => $progress->content->title_ar ?: $progress->content->title_en,
    //                 'poster_full_url' => $progress->content->poster_full_url,
    //                 'backdrop_full_url' => $progress->content->backdrop_full_url,
    //                 'duration_minutes' => $progress->content->duration_minutes,
    //                 'imdb_rating' => $progress->content->imdb_rating,
    //                 'language' => $progress->content->language,
    //                 'slug' => $progress->content->slug,
    //                 'view_count' => $progress->content->view_count,
    //                 'categories' => $progress->content->categories
    //             ];
    //         }
    //         return null;
    //     })->filter()->values();

    //     return [
    //         'name' => 'continue_watching',
    //         'title' => 'متابعة المشاهدة',
    //         'series' => $series
    //     ];
    // }

    /**
     * قسم أفضل 10 أفلام
     */
    public function getTop10Section()
    {
        $series = Cache::remember('series_top10', 3600, function () {
            return Series::published()
                ->orderBy('imdb_rating', 'desc')
                ->with('categories')
                ->limit(10)
                ->get()
                ->map(function ($series) {
                    return [
                        'id' => $series->id,
                        'title' => $series->title_ar ?: $series->title_en,
                        'backdrop_full_url' => $series->backdrop_full_url,
                        'poster_full_url' => $series->poster_full_url,
                        'view_count' => $series->view_count,
                        'imdb_rating' => $series->imdb_rating,
                        'language' => $series->language,
                        'slug' => $series->slug,
                        'duration_minutes' => $series->duration_minutes,
                        'categories' => $series->categories
                    ];
                });
        });
        return [
            'name' => 'top10',
            'title' => 'أفضل 10 مسلسلات',
            'series' => $series
        ];
    }

    /**
     * قسم الأكثر مشاهدة
     */
    public function getMostViewedSection()
    {
        $series = Cache::remember('series_most_viewed', 3600, function () {
            return Series::published()
                ->orderBy('view_count', 'desc')
                ->with('categories')
                ->limit(12)
                ->get()
                ->map(function ($series) {
                    return [
                        'id' => $series->id,
                        'title' => $series->title_ar ?: $series->title_en,
                        'poster_full_url' => $series->poster_full_url,
                        'backdrop_full_url' => $series->backdrop_full_url,
                        'duration_minutes' => $series->duration_minutes,
                        'imdb_rating' => $series->imdb_rating,
                        'language' => $series->language,
                        'slug' => $series->slug,
                        'view_count' => $series->view_count,
                        'categories' => $series->categories
                    ];
                });
        });

        return [
            'name' => 'most_viewed',
            'title' => 'الأكثر مشاهدة',
            'display_type' => 'horizontal',
            'series' => $series
        ];
    }

    /**
     * قسم أحدث الأفلام
     */
    public function getLatestSection()
    {
        $series = Series::published()
            ->orderBy('created_at', 'desc')
            ->with('categories')
            ->limit(12)
            ->get()
            ->map(function ($series) {
                return [
                    'id' => $series->id,
                    'title' => $series->title_ar ?: $series->title_en,
                    'poster_full_url' => $series->poster_full_url,
                    'backdrop_full_url' => $series->backdrop_full_url,
                    'duration_minutes' => $series->duration_minutes,
                    'imdb_rating' => $series->imdb_rating,
                    'language' => $series->language,
                    'slug' => $series->slug,
                    'view_count' => $series->view_count,
                    'categories' => $series->categories
                ];
            });

        return [
            'name' => 'latest',
            'title' => 'أحدث الإضافات',
            'display_type' => 'horizontal',
            'series' => $series
        ];
    }

    /**
     * قسم حسب التصنيف
     */
    public function getCategorySection($loadedSections)
    {
        $categories = Category::active()
            ->orderBy('sort_order')
            ->get();

        foreach ($categories as $category) {
            $sectionName = 'category_' . $category->id;

            if (!in_array($sectionName, $loadedSections)) {
                $series = Cache::remember("series_category_{$category->id}", 3600, function () use ($category) {
                    return $category->series()
                        ->published()
                        ->with('categories')
                        ->limit(12)
                        ->get()
                        ->map(function ($series) {
                            return [
                                'id' => $series->id,
                                'title' => $series->title_ar ?: $series->title_en,
                                'poster_full_url' => $series->poster_full_url,
                                'backdrop_full_url' => $series->backdrop_full_url,
                                'duration_minutes' => $series->duration_minutes,
                                'imdb_rating' => $series->imdb_rating,
                                'language' => $series->language,
                                'slug' => $series->slug,
                                'view_count' => $series->view_count,
                                'categories' => $series->categories
                            ];
                        });
                });

                if ($series->isNotEmpty()) {
                    return [
                        'name' => $sectionName,
                        'title' => $category->name_ar ?: $category->name_en,
                        'series' => $series
                    ];
                }
            }
        }

        return null;
    }
}
