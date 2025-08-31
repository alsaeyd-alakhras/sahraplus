<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ViewingHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       return [
            'id'                     => $this->id,
            'user_id'                => $this->user_id,
            'profile_id'             => $this->profile_id,
            'content_type'           => $this->content_type,
            'content_id'             => $this->content_id,
            'watch_duration_seconds' => $this->watch_duration_seconds,
            'completion_percentage'  => $this->completion_percentage,
            'device_type'            => $this->device_type,
            'quality_watched'        => $this->quality_watched,
            'watched_at'             => $this->watched_at,
            'created_at'             => $this->created_at,
            'updated_at'             => $this->updated_at,
        ];
    }
}
