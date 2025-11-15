<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ViewingHistory;
use App\Traits\ApiResponse;

class ViewingHistoryController extends Controller
{
    use ApiResponse;
    // GET /api/v1/history
    public function index(Request $request)
    {
        $request->validate([
            'content_type' => 'nullable|in:movie,episode,short,series'
        ]);

        $items = ViewingHistory::where('user_id', $request->user()->id)
            ->when($request->content_type, function ($q) use ($request) {
                $q->where('content_type', $request->content_type);
            })
            ->whereIn('profile_id', $request->user()->profiles->pluck('id'))
            ->with('content')
            ->latest()
            ->paginate(20);

        $data = $items->map(function ($h) {
            return [
                'type'  => $h->content_type,
                'id'    => $h->content_id,
                'at'    => $h->created_at->toIso8601String(),
                'title' => optional($h->content)->title_ar
                    ?? optional($h->content)->title_en
                    ?? optional($h->content)->title,
            ];
        });

        if ($data->isEmpty()) {
            return $this->error('Not Exists in history', 404);
        }

        return $this->success($data, 'Get Data Successfully', 200);
    }


    //احصاءات تاريخ المشاهدة
    //Get api/profiles/{id}/history/stats
    public function analytic_history($profileId)
    {
        $user = auth('sanctum')->user();
        if (!in_array($profileId, $user->profiles->pluck('id')->toArray())) {
            return $this->error('Profile not found', 403);
        }

        $total = ViewingHistory::where('profile_id', $profileId)
            ->where('user_id', $user->id)
            ->count();

        if ($total === 0) {
            return $this->error('Not Exists in history', 404);
        }

        $last30 = ViewingHistory::where('profile_id', $profileId)
            ->where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        $data = [
            'total' => $total,
            'last_30_days' => $last30,
        ];

        return $this->success($data, 'Get Data Successfully', 200);
    }
}
