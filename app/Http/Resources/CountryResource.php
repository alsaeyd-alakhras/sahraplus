<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
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
            'code' => $this->code,
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'dial_code' => $this->dial_code,
            'currency' => $this->currency,
            'flag_url' => $this->flag_url,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
        ];
    }
}
