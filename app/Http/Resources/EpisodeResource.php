<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EpisodeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'season_id'       => $this->season_id,
            'episode_number'  => $this->episode_number,
            'title_ar'        => $this->title_ar,
            'title_en'        => $this->title_en,
            'description_ar'  => $this->description_ar,
            'description_en'  => $this->description_en,
            'thumbnail_url'   => $this->thumbnail_url,
            'duration_minutes'=> $this->duration_minutes,
            'air_date'        => $this->air_date?->format('Y-m-d'),
            'imdb_rating'     => $this->imdb_rating,
            'status'          => $this->status,
            'view_count'      => $this->view_count,
            'tmdb_id'         => $this->tmdb_id,
            'created_at'      => $this->created_at?->toDateTimeString(),
            'updated_at'      => $this->updated_at?->toDateTimeString(),
            'videoFiles' => VideoFileResource::collection($this->whenLoaded('videoFiles')),
        ];
    }
}