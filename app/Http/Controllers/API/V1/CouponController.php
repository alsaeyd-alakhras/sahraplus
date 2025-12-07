<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Payment;
use App\Services\Billing\PaylinkGateway;
use App\Models\UserSubscription;
use Illuminate\Http\Request;

class CouponController extends Controller
{


    public function validateCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'subscription_id' => 'integer',
            'amount' => 'required|numeric',
        ]);

        $subscription = UserSubscription::find($request->subscription_id);
        if($request->subscription_id && !$subscription){
            return response()->json(['status' => false, 'message' => 'Subscription not found.'], 404);
        }


        $user = Auth('sanctum')->user();

        $coupon = Coupon::where('code', $request->code)->first();

        if (!$coupon) {
            return response()->json(['status' => false, 'message' => 'Invalid coupon.'], 404);
        }

        if (!$coupon->is_active) {
            return response()->json(['status' => false, 'message' => 'Coupon is not active.'], 400);
        }

        if ($coupon->starts_at && now()->lt($coupon->starts_at)) {
            return response()->json(['status' => false, 'message' => 'Coupon not started yet.'], 400);
        }

        if ($coupon->expires_at && now()->gt($coupon->expires_at)) {
            return response()->json(['status' => false, 'message' => 'Coupon expired.'], 400);
        }

        $exists = CouponUsage::where('coupon_id', $coupon->id)
            ->where('user_id', $user->id)
            ->when($request->subscription_id, function ($q) use ($request) {
                $q->where('subscription_id', $request->subscription_id);
            })
            ->first();

        if ($exists) {
            return response()->json(['status' => false, 'message' => 'You have already used this coupon.'], 400);
        }

        // حساب الخصم
        $originalAmount = $request->amount;

        $discountAmount = $coupon->discount_type === 'percent'
            ? ($originalAmount * ($coupon->discount_value / 100))
            : $coupon->discount_value;

        $finalAmount = max($originalAmount - $discountAmount, 0);

        // === تحديث الاشتراك لو موجود ===
        if ($request->subscription_id) {
            if ($subscription) {
                $subscription->update([
                    'amount'          => $originalAmount,
                    'discount_amount' => $discountAmount,
                ]);
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Coupon is valid.',
            'data' => [
                'coupon_id' => $coupon->id,
                'original_amount' => $originalAmount,
                'discount_amount' => $discountAmount,
                'final_amount' => $finalAmount,
                'discount_type' => $coupon->discount_type,
                'discount_value' => $coupon->discount_value,
            ]
        ]);
    }

}