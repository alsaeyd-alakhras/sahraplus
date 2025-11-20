<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Http\Resources\SubscriptionPlanResource;

class PlanController extends Controller
{
    /**
     * Display a listing of active subscription plans.
     */
    public function index()
    {
        $plans = SubscriptionPlan::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('price')
            ->get();

        return SubscriptionPlanResource::collection($plans);
    }

    /**
     * Display the specified subscription plan.
     */
    public function show($id)
    {
        $plan = SubscriptionPlan::where('is_active', true)->findOrFail($id);

        return new SubscriptionPlanResource($plan);
    }
}
