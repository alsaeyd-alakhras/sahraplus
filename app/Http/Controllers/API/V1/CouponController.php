<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\SystemSetting;
use App\Models\Payments;
use App\Services\Billing\PaylinkGateway;
use App\Models\UserSubscription;
use Illuminate\Http\Request;

class CouponController extends Controller
{

    protected $setting;
    public function __construct()
    {
        $this->setting = SystemSetting::where('key', 'site_tax')->first();
    }

    public function validateCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'subscription_id' => 'integer',
            'amount' => 'numeric',
        ]);

        $subscription = UserSubscription::find($request->subscription_id);
        if ($request->subscription_id && !$subscription) {
            return response()->json(['status' => false, 'message' => 'Subscription not found.'], 404);
        }

        $user = Auth('sanctum')->user();

        $coupon = Coupon::where('code', $request->code)->first();
        if (!$coupon) {
            return response()->json(['status' => false, 'message' => 'Coupon not found.'], 404);
        }

        // فحص اذا الكوبون نشط
        if (!$coupon->is_active) {
            return response()->json(['status' => false, 'message' => 'Coupon is not active.'], 400);
        }

        // فحص تاريخ البداية
        if ($coupon->starts_at && now()->lt($coupon->starts_at)) {
            return response()->json(['status' => false, 'message' => 'Coupon not started yet.'], 400);
        }

        // فحص تاريخ الانتهاء
        if ($coupon->expires_at && now()->gt($coupon->expires_at)) {
            return response()->json(['status' => false, 'message' => 'Coupon expired.'], 400);
        }

        // فحص هل المستخدم استخدم الكوبون قبل كده
        $exists = CouponUsage::where('coupon_id', $coupon->id)
            ->where('user_id', $user->id)
            ->when($request->subscription_id, function ($q) use ($request) {
                $q->where('subscription_id', $request->subscription_id);
            })
            ->first();

        if ($exists) {
            return response()->json(['status' => false, 'message' => 'You have already used this coupon.'], 400);
        }

        // فحص plan_id
        if ($coupon->plan_id && $subscription && $coupon->plan_id !== $subscription->plan_id) {
            return response()->json([
                'status' => false,
                'message' => 'This coupon is not valid for your subscription plan.'
            ], 400);
        }

        // حساب الخصم
        $originalAmount = $subscription->amount ?? 0;
        $discountAmount = $coupon->discount_type === 'percentage'
            ? ($originalAmount * ($coupon->discount_value / 100))
            : $coupon->discount_value;

        // تحديث الاشتراك لو موجود
        $taxPercent = $this->setting->value ?? 0;
        $amountAfterDiscount = max($originalAmount - $discountAmount, 0);
        $taxAmount = round($amountAfterDiscount * $taxPercent / 100, 2);
        $totalAmount = $amountAfterDiscount + $taxAmount;

        if ($request->subscription_id && $subscription) {
            $subscription->update([
                'discount_amount' => $discountAmount,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Coupon is valid.',
            'data' => [
                'coupon_id' => $coupon->id,
                'original_amount' => $originalAmount,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'final_amount' => $totalAmount,
                'discount_type' => $coupon->discount_type,
                'discount_value' => $coupon->discount_value,
            ]
        ]);
    }
}