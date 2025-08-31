<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserRatingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'user_id'       => $this->user_id,
            'profile_id'    => $this->profile_id,
            'content_type'  => $this->content_type,
            'content_id'    => $this->content_id,
            'rating'        => (float) $this->rating,
            'review'        => $this->review,
            'is_spoiler'    => (bool) $this->is_spoiler,
            'helpful_count' => $this->helpful_count,
            'status'        => $this->status,
            'reviewed_at'   => $this->reviewed_at?->toISOString(),
            'created_at'    => $this->created_at?->toISOString(),
            'updated_at'    => $this->updated_at?->toISOString(),
        ];
    }
}
