<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Payments;
use App\Models\SubscriptionPlan;
use App\Models\SystemSetting;
use App\Models\Tax;
use App\Models\User;
use App\Services\Billing\PaylinkGateway;
use App\Models\UserSubscription;
use App\Services\Billing\TelrGateway;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    use ApiResponse;

    public function initiateCheckout(Request $request, TelrGateway $telrGateway)
    {
        $request->validate([
            'subscription_id' => 'required|exists:user_subscriptions,id',
        ]);

        $user = Auth('sanctum')->user();
        if (!$user) return $this->error("Unauthenticated user", 401);

        $subscription = UserSubscription::where('id', $request->subscription_id)
            ->where('user_id', $user->id)
            ->first();
        if (!$subscription) return $this->error("Not found Subscription", 404);

        if ($subscription->is_active) {
            return response()->json([
                'status' => false,
                'message' => 'Subscription is active.'
            ], 422);
        }

        if ($subscription->status == 'trial' || $subscription->trial_ends_at > now()) {
            return response()->json([
                'status' => false,
                'message' => 'Subscription is trial.'
            ], 422);
        }

        $plan = $subscription->plan;
        if (!$plan) return $this->error("Not found Plan", 401);

        $checkoutResult = $telrGateway->createCheckout($user, $plan, $subscription, [
            'isApp' => $request->header('Is-App', false)
        ]);

        if ($checkoutResult['success']) {
            return response()->json([
                'success' => true,
                'payment_url' => $checkoutResult['payment_url'],
                //'transaction_id' => $checkoutResult['transaction_id'],
            ], 200);
        }

        return response()->json([
            'success' => false,
            'error' => $checkoutResult['error'] ?? 'Failed to initiate payment.',
        ], 400);
    }

    // ==============================
    // Webhook لمعالجة الدفع
    //  {{api}}/v1/billing/webhook
    // ==============================
    public function handleTelrWebhook(Request $request, TelrGateway $telrGateway)
    {
        $transactionRef = $request->input('ref') ?? $request->query('ref');
        if (empty($transactionRef)) return response('ACKNOWLEDGED (No Ref)', 200);

        $telrResponse = $telrGateway->handleWebhook($transactionRef);
        if (!$telrResponse || !isset($telrResponse['order'])) return response('ACKNOWLEDGED (Verification Failed)', 200);

        $authStatus = $telrResponse['order']['status']['code']; // 3=Paid, 6=Captured
        $cartId = $telrResponse['order']['cartid'] ?? null;

        $payment = Payments::where('gateway_transaction_id', $transactionRef)->first();
        if (!$payment) {
            return response('ACKNOWLEDGED (Payment Record Not Found)', 200);
        }
        if ($payment->status === 'completed') {
            return response('Payment already completed ✅', 200);
        }


        $subscription = UserSubscription::find($cartId);
        if (!$subscription) return response('ACKNOWLEDGED (Subscription not found)', 200);

        if (in_array($authStatus, [3, 6])) {
            DB::beginTransaction();
            try {
                // تحديث الدفع
                $payment->update([
                    'status' => 'completed',
                    'gateway_response' => json_encode($telrResponse),
                    'processed_at' => now(),
                ]);

                // تفعيل الاشتراك الجديد
                $subscription->update([
                    'status' => 'active',
                    'starts_at' => now(),
                    'ends_at' => now()->addMonth(),
                ]);

                // إلغاء الاشتراك القديم إذا كان موجود
                $oldSubscription = UserSubscription::where('user_id', $subscription->user_id)
                    ->where('id', '!=', $subscription->id)
                    ->where('status', 'active')
                    ->latest('starts_at')
                    ->first();

                if ($oldSubscription) {
                    $oldSubscription->update([
                        'status' => 'canceled',
                        'canceled_at' => now()
                    ]);
                }
                if ($subscription->discount_amount > 0 && $subscription->coupon_id) {
                    $couponExists =Coupon::find($subscription->coupon_id);
                    if ($couponExists) {
                        $exists = CouponUsage::where('coupon_id', $subscription->coupon_id)
                            ->where('user_id', $subscription->user_id)
                            ->where('subscription_id', $subscription->id)
                            ->where('payment_id', $payment->id)
                            ->first();

                        if (!$exists) {
                            CouponUsage::create([
                                'coupon_id'       => $subscription->coupon_id,
                                'user_id'         => $subscription->user_id,
                                'subscription_id' => $subscription->id,
                                'payment_id'      => $payment->id,
                                'original_amount' => $subscription->amount,
                                'discount_amount' => $subscription->discount_amount,
                                'final_amount'    => $subscription->total_amount,
                                'currency'        => $subscription->currency,
                            ]);
                        }
                    } else {
                        Log::warning('Telr Callback: Coupon ID not found in coupons table, skipping usage record.', [
                            'coupon_id' => $subscription->coupon_id,
                            'subscription_id' => $subscription->id
                        ]);
                    }
                }
              //  return $oldSubscription;
             //   if ($payment->status === 'completed') return response('Payment already completed ✅', 200);

                DB::commit();
              if ($payment->status === 'completed') return response('Payment already completed ✅', 200);

            } catch (\Throwable $e) {
                DB::rollBack();
                return response('Payment Error', 200);
            }
        }

        return response('ACKNOWLEDGED', 200);
    }

}
