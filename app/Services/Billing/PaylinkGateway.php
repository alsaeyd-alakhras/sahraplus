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

class PaylinkGateway implements PaymentGatewayInterface
{
    protected bool $testMode;
    protected string $apiUrl;
    protected string $authUrl;
    protected ?string $apiKey;
    protected ?string $secretKey;

    public function __construct(?bool $testMode = null)
    {
        $this->testMode = $testMode ?? config('settings.subscriptions.test_mode', true);

        //base url api to create invoice
        $this->apiUrl = $this->testMode
            ? config('services.paylink.sandbox_url', env('PAYLINK_SANDBOX_URL'))
            : config('services.paylink.api_url', env('PAYLINK_API_URL'));
        // Sandbox URL = https://restpilot.paylink.sa

        $this->apiKey = config('services.paylink.api_key', env('PAYLINK_API_KEY'));
        $this->secretKey = config('services.paylink.secret_key', env('PAYLINK_SECRET_KEY'));
    }

    public function getName(): string
    {
        return 'paylink';
    }

    /**
     * Create a checkout and return payment URL
     */
    public function createCheckout(User $user, SubscriptionPlan $plan, UserSubscription $subscription, array $options = []): array
    {

        try {
            //  STEP 1: Authenticate (get id_token)
            $authResp = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '/api/auth', [
                'apiId' => $this->apiKey,
                'secretKey' => $this->secretKey,
                'persistToken' => false,
            ]);

            if (!$authResp->successful()) {
                return [
                    'success' => false,
                    'error' => 'Authentication with Paylink failed',
                    'gateway' => 'paylink',
                ];
            }

            // get token
            $token = $authResp->json('id_token');

            if (!$token) {
                return [
                    'success' => false,
                    'error' => 'No token received from Paylink',
                    'gateway' => 'paylink',
                ];
            }

            //  STEP 2: Build addInvoice payload
            //$orderNumber = $options['order_number'] ?? 'sub_' . $subscription->id . '_' . time();
            $orderNumber = (string) $subscription->id;


            // products object minimal required fields: title, price, qty
            $products = [
                [
                    'title' => $plan->name_en ?? $plan->name_ar ?? 'Subscription: ' . ($plan->name_en ?? $plan->id),
                    'price' => (float) $subscription->total_amount,
                    'qty' => 1,
                    'description' => $plan->description_ar ?? null,
                    'isDigital' => true,
                ]
            ];

            $payload = [
                'orderNumber'  => (string) $orderNumber,
                'amount'       => (float) $subscription->total_amount,
                // 'callBackUrl'  => config('app.url') . '/api/v1/billing/paylink/webhook',
                // 'cancelUrl'    => config('app.url') . '/api/v1/billing/cancel',
                'callBackUrl'  => 'https://unattended-lucila-nonprosaically.ngrok-free.dev/api/v1/billing/paylink/webhook',
                'cancelUrl'    => 'https://unattended-lucila-nonprosaically.ngrok-free.dev/api/v1/billing/cancel',


                'clientName' => $user->first_name ?? $user->name ?? 'Customer ' . $user->id,
                'clientMobile' => $user->phone ?? '',
                'currency'     => $subscription->currency ?? $plan->currency ?? 'SAR',
                'products'     => $products,
                'note'         => $options['note'] ?? "Subscription to " . ($plan->name_en ?? $plan->name_ar),
            ];

            //  STEP 3: Call addInvoice with Bearer token

            $invoiceResp = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '/api/addInvoice', $payload);

            if (!$invoiceResp->successful()) {
                return [
                    'success' => false,
                    'error' => 'Failed to create invoice on Paylink',
                    'gateway' => 'paylink',
                ];
            }

            $data = $invoiceResp->json();
            if (!isset($data['url']) && !isset($data['mobileUrl']) && !isset($data['qrUrl']) && !data_get($data, 'gatewayOrderRequest.url')) {
                return [
                    'success' => false,
                    'error' => 'No payment URL received from Paylink',
                    'gateway' => 'paylink',
                ];
            }

            // Try to extract payment URL and transaction id from response in safe order
            $paymentUrl = $data['url'] ?? $data['mobileUrl'] ?? $data['qrUrl'] ?? data_get($data, 'gatewayOrderRequest.url');
            $transactionId = $data['transactionNo'] ?? data_get($data, 'gatewayOrderRequest.transactionNo') ?? $data['invoiceId'] ?? null;

            // Update subscription record with orderNumber / transaction if you want
            try {
                // $subscription->update([
                //     'order_number' => $orderNumber,
                //     'transaction_id' => $transactionId,
                //     'total_amount' => $subscription->total_amount,
                // ]);
                Payments::create([
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
                    'gateway_transaction_id' => $transactionId,
                    'status'                 => 'processing',
                    'gateway_response'       => json_encode(request()->all()),
                    'processed_at'           => now(),
                ]);
            } catch (\Throwable $e) {
                Log::warning('Failed to update subscription with paylink data', ['err' => $e->getMessage()]);
            }

            return [
                'success' => true,
                'payment_url' => $paymentUrl,
                'transaction_id' => $transactionId,
                'raw' => $data,
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

    /**
     * Handle incoming webhook from Paylink (expects Paylink payload)
     */
    public function handleWebhook(Request $request): array
    {

        try {
            $data = $request->all();
            $orderNumber = $data['orderNumber'] ?? null;
            $subscriptionId = intval($orderNumber); // نحوله لرقم
            $status = $data['orderStatus'] ?? $data['order_status'] ?? $data['status'] ?? null;
            $transactionId = $data['transactionNo'] ?? $data['transaction_no'] ?? $data['transaction_id'] ?? null;

            $success = in_array(strtolower((string)$status), ['paid', 'success', 'completed', 'a']);

            return [
                'success' => $success,
                'subscription_id' => $subscriptionId,
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
}
