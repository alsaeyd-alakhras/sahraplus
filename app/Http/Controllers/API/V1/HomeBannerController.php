<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\HomeBannerResource;
use App\Models\HomeBanner;
use App\Services\ProfileContextService;
use Illuminate\Http\Request;

class HomeBannerController extends Controller
{
    protected ProfileContextService $profileContextService;

    public function __construct(ProfileContextService $profileContextService)
    {
        $this->profileContextService = $profileContextService;
    }

    /**
     * GET /api/v1/home/banners
     * جلب بانرات الصفحة الرئيسية للجوال
     */
    public function index(Request $request)
    {
        // استخراج البروفايل من الطلب
        $profile = $this->profileContextService->resolveProfile($request);

        // بدء الاستعلام: بانرات الجوال النشطة فقط
        $query = HomeBanner::with(['movie:id,title_ar,title_en,slug,poster_url,backdrop_url,description_ar,description_en', 
                                    'series:id,title_ar,title_en,slug,poster_url,backdrop_url,description_ar,description_en'])
            ->active()
            ->forPlacement('mobile_banner')
            ->ordered();

        // تطبيق فلتر الأطفال إذا كان البروفايل طفلاً
        if ($this->profileContextService->shouldApplyKidsFilter($profile)) {
            $query->kids();
        }

        // تحديد حد أقصى 5 عناصر للسلايدر
        $banners = $query->limit(5)->get();

        return HomeBannerResource::collection($banners);
    }
}

