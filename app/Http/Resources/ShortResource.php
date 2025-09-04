<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShortResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'video_path' => $this->video_path,
            'poster_path' => $this->poster_path,
            'aspect_ratio' => $this->aspect_ratio,
            'likes_count' => $this->likes_count,
            'comments_count' => $this->comments_count,
            'shares_count' => $this->shares_count,
            'share_url' => $this->share_url,
            'is_featured' => $this->is_featured,
            'status' => $this->status,
            'created_by' => $this->created_by
        ];
    }
}
