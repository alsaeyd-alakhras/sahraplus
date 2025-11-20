<?php

namespace App\Services\Billing;

use App\Services\Billing\Contracts\PaymentGatewayInterface;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaylinkGateway implements PaymentGatewayInterface
{
    protected $testMode;
    protected $apiUrl;
    protected $apiKey;
    protected $secretKey;

    public function __construct(bool $testMode = false)
    {
        $this->testMode = $testMode;
        $this->apiUrl = $testMode 
            ? config('services.paylink.sandbox_url', 'https://sandbox.paylink.sa/api')
            : config('services.paylink.api_url', 'https://api.paylink.sa/api');
        $this->apiKey = config('services.paylink.api_key');
        $this->secretKey = config('services.paylink.secret_key');
    }

    public function getName(): string
    {
        return 'paylink';
    }

    public function createCheckout(User $user, SubscriptionPlan $plan, UserSubscription $subscription, array $options = []): array
    {
        if ($this->testMode) {
            return $this->createTestCheckout($user, $plan, $subscription, $options);
        }

        try {
            $payload = [
                'amount' => $subscription->total_amount,
                'currency' => $subscription->currency,
                'client_name' => $user->name,
                'client_email' => $user->email,
                'client_mobile' => $user->phone ?? '',
                'order_id' => $subscription->id,
                'callback_url' => route('api.billing.webhook', ['gateway' => 'paylink']),
                'success_url' => $options['success_url'] ?? config('app.url') . '/subscription/success',
                'cancel_url' => $options['cancel_url'] ?? config('app.url') . '/subscription/cancel',
                'note' => "Subscription to {$plan->name_en}",
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '/order', $payload);

            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'success' => true,
                    'payment_url' => $data['url'] ?? null,
                    'transaction_id' => $data['order_id'] ?? null,
                    'gateway' => 'paylink',
                ];
            }

            Log::error('Paylink checkout failed', ['response' => $response->json()]);
            
            return [
                'success' => false,
                'error' => 'Failed to create payment session',
                'gateway' => 'paylink',
            ];

        } catch (\Exception $e) {
            Log::error('Paylink exception', ['error' => $e->getMessage()]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'gateway' => 'paylink',
            ];
        }
    }

    public function handleWebhook(Request $request): array
    {
        if ($this->testMode) {
            return $this->handleTestWebhook($request);
        }

        try {
            $data = $request->all();
            
            // Verify signature if needed
            // $signature = $request->header('X-Paylink-Signature');
            
            $orderId = $data['order_id'] ?? null;
            $status = $data['order_status'] ?? null;
            $transactionId = $data['transaction_id'] ?? null;

            $success = in_array($status, ['paid', 'success', 'completed']);

            return [
                'success' => $success,
                'subscription_id' => $orderId,
                'transaction_id' => $transactionId,
                'status' => $status,
                'gateway' => 'paylink',
                'data' => $data,
            ];

        } catch (\Exception $e) {
            Log::error('Paylink webhook exception', ['error' => $e->getMessage()]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'gateway' => 'paylink',
            ];
        }
    }

    protected function createTestCheckout(User $user, SubscriptionPlan $plan, UserSubscription $subscription, array $options = []): array
    {
        Log::info('Paylink Test Mode: Creating checkout', [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'subscription_id' => $subscription->id,
            'amount' => $subscription->total_amount,
        ]);

        return [
            'success' => true,
            'payment_url' => route('api.billing.test-payment', [
                'gateway' => 'paylink',
                'subscription_id' => $subscription->id,
            ]),
            'transaction_id' => 'test_' . uniqid(),
            'gateway' => 'paylink',
            'test_mode' => true,
        ];
    }

    protected function handleTestWebhook(Request $request): array
    {
        Log::info('Paylink Test Mode: Handling webhook', $request->all());

        return [
            'success' => true,
            'subscription_id' => $request->input('subscription_id'),
            'transaction_id' => $request->input('transaction_id', 'test_' . uniqid()),
            'status' => 'paid',
            'gateway' => 'paylink',
            'test_mode' => true,
            'data' => $request->all(),
        ];
    }
}

