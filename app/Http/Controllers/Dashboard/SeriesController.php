<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Series;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Person;
use App\Services\SeriesService;
use App\Http\Controllers\Controller;
use App\Http\Requests\SeriesRequest;
use App\Models\SeriesCast;
use App\Models\TmdbSyncLog;
use App\Services\TMDBService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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

        $allCategories = Category::select('id', 'name_ar', 'name_en')->orderBy('name_ar')->get();
        $allPeople = Person::select('id', 'name_ar', 'name_en')->orderBy('name_ar')->get();

        return view('dashboard.series.create', compact('series', 'contentRatingOptions', 'languageOptions', 'countries', 'statusOptions', 'seriesStatusOptions', 'allCategories', 'allPeople'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SeriesRequest $request)
    {
       // return $request;
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
        $countries = Country::select('code', 'name_ar', 'name_en')->get()
            ->mapWithKeys(function ($country) {
                return [
                    $country->code => app()->getLocale() == 'ar' ? $country->name_ar : $country->name_en,
                ];
            });
        $statusOptions = $this->statusOptions;
        $seriesStatusOptions = $this->seriesStatusOptions;

        $series->load(['categories:id', 'people']);

        $allCategories = Category::select('id', 'name_ar', 'name_en')->orderBy('name_ar')->get();
        $allPeople     = Person::select('id', 'name_ar', 'name_en')->orderBy('name_ar')->get();

        return view('dashboard.series.edit', compact('series', 'btn_label', 'contentRatingOptions', 'languageOptions', 'countries', 'statusOptions', 'seriesStatusOptions', 'allCategories', 'allPeople',));
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
        $row = json_decode($request->row, true);

        $allPeople = Person::select('id', 'name_ar', 'name_en')->orderBy('name_ar')->get();
        $roleTypes = [
            'actor'           => __('admin.actor'),
            'director'        => __('admin.director'),
            'writer'          => __('admin.writer'),
            'producer'        => __('admin.producer'),
            'cinematographer' => __('admin.cinematographer'),
            'composer'        => __('admin.composer'),
        ];
        return view('dashboard.series.partials._cast_row', compact('i', 'row', 'allPeople', 'roleTypes'))->render();
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

    public function deleteCast($id)
    {
        $cast = SeriesCast::find($id);
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

    public function syncSeriesFromTmdb($id)
    {
        try {
            $tmdb = new TMDBService();
            $data = $tmdb->get("tv/{$id}", ['append_to_response' => 'credits,images,videos']);

            if (!$data || (isset($data['success']) && $data['success'] === false)) {
                return response()->json([
                    'status' => false,
                    'message' => 'المعرف غير صحيح أو غير موجود في TMDB'
                ]);
            }

            // =============================
            // التصنيفات (Genres)
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
            // بيانات المسلسل
            // =============================
            $seriesData = [
                'title_ar'        => $data['name'] ?? '',
                'title_en'        => $data['original_name'] ?? '',
                'slug'            => Str::slug($data['name'] ?? $data['original_name'] ?? 'series-' . $id),

                'description_ar'  => $data['overview'] ?? '',
                'description_en'  => $data['overview'] ?? '',

                'poster_url'      => !empty($data['poster_url'])
                    ? 'https://image.tmdb.org/t/p/w500' . $data['poster_url']
                    : null,

                'backdrop_url'    => !empty($data['backdrop_url'])
                    ? 'https://image.tmdb.org/t/p/w780' . $data['backdrop_url']
                    : null,

                'trailer_url'     => !empty($data['videos']['results'][0]['key'])
                    ? 'https://www.youtube.com/watch?v=' . $data['videos']['results'][0]['key']
                    : null,

                'first_air_date'  => $data['first_air_date'] ?? null,
                'last_air_date'   => $data['last_air_date'] ?? null,

                'seasons_count'   => count($data['seasons'] ?? []),
                'episodes_count'  => $data['number_of_episodes'] ?? 0,

                'imdb_rating'     => $data['vote_average'] ?? null,
                'content_rating'  => $data['adult'] ? 'R' : 'G',

                'language'        => $data['original_language'] ?? 'ar',
                'country'         => $data['origin_country'][0] ?? null,

                'status'          => $data['status'],
                'series_status'   => $data['in_production'] ? 'returning' : 'ended',

                'is_featured'     => false,
                'view_count'      => $data['popularity'] ?? 0,

                'tmdb_id'         => $id,
                'category_ids' => $categoryIds,

                'created_by'      => Auth::guard('admin')->id() ?? null,
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
                'data' => $seriesData,
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
