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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BillingController extends Controller
{

    //protected $setting;
    protected $gateway;

    public function __construct()
    {
        //$this->setting = SystemSetting::first();
        $this->gateway = new TelrGateway();
    }

    public function pay(Request $request, $subscription_id)
    {
         $user = auth('web')->user();
         $subscription = UserSubscription::find($subscription_id);

        $plan = $subscription->plan;
        if (!$plan)
            return back()->with(
                'error',
                "Not found Plan"
            );


        if (!$user || !$subscription || !$plan) {
            return back()->with('error', 'خطأ: بيانات المستخدم أو الاشتراك غير موجودة للاختبار.');
        }
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

        $result = $this->gateway->createCheckout($user, $plan, $subscription);

        if ($result['success']) {
            Log::info('Telr Checkout Creation success', ['result' => $result]);

            $paymentUrl = $result['payment_url'];
            $transactionId = $result['transaction_id'];

            // حفظ مرجع المعاملة في الـ Session
            session(['transaction_id' => $transactionId]);
            Log::info('Transaction ID saved in session: ' . session('transaction_id'));

            return response(view('site.telr-iframe', ['paymentUrl' => $paymentUrl])->render());
        }

        Log::error('Telr Checkout Creation Failed', ['result' => $result]);
        return back()->with('error', $result['error'] ?? 'فشل في بدء عملية الدفع.');
    }

    public function callback(Request $request)
    {
        Log::info('Telr Callback Reached the Controller.', ['request_data' => $request->all()]);

        // 1️⃣ إيجاد آخر دفعة processing (قد تكون من checkout الأخير)
        $payment = Payments::where('status', 'processing')->latest()->first();

        if (!$payment) {
            Log::error('Telr Callback Error: No processing payment found in DB.');
            return "<h1>Payment Error ❌</h1><p>لم يتم العثور على أي دفعة قيد المعالجة.</p>";
        }

        if ($payment->status === 'completed') {
            return "<h1>Payment Error ❌</h1><p>Payment already completed ✅.</p>";
        }

        $transactionRef = $payment->gateway_transaction_id;

        // 2️⃣ التحقق من حالة المعاملة من Telr مباشرة
        try {
            $verification = $this->gateway->handleWebhook($transactionRef);
            Log::info('Telr verification result:', ['transaction_ref' => $transactionRef, 'verification' => $verification]);
        } catch (\Exception $e) {
            Log::error('Telr Callback Error: Verification failed with exception.', [
                'ref' => $transactionRef,
                'error' => $e->getMessage()
            ]);
            return "<h1>Payment Error ❌</h1><p>فشل في التحقق من حالة المعاملة مع Telr.</p>";
        }

        if (!$verification || !isset($verification['order'])) {
            Log::error('Telr Callback Error: Invalid verification result.', ['ref' => $transactionRef, 'verification' => $verification]);
            return "<h1>Payment Error ❌</h1><p>التحقق من المعاملة غير صالح.</p>";
        }

        $status = $verification['order']['status']['code']; // 3=Paid, 6=Captured
        $cartId = $verification['order']['cartid'] ?? null;

        if (!$cartId) {
            Log::error('Telr Callback Error: Cart ID missing in verification data.', ['ref' => $transactionRef]);
            return "<h1>Payment Error ❌</h1><p>معرف الاشتراك غير موجود.</p>";
        }

        $subscription = UserSubscription::find($cartId);
        if (!$subscription) {
            Log::error('Telr Callback Error: Subscription not found.', ['cart_id' => $cartId]);
            return "<h1>Payment Error ❌</h1><p>الاشتراك غير موجود.</p>";
        }

        // 3️⃣ الدفع ناجح
        if (in_array($status, [3, 6])) {
            // منع المعالجة المزدوجة
            if ($payment->status === 'completed') {
                Log::warning('Telr Callback: Payment already completed.', ['ref' => $transactionRef]);
                return "<h1>Payment Successful! ✅</h1><p>تم تأكيد الدفع مسبقاً.</p><a href='/home'>العودة للرئيسية</a>";
            }

            DB::beginTransaction();
            try {
                $payment->update([
                    'status' => 'completed',
                    'gateway_response' => json_encode($verification),
                    'processed_at' => now(),
                ]);

                $subscription->update([
                    'status' => 'active',
                    'starts_at' => now(),
                    'ends_at' => now()->addMonth(),
                ]);

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
                    $couponExists = Coupon::find($subscription->coupon_id);
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

                DB::commit();

                return "<h1>Payment Successful! ✅</h1>
                <p>تم تأكيد الدفع بنجاح.</p>
                <p>رقم المعاملة: $transactionRef</p>
                <a href='/home'>العودة للرئيسية</a>";
            } catch (\Throwable $e) {
                DB::rollBack();
                Log::error('Telr Callback Error: DB transaction failed.', ['ref' => $transactionRef, 'error' => $e->getMessage()]);
                return "<h1>Payment Error ❌</h1><p>حدث خطأ أثناء معالجة الدفع.</p>";
            }
        }

        // 4️⃣ الدفع فشل
        Log::warning('Telr Callback Failed: Status not successful.', ['ref' => $transactionRef, 'status_code' => $status]);
        return "<h1>Payment Failed ❌</h1>
        <p>عذراً، فشلت عملية الدفع.</p>
        <p>رقم المعاملة: $transactionRef</p>
        <a href='/retry'>إعادة المحاولة</a>";
    }



    /**
     * 3. صفحة الإلغاء (Cancel)
     */
    public function cancel()
    {
        return "<h1>Payment Cancelled ⚠️</h1><p>لقد قمت بإلغاء عملية الدفع. يمكنك المحاولة مرة أخرى.</p><a href='/pay/test'>إعادة المحاولة</a>";
    }


    //paylink payment
    // public function checkout(Request $request)
    // {
    //     $gateway = $request->query('gateway', 'paylink');
    //     $planId = $request->query('plan_id');
    //     $subscriptionId = $request->query('subscription_id');

    //     $plan = SubscriptionPlan::findOrFail($planId);
    //     $subscription = UserSubscription::findOrFail($subscriptionId);

    //     $taxFromSetting = $this->setting->tax ?? 0;


    //     $tax = $subscription->amount * $taxFromSetting / 100;
    //     $subscription->update([
    //         'tax_amount'=>$tax
    //     ]);


    //     if ($gateway !== 'paylink') {
    //         return response()->json(['error' => 'Unknown gateway'], 400);
    //     }

    //     $gateway = new PaylinkGateway(config('settings.subscriptions.test_mode'));
    //     $checkoutData = $gateway->createCheckout(
    //         $request->user(),
    //         $plan,
    //         $subscription,
    //     );

    //     if (!$checkoutData['success']) {
    //         return 'error';
    //         //response()->json(['error' => 'An error occurred during payment initiation.'], 500);
    //     }

    //     return $checkoutData;
    //     // return response()->json($checkoutData);
    // }

    // public function webhook(Request $request)
    // {
    //     // // ... ثم أكمل عملية Payment::create و $subscription->update
    //     // if ($gateway !== 'paylink') {
    //     //     return response()->json(['error' => 'Unknown gateway'], 400);
    //     // }

    //     $gateway = new PaylinkGateway(config('settings.subscriptions.test_mode'));
    //     $result = $gateway->handleWebhook($request);
    //     return $result;


    //     // // تحقق قبل إنشاء سجل الدفع
    //     // if (Payment::where('gateway_transaction_id', $result['transaction_id'])->exists()) {
    //     //     // قم بإرجاع نجاح، لمنع البوابة من إعادة إرسال الـ Webhook
    //     //     return response()->json(['message' => 'Payment already processed']);
    //     // }

    //     // if (!$result['success']) {
    //     //     return response()->json(['error' => 'Payment failed'], 400);
    //     // }

    //     // $subscription = UserSubscription::find($result['subscription_id']);

    //     // if (!$subscription) {
    //     //     return response()->json(['error' => 'Subscription not found'], 404);
    //     // }

    //     // // === إنشاء سجل الدفع ===
    //     // Payment::create([
    //     //     'user_id'                => $subscription->user_id,
    //     //     'subscription_id'        => $subscription->id,
    //     //     'payment_reference'      => 'PAYLINK_' . uniqid(),
    //     //     'amount'                 => $subscription->total_amount,
    //     //     'currency'               => $subscription->currency,
    //     //     'tax_amount'             => 0,
    //     //     'fee_amount'             => 0,
    //     //     'net_amount'             => $subscription->total_amount,
    //     //     'payment_method'         => 'credit_card',
    //     //     'gateway'                => 'paylink',
    //     //     'gateway_transaction_id' => $result['transaction_id'],
    //     //     'status'                 => 'completed',
    //     //     'gateway_response'       => json_encode($result['data']),
    //     //     'processed_at'           => now(),
    //     // ]);

    //     // // === تفعيل الاشتراك ===
    //     // $subscription->update([
    //     //     'status'         => 'active',
    //     //     'transaction_id' => $result['transaction_id'],
    //     //     'starts_at'      => now(),
    //     //     'ends_at'        => now()->addMonth()
    //     // ]);


    //     // return response()->json(['message' => 'Subscription activated & payment recorded']);
    // }

    // public function successPayment(Request $request)
    // {
    //     $subscription = UserSubscription::find($request->subscription_id);

    //     if (!$subscription) {
    //         return "Subscription not found.";
    //     }

    //     // إنشاء سجل الدفع
    //     $payment = Payment::create([
    //         'user_id'                => $subscription->user_id,
    //         'subscription_id'        => $subscription->id,
    //         'payment_reference'      => 'PAYLINK_' . uniqid(),
    //         'amount'                 => $subscription->total_amount,
    //         'currency'               => $subscription->currency,
    //         'tax_amount'             => 0,
    //         'fee_amount'             => 0,
    //         'net_amount'             => $subscription->total_amount,
    //         'payment_method'         => 'credit_card',
    //         'gateway'                => 'paylink',
    //         'gateway_transaction_id' => request('transaction_id'),
    //         'status'                 => 'completed',
    //         'gateway_response'       => json_encode(request()->all()),
    //         'processed_at'           => now(),
    //     ]);

    //     // تفعيل الاشتراك
    //     $subscription->update([
    //         'status'         => 'active',
    //         'transaction_id' => request('transaction_id'),
    //         'starts_at'      => now(),
    //         'ends_at'        => now()->addMonth()
    //     ]);

    //     // تسجيل استخدام الكوبون لو موجود
    //     // if ($subscription->discount_amount > 0 && $subscription->coupon_id) {
    //     //     $this->useCoupon(new Request([
    //     //         'coupon_id'       => $subscription->coupon_id,
    //     //         'subscription_id' => $subscription->id,
    //     //         'payment_id'      => $payment->id,
    //     //         'original_amount' => $subscription->amount,
    //     //         'discount_amount' => $subscription->discount_amount,
    //     //         'final_amount'    => $subscription->total_amount,
    //     //         'currency'        => $subscription->currency,
    //     //     ]));
    //     // }

    //     return "Payment success & subscription activated 🎉";
    // }

    // public function updateTaxAfterCoupon(UserSubscription $subscription)
    // {
    //     $netAmount = $subscription->amount - $subscription->discount_amount;
    //     if ($netAmount < 0) $netAmount = 0;

    //     $tax = 0;
    //     $taxes = Tax::where('is_active', 1)
    //         ->whereDate('effective_from', '<=', now())
    //         ->where(function ($q) {
    //             $q->whereDate('effective_until', '>=', now())->orWhereNull('effective_until');
    //         })
    //         ->orderBy('sort_order')
    //         ->get();

    //     foreach ($taxes as $taxRow) {
    //         $applicablePlans = $taxRow->applicable_plans ? json_decode($taxRow->applicable_plans, true) : [];
    //         if ($applicablePlans && !in_array($subscription->plan_id, $applicablePlans)) continue;

    //         if ($netAmount < $taxRow->min_amount) continue;
    //         if ($taxRow->max_amount && $netAmount > $taxRow->max_amount) continue;

    //         if ($taxRow->tax_type === 'percentage') {
    //             $tax += $netAmount * ($taxRow->tax_rate / 100);
    //         } else {
    //             $tax += $taxRow->tax_rate;
    //         }

    //         if ($taxRow->compound_tax) {
    //             $netAmount += $tax;
    //         }
    //     }

    //     $tax = round($tax, 2);
    //     $total = $netAmount + $tax;

    //     $subscription->update([
    //         'tax_amount' => $tax,
    //         'total_amount' => $total
    //     ]);

    //     return $subscription;
    // }

}
