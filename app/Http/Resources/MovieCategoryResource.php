<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovieCategoryResource extends JsonResource
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
            'name_ar'          => $this->name_ar,
            'name_en'          => $this->name_en,
            'slug'             => $this->slug,
            'description_ar'   => $this->description_ar,
            'description_en'   => $this->description_en,
            'image_url'        => $this->image_url,
            'color'            => $this->color,
            'sort_order'       => $this->sort_order,
            'is_active'        => $this->is_active,
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
        ];
    }
}
