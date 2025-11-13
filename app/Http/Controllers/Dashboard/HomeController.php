<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Download;
use App\Models\User;
use App\Models\UserRating;
use App\Models\Watchlist;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        //  أكثر الأعمال مشاهدة
        $topWatched = Watchlist::select('content_id', 'content_type', DB::raw('COUNT(*) as total'))
            ->groupBy('content_id', 'content_type')
            ->orderByDesc('total')
            ->take(10)
            ->get();
        //  أكثر الأعمال تقييمًا
        $topRated = UserRating::select('content_id', 'content_type', DB::raw('AVG(rating) as avg_rating'), DB::raw('COUNT(*) as total'))
            ->groupBy('content_id', 'content_type')
            ->orderByDesc('avg_rating')
            ->take(10)
            ->get();

        //  أكثر الأعمال تحميلًا
        $topDownloaded = Download::select('content_id', 'content_type', DB::raw('COUNT(*) as total'))
            ->groupBy('content_id', 'content_type')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        //  التفاعل اليومي (مشاهدات + تقييمات + تحميلات)
        $dailyInteractions = collect();

        $watchlistDaily = Watchlist::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'))
            ->groupBy('date')->get();
        $ratingsDaily = UserRating::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'))
            ->groupBy('date')->get();
        $downloadsDaily = Download::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'))
            ->groupBy('date')->get();

        // دمجهم حسب التاريخ
        $dates = collect([$watchlistDaily, $ratingsDaily, $downloadsDaily])
            ->flatten(1)
            ->groupBy('date')
            ->map(function ($items) {
                return $items->sum('total');
            });

        foreach ($dates as $date => $total) {
            $dailyInteractions->push(['date' => $date, 'total' => $total]);
        }


        //  المستخدمين الأكثر نشاطًا
        $activeUsers = User::select('users.id', 'users.first_name', 'users.last_name', DB::raw('
            (
                (SELECT COUNT(*) FROM watchlists WHERE watchlists.user_id = users.id) +
                (SELECT COUNT(*) FROM user_ratings WHERE user_ratings.user_id = users.id) +
                (SELECT COUNT(*) FROM downloads WHERE downloads.user_id = users.id)
            ) as total_activity
        '))
            ->orderByDesc('total_activity')
            ->take(10)
            ->get();

        //  أكثر العناصر إضافة إلى قوائم المشاهدة (بغض النظر عن المستخدم)
        $mostAdded = Watchlist::select('content_id', 'content_type', DB::raw('COUNT(*) as total'))
            ->groupBy('content_id', 'content_type')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        // المحتوى الأكثر حفظًا من قبل المستخدمين (كم مستخدم أضاف نفس العنصر)
        $mostSavedByUsers = Watchlist::select('content_id', 'content_type', DB::raw('COUNT(DISTINCT user_id) as unique_users'))
            ->groupBy('content_id', 'content_type')
            ->orderByDesc('unique_users')
            ->take(10)
            ->get();

        return view('dashboard.index', [
            'topWatched' => $topWatched,
            'topRated' => $topRated,
            'topDownloaded' => $topDownloaded,
            'dailyInteractions' => $dailyInteractions,
            'activeUsers' => $activeUsers,
            'mostSavedByUsers' => $mostSavedByUsers,
            'mostAdded' => $mostAdded,
        ]);
    }
}