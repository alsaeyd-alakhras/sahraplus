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
use App\Services\TMDBService;
use Illuminate\Support\Str;

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
        $allCategories = Category::select('id', 'name_ar', 'name_en')->orderBy('name_ar')->get();
        $allPeople     = Person::select('id', 'name_ar', 'name_en')->orderBy('name_ar')->get();
        $contentRatingOptions = $this->contentRatingOptions;
        $languageOptions = $this->languageOptions;
        $countries = Country::select('code', 'name_ar', 'name_en')
            ->get()
            ->mapWithKeys(function ($country) {
                return [
                    $country->code => app()->getLocale() == 'ar' ? $country->name_ar : $country->name_en,
                ];
            })
            ->toArray(); // optional لو حاب ترسل array عادية للـ Blade

        $statusOptions = $this->statusOptions;

        return view('dashboard.movies.create', compact('movie', 'contentRatingOptions', 'languageOptions', 'countries', 'statusOptions', 'allCategories', 'allPeople'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MovieRequest $request)
    {
        //return $request;
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

        $movie->load(['categories:id', 'people', 'videoFiles', 'subtitles']);

        $btn_label = "تعديل";
        $contentRatingOptions = $this->contentRatingOptions;

        $languageOptions = $this->languageOptions;
        $countries = Country::select('code', 'name_ar', 'name_en')
            ->get()
            ->mapWithKeys(function ($country) {
                return [
                    $country->code => app()->getLocale() == 'ar' ? $country->name_ar : $country->name_en,
                ];
            })
            ->toArray(); // optional لو حاب ترسل array عادية للـ Blade

        $allCategories = Category::select('id', 'name_ar', 'name_en')->orderBy('name_ar')->get();
        $allPeople     = Person::select('id', 'name_ar', 'name_en')->orderBy('name_ar')->get();
        $statusOptions = $this->statusOptions;
        return view('dashboard.movies.edit', compact('movie', 'btn_label', 'contentRatingOptions', 'languageOptions', 'countries', 'statusOptions', 'allCategories', 'allPeople'));
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
        $row = json_decode($request->row, true);
        $allPeople = Person::select('id', 'name_ar', 'name_en')->orderBy('name_ar')->get();

        $roleTypes = [
            'actor' => __('admin.actor'),
            'director' => __('admin.director'),
            'writer' => __('admin.writer'),
            'producer' => __('admin.producer'),
            'cinematographer' => __('admin.cinematographer'),
            'composer' => __('admin.composer'),
        ];

        return view('dashboard.movies.partials._cast_row', compact('i', 'row', 'allPeople',  'roleTypes'))->render();
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
    public function deleteCast($id)
    {
        $cast = MovieCast::find($id);
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

    public function fetchFromTMDB(Request $request)
    {
        // $tmdbId = $request->input('tmdb_id');

        $movieId = 4614;
        $tmdb = new TMDBService();
        $dataEn = $tmdb->get("movie/{$movieId}", [
            'language' => 'en-US',
            'append_to_response' => 'videos,images,credits'
        ]);

        $dataAr = $tmdb->get("movie/{$movieId}", [
            'language' => 'ar-SA',
            'append_to_response' => 'videos,images'
        ]);


        if ($dataEn) {
            return response()->json([
                'status' => true,
                'data' => $dataEn
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'تعذر جلب بيانات الفيلم من TMDB.'
            ], 404);
        }
    }

    public function syncFromTmdb($id)
    {
        try {
            $tmdb = new TMDBService();
            $data = $tmdb->get("movie/{$id}", [
                'append_to_response' => 'credits,images,videos'
            ]);

            if (!$data || (isset($data['success']) && $data['success'] === false)) {
                return response()->json([
                    'status' => false,
                    'message' => 'المعرف غير صحيح أو غير موجود في TMDB'
                ]);
            }

            // =============================
            // التصنيفات
            // =============================
            $categoryIds = [];
            $categories = [];

            if (!empty($data['genres'])) {
                foreach ($data['genres'] as $genre) {
                    $category = Category::firstOrCreate(
                        ['name_ar' => $genre['name']],
                        [
                            'slug' => Str::slug($genre['name']),
                            'name_en' => $genre['name']
                        ]
                    );

                    $categoryIds[] = $category->id;
                    $categories[] = [
                        'id' => $category->id,
                        'name' => $category->name_ar,
                    ];
                }
            }

            // =============================
            // بيانات الفيلم
            // =============================
            $movieData = [
                'title_ar' => $data['title'] ?? '',
                'title_en' => $data['original_title'] ?? '',
                'description_ar' => $data['overview'] ?? '',
                'description_en' => $data['overview'] ?? '',
                'release_date' => $data['release_date'] ?? null,
                'duration_minutes' => $data['runtime'] ?? null,
                'imdb_rating' => $data['vote_average'] ?? null,
                'content_rating' => $data['adult'] ? 'R' : 'G',
                'language' => $data['original_language'] ?? 'en',
                'poster_url_out' => !empty($data['poster_path']) ? 'https://image.tmdb.org/t/p/w500' . $data['poster_path'] : null,
                'backdrop_url_out' => !empty($data['backdrop_path']) ? 'https://image.tmdb.org/t/p/w780' . $data['backdrop_path'] : null,
                'tmdb_id' => $id,
                'view_count' => $data['popularity'] ?? 0,
                'intro_skip_time' => 0,
                'category_ids' => $categoryIds,
                'logo_url' => !empty($data['belongs_to_collection']['poster_path'])
                    ? 'https://image.tmdb.org/t/p/w500' . $data['belongs_to_collection']['poster_path']
                    : null,
            ];

            // =============================
            // الأشخاص (CAST + CREW)
            // =============================
            $castRows = [];

            // ---- CAST (actors)
            if (!empty($data['credits']['cast'])) {
                $order = 0;

                foreach ($data['credits']['cast'] as $c) {
                    $person = Person::firstOrCreate(
                        ['tmdb_id' => $c['id']],
                        [
                            'name_en' => $c['original_name'] ?? $c['name'],
                            'name_ar' => $c['name'] ?? $c['original_name'],
                            'photo_url' => !empty($c['profile_path']) ? 'https://image.tmdb.org/t/p/w300' . $c['profile_path'] : null,
                            'is_active' => true,
                        ]
                    );

                    $castRows[] = [
                        'person_id' => $person->id,
                        'person_name' => $person->name_ar ?? $person->name_en,
                        'role_type' => 'actor',
                        'character_name' => $c['character'] ?? null,
                        'sort_order' => $order++,
                    ];
                }
            }

            // ---- CREW (director - writer - producer ..)
            if (!empty($data['credits']['crew'])) {
                foreach ($data['credits']['crew'] as $c) {

                    $person = Person::firstOrCreate(
                        ['tmdb_id' => $c['id']],
                        [
                            'name_en' => $c['original_name'] ?? $c['name'],
                            'name_ar' => $c['name'] ?? $c['original_name'],
                            'photo_url' => !empty($c['profile_path']) ? 'https://image.tmdb.org/t/p/w300' . $c['profile_path'] : null,
                            'is_active' => true,
                        ]
                    );

                    //'actor','director','writer','producer','cinematographer','composer'
                    $roleType = match (strtolower($c['job'])) {
                        "director" => "director",
                        "writer", "screenplay" => "writer",
                        "producer" => "producer",
                        "cinematography" => "cinematographer",
                        "music", "composer" => "composer",
                        default => null
                    };

                    if ($roleType) {
                        $castRows[] = [
                            'person_id' => $person->id,
                            'person_name' => $person->name_ar ?? $person->name_en,
                            'role_type' => $roleType,
                            'character_name' => null,
                            'sort_order' => 0,
                        ];
                    }
                }
            }

            return response()->json([
                'status' => true,
                'data' => $movieData,
                'categories' => $categories,
                'cast' => $castRows,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
