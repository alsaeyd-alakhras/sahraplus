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

        $query = Movie::with(['categories','cast'])->published()
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

        // $user = auth('sanctum')->user();
        // if(!$user){
        //     return response()->json([
        //         "success" => false,
        //         "message" => "User Not Auth"
        //     ], 401);
        // }
        // $movie = Movie::with(['categories', 'cast', 'videoFiles', 'subtitles'])->findOrFail($id);

        // 1) فحص الاشتراك
        // $subscription = $user?->activeSubscription;
        // $hasAccess = false;

        // if ($subscription) {
        //     $hasAccess = $subscription->plan
        //         ->contentAccess()
        //         ->where('content_type', 'movie')
        //         ->where('content_id', $movie->id)
        //         ->where('access_type', 'allow')
        //         ->exists();
        // }

        // // 2) لو ما عنده وصول → نحذف روابط الفيديو فقط
        // if (!$hasAccess) {
        //     foreach ($movie->videoFiles as $video) {
        //        // unset($video->file_url); // حذف الرابط
        //         $video->file_url = $hasAccess ? $video->file_url : null;
        //     }
        // }

        // return response()->json([
        //     "success" => true,
        //     "status_subscription" => $hasAccess ? "Access granted" : "Upgrade your plan to watch this movie",
        //     "movie" => new MovieResource($movie),
        // ]);
    }
}
