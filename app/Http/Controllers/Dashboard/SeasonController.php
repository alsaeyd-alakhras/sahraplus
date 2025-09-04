<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Season;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Series;

class SeasonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize('create', Series::class);
        $lastSeasonNumber = Season::latest('season_number')->first()->season_number ?? 0;
        $season = new Season();
        $season->season_number = $lastSeasonNumber + 1;
        $season->status = 'draft';
        if ($request->ajax()) {
            return response()->json($season);
        }
        // return view('dashboard.season.create', compact( 'season', 'contentRatingOptions', 'languageOptions', 'countries', 'statusOptions', 'seasonStatusOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Series::class);
        $request->validate([
            'series_id' => 'required|exists:series,id',
            'season_number' => 'required|integer',
            'title_ar' => 'required|string|max:200',
            'title_en' => 'required|string|max:200',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
        ]);
        $season = Season::create($request->all());
        $season->title = app()->getLocale() == 'ar' ? $season->title_ar : $season->title_en;
        $season->description = app()->getLocale() == 'ar' ? $season->description_ar : $season->description_en;
        $season->episodes_count = $season->episodes()->count();
        return response()->json($season);
    }

    /**
     * Display the specified resource.
     */
    public function show(Season $season)
    {
        $this->authorize('show', Series::class);

        return request()->ajax() ?
            response()->json($season) :
            view('dashboard.series.seasons.show', compact('season'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Season $season)
    {
        $this->authorize('update', Series::class);

        $btn_label = "تعديل";
        return $request->ajax() ?
            response()->json($season) :
            view('dashboard.season.edit', compact('season', 'btn_label'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Season $season)
    {
        $this->authorize('update', Series::class);
        $request->validate([
            'series_id' => 'required|exists:series,id',
            'season_number' => 'required|integer',
            'title_ar' => 'required|string|max:200',
            'title_en' => 'required|string|max:200',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
        ]);
        $season->update($request->all());
        $season->title = app()->getLocale() == 'ar' ? $season->title_ar : $season->title_en;
        $season->description = app()->getLocale() == 'ar' ? $season->description_ar : $season->description_en;
        $season->episodes_count = $season->episodes()->count();

        return $request->ajax() ?
            response()->json($season) :
            redirect()->route('dashboard.season.index')->with('success', __('controller.Updated_item_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Season $season)
    {
        $this->authorize('delete', Series::class);

        $season->delete();

        return $request->ajax() ?
            response()->json(['status' => true, 'message' => __('controller.Deleted_item_successfully')]) :
            redirect()->route('dashboard.season.index')->with('success', __('controller.Deleted_item_successfully'));
    }
}
