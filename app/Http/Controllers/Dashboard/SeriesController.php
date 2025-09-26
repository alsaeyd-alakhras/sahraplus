<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Series;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Models\MovieCategory;
use App\Models\Person;
use App\Services\SeriesService;
use App\Http\Controllers\Controller;
use App\Http\Requests\SeriesRequest;

class SeriesController extends Controller
{
    protected SeriesService $seriesService;
    protected $contentRatingOptions;
    protected $languageOptions;
    protected $statusOptions;
    protected $seriesStatusOptions;

    public function __construct(SeriesService $seriesService)
    {
        $this->seriesService = $seriesService;
        $this->contentRatingOptions = [
            'G' => 'G - مناسب لجميع الأعمار',
            'PG' => 'PG - بإرشاد عائلي',
            'PG-13' => 'PG-13 - غير مناسب أقل من 13 سنة',
            'R' => 'R - للبالغين (مع مرافقة)',
            'NC-17' => 'NC-17 - للبالغين فقط'
        ];
        $this->languageOptions = [
            'ar' => 'العربية',
            'en' => 'English',
            'fr' => 'Français',
            'es' => 'Español'
        ];
        $this->statusOptions = [
            'draft' => __('admin.draft'),
            'published' => __('admin.published'),
            'archived' => __('admin.archived')
        ];
        $this->seriesStatusOptions = [
            'returning' => __('admin.ongoing_series'),
            'ended'     => __('admin.ended_series'),
            'canceled'  => __('admin.canceled_series'),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('view', Series::class);

        if (request()->ajax()) {
            return $this->seriesService->datatableIndex(request());
        }

        return view('dashboard.series.index');
    }

    /**
     * Return distinct values for column filters (for Datatable for example).
     */
    public function getFilterOptions(Request $request, $column)
    {
        return $this->seriesService->getFilterOptions($request, $column);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize('create', Series::class);
        $series = new Series();
        $contentRatingOptions = $this->contentRatingOptions;
        $languageOptions = $this->languageOptions;
        $countries = Country::select('code', 'name_ar', 'name_en')->get()
            ->mapWithKeys(function ($country) {
                return [
                    $country->code => app()->getLocale() == 'ar' ? $country->name_ar : $country->name_en,
                ];
            });

        $statusOptions = $this->statusOptions;

        // جديد: خيارات حالة المسلسل
        $seriesStatusOptions = $this->seriesStatusOptions;

        $allCategories = MovieCategory::select('id','name_ar','name_en')->orderBy('name_ar')->get();
        $allPeople = Person::select('id','name_ar','name_en')->orderBy('name_ar')->get();

        return view('dashboard.series.create', compact( 'series', 'contentRatingOptions', 'languageOptions', 'countries', 'statusOptions', 'seriesStatusOptions','allCategories','allPeople'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SeriesRequest $request)
    {
        $this->authorize('create', Series::class);
        $this->seriesService->save($request->validated());
        return redirect()
            ->route('dashboard.series.index')
            ->with('success', __('controller.Created_item_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Series $series)
    {
        $this->authorize('show', Series::class);
        return view('dashboard.series.show', compact('series'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Series $series)
    {
        $this->authorize('update', Series::class);

        $btn_label = "تعديل";
        $contentRatingOptions = $this->contentRatingOptions;
        $languageOptions = $this->languageOptions;
        $countries = Country::select('code', 'name_ar', 'name_en')->get()->map(function ($country) {
            return [
                $country->code => app()->getLocale() == 'ar' ? $country->name_ar : $country->name_en,
            ];
        });
        $statusOptions = $this->statusOptions;
        $seriesStatusOptions = $this->seriesStatusOptions;

        $series->load(['categories:id','people']);

       $allCategories = MovieCategory::select('id','name_ar','name_en')->orderBy('name_ar')->get();
       $allPeople     = Person::select('id','name_ar','name_en')->orderBy('name_ar')->get();

        return view('dashboard.series.edit', compact('series', 'btn_label', 'contentRatingOptions', 'languageOptions', 'countries', 'statusOptions', 'seriesStatusOptions','allCategories','allPeople',));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SeriesRequest $request, Series $series)
    {
        $this->authorize('update', Series::class);

        $this->seriesService->update($request->validated(), $series->id);

        return redirect()
            ->route('dashboard.series.index')
            ->with('success', __('controller.Updated_item_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Series $series)
    {
        $this->authorize('delete', Series::class);

        $this->seriesService->deleteById($series->id);

        return request()->ajax()
            ? response()->json(['status' => true, 'message' => __('controller.Deleted_item_successfully')])
            : redirect()->route('dashboard.series.index')->with('success', __('controller.Deleted_item_successfully'));
    }

    public function castRowPartial(Request $request)
{
    $i = (int) $request->get('i', 0);
    $allPeople = Person::select('id','name_ar','name_en')->orderBy('name_ar')->get();
    $roleTypes = [
        'actor'           => __('admin.actor'),
        'director'        => __('admin.director'),
        'writer'          => __('admin.writer'),
        'producer'        => __('admin.producer'),
        'cinematographer' => __('admin.cinematographer'),
        'composer'        => __('admin.composer'),
    ];
    return view('dashboard.series.partials._cast_row', compact('i','allPeople','roleTypes'));
}

 public function videoRowPartial(Request $request)
    {
        $i = (int) $request->get('i', 0);
        $row = [];
        return view('dashboard.series.episodes.partials._video_row', compact('i', 'row'));
    }

    public function subtitleRowPartial(Request $request)
    {
        $i = (int) $request->get('i', 0);
        $row = [];
        return view('dashboard.series.episodes.partials._subtitle_row', compact('i', 'row'));
    }

}
