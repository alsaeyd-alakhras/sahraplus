<?php

namespace App\Services\Billing;

use App\Services\Billing\Contracts\PaymentGatewayInterface;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelrGateway implements PaymentGatewayInterface
{
    protected $testMode;
    protected $apiUrl;
    protected $storeId;
    protected $authKey;

    public function __construct(bool $testMode = false)
    {
        $this->testMode = $testMode;
        $this->apiUrl = $testMode 
            ? config('services.telr.sandbox_url', 'https://secure.telr.com/gateway/order.json')
            : config('services.telr.api_url', 'https://secure.telr.com/gateway/order.json');
        $this->storeId = config('services.telr.store_id');
        $this->authKey = config('services.telr.auth_key');
    }

    public function getName(): string
    {
        return 'telr';
    }

    public function createCheckout(User $user, SubscriptionPlan $plan, UserSubscription $subscription, array $options = []): array
    {
        if ($this->testMode) {
            return $this->createTestCheckout($user, $plan, $subscription, $options);
        }

        try {
            $payload = [
                'method' => 'create',
                'store' => $this->storeId,
                'authkey' => $this->authKey,
                'order' => [
                    'cartid' => $subscription->id,
                    'amount' => $subscription->total_amount,
                    'currency' => $subscription->currency,
                    'description' => "Subscription to {$plan->name_en}",
                ],
                'customer' => [
                    'name' => [
                        'forenames' => $user->name,
                        'surname' => $user->name,
                    ],
                    'email' => $user->email,
                    'phone' => $user->phone ?? '',
                ],
                'return' => [
                    'authorised' => $options['success_url'] ?? config('app.url') . '/subscription/success',
                    'declined' => $options['cancel_url'] ?? config('app.url') . '/subscription/cancel',
                    'cancelled' => $options['cancel_url'] ?? config('app.url') . '/subscription/cancel',
                ],
                'webhook' => [
                    'url' => route('api.billing.webhook', ['gateway' => 'telr']),
                ],
            ];

            $response = Http::post($this->apiUrl, $payload);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['order']['url'])) {
                    return [
                        'success' => true,
                        'payment_url' => $data['order']['url'],
                        'transaction_id' => $data['order']['ref'] ?? null,
                        'gateway' => 'telr',
                    ];
                }
            }

            Log::error('Telr checkout failed', ['response' => $response->json()]);
            
            return [
                'success' => false,
                'error' => 'Failed to create payment session',
                'gateway' => 'telr',
            ];

        } catch (\Exception $e) {
            Log::error('Telr exception', ['error' => $e->getMessage()]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'gateway' => 'telr',
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
            
            $cartId = $data['cartid'] ?? null;
            $status = $data['status'] ?? null;
            $transactionRef = $data['tranref'] ?? null;

            $success = in_array($status, ['paid', 'A', 'H', 'authorized']);

            return [
                'success' => $success,
                'subscription_id' => $cartId,
                'transaction_id' => $transactionRef,
                'status' => $status,
                'gateway' => 'telr',
                'data' => $data,
            ];

        } catch (\Exception $e) {
            Log::error('Telr webhook exception', ['error' => $e->getMessage()]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'gateway' => 'telr',
            ];
        }
    }

    protected function createTestCheckout(User $user, SubscriptionPlan $plan, UserSubscription $subscription, array $options = []): array
    {
        Log::info('Telr Test Mode: Creating checkout', [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'subscription_id' => $subscription->id,
            'amount' => $subscription->total_amount,
        ]);

        return [
            'success' => true,
            'payment_url' => route('api.billing.test-payment', [
                'gateway' => 'telr',
                'subscription_id' => $subscription->id,
            ]),
            'transaction_id' => 'test_' . uniqid(),
            'gateway' => 'telr',
            'test_mode' => true,
        ];
    }

    protected function handleTestWebhook(Request $request): array
    {
        Log::info('Telr Test Mode: Handling webhook', $request->all());

        return [
            'success' => true,
            'subscription_id' => $request->input('subscription_id'),
            'transaction_id' => $request->input('transaction_id', 'test_' . uniqid()),
            'status' => 'paid',
            'gateway' => 'telr',
            'test_mode' => true,
            'data' => $request->all(),
        ];
    }
}

