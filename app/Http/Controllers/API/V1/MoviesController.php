<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Http\Resources\MovieResource;
use Illuminate\Http\Request;

class MoviesController extends Controller
{
    // GET /api/v1/movies
    public function index(Request $request)
    {
        $q = $request->query('q');
        $category = $request->query('category');
        $year = $request->query('year');

        $movies = Movie::with(['categories','cast'])
            ->when($q, fn($qr)=>$qr->where('title_ar','like',"%$q%")->orWhere('title_en','like',"%$q%"))
            ->when($category, fn($qr)=>$qr->whereHas('categories', fn($c)=>$c->where('slug',$category)))
            ->when($year, fn($qr)=>$qr->whereYear('release_date', $year))
            // ->orderByDesc('release_date')
            ->published()
            ->paginate(20);

        return MovieResource::collection($movies);
    }

    // GET /api/v1/movies/{id}

    public function show($id)
    {
        $user = auth('sanctum')->user();
        $movie = Movie::with(['categories', 'cast', 'videoFiles', 'subtitles'])->findOrFail($id);

        // 1) فحص الاشتراك
        $subscription = $user?->activeSubscription;
        $hasAccess = false;

        if ($subscription) {
            $hasAccess = $subscription->plan
                ->contentAccess()
                ->where('content_type', 'movie')
                ->where('content_id', $movie->id)
                ->where('access_type', 'allow')
                ->exists();
        }

        // 2) لو ما عنده وصول → نحذف روابط الفيديو فقط
        if (!$hasAccess) {
            foreach ($movie->videoFiles as $video) {
                unset($video->file_url); // حذف الرابط
            }
        }

        return response()->json([
            "success" => true,
            "status_supsecribtion" => $hasAccess,
            "movie" => new MovieResource($movie),
        ]);
    }
}
