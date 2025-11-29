<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LiveTvChannelResource extends JsonResource
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
            'category_id' => $this->category_id,
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'slug' => $this->slug,
            'description_ar' => $this->description_ar,
            'description_en' => $this->description_en,
            'logo_url' => $this->logo_url ? (filter_var($this->logo_url, FILTER_VALIDATE_URL) ? $this->logo_url : url('storage/' . $this->logo_url)) : null,
            'poster_url' => $this->poster_url ? (filter_var($this->poster_url, FILTER_VALIDATE_URL) ? $this->poster_url : url('storage/' . $this->poster_url)) : null,
            'stream_url' => $this->stream_url,
            'stream_type' => $this->stream_type,
            'stream_health_status' => $this->stream_health_status,
            'stream_health_last_check' => $this->stream_health_last_check,
            'viewer_count' => $this->viewer_count,
            'sort_order' => $this->sort_order,
            'is_featured' => $this->is_featured,
            'is_active' => $this->is_active,
            'language' => $this->language,
            'country' => $this->country,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
