<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoFileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       return [
            'id'               => $this->id,
            'content_type'     => $this->content_type,
            'content_id'       => $this->content_id,
            'video_type'       => $this->video_type,
            'quality'          => $this->quality,
            'format'           => $this->format,
            'file_url'         => $this->file_url,
            'file_size'        => $this->file_size,
            'duration_seconds' => $this->duration_seconds,
            'is_downloadable'  => $this->is_downloadable,
            'is_active'        => $this->is_active,
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
        ];
    }
}
