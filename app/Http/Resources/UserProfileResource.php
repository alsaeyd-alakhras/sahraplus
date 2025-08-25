<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
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
            'name' => $this->name,
            'avatar_url' => $this->avatar_full_url,
            'is_default' => $this->is_default,
            'is_child_profile' => $this->is_child_profile,
            'pin_code' => $this->pin_code,
            'language' => $this->language,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
        ];
    }
}
