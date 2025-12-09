<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovieResource extends JsonResource
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
            'title_ar' => $this->title_ar,
            'title_en' => $this->title_en,
            'slug' => $this->slug,
            'description_ar' => $this->description_ar,
            'description_en' => $this->description_en,
            'poster_url' => $this->poster_url,
            'poster_full_url' => $this->poster_full_url,
            'backdrop_url' => $this->backdrop_url,
            'backdrop_full_url' => $this->backdrop_full_url,
            'trailer_url' => $this->trailer_url,
            'release_date' => $this->release_date,
            'duration_minutes' => $this->duration_minutes,
            'imdb_rating' => $this->imdb_rating,
            'content_rating' => $this->content_rating,
            'language' => $this->language,
            'country' => $this->country,
            'status' => $this->status,
            'is_featured' => $this->is_featured,
            'view_count' => $this->view_count,
            'tmdb_id' => $this->tmdb_id,
            'created_by' => $this->created_by,
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'videoFiles' => VideoFileResource::collection($this->whenLoaded('videoFiles')),
            'subtitles' => SubtitleResource::collection($this->whenLoaded('subtitles')),
        ];
    }
}