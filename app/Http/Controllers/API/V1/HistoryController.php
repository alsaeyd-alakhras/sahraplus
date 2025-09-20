<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ViewingHistory;

class HistoryController extends Controller
{
    // GET /api/v1/history
    public function index(Request $request)
    {
        $items = ViewingHistory::where('user_id', $request->user()->id)
            ->with('viewable')
            ->latest()
            ->paginate(20);

        $data = $items->map(function ($h) {
            return [
                'type'  => strtolower(class_basename($h->viewable_type)),
                'id'    => $h->viewable_id,
                'at'    => $h->created_at->toIso8601String(),
                'title' => $h->viewable->title_ar ?? $h->viewable->title_en ?? $h->viewable->title ?? null,
            ];
        });

        return response()->json($data);
    }
}
