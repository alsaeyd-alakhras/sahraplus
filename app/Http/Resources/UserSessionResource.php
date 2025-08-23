<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserSessionResource extends JsonResource
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
            'device_name' => $this->device_name,
            'device_type' => $this->device_type,
            'platform' => $this->platform,
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
            'session_token' => $this->session_token,
            'is_active' => $this->is_active,
            'last_activity' => $this->last_activity,
            'expires_at' => $this->expires_at,
            'created_at' => $this->created_at,
        ];
    }
}
