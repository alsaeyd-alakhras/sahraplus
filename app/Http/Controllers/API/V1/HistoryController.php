<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ViewingHistory;
use App\Traits\ApiResponse;

class HistoryController extends Controller
{
    use ApiResponse;
    // GET /api/v1/history
    public function index(Request $request)
    {
        $items = ViewingHistory::where('user_id', $request->user()->id)
            ->with('content')
            ->latest()
            ->paginate(20);

        $data = $items->map(function ($h) {
            return [
                'type'  => $h->content_type, // أو strtolower(class_basename($h->content_type)) حسب تخزينك
                'id'    => $h->content_id,
                'at'    => $h->created_at->toIso8601String(),
                'title' => optional($h->content)->title_ar
                    ?? optional($h->content)->title_en
                    ?? optional($h->content)->title
                    ?? null,
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
        $total = ViewingHistory::where('profile_id', $profileId)->count();
        $last30 = ViewingHistory::where('profile_id', $profileId)
            ->where('created_at', '>=', now()->subDays(30))->count();

            $data=[ 'total' => $total,
            'last_30_days' => $last30,];
            return $this->success($data, 'Get Data Successfully',201);
    }
}