<?php

namespace App\Services\Billing;

use App\Services\Billing\Contracts\PaymentGatewayInterface;
use App\Services\Billing\PaylinkGateway;
use App\Services\Billing\TelrGateway;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Illuminate\Http\Request;

class PaymentGatewayManager
{
    protected $gateway;
    protected $testMode;

    public function __construct()
    {
        $this->testMode = config('settings.subscriptions.test_mode', false);
        $defaultGateway = config('settings.subscriptions.default_gateway', 'paylink');

        $this->gateway = $this->resolveGateway($defaultGateway);
    }

    /**
     * Resolve the gateway instance
     */
    protected function resolveGateway(string $gatewayName): PaymentGatewayInterface
    {
        return match($gatewayName) {
            'paylink' => new PaylinkGateway($this->testMode),
            'telr' => new TelrGateway($this->testMode),
            default => throw new \InvalidArgumentException("Unsupported gateway: {$gatewayName}"),
        };
    }

    /**
     * Create checkout session
     */
    public function createCheckout(User $user, SubscriptionPlan $plan, UserSubscription $subscription, array $options = []): array
    {
        return $this->gateway->createCheckout($user, $plan, $subscription, $options);
    }

    /**
     * Handle webhook
     */
    public function handleWebhook(Request $request, ?string $gatewayName = null): array
    {
        if ($gatewayName) {
            $gateway = $this->resolveGateway($gatewayName);
            return $gateway->handleWebhook($request);
        }

        return $this->gateway->handleWebhook($request);
    }

    /**
     * Get current gateway name
     */
    public function getGatewayName(): string
    {
        return $this->gateway->getName();
    }

    /**
     * Check if in test mode
     */
    public function isTestMode(): bool
    {
        return $this->testMode;
    }
}

