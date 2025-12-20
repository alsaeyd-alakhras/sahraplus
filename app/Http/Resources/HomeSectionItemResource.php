<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomeSectionItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"                => $this->id,
            "content_type"     => $this->content_type,
            "content_id"       => $this->content_id,
            "sort_order"       => $this->sort_order,

            "created_at" => $this->created_at ? $this->created_at->format('Y-m-d') : null,
            "updated_at" => $this->updated_at ? $this->updated_at->format('Y-m-d') : null,

            // Content relationship (optional)
            "content" => $this->when($this->content, function () {
                if ($this->content_type === 'movie' && $this->content) {
                    return new MovieResource($this->content);
                } elseif ($this->content_type === 'series' && $this->content) {
                    return new SeriesResource($this->content);
                }
                return null;
            }),
        ];
    }
}

