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
            'id'             => $this->id,
            'title'          => $this->title,
            'description'    => $this->description,
            'video_path'     => $this->video_path,
            'poster_path'    => $this->poster_path,
            'aspect_ratio'   => $this->aspect_ratio,   // vertical | horizontal
            'likes_count'    => (int) $this->likes_count,
            'comments_count' => (int) $this->comments_count,
            'shares_count'   => (int) $this->shares_count,
            'share_url'      => $this->share_url,
            'is_featured'    => (bool) $this->is_featured,
            'status'         => $this->status,         // active | inactive
            'created_by'     => $this->created_by,
            'created_at'     => $this->created_at?->toDateTimeString(),
            'updated_at'     => $this->updated_at?->toDateTimeString(),
        ];
    }
}
