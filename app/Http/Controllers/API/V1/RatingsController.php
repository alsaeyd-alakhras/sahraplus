<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserRating;
use App\Traits\ApiResponse;

class RatingsController extends Controller
{
    use ApiResponse;
    // GET /api/v1/ratings/{type}/{id}
    public function show(Request $request, string $type, int $id)
    {
        // $map = [
        //     'movie'   => \App\Models\Movie::class,
        //     'series'  => \App\Models\Series::class,
        //     'episode' => \App\Models\Episode::class,
        //     'short'   => \App\Models\Short::class,
        // ];
        $map = [
            'movie'   => 'movie',
            'series'  => 'series',
            'episode' => 'episode',
            'short'   => 'short',
        ];
        abort_unless(isset($map[$type]), 404);

        $class = $map[$type];

        // تقييم المستخدم الحالي
        $rating_current_user = UserRating::where('user_id', $request->user()->id)
            ->where('content_type', $class)
            ->where('content_id', $id)
            ->get();

        // متوسط تقييمات باقي المستخدمين
        $avg_rating_users = UserRating::where('content_type', $class)
            ->where('content_id', $id)
            ->avg('rating');

        // مجموع تقييمات باقي المستخدمين

        $total_rating_users = UserRating::where('content_type', $class)
            ->where('content_id', $id)
            ->count();
        if ($rating_current_user) {
            return $this->success([
                'current_user_rating' => $rating_current_user->makeHidden(['user_id', 'profile_id', 'reviewed_at', 'created_at', 'updated_at']) ?? null,
                'average_rating' => round($avg_rating_users, 2) ?? 0,
                'total_ratings' => $total_rating_users,
            ], 'Get Rating Successfully', 200);
        }
        return $this->error( 'Not Found Data', 404);
    }
    // Post /api/v1/rating-store/{type}/{id}

    public function store_rating(Request $request, string $type, int $content_id)
    {
        $data = $request->validate([
            'rating' => 'required|numeric|min:1|max:5',
            'review' => 'nullable|string',
            'is_spoiler' => 'nullable|boolean'
        ]);

        $user_id = $request->user()->id;

        $rating_user = UserRating::updateOrCreate([
            'user_id' => $user_id,
            'content_type' => $type,
            'content_id' => $content_id
        ], [
            'rating' => $data['rating'],
            'review' => $data['review'] ?? null,
            'is_spoiler' => $data['is_spoiler'] ?? false,
            'status' => 'approved',
            'reviewed_at' => now()
        ]);

        $avg = UserRating::where('content_type', $type)
            ->where('content_id', $content_id)
            ->avg('rating');

        return $this->success([
            'avg_rating' => round($avg, 2),
            'rating_user' => $rating_user,
        ], 'Successfully stored', 201);
    }

    // DELETE /api/v1/{id}/rating/delete
    public function destroy(Request $request, int $id)
    {
        $user_rating = UserRating::findOrFail($id);
        // soft delete
        if ($user_rating) $user_rating->delete();

        return $this->success(null, 'Removed Successfully', 200);
    }
}
