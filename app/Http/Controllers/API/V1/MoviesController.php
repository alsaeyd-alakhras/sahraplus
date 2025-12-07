<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Http\Resources\MovieResource;
use App\Services\ProfileContextService;
use Illuminate\Http\Request;

class MoviesController extends Controller
{
    protected ProfileContextService $profileContextService;

    public function __construct(ProfileContextService $profileContextService)
    {
        $this->profileContextService = $profileContextService;
    }

    // GET /api/v1/movies
    public function index(Request $request)
    {
        $q = $request->query('q');
        $category = $request->query('category');
        $year = $request->query('year');

        $query = Movie::with(['categories','cast'])
            ->when($q, fn($qr)=>$qr->where('title_ar','like',"%$q%")->orWhere('title_en','like',"%$q%"))
            ->when($category, fn($qr)=>$qr->whereHas('categories', fn($c)=>$c->where('slug',$category)))
            ->when($year, fn($qr)=>$qr->whereYear('release_date', $year));

        // تطبيق فلتر محتوى الأطفال إذا لزم الأمر
        $query = $this->profileContextService->applyKidsFilterIfNeeded($query, $request);

        $movies = $query->orderByDesc('release_date')->paginate(20);

        return MovieResource::collection($movies);
    }

    // GET /api/v1/movies/{id}
    public function show(Request $request, Movie $movie)
    {
        // التحقق من أن الفيلم مناسب للبروفايل (لو كان طفل)
        $profile = $this->profileContextService->resolveProfile($request);
        if ($this->profileContextService->shouldApplyKidsFilter($profile)) {
            if (!$movie->is_kids) {
                return response()->json(['message' => 'هذا المحتوى غير متاح لملفات الأطفال'], 403);
            }
        }

        $movie->load(['categories','cast','videoFiles','subtitles','comments.user']);
        return new MovieResource($movie);
    }
}
