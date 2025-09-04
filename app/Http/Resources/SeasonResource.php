<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SeasonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'series_id'     => $this->series_id,
            'season_number' => $this->season_number,
            'title_ar'      => $this->title_ar,
            'title_en'      => $this->title_en,
            'description_ar'=> $this->description_ar,
            'description_en'=> $this->description_en,
            'poster_url'    => $this->poster_url,
            'air_date'      => $this->air_date?->format('Y-m-d'),
            'episode_count' => $this->episode_count,
            'status'        => $this->status,
            'tmdb_id'       => $this->tmdb_id,
            'created_at'    => $this->created_at?->toDateTimeString(),
            'updated_at'    => $this->updated_at?->toDateTimeString(),
        ];
    }
}
