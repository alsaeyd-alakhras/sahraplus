<?php

namespace App\Services\Billing;

use App\Models\Payments;
use App\Services\Billing\Contracts\PaymentGatewayInterface;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Stevebauman\Location\Facades\Location;


class TelrGateway
{
    protected $testMode;
    protected $apiUrl;
    protected $storeId;
    protected $authKey;

    public function __construct()
    {
        // استخدام القيمة الافتراضية من config
        $this->testMode = config('services.telr.test_mode', true);
        $this->apiUrl = 'https://secure.telr.com/gateway/order.json';
        $this->storeId = config('services.telr.store_id');
        $this->authKey = config('services.telr.auth_key');
    }

    public function getName(): string
    {
        return 'telr';
    }

    public function createCheckout(User $user, SubscriptionPlan $plan, UserSubscription $subscription, array $options = []): array
    {
        $ip = request()->ip();

        try {
            $cacheKey = "geo_location_$ip";
            $location = cache()->remember($cacheKey, now()->addMinutes(30), function () use ($ip) {
                return Location::get($ip);
            });
        } catch (\Exception $e) {
            Log::warning("Location lookup failed for IP $ip: " . $e->getMessage());
            $location = null;
        }

        try {
            $testFlag = $this->testMode ? 1 : 0;

            $payload = [
                'method' => 'create',
                'store' => $this->storeId,
                'authkey' => $this->authKey,
                'framed' => 1,
                'order' => [
                    'cartid' => (string) $subscription->id,
                    'test' => $testFlag,
                    'amount' => (string) $subscription->total_amount,
                    'currency' => $subscription->currency ?? 'SAR',
                    'description' => "Subscription to {$plan->name_en} and description {$plan->description_en}",
                ],
                'customer' => [
                    'name' => [
                        'forenames' => $user->first_name ?? 'Customer',
                        'surname' => $user->last_name ?? 'Customer',
                    ],
                    'email' => $user->email,
                    'phone' => $user->phone ?? '',
                    'address' => [
                        'line1' => 'Unknown',
                        'city' => $location->cityName ?? 'Unknown',
                        'country' => $location->countryCode ?? 'AE',
                    ],
                ],

                // 'return' => [
                //     'authorised' => $options['isApp'] ?? false ? null : 'https://unattended-lucila-nonprosaically.ngrok-free.dev/payment/callback',
                //     'declined' =>  $options['isApp'] ?? false ? null : 'https://unattended-lucila-nonprosaically.ngrok-free.dev/payment/cancel',
                //     'cancelled' =>  $options['isApp'] ?? false ? null : 'https://unattended-lucila-nonprosaically.ngrok-free.dev/payment/cancel',
                // ],

                'return' => [
                    'authorised' => $options['isApp'] ?? false ? null : route('payment.callback'),
                    'declined'   => $options['isApp'] ?? false ? null : route('payment.cancel'),
                    'cancelled'  => $options['isApp'] ?? false ? null : route('payment.cancel'),
                ],


            ];

            Log::info('Telr Init Request:', $payload);

            $response = Http::post($this->apiUrl, $payload);
            $data = $response->json();

            if (!$response->successful() || !isset($data['order']['ref'])) {
                Log::error('Telr checkout failed or no ref returned', ['response' => $data]);
                return [
                    'success' => false,
                    'error' => $data['error']['message'] ?? 'Failed to initiate payment. Check API keys.',
                ];
            }

            $transactionId = $data['order']['ref'];

            // 3️⃣ حفظ الدفع مع ضمان uniqueness للـ transaction_id
            Payments::create([
                'user_id' => $subscription->user_id,
                'subscription_id' => $subscription->id,
                'payment_reference' => 'TELR_' . uniqid('', true), // unique more robust
                'amount'   => $subscription->total_amount,
                'currency'  => $subscription->currency,
                'tax_amount'   => $subscription->tax_amount,
                'fee_amount'   => 0,
                'net_amount'   => $subscription->total_amount,
                'payment_method'  => 'credit_card',
                'gateway'  => 'telr',
                'gateway_transaction_id' => $transactionId,
                'status'   => 'processing',
                'gateway_response' => json_encode($data),
                'processed_at'   => now(),
            ]);

            if (isset($data['order']['url'])) {
                return [
                    'success' => true,
                    'payment_url' => $data['order']['url'],
                    'transaction_id' => $transactionId,
                    'gateway' => 'telr',
                ];
            }

            Log::error('Telr checkout failed (no URL)', ['response' => $data]);
            return [
                'success' => false,
                'error' => $data['error']['message'] ?? 'Failed to initiate payment and URL not returned.',
            ];
        } catch (\Exception $e) {
            Log::error('Telr exception', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }


    public function handleWebhook(string $transactionRef)
    {
        try {
            // استخدام قيمة $this->testMode لتعيين وضع الاختبار
            $testFlag = $this->testMode ? 1 : 0;

            $payload = [
                'method' => 'check',
                'store'  => $this->storeId,
                'authkey' => $this->authKey,
                'order'  => [
                    'ref' => $transactionRef,
                    'test' => 1,
                ],
            ];

            Log::info('Telr verification request payload:', ['payload' => $payload]);

            $response = Http::post($this->apiUrl, $payload);

            // تحقق من نجاح الاستجابة على مستوى HTTP
            if (!$response->successful()) {
                Log::error('Telr verification HTTP failure', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return null;
            }

            $data = $response->json();

            // تسجيل محتوى JSON المستلم
            Log::info('Telr verification response:', [
                'transaction_ref' => $transactionRef,
                'response'        => $data,
            ]);

            return $data; // إرجاع محتوى JSON كـ array

        } catch (\Exception $e) {
            Log::error('Telr verification failed (Exception)', [
                'transaction_ref' => $transactionRef,
                'error'           => $e->getMessage(),
                'trace'           => $e->getTraceAsString(),
            ]);
            return null;
        }
    }
}