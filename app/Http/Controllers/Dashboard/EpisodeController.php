<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\EpisodeRequest;
use App\Services\EpisodeService;
use App\Models\Episode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Season;
use App\Models\Series;
use App\Models\Subtitle;
use App\Models\VideoFiles;
use Carbon\Carbon;

class EpisodeController extends Controller
{

    public function __construct(private EpisodeService $episodeService) {}

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
        return view('dashboard.series.episodes.create', compact('episode', 'season'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EpisodeRequest $request)
    {
        $this->authorize('create', Series::class);

        $episode = $this->episodeService->save($request->validated());

        $episode->title       = app()->getLocale() == 'ar' ? $episode->title_ar : $episode->title_en;
        $episode->description = app()->getLocale() == 'ar' ? $episode->description_ar : $episode->description_en;

        return $request->ajax()
            ? response()->json($episode)
            : redirect()->route('dashboard.seasons.show', $episode->season->id)
            ->with('success', __('controller.Created_item_successfully'));
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
    public function update(EpisodeRequest $request, Episode $episode)
    {
        $this->authorize('update', Series::class);

        $episode = $this->episodeService->update($request->validated(), $episode->id);

        $episode->title       = app()->getLocale() == 'ar' ? $episode->title_ar : $episode->title_en;
        $episode->description = app()->getLocale() == 'ar' ? $episode->description_ar : $episode->description_en;

        return $request->ajax()
            ? response()->json($episode)
            : redirect()->route('dashboard.seasons.show', $episode->season->id)
            ->with('success', __('controller.Updated_item_successfully'));
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
        if ($id == null) {
            $episode = Episode::where('episode_number', $episodeNumber)->where('season_id', $season_id)->first();
            if ($episode) {
                return response()->json(['status' => false, 'message' => __('controller.Episod_number_exists')]);
            }
        } else {
            $episode = Episode::where('episode_number', $episodeNumber)->where('season_id', $season_id)->where('id', '!=', $id)->first();
            if ($episode) {
                return response()->json(['status' => false, 'message' => __('controller.Episod_number_exists')]);
            }
        }
        return response()->json(['status' => true, 'message' => __('controller.Episod_number_available')]);
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
    public function deleteVideo($id)
    {
        $video = VideoFiles::find($id);
        if ($video) {
            $video->delete();
            return response()->json([
                'status' => true,
                'message' => 'تم حذف الفيديو بنجاح'
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'الفيديو غير موجود'
        ], 404);
    }
    public function deleteSubtitle($id)
    {
        $subtitle = Subtitle::find($id);
        if ($subtitle) {
            $subtitle->delete();
            return response()->json([
                'status' => true,
                'message' => 'تم حذف الترجمة بنجاح'
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'الترجمة غير موجودة'
        ], 404);
    }
}