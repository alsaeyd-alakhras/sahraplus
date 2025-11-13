<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Download;
use App\Models\Episode;
use App\Models\Movie;
use App\Models\Series;
use App\Models\Short;
use App\Models\Watchlist;
use App\Models\UserRating;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    use ApiResponse;

    // GET /api/v1/admin/analytics
    public function index(Request $request)
    {
        $from = $request->get('from');
        $to = $request->get('to');
        $type = $request->get('content_type');

        // خريطة الأنواع
        $map = [
            'movie'   => Movie::class,
            'series'  => Series::class,
            'episode' => Episode::class,
            'short'   => Short::class,
        ];

        // إذا النوع مش موجود في القائمة، يرجع 404
        if ($type && !isset($map[$type])) {
            return $this->error('Invalid content type', 404);
        }

        // أكثر مشاهدة
        $watchQuery = Watchlist::select('content_type', 'content_id', DB::raw('COUNT(*) as total'))
            ->when($type, fn($q) => $q->where('content_type', $type))
            ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
            ->groupBy('content_type', 'content_id')
            ->orderByDesc('total')
            ->take(20)
            ->get()
            ->map(fn($item) => [
                'id' => $item->content_id,
                'type' => $item->content_type,
                'total' => $item->total,
                'content' => $this->getContentData($item->content_type, $item->content_id),
            ]);

        // أكثر تقييمًا
        $ratedQuery = UserRating::select('content_type', 'content_id', DB::raw('AVG(rating) as avg_rating'), DB::raw('COUNT(*) as total_ratings'))
            ->when($type, fn($q) => $q->where('content_type', $type))
            ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
            ->groupBy('content_type', 'content_id')
            ->orderByDesc('avg_rating')
            ->take(20)
            ->get()
            ->map(fn($item) => [
                'id' => $item->content_id,
                'type' => $item->content_type,
                'average_rating' => round($item->avg_rating, 2),
                'total_ratings' => $item->total_ratings,
                'content' => $this->getContentData($item->content_type, $item->content_id),
            ]);

        // أكثر تحميلًا
        $downloadQuery = Download::select('content_type', 'content_id', DB::raw('COUNT(*) as total'))
            ->when($type, fn($q) => $q->where('content_type', $type))
            ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
            ->groupBy('content_type', 'content_id')
            ->orderByDesc('total')
            ->take(20)
            ->get()
            ->map(fn($item) => [
                'id' => $item->content_id,
                'type' => $item->content_type,
                'total' => $item->total,
                'content' => $this->getContentData($item->content_type, $item->content_id),
            ]);

        // تجميع النتائج
        $data = [
            'count_top_watched' => count($watchQuery),
            'top_watched' => $watchQuery,
            'count_top_downloaded' => count($downloadQuery),
            'top_downloaded' => $downloadQuery,
            'count_top_rated' => count($ratedQuery),
            'top_rated' => $ratedQuery,
        ];

        return $this->success($data);
    }

    /**
     * دالة مساعده لجلب بيانات المحتوى حسب النوع
     */
    private function getContentData($type, $id)
    {
        $map = [
            'movie' => Movie::class,
            'series' => Series::class,
            'episode' => Episode::class,
            'short' => Short::class,
        ];

        if (!isset($map[$type])) {
            return null;
        }

        $model = $map[$type];
        $content = $model::find($id);

        if (!$content) {
            return null;
        }

        return [
            'id' => $content->id,
            'title' => app()->getLocale() == 'ar' ? $content->title_ar : $content->title_en,
        ];
    }
}
