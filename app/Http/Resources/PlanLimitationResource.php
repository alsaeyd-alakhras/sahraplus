<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanLimitationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"                => $this->id,
            "limitation_key"    => $this->limitation_key,
            "limitation_value"  => $this->limitation_value,
            "limitation_type"   => $this->limitation_type,
            "limitation_unit"   => $this->limitation_unit,

            "description_ar"    => $this->description_ar,
            "description_en"    => $this->description_en,

            "created_at" => $this->created_at ? $this->created_at->format('Y-m-d') : null,
            "updated_at" => $this->updated_at ? $this->updated_at->format('Y-m-d') : null,
        ];
    }
}
