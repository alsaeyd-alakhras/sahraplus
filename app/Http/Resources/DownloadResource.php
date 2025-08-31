<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DownloadResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'user_id'             => $this->user_id,
            'profile_id'          => $this->profile_id,
            'content_type'        => $this->content_type,
            'content_id'          => $this->content_id,
            'quality'             => $this->quality,
            'format'              => $this->format,
            'file_size'           => $this->file_size,
            'status'              => $this->status,
            'progress_percentage' => $this->progress_percentage,
            'device_id'           => $this->device_id,
            'download_token'      => $this->download_token,
            'expires_at'          => $this->expires_at,
            'completed_at'        => $this->completed_at,
            'created_at'          => $this->created_at,
            'updated_at'          => $this->updated_at,
        ];
    }
}
