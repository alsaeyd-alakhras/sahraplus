<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'code' => $this->code,
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discount_value,
            'starts_at' => $this->starts_at,
            'expires_at' => $this->expires_at,
            'usage_limit' => $this->usage_limit,
            'times_used' => $this->times_used,
            'remaining_uses' => $this->usage_limit ? ($this->usage_limit - $this->times_used) : null,
            'plan_id' => $this->plan_id,
        ];
    }
}

