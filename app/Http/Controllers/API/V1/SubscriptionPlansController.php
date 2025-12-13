<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriptionPlanResource;
use App\Models\SubscriptionPlan;
use App\Models\WatchProgres;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class SubscriptionPlansController extends Controller
{
    use ApiResponse;
    // GET /api/v1/subscription_plans

    public function index()
    {
        $plans = SubscriptionPlan::where('is_active', true)
            ->with(['countryPrices', 'contentAccess'])
            ->orderByDesc('sort_order')
            ->get();

        if ($plans->isEmpty()) {
            return $this->error("Plans Not Found", 404);
        }

        return $this->success([
            "items" => SubscriptionPlanResource::collection($plans),
            "count" => $plans->count(),
        ], "Get Plans Successfully");
    }

    // GET /api/v1/subscription_plan/{id}
    public function show($id)
    {
        $plan = SubscriptionPlan::with(['countryPrices', 'contentAccess'])->find($id);

        if (!$plan) {
            return $this->error("Plan Not Found", 404);
        }

        return $this->success([
            "item" => new SubscriptionPlanResource($plan),
        ], "Get Plan Successfully");
    }
}
