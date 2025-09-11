<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Episode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Season;
use App\Models\Series;
use Carbon\Carbon;

class EpisodeController extends Controller
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
        $lastEpisodNumber = Episode::latest('episode_number')->first()->episode_number ?? 0;
        $episode = new Episode();
        $episode->episode_number = $lastEpisodNumber + 1;
        $episode->status = 'draft';
        $episode->season_id = $request->season_id;
        $season = Season::findOrFail($request->season_id);
        if ($request->ajax()) {
            return response()->json($episode);
        }
        return view('dashboard.series.episodes.create', compact( 'episode', 'season'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Series::class);
        $request->validate([
            'season_id' => 'required|integer|exists:seasons,id',
            'episode_number' => 'required|integer',
            'title_ar' => 'required|string|max:200',
            'title_en' => 'required|string|max:200',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
        ]);
        $data = $request->all();
        if($data['thumbnail_url_out'] != null){
            $data['thumbnail_url'] = $data['thumbnail_url_out'];
        }
        $episode = Episode::create($data);
        $episode->title = app()->getLocale() == 'ar' ? $episode->title_ar : $episode->title_en;
        $episode->description = app()->getLocale() == 'ar' ? $episode->description_ar : $episode->description_en;
        return $request->ajax() ?
            response()->json($episode) :
            redirect()->route('dashboard.seasons.show', $episode->season->id)->with('success', __('controller.Created_item_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Episode $episode)
    {
        $this->authorize('show', Series::class);

        return request()->ajax() ?
            response()->json($episode) :
            view('dashboard.series.episodes.show', compact('episode'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Episode $episode)
    {
        $this->authorize('update', Series::class);

        $btn_label = "تعديل";
        $season = Season::findOrFail($episode->season_id);
        return $request->ajax() ?
            response()->json($episode) :
            view('dashboard.series.episodes.edit', compact('episode', 'btn_label', 'season'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Episode $episode)
    {
        $this->authorize('update', Series::class);
        $request->validate([
            'season_id' => 'required|integer|exists:seasons,id',
            'episode_number' => 'required|integer',
            'title_ar' => 'required|string|max:200',
            'title_en' => 'required|string|max:200',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
        ]);
        $data = $request->all();
        if($data['thumbnail_url_out'] != null){
            $data['thumbnail_url'] = $data['thumbnail_url_out'];
        }
        if($data['thumbnail_url'] == null){
            $data['thumbnail_url'] = $episode->thumbnail_url;
        }
        $episode->update($data);
        $episode->title = app()->getLocale() == 'ar' ? $episode->title_ar : $episode->title_en;
        $episode->description = app()->getLocale() == 'ar' ? $episode->description_ar : $episode->description_en;

        return $request->ajax() ?
            response()->json($episode) :
            redirect()->route('dashboard.seasons.show', $episode->season->id)->with('success', __('controller.Updated_item_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Episode $episode)
    {
        $this->authorize('delete', Series::class);

        $episode->delete();

        return $request->ajax() ?
            response()->json(['status' => true, 'message' => __('controller.Deleted_item_successfully')]) :
            redirect()->route('dashboard.seasons.show', $episode->season->id)->with('success', __('controller.Deleted_item_successfully'));
    }

    public function checkEpisodNumber(Request $request)
    {
        $episodeNumber = $request->episode_number;
        $id = $request->id;
        $season_id = $request->season_id;
        if($id == null){
            $episode = Episode::where('episode_number', $episodeNumber)->where('season_id', $season_id)->first();
            if($episode){
                return response()->json(['status' => false, 'message' => __('controller.Episod_number_exists')]);
            }
        }else{
            $episode = Episode::where('episode_number', $episodeNumber)->where('season_id', $season_id)->where('id', '!=', $id)->first();
            if($episode){
                return response()->json(['status' => false, 'message' => __('controller.Episod_number_exists')]);
            }
        }
        return response()->json(['status' => true, 'message' => __('controller.Episod_number_available')]);
    }
}
