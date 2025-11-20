<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CheckoutRequest;
use App\Models\UserSubscription;
use App\Models\Coupon;
use App\Models\CouponRedemption;
use App\Services\Billing\PaymentGatewayManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BillingController extends Controller
{
    protected $paymentGateway;

    public function __construct(PaymentGatewayManager $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    /**
     * Create checkout session
     */
    public function checkout(CheckoutRequest $request)
    {
        $user = $request->user();
        $subscription = UserSubscription::with('plan')
            ->where('id', $request->subscription_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($subscription->status !== 'pending') {
            return response()->json([
                'message' => 'This subscription has already been processed',
            ], 422);
        }

        try {
            $result = $this->paymentGateway->createCheckout(
                $user,
                $subscription->plan,
                $subscription,
                [
                    'success_url' => $request->input('success_url'),
                    'cancel_url' => $request->input('cancel_url'),
                ]
            );

            if ($result['success']) {
                // Update subscription metadata with transaction info
                $metadata = $subscription->metadata ?? [];
                $metadata['payment_gateway'] = $result['gateway'];
                $metadata['transaction_id'] = $result['transaction_id'] ?? null;
                $subscription->metadata = $metadata;
                $subscription->save();

                return response()->json([
                    'message' => 'Checkout session created successfully',
                    'payment_url' => $result['payment_url'],
                    'transaction_id' => $result['transaction_id'] ?? null,
                    'gateway' => $result['gateway'],
                    'test_mode' => $result['test_mode'] ?? false,
                ]);
            }

            return response()->json([
                'message' => 'Failed to create checkout session',
                'error' => $result['error'] ?? 'Unknown error',
            ], 500);

        } catch (\Exception $e) {
            Log::error('Checkout failed', [
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Failed to create checkout session',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle payment gateway webhook
     */
    public function webhook(Request $request)
    {
        $gateway = $request->input('gateway') ?? $request->route('gateway');

        Log::info('Webhook received', [
            'gateway' => $gateway,
            'data' => $request->all(),
        ]);

        try {
            $result = $this->paymentGateway->handleWebhook($request, $gateway);

            if ($result['success']) {
                DB::beginTransaction();
                try {
                    $subscription = UserSubscription::find($result['subscription_id']);

                    if ($subscription) {
                        // Update subscription status to active
                        $subscription->status = 'active';
                        $subscription->external_subscription_id = $result['transaction_id'] ?? null;
                        
                        $metadata = $subscription->metadata ?? [];
                        $metadata['payment_confirmed_at'] = Carbon::now()->toIso8601String();
                        $metadata['webhook_data'] = $result['data'] ?? [];
                        $subscription->metadata = $metadata;
                        
                        $subscription->save();

                        // Process coupon if used
                        if (isset($metadata['coupon_code'])) {
                            $coupon = Coupon::where('code', $metadata['coupon_code'])->first();
                            if ($coupon) {
                                // Increment coupon usage
                                $coupon->increment('times_used');

                                // Create redemption record
                                CouponRedemption::create([
                                    'coupon_id' => $coupon->id,
                                    'user_id' => $subscription->user_id,
                                    'subscription_id' => $subscription->id,
                                    'discount_amount' => $subscription->discount_amount,
                                ]);
                            }
                        }

                        DB::commit();

                        Log::info('Subscription activated', [
                            'subscription_id' => $subscription->id,
                            'user_id' => $subscription->user_id,
                        ]);

                        return response()->json([
                            'message' => 'Webhook processed successfully',
                            'subscription_id' => $subscription->id,
                        ]);
                    }

                    DB::rollBack();
                    return response()->json([
                        'message' => 'Subscription not found',
                    ], 404);

                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Webhook processing failed', ['error' => $e->getMessage()]);
                    throw $e;
                }
            }

            return response()->json([
                'message' => 'Payment not successful',
                'status' => $result['status'] ?? 'unknown',
            ], 200);

        } catch (\Exception $e) {
            Log::error('Webhook exception', ['error' => $e->getMessage()]);
            
            return response()->json([
                'message' => 'Webhook processing failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
