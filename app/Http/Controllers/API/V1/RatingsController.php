<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserRating;

class RatingsController extends Controller
{
    // GET /api/v1/ratings/{type}/{id}
    public function show(Request $request, string $type, int $id)
    {
        $map = [
            'movie'   => \App\Models\Movie::class,
            'series'  => \App\Models\Series::class,
            'episode' => \App\Models\Episode::class,
            'short'   => \App\Models\Short::class,
        ];

        abort_unless(isset($map[$type]), 404);

        $class = $map[$type];

        // تقييم المستخدم الحالي
        $my = UserRating::where('user_id', $request->user()->id)
            ->where('content_type', $class)
            ->where('content_id', $id)
            ->value('rating'); // ✅ العمود الصحيح

        // متوسط تقييمات باقي المستخدمين
        $avg = UserRating::where('content_type', $class)
            ->where('content_id', $id)
            ->avg('rating'); // ✅ العمود الصحيح

        return [
            'my_rating'    => $my ? (float) $my : null,
            'avg_rating'   => $avg ? round((float) $avg, 1) : null, // decimal:1
            'total_raters' => UserRating::where('content_type', $class)
                                        ->where('content_id', $id)
                                        ->count(),
        ];
    }
}
