<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\HomeBannerRequest;
use App\Models\HomeBanner;
use App\Models\Movie;
use App\Models\Series;
use App\Services\HomeBannerService;
use Illuminate\Http\Request;

class HomeBannerController extends Controller
{
    protected HomeBannerService $homeBannerService;
    protected $placementOptions;

    public function __construct(HomeBannerService $homeBannerService)
    {
        $this->homeBannerService = $homeBannerService;
        $this->placementOptions = [
            'mobile_banner' => __('admin.MobileBanner'),
            'frontend_slider' => __('admin.FrontendSlider'),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('view', HomeBanner::class);

        if (request()->ajax()) {
            return $this->homeBannerService->datatableIndex(request());
        }

        return view('dashboard.home-banners.index');
    }

    /**
     * Return distinct values for column filters (for Datatable).
     */
    public function getFilterOptions(Request $request, $column)
    {
        return $this->homeBannerService->getFilterOptions($request, $column);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize('create', HomeBanner::class);
        
        $homeBanner = new HomeBanner();
        $placementOptions = $this->placementOptions;
        
        // جلب قوائم الأفلام والمسلسلات للاختيار
        $movies = Movie::select('id', 'title_ar', 'title_en')->published()->orderBy('title_ar')->get();
        $series = Series::select('id', 'title_ar', 'title_en')->published()->orderBy('title_ar')->get();

        return view('dashboard.home-banners.create', compact('homeBanner', 'placementOptions', 'movies', 'series'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(HomeBannerRequest $request)
    {
        $this->authorize('create', HomeBanner::class);

        $this->homeBannerService->save($request->validated());

        return redirect()
            ->route('dashboard.home-banners.index')
            ->with('success', __('admin.BannerAddedSuccessfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(HomeBanner $homeBanner)
    {
        $this->authorize('show', HomeBanner::class);
        return view('dashboard.home-banners.show', compact('homeBanner'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, HomeBanner $homeBanner)
    {
        $this->authorize('update', HomeBanner::class);

        $homeBanner->load(['movie:id,title_ar,title_en', 'series:id,title_ar,title_en']);

        $btn_label = __('admin.Edit');
        $placementOptions = $this->placementOptions;
        
        // جلب قوائم الأفلام والمسلسلات للاختيار
        $movies = Movie::select('id', 'title_ar', 'title_en')->published()->orderBy('title_ar')->get();
        $series = Series::select('id', 'title_ar', 'title_en')->published()->orderBy('title_ar')->get();

        return view('dashboard.home-banners.edit', compact('homeBanner', 'btn_label', 'placementOptions', 'movies', 'series'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(HomeBannerRequest $request, HomeBanner $homeBanner)
    {
        $this->authorize('update', HomeBanner::class);

        $this->homeBannerService->update($request->validated(), $homeBanner->id);

        return redirect()
            ->route('dashboard.home-banners.index')
            ->with('success', __('admin.BannerUpdatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HomeBanner $homeBanner)
    {
        $this->authorize('delete', HomeBanner::class);

        $this->homeBannerService->deleteById($homeBanner->id);

        return request()->ajax()
            ? response()->json(['status'=>true,'message'=>__('admin.BannerDeletedSuccessfully')])
            : redirect()->route('dashboard.home-banners.index')->with('success', __('admin.BannerDeletedSuccessfully'));
    }
}

