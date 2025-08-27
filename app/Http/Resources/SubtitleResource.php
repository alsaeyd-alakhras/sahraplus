<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubtitleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'content_type' => $this->content_type,
            'content_id'   => $this->content_id,
            'language'     => $this->language,
            'label'        => $this->label,
            'file_url'     => $this->file_url,
            'format'       => $this->format,
            'is_default'   => $this->is_default,
            'is_forced'    => $this->is_forced,
            'is_active'    => $this->is_active,
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
        ];
    }
}
