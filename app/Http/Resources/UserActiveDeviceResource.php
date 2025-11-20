<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserActiveDeviceResource extends JsonResource
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
            'device_id' => $this->device_id,
            'ip_address' => $this->ip_address,
            'last_activity' => $this->last_activity,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
        ];
    }
}

