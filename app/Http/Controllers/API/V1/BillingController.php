<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\CouponUsage;
use App\Models\Payment;
use App\Models\SubscriptionPlan;
use App\Models\Tax;
use App\Services\Billing\PaylinkGateway;
use App\Models\UserSubscription;
use Illuminate\Http\Request;

class BillingController extends Controller
{

    public function checkout(Request $request)
    {
        $gateway = $request->query('gateway', 'paylink');
        $planId = $request->query('plan_id');
        $subscriptionId = $request->query('subscription_id');

        $plan = SubscriptionPlan::findOrFail($planId);
        $subscription = UserSubscription::findOrFail($subscriptionId);

        // === تحديث الضريبة بعد تطبيق الكوبون ===
        $subscription = $this->updateTaxAfterCoupon($subscription);

        if ($gateway !== 'paylink') {
            return response()->json(['error' => 'Unknown gateway'], 400);
        }

        $gateway = new PaylinkGateway(config('settings.subscriptions.test_mode'));
        $checkoutData = $gateway->createCheckout(
            $request->user(),
            $plan,
            $subscription,
        );

        if (!$checkoutData['success']) {
            return 'error';
            //response()->json(['error' => 'An error occurred during payment initiation.'], 500);
        }

        return $checkoutData;
       // return response()->json($checkoutData);
    }
    public function webhook(Request $request, $gateway)
    {
        return $request;
        // // ... ثم أكمل عملية Payment::create و $subscription->update
        // if ($gateway !== 'paylink') {
        //     return response()->json(['error' => 'Unknown gateway'], 400);
        // }

        // $gateway = new PaylinkGateway(config('settings.subscriptions.test_mode'));
        // $result = $gateway->handleWebhook($request);


        // // تحقق قبل إنشاء سجل الدفع
        // if (Payment::where('gateway_transaction_id', $result['transaction_id'])->exists()) {
        //     // قم بإرجاع نجاح، لمنع البوابة من إعادة إرسال الـ Webhook
        //     return response()->json(['message' => 'Payment already processed']);
        // }

        // if (!$result['success']) {
        //     return response()->json(['error' => 'Payment failed'], 400);
        // }

        // $subscription = UserSubscription::find($result['subscription_id']);

        // if (!$subscription) {
        //     return response()->json(['error' => 'Subscription not found'], 404);
        // }

        // // === إنشاء سجل الدفع ===
        // Payment::create([
        //     'user_id'                => $subscription->user_id,
        //     'subscription_id'        => $subscription->id,
        //     'payment_reference'      => 'PAYLINK_' . uniqid(),
        //     'amount'                 => $subscription->total_amount,
        //     'currency'               => $subscription->currency,
        //     'tax_amount'             => 0,
        //     'fee_amount'             => 0,
        //     'net_amount'             => $subscription->total_amount,
        //     'payment_method'         => 'credit_card',
        //     'gateway'                => 'paylink',
        //     'gateway_transaction_id' => $result['transaction_id'],
        //     'status'                 => 'completed',
        //     'gateway_response'       => json_encode($result['data']),
        //     'processed_at'           => now(),
        // ]);

        // // === تفعيل الاشتراك ===
        // $subscription->update([
        //     'status'         => 'active',
        //     'transaction_id' => $result['transaction_id'],
        //     'starts_at'      => now(),
        //     'ends_at'        => now()->addMonth()
        // ]);


        // return response()->json(['message' => 'Subscription activated & payment recorded']);
    }

