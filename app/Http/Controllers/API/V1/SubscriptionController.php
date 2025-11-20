<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreSubscriptionRequest;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Models\Coupon;
use App\Models\CouponRedemption;
use App\Http\Resources\UserSubscriptionResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    /**
     * Create a new subscription
     */
    public function store(StoreSubscriptionRequest $request)
    {
        $user = $request->user();
        $plan = SubscriptionPlan::findOrFail($request->plan_id);

        // Check if user already has an active subscription
        $existingSubscription = UserSubscription::where('user_id', $user->id)
            ->whereIn('status', ['active', 'trial', 'pending'])
            ->where('ends_at', '>', Carbon::now())
            ->first();

        if ($existingSubscription) {
            return response()->json([
                'message' => 'You already have an active subscription',
                'subscription' => new UserSubscriptionResource($existingSubscription->load('plan')),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $amount = $plan->price;
            $discountAmount = 0;
            $coupon = null;

            // Apply coupon if provided
            if ($request->coupon_code) {
                $coupon = Coupon::where('code', $request->coupon_code)->first();

                if ($coupon && $coupon->canBeUsedByUser($user->id)) {
                    // Check if coupon is valid for this plan
                    if ($coupon->plan_id && $coupon->plan_id != $plan->id) {
                        DB::rollBack();
                        return response()->json([
                            'message' => 'This coupon is not valid for the selected plan',
                        ], 422);
                    }

                    $discountAmount = $coupon->calculateDiscount($amount);
                } else {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'Invalid or expired coupon code',
                    ], 422);
                }
            }

            $totalAmount = $amount - $discountAmount;

            // Calculate dates
            $startsAt = Carbon::now();
            $endsAt = match($plan->billing_period) {
                'monthly' => $startsAt->copy()->addMonth(),
                'quarterly' => $startsAt->copy()->addMonths(3),
                'yearly' => $startsAt->copy()->addYear(),
            };

            $trialEndsAt = $plan->trial_days > 0 ? $startsAt->copy()->addDays($plan->trial_days) : null;

            // Create subscription
            $subscription = UserSubscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'status' => 'pending',
                'amount' => $amount,
                'currency' => $plan->currency,
                'tax_amount' => 0,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'trial_ends_at' => $trialEndsAt,
                'auto_renew' => true,
                'payment_method' => $request->payment_method,
            ]);

            // Record coupon redemption (but don't increment times_used until payment is complete)
            if ($coupon) {
                $subscription->metadata = ['coupon_code' => $coupon->code];
                $subscription->save();
            }

            DB::commit();

            return response()->json([
                'message' => 'Subscription created successfully',
                'subscription' => new UserSubscriptionResource($subscription->load('plan')),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create subscription',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get current user's active subscription
     */
    public function me(Request $request)
    {
        $user = $request->user();

        $subscription = UserSubscription::where('user_id', $user->id)
            ->whereIn('status', ['active', 'trial'])
            ->where('ends_at', '>', Carbon::now())
            ->with('plan')
            ->first();

        if (!$subscription) {
            return response()->json([
                'message' => 'No active subscription found',
                'subscription' => null,
            ], 404);
        }

        return response()->json([
            'subscription' => new UserSubscriptionResource($subscription),
        ]);
    }

    /**
     * Cancel user's subscription
     */
    public function cancel(Request $request)
    {
        $user = $request->user();

        $subscription = UserSubscription::where('user_id', $user->id)
            ->whereIn('status', ['active', 'trial'])
            ->where('ends_at', '>', Carbon::now())
            ->first();

        if (!$subscription) {
            return response()->json([
                'message' => 'No active subscription found',
            ], 404);
        }

        $subscription->update([
            'status' => 'canceled',
            'canceled_at' => Carbon::now(),
            'cancellation_reason' => $request->input('reason', 'User requested cancellation'),
            'auto_renew' => false,
        ]);

        return response()->json([
            'message' => 'Subscription canceled successfully',
            'subscription' => new UserSubscriptionResource($subscription->load('plan')),
        ]);
    }

    /**
     * Check if user can use specific video quality
     */
    public function checkQuality(Request $request)
    {
        $request->validate([
            'quality' => ['required', 'string', 'in:sd,hd,uhd'],
        ]);

        $user = $request->user();
        $quality = $request->input('quality');

        $subscription = UserSubscription::where('user_id', $user->id)
            ->whereIn('status', ['active', 'trial'])
            ->where('ends_at', '>', Carbon::now())
            ->with('plan.limitations')
            ->first();

        if (!$subscription) {
            return response()->json([
                'allowed' => false,
                'reason' => 'no_active_subscription',
            ]);
        }

        $canUse = $subscription->canUseQuality($quality);

        return response()->json([
            'allowed' => $canUse,
            'quality' => $quality,
            'plan_max_quality' => $subscription->plan->video_quality,
        ]);
    }
}
