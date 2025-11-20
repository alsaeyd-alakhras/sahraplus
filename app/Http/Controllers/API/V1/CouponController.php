<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CouponValidateRequest;
use App\Models\Coupon;
use App\Models\SubscriptionPlan;
use App\Http\Resources\CouponResource;

class CouponController extends Controller
{
    /**
     * Validate a coupon code
     */
    public function validate(CouponValidateRequest $request)
    {
        $user = $request->user();
        $coupon = Coupon::where('code', $request->code)->first();
        $plan = SubscriptionPlan::findOrFail($request->plan_id);

        if (!$coupon) {
            return response()->json([
                'valid' => false,
                'message' => 'Coupon not found',
            ], 404);
        }

        // Check if coupon is valid
        if (!$coupon->isValid()) {
            return response()->json([
                'valid' => false,
                'message' => 'Coupon is expired or inactive',
            ], 422);
        }

        // Check if user can use this coupon
        if (!$coupon->canBeUsedByUser($user->id)) {
            return response()->json([
                'valid' => false,
                'message' => 'You have already used this coupon the maximum number of times',
            ], 422);
        }

        // Check if coupon is valid for this plan
        if ($coupon->plan_id && $coupon->plan_id != $plan->id) {
            return response()->json([
                'valid' => false,
                'message' => 'This coupon is not valid for the selected plan',
            ], 422);
        }

        // Calculate discount
        $discountAmount = $coupon->calculateDiscount($plan->price);
        $finalAmount = $plan->price - $discountAmount;

        return response()->json([
            'valid' => true,
            'message' => 'Coupon is valid',
            'coupon' => new CouponResource($coupon),
            'discount_calculation' => [
                'original_amount' => $plan->price,
                'discount_amount' => $discountAmount,
                'final_amount' => max(0, $finalAmount),
                'currency' => $plan->currency,
            ],
        ]);
    }
}