    public function successPayment(Request $request)
    {
        $subscription = UserSubscription::find($request->subscription_id);

        if (!$subscription) {
            return "Subscription not found.";
        }

        // إنشاء سجل الدفع
       $payment= Payment::create([
            'user_id'                => $subscription->user_id,
            'subscription_id'        => $subscription->id,
            'payment_reference'      => 'PAYLINK_' . uniqid(),
            'amount'                 => $subscription->total_amount,
            'currency'               => $subscription->currency,
            'tax_amount'             => 0,
            'fee_amount'             => 0,
            'net_amount'             => $subscription->total_amount,
            'payment_method'         => 'credit_card',
            'gateway'                => 'paylink',
            'gateway_transaction_id' => request('transaction_id'),
            'status'                 => 'completed',
            'gateway_response'       => json_encode(request()->all()),
            'processed_at'           => now(),
        ]);

        // تفعيل الاشتراك
        $subscription->update([
            'status'         => 'active',
            'transaction_id' => request('transaction_id'),
            'starts_at'      => now(),
            'ends_at'        => now()->addMonth()
        ]);

        // تسجيل استخدام الكوبون لو موجود
        // if ($subscription->discount_amount > 0 && $subscription->coupon_id) {
        //     $this->useCoupon(new Request([
        //         'coupon_id'       => $subscription->coupon_id,
        //         'subscription_id' => $subscription->id,
        //         'payment_id'      => $payment->id,
        //         'original_amount' => $subscription->amount,
        //         'discount_amount' => $subscription->discount_amount,
        //         'final_amount'    => $subscription->total_amount,
        //         'currency'        => $subscription->currency,
        //     ]));
        // }

        return "Payment success & subscription activated 🎉";
    }

    public function updateTaxAfterCoupon(UserSubscription $subscription)
    {
        $netAmount = $subscription->amount - $subscription->discount_amount;
        if ($netAmount < 0) $netAmount = 0;

        $tax = 0;
        $taxes = Tax::where('is_active', 1)
            ->whereDate('effective_from', '<=', now())
            ->where(function ($q) {
                $q->whereDate('effective_until', '>=', now())->orWhereNull('effective_until');
            })
            ->orderBy('sort_order')
            ->get();

        foreach ($taxes as $taxRow) {
            $applicablePlans = $taxRow->applicable_plans ? json_decode($taxRow->applicable_plans, true) : [];
            if ($applicablePlans && !in_array($subscription->plan_id, $applicablePlans)) continue;

            if ($netAmount < $taxRow->min_amount) continue;
            if ($taxRow->max_amount && $netAmount > $taxRow->max_amount) continue;

            if ($taxRow->tax_type === 'percentage') {
                $tax += $netAmount * ($taxRow->tax_rate / 100);
            } else {
                $tax += $taxRow->tax_rate;
            }

            if ($taxRow->compound_tax) {
                $netAmount += $tax;
            }
        }

        $tax = round($tax, 2);
        $total = $netAmount + $tax;

        $subscription->update([
            'tax_amount' => $tax,
            'total_amount' => $total
        ]);

        return $subscription;
    }


    public function useCoupon(Request $request)
    {
        $request->validate([
            'coupon_id' => 'required|integer',
            'subscription_id' => 'nullable|integer',
            'payment_id' => 'nullable|integer',
            'original_amount' => 'required|numeric',
            'discount_amount' => 'required|numeric',
            'final_amount' => 'required|numeric',
            'currency' => 'required|string|max:3',
        ]);

        $user = Auth('sanctum')->user();

        $subscription = null;
        $payment = null;

        if ($request->subscription_id) {
            $subscription = UserSubscription::find($request->subscription_id);
            if (!$subscription) {
                return response()->json(['status' => false, 'message' => 'Subscription not found.'], 404);
            }
        }

        if ($request->payment_id) {
            $payment = Payment::find($request->payment_id);
            if (!$payment) {
                return response()->json(['status' => false, 'message' => 'Payment not found.'], 404);
            }
        }

        // تحقق إذا تم استخدام الكوبون مسبقاً
        $exists = CouponUsage::where('coupon_id', $request->coupon_id)
            ->where('user_id', $user->id)
            ->when($request->subscription_id, fn($q) => $q->where('subscription_id', $request->subscription_id))
            ->when($request->payment_id, fn($q) => $q->where('payment_id', $request->payment_id))
            ->first();

        if ($exists) {
            return response()->json(['status' => false, 'message' => 'You have already used this coupon.'], 400);
        }

        // سجل الاستخدام
        CouponUsage::create([
            'coupon_id'       => $request->coupon_id,
            'user_id'         => $user->id,
            'subscription_id' => $subscription?->id,
            'payment_id'      => $payment?->id,
            'original_amount' => $request->original_amount,
            'discount_amount' => $request->discount_amount,
            'final_amount'    => $request->final_amount,
            'currency'        => $request->currency,
        ]);

        return response()->json(['status' => true, 'message' => 'Coupon usage recorded successfully.']);
    }
}