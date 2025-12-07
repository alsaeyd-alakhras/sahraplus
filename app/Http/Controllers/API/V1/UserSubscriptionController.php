<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Services\Billing\PaylinkGateway;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserSubscriptionController extends Controller
{
    use ApiResponse;

    // ==============================
    // عرض الاشتراك الحالي
    // GET /api/v1/user_subscription/me
    // ==============================
    public function index()
    {
        $user = Auth('sanctum')->user();

        $activeSubscription = $user->activeSubscription()->first();

        if (!$activeSubscription) {
            return $this->error("No Active Subscription Found", 404);
        }

        return $this->success([
            "activeSubscription" => $activeSubscription,
            "currentPlan" => $user->currentPlan()->first(),
            "limitations" => $user->currentContentAccess()
        ], "Get Active Subscription Successfully");
    }

    // ==============================
    // إضافة / ترقية اشتراك
    // POST /api/v1/user_subscription
    // ==============================
    public function store(Request $request)
    {
        $user = Auth('sanctum')->user();
        if (!$user) return $this->error("Unauthenticated user", 401);

        $plan = SubscriptionPlan::find($request->plan_id);
        if (!$plan) return $this->error("Subscription Plan Not Found", 404);

        $now = now();
        $current =  $user->activeSubscription()->first();
        $amountToCharge = $plan->price;


        // نفس الخطة → لا نفعل جديد
        if ($current &&   $current->plan_id == $plan->id) {
            return $this->success([
                'subscription' => $current,
                'amount_to_charge' => 0
            ], "You already have an active subscription for this plan");
        }

        // ترقية لخطة أعلى
        if ($current && $current->plan_id != $plan->id) {
            $startsAt = Carbon::parse($current?->starts_at);
            $endsAt = Carbon::parse($current?->ends_at);
            $remainingDays = $now->diffInDays($endsAt, false);
            $totalDays = max(1, $startsAt->diffInDays($endsAt));
            $diffPrice = $plan->price - $current->plan->price;

            // إذا هناك فرق سعر وحسابه بشكل prorated
            if ($diffPrice > 0 && $remainingDays > 0) {
                $amountToCharge = round($diffPrice * ($remainingDays / $totalDays), 2);
            } else {
                $amountToCharge = 0;
            }

            // الغاء الاشتراك القديم مرة واحدة
            $current->update([
                'status' => 'canceled',
                'canceled_at' => $now
            ]);
        }

        // إنشاء أو تحديث الاشتراك الجديد
        $subscription = UserSubscription::updateOrCreate(
            [
                'user_id' => $user->id,
                'plan_id' => $plan->id
            ],
            [
                'status' => $plan->trial_days > 0 ? 'trial' : 'pending',
                'starts_at' => now(),
                'trial_ends_at' => $plan->trial_days > 0 ? $now->addDays($plan->trial_days) : null,
                'ends_at' => $now->addMonth(),
                'amount' => $amountToCharge,
                'total_amount' => $amountToCharge,
                'currency' => $plan->currency,
                'payment_method' => $request->payment_method,
                'auto_renew' => true
            ]
        );

        // $gateway = new PaylinkGateway(config('settings.subscriptions.test_mode'));
        // $checkout = $gateway->createCheckout($user, $plan, $subscription);
        // return $checkout;
        // if (!$checkout['success']) {
        //     return 'An error occurred during payment initiation.';
        // }

        // return redirect()->away($checkout['payment_url']);

        // return $this->success([
        //     'subscription_id' => $subscription->id,
        //     'payment_url' => $checkout['payment_url'],
        //     'amount_to_charge' => $amountToCharge
        // ], "Checkout created successfully");

        return $this->success([
            'subscription' => $subscription->load('plan'),
            'amount_to_charge' => $amountToCharge
        ], "Subscription processed successfully");
    }

    // ==============================
    // إلغاء الاشتراك
    // POST /api/v1/user_subscription/cancel
    // ==============================
    public function cancel_user_subscription(Request $request)
    {
        $user = Auth('sanctum')->user();
        $current = $user->activeSubscription()->first();

        if (!$current) {
            return $this->error("No active subscription to cancel", 404);
        }

        $current->update([
            'status' => 'canceled',
            'canceled_at' => now(),
            'cancellation_reason' => $request->reason ?? null
        ]);

        return $this->success([
            'subscription' => $current
        ], "Subscription canceled successfully");
    }

}