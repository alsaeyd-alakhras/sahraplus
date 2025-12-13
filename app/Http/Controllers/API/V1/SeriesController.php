<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Series;
use App\Http\Resources\SeriesResource;
use App\Services\ProfileContextService;
use App\Services\PlanContentAccessContextService;
use Illuminate\Http\Request;

class SeriesController extends Controller
{
    protected ProfileContextService $profileContextService;
    protected PlanContentAccessContextService $planContentAccessService;

    public function __construct(
        ProfileContextService $profileContextService,
        PlanContentAccessContextService $planContentAccessService
    ) {
        $this->profileContextService = $profileContextService;
        $this->planContentAccessService = $planContentAccessService;
    }

    // GET /api/v1/series
    public function index(Request $request)
    {
        $q = $request->query('q');

        $query = Series::with('categories')
            ->when($q, fn($qr)=>$qr->where('title_ar','like',"%$q%")->orWhere('title_en','like',"%$q%"));

        // تطبيق فلتر محتوى الأطفال إذا لزم الأمر
        $query = $this->profileContextService->applyKidsFilterIfNeeded($query, $request);

        $series = $query->orderByDesc('created_at')->paginate(20);

        return SeriesResource::collection($series);
    }

    // GET /api/v1/series/{id}
    public function show(Request $request, Series $series)
    {
        // التحقق من أن المسلسل مناسب للبروفايل (لو كان طفل)
        $profile = $this->profileContextService->resolveProfile($request);
        if ($this->profileContextService->shouldApplyKidsFilter($profile)) {
            if (!$series->is_kids) {
                return response()->json(['message' => 'هذا المحتوى غير متاح لملفات الأطفال'], 403);
            }
        }

        $series->load(['categories','seasons.episodes']);
        
        // فحص الوصول حسب الخطط
        $accessResult = $this->planContentAccessService->checkSeriesAccess($series, $request);
        $series = $accessResult['series'];
        $hasAccess = $accessResult['has_access'];
        
        return (new SeriesResource($series))->additional([
            'has_access' => $hasAccess
        ]);
    }
}
