<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Movie;
use App\Models\Person;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Services\MovieService;
use App\Http\Requests\MovieRequest;
use App\Http\Controllers\Controller;
use App\Models\MovieCast;
use App\Models\Subtitle;
use App\Models\VideoFiles;

class MoviesController extends Controller
{
    protected MovieService $movieService;
    protected $contentRatingOptions;
    protected $languageOptions;
    protected $statusOptions;

    public function __construct(MovieService $movieService)
    {
        $this->movieService = $movieService;
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
            'draft' => 'مسودة',
            'published' => 'منشور',
            'archived' => 'مؤرشف'
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('view', Movie::class);

        if (request()->ajax()) {
            return $this->movieService->datatableIndex(request());
        }

        return view('dashboard.movies.index');
    }

    /**
     * Return distinct values for column filters (for Datatable for example).
     */
    public function getFilterOptions(Request $request, $column)
    {
        return $this->movieService->getFilterOptions($request, $column);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize('create', Movie::class);
        $movie = new Movie();
        $allCategories = Category::select('id','name_ar','name_en')->orderBy('name_ar')->get();
        $allPeople     = Person::select('id','name_ar','name_en')->orderBy('name_ar')->get();
        $contentRatingOptions = $this->contentRatingOptions;
        $languageOptions = $this->languageOptions;
        $countries = Country::select('code', 'name_ar', 'name_en')->get()->pluck(
            app()->getLocale() == 'ar' ? 'name_ar' : 'name_en',
            'code'
        )->toArray();
        $statusOptions = $this->statusOptions;

        return view('dashboard.movies.create', compact('movie', 'contentRatingOptions', 'languageOptions', 'countries', 'statusOptions', 'allCategories', 'allPeople'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MovieRequest $request)
    {
        $this->authorize('create', Movie::class);

        $this->movieService->save($request->validated());

        return redirect()
            ->route('dashboard.movies.index')
            ->with('success', __('controller.Created_item_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Movie $movie)
    {
        $this->authorize('show', Movie::class);
        return view('dashboard.movies.show', compact('movie'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Movie $movie)
    {
        $this->authorize('update', Movie::class);

        $movie->load(['categories:id','people','videoFiles','subtitles']);

        $btn_label = "تعديل";
        $contentRatingOptions = $this->contentRatingOptions;

        $languageOptions = $this->languageOptions;
        $countries = Country::select('code', 'name_ar', 'name_en')->get()->pluck(
            app()->getLocale() == 'ar' ? 'name_ar' : 'name_en',
            'code'
        )->toArray();
        $allCategories = Category::select('id','name_ar','name_en')->orderBy('name_ar')->get();
        $allPeople     = Person::select('id','name_ar','name_en')->orderBy('name_ar')->get();
        $statusOptions = $this->statusOptions;
        return view('dashboard.movies.edit', compact('movie', 'btn_label', 'contentRatingOptions', 'languageOptions', 'countries', 'statusOptions','allCategories','allPeople'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MovieRequest $request, Movie $movie)
    {
        $this->authorize('update', Movie::class);

        $this->movieService->update($request->validated(), $movie->id);

        return redirect()
            ->route('dashboard.movies.index')
            ->with('success', __('controller.Updated_item_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Movie $movie)
    {
        $this->authorize('delete', Movie::class);

        $this->movieService->deleteById($movie->id);

        return request()->ajax()
            ? response()->json(['status' => true, 'message' => __('controller.Deleted_item_successfully')])
            : redirect()->route('dashboard.movies.index')->with('success', __('controller.Deleted_item_successfully'));
    }

    public function castRowPartial(Request $request)
    {
        $i = $request->i;
        $allPeople = Person::select('id','name_ar','name_en')->orderBy('name_ar')->get();
        $roleTypes = [
            'actor'           => __('admin.actor'),
            'director'        => __('admin.director'),
            'writer'          => __('admin.writer'),
            'producer'        => __('admin.producer'),
            'cinematographer' => __('admin.cinematographer'),
            'composer'        => __('admin.composer'),
        ];
        return view('dashboard.movies.partials._cast_row', compact('i', 'allPeople', 'roleTypes'));
    }
    public function subRowPartial(Request $request)
    {
        $i = $request->i;
        return view('dashboard.subscription_plans.partials._cast_row', compact('i'));
    }

    public function videoRowPartial(Request $request)
    {
        $i   = (int) $request->get('i', 0);
        $row = [];
        return view('dashboard.movies.partials._video_row', compact('i', 'row'));
    }
    public function subtitleRowPartial(Request $request)
    {
        $i = $request->i;
        return view('dashboard.movies.partials._subtitle_row', compact('i'));
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
    // حذف كاست
    public function deleteCast($id)
    {
        $cast = MovieCast::find($id);
        // افترض اسم الموديل Cast
        if ($cast) {
            $cast->delete();
            return response()->json([
                'status' => true,
                'message' => 'تم حذف الممثل بنجاح'
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'الممثل غير موجود'
        ], 404);
    }

    // حذف ترجمة
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
