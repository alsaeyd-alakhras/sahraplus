<?php

namespace App\Services\Billing\Contracts;

use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    /**
     * Create a checkout session
     *
     * @param User $user
     * @param SubscriptionPlan $plan
     * @param UserSubscription $subscription
     * @param array $options
     * @return array
     */
    public function createCheckout(User $user, SubscriptionPlan $plan, UserSubscription $subscription, array $options = []): array;

    /**
     * Handle webhook from payment gateway
     *
     * @param Request $request
     * @return array
     */
    public function handleWebhook(Request $request): array;

    /**
     * Get gateway name
     *
     * @return string
     */
    public function getName(): string;
}

