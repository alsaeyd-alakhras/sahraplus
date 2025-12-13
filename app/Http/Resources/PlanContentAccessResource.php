<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanContentAccessResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"                => $this->id,
            "content_type"    => $this->content_type,
            "content_id"  => $this->content_id,
            "access_type" => $this->access_type,

            "created_at" => $this->created_at ? $this->created_at->format('Y-m-d') : null,
            "updated_at" => $this->updated_at ? $this->updated_at->format('Y-m-d') : null,
        ];
    }
}
