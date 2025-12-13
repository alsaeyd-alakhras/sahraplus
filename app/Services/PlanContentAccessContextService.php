<?php

namespace App\Services;

use App\Models\UserProfile;
use App\Models\PlanContentAccess;
use App\Models\SubscriptionPlan;
use App\Models\Movie;
use App\Models\Series;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlanContentAccessContextService
{

    /**
     * التحقق إذا كان التصنيف مدفوع في أي خطة
     * 
     * @param int $categoryId
     * @return bool
     */
    public function isCategoryPaid(int $categoryId): bool
    {
        return PlanContentAccess::where('content_type', 'category')
            ->where('content_id', $categoryId)
            ->where('access_type', 'allow')
            ->exists();
    }

    /**
     * التحقق إذا كان الفيلم مدفوع في أي خطة
     * 
     * @param int $movieId
     * @return bool
     */
    public function isMoviePaid(int $movieId): bool
    {
        return PlanContentAccess::where('content_type', 'movie')
            ->where('content_id', $movieId)
            ->where('access_type', 'allow')
            ->exists();
    }

    /**
     * التحقق إذا كان المسلسل مدفوع في أي خطة
     * 
     * @param int $seriesId
     * @return bool
     */
    public function isSeriesPaid(int $seriesId): bool
    {
        return PlanContentAccess::where('content_type', 'series')
            ->where('content_id', $seriesId)
            ->where('access_type', 'allow')
            ->exists();
    }

    /**
     * التحقق إذا كان الفيلم أو أي من تصنيفاته مدفوع
     * 
     * @param Movie $movie
     * @return bool
     */
    public function isMovieOrCategoriesPaid(Movie $movie): bool
    {
        // فحص التصنيفات أولاً (إذا كانت محملة)
        if ($movie->relationLoaded('categories')) {
            foreach ($movie->categories as $category) {
                if ($this->isCategoryPaid($category->id)) {
                    return true;
                }
            }
        } else {
            // إذا لم تكن محملة، نفحص مباشرة من قاعدة البيانات
            $categoryIds = $movie->categories()->pluck('categories.id');
            if ($categoryIds->isNotEmpty()) {
                $hasPaidCategory = PlanContentAccess::where('content_type', 'category')
                    ->whereIn('content_id', $categoryIds)
                    ->where('access_type', 'allow')
                    ->exists();
                
                if ($hasPaidCategory) {
                    return true;
                }
            }
        }

        // ثم فحص الفيلم نفسه
        return $this->isMoviePaid($movie->id);
    }

    /**
     * التحقق إذا كان المسلسل أو أي من تصنيفاته مدفوع
     * 
     * @param Series $series
     * @return bool
     */
    public function isSeriesOrCategoriesPaid(Series $series): bool
    {
        // فحص التصنيفات أولاً (إذا كانت محملة)
        if ($series->relationLoaded('categories')) {
            foreach ($series->categories as $category) {
                if ($this->isCategoryPaid($category->id)) {
                    return true;
                }
            }
        } else {
            // إذا لم تكن محملة، نفحص مباشرة من قاعدة البيانات
            $categoryIds = $series->categories()->pluck('categories.id');
            if ($categoryIds->isNotEmpty()) {
                $hasPaidCategory = PlanContentAccess::where('content_type', 'category')
                    ->whereIn('content_id', $categoryIds)
                    ->where('access_type', 'allow')
                    ->exists();
                
                if ($hasPaidCategory) {
                    return true;
                }
            }
        }

        // ثم فحص المسلسل نفسه
        return $this->isSeriesPaid($series->id);
    }

    /**
     * الحصول على الخطط التي تحتوي على المحتوى مرتبة حسب sort_order
     * 
     * @param string $contentType ('movie', 'series', 'category')
     * @param int $contentId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPlansWithContent(string $contentType, int $contentId)
    {
        return SubscriptionPlan::whereHas('contentAccess', function ($query) use ($contentType, $contentId) {
            $query->where('content_type', $contentType)
                  ->where('content_id', $contentId)
                  ->where('access_type', 'allow');
        })->orderBy('sort_order', 'asc')->get();
    }

    /**
     * الحصول على أقل sort_order لخطة تحتوي على المحتوى
     * 
     * @param string $contentType
     * @param int $contentId
     * @return int|null
     */
    public function getMinPlanSortOrderForContent(string $contentType, int $contentId): ?int
    {
        $plan = SubscriptionPlan::whereHas('contentAccess', function ($query) use ($contentType, $contentId) {
            $query->where('content_type', $contentType)
                  ->where('content_id', $contentId)
                  ->where('access_type', 'allow');
        })->orderBy('sort_order', 'asc')->first();

        return $plan ? $plan->sort_order : null;
    }

    /**
     * التحقق إذا كان المستخدم لديه اشتراك في خطة تحتوي على المحتوى أو خطط أعلى
     * 
     * @param \App\Models\User|null $user
     * @param string $contentType
     * @param int $contentId
     * @return bool
     */
    public function userHasAccessToContent($user, string $contentType, int $contentId): bool
    {
        if (!$user) {
            return false;
        }

        $subscription = $user->activeSubscription;
        if (!$subscription || !$subscription->plan) {
            return false;
        }

        // الحصول على أقل sort_order لخطة تحتوي على المحتوى
        $minSortOrder = $this->getMinPlanSortOrderForContent($contentType, $contentId);
        
        if ($minSortOrder === null) {
            // المحتوى غير مدفوع، الوصول متاح
            return true;
        }

        // التحقق إذا كان المستخدم لديه اشتراك في خطة بترتيب أكبر أو يساوي
        // (الترتيب الأقل = خطة أعلى، الترتيب الأعلى = خطة أقل)
        // لكن حسب المنطق: إذا كان المحتوى في خطة بترتيب 1، والمسخدم في خطة بترتيب 2،
        // فهذا يعني أن المستخدم في خطة أعلى (أفضل) وبالتالي لديه وصول
        // لذلك نفحص إذا كان sort_order للمستخدم <= sort_order للمحتوى
        return $subscription->plan->sort_order <= $minSortOrder;
    }

    /**
     * التحقق إذا كان المستخدم لديه وصول للفيلم
     * 
     * @param \App\Models\User|null $user
     * @param Movie $movie
     * @return bool
     */
    public function userHasAccessToMovie($user, Movie $movie): bool
    {
        // إذا لم يكن الفيلم أو تصنيفاته مدفوع، الوصول متاح
        if (!$this->isMovieOrCategoriesPaid($movie)) {
            return true;
        }

        // فحص التصنيفات أولاً
        if ($movie->relationLoaded('categories')) {
            foreach ($movie->categories as $category) {
                if ($this->isCategoryPaid($category->id)) {
                    if ($this->userHasAccessToContent($user, 'category', $category->id)) {
                        return true;
                    }
                }
            }
        } else {
            // إذا لم تكن محملة، نفحص مباشرة
            $categories = $movie->categories;
            foreach ($categories as $category) {
                if ($this->isCategoryPaid($category->id)) {
                    if ($this->userHasAccessToContent($user, 'category', $category->id)) {
                        return true;
                    }
                }
            }
        }

        // ثم فحص الفيلم نفسه
        if ($this->isMoviePaid($movie->id)) {
            return $this->userHasAccessToContent($user, 'movie', $movie->id);
        }

        return false;
    }

    /**
     * التحقق إذا كان المستخدم لديه وصول للمسلسل
     * 
     * @param \App\Models\User|null $user
     * @param Series $series
     * @return bool
     */
    public function userHasAccessToSeries($user, Series $series): bool
    {
        // إذا لم يكن المسلسل أو تصنيفاته مدفوع، الوصول متاح
        if (!$this->isSeriesOrCategoriesPaid($series)) {
            return true;
        }

        // فحص التصنيفات أولاً
        if ($series->relationLoaded('categories')) {
            foreach ($series->categories as $category) {
                if ($this->isCategoryPaid($category->id)) {
                    if ($this->userHasAccessToContent($user, 'category', $category->id)) {
                        return true;
                    }
                }
            }
        } else {
            // إذا لم تكن محملة، نفحص مباشرة
            $categories = $series->categories;
            foreach ($categories as $category) {
                if ($this->isCategoryPaid($category->id)) {
                    if ($this->userHasAccessToContent($user, 'category', $category->id)) {
                        return true;
                    }
                }
            }
        }

        // ثم فحص المسلسل نفسه
        if ($this->isSeriesPaid($series->id)) {
            return $this->userHasAccessToContent($user, 'series', $series->id);
        }

        return false;
    }

    /**
     * تطبيق فحص الوصول على الفيلم وإخفاء videoFiles إذا لزم الأمر
     * 
     * @param Movie $movie
     * @param Request $request
     * @return array ['has_access' => bool, 'movie' => Movie]
     */
    public function checkMovieAccess(Movie $movie, Request $request): array
    {
        $user = Auth::guard('sanctum')->user();
        $hasAccess = $this->userHasAccessToMovie($user, $movie);

        // إذا لم يكن لديه وصول، نخفي videoFiles
        if (!$hasAccess && $movie->relationLoaded('videoFiles')) {
            $movie->setRelation('videoFiles', collect());
        }

        return [
            'has_access' => $hasAccess,
            'movie' => $movie
        ];
    }

    /**
     * تطبيق فحص الوصول على المسلسل وإخفاء الحلقات إذا لزم الأمر
     * 
     * @param Series $series
     * @param Request $request
     * @return array ['has_access' => bool, 'series' => Series]
     */
    public function checkSeriesAccess(Series $series, Request $request): array
    {
        $user = Auth::guard('sanctum')->user();
        $hasAccess = $this->userHasAccessToSeries($user, $series);

        // إذا لم يكن لديه وصول، نخفي الحلقات
        if (!$hasAccess) {
            if ($series->relationLoaded('seasons')) {
                $series->seasons->each(function ($season) {
                    if ($season->relationLoaded('episodes')) {
                        $season->setRelation('episodes', collect());
                    } else {
                        // إذا لم تكن محملة، نحمّلها ثم نخفيها
                        $season->load('episodes');
                        $season->setRelation('episodes', collect());
                    }
                });
            } else {
                // إذا لم تكن seasons محملة، نحملها ثم نخفي الحلقات
                $series->load('seasons.episodes');
                $series->seasons->each(function ($season) {
                    $season->setRelation('episodes', collect());
                });
            }
        }

        return [
            'has_access' => $hasAccess,
            'series' => $series
        ];
    }

    /**
     * تطبيق فحص الوصول على الحلقة
     * 
     * @param \App\Models\Episode $episode
     * @param Request $request
     * @return array ['has_access' => bool, 'episode' => Episode]
     */
    public function checkEpisodeAccess($episode, Request $request): array
    {
        $user = Auth::guard('sanctum')->user();
        
        // فحص الوصول للمسلسل الذي تنتمي إليه الحلقة
        $series = $episode->season->series;
        $hasAccess = $this->userHasAccessToSeries($user, $series);

        // إذا لم يكن لديه وصول، نخفي videoFiles
        if (!$hasAccess && $episode->relationLoaded('videoFiles')) {
            $episode->setRelation('videoFiles', collect());
        }

        return [
            'has_access' => $hasAccess,
            'episode' => $episode
        ];
    }
}

