<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserSubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'tax_amount' => $this->tax_amount,
            'discount_amount' => $this->discount_amount,
            'total_amount' => $this->total_amount,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'trial_ends_at' => $this->trial_ends_at,
            'auto_renew' => $this->auto_renew,
            'payment_method' => $this->payment_method,
            'canceled_at' => $this->canceled_at,
            'cancellation_reason' => $this->cancellation_reason,
            'plan' => new SubscriptionPlanResource($this->whenLoaded('plan')),
            'created_at' => $this->created_at,
        ];
    }
}

