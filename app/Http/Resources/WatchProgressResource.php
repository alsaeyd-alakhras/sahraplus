<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WatchProgressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       return [
            'id'                 => $this->id,
            'user_id'            => $this->user_id,
            'profile_id'         => $this->profile_id,
            'content_type'       => $this->content_type,
            'content_id'         => $this->content_id,
            'watched_seconds'    => $this->watched_seconds,
            'total_seconds'      => $this->total_seconds,
            'progress_percentage'=> $this->progress_percentage,
            'is_completed'       => $this->is_completed,
            'last_watched_at'    => $this->last_watched_at,
            'created_at'         => $this->created_at,
            'updated_at'         => $this->updated_at,
        ];
    }
}
