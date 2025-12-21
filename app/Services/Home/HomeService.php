<?php

namespace App\Services\Home;

use App\Models\Category;
use App\Models\HomeSection;
use App\Models\Movie;
use App\Models\Series;
use App\Models\ViewingHistory;

class HomeService
{
    public function buildHome(string $platform, $profile = null): array
    {
        $isChild = $profile?->is_child_profile ?? false;

        return [
            'sections' => array_values(array_filter([
                ...$this->manualSections($platform, $isChild),
                ...$this->dynamicSections($platform, $isChild, $profile),
                $this->categoriesSection($platform),
            ])),
        ];
    }

    protected function manualSections(string $platform, bool $isChild): array
    {
        $sections = HomeSection::query()
            ->active()
            ->forPlatform($platform)
            ->forKids($isChild)
            ->currentlyVisible()
            ->orderBy('sort_order')
            ->with(['items'])
            ->get();

        $movieIds = [];
        $seriesIds = [];

        foreach ($sections as $section) {
            foreach ($section->items as $item) {
                $item->content_type === 'movie'
                    ? $movieIds[] = $item->content_id
                    : $seriesIds[] = $item->content_id;
            }
        }

        $movies = empty($movieIds)
            ? collect()
            : Movie::selectBasic()->whereIn('id', $movieIds)->get()->keyBy('id');

        $series = empty($seriesIds)
            ? collect()
            : Series::selectBasic()->whereIn('id', $seriesIds)->get()->keyBy('id');

        return $sections->map(function ($section) use ($movies, $series) {
            return [
                'type' => 'manual',
                'id' => $section->id,
                'title' => $section->title,
                'items' => $section->items->map(function ($item) use ($movies, $series) {
                    return [
                        'type' => $item->content_type,
                        'data' => $item->content_type === 'movie'
                            ? ($movies[$item->content_id] ?? null)
                            : ($series[$item->content_id] ?? null),
                    ];
                })->filter()->values(),
            ];
        })->toArray();
    }

    protected function dynamicSections(string $platform, bool $isChild, $profile = null): array
    {
        $sections = [];

        // 1. الأكثر مشاهدة
        $mostViewed = ViewingHistory::mostViewedMixed(12, $isChild);
        if ($mostViewed->isNotEmpty()) {
            $sections[] = $this->dynamicBlock(
                key: 'most_viewed',
                title: __('Most Watched'),
                items: $mostViewed
            );
        }

        // 2. متابعة المشاهدة (فقط إذا فيه بروفايل)
        if ($profile) {
            $continueWatching = WatchProgress::continueWatchingMixed($profile->id, 12);
            if ($continueWatching->isNotEmpty()) {
                $sections[] = $this->dynamicBlock(
                    key: 'continue_watching',
                    title: __('Continue Watching'),
                    items: $continueWatching
                );
            }
        }

        // 3. قائمتي
        if ($profile) {
            $watchlist = Watchlist::mixedForProfile($profile->id, 12);
            if ($watchlist->isNotEmpty()) {
                $sections[] = $this->dynamicBlock(
                    key: 'watchlist',
                    title: __('My List'),
                    items: $watchlist
                );
            }
        }

        return $sections;
    }

    protected function categoriesSection(string $platform): array
    {
        // if ($platform !== 'web') {
        //     return [];
        // }

        $categories = Category::active()
            ->orderBy('sort_order')
            ->get(['id', 'name_ar', 'name_en', 'slug']);

        return [
            'type' => 'categories',
            'title' => __('Categories'),
            'items' => $categories->map(function ($category) {
                // جلب الأفلام والمسلسلات لكل كاتيجوري
                $movies = Movie::selectBasic()
                    ->whereHas('categories', fn($q) => $q->where('categories.id', $category->id))
                    ->get()
                    ->map(fn($movie) => [
                        'type' => 'movie',
                        'data' => $movie,
                    ]);

                $series = Series::selectBasic()
                    ->whereHas('categories', fn($q) => $q->where('categories.id', $category->id))
                    ->get()
                    ->map(fn($series) => [
                        'type' => 'series',
                        'data' => $series,
                    ]);

                // دمج الأفلام والمسلسلات معاً
                $items = $movies->merge($series)->values();

                return [
                    'id' => $category->id,
                    'name_ar' => $category->name_ar,
                    'name_en' => $category->name_en,
                    'slug' => $category->slug,
                    'items' => $items,
                ];
            }),
        ];
    }

    protected function dynamicBlock(string $key, string $title, $items): array
    {
        return [
            'type' => 'dynamic',
            'key' => $key,
            'title' => $title,
            'items' => $items,
        ];
    }
}
