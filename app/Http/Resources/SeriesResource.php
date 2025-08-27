<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SeriesResource extends JsonResource
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
            'title_ar'        => $this->title_ar,
            'title_en'        => $this->title_en,
            'slug'            => $this->slug,
            'description_ar'  => $this->description_ar,
            'description_en'  => $this->description_en,
            'poster_url'      => $this->poster_url,
            'backdrop_url'    => $this->backdrop_url,
            'trailer_url'     => $this->trailer_url,
            'first_air_date'  => $this->first_air_date,
            'last_air_date'   => $this->last_air_date,
            'seasons_count'   => $this->seasons_count,
            'episodes_count'  => $this->episodes_count,
            'imdb_rating'     => $this->imdb_rating,
            'content_rating'  => $this->content_rating,
            'language'        => $this->language,
            'country'         => $this->country,
            'status'          => $this->status,
            'series_status'   => $this->series_status,
            'is_featured'     => $this->is_featured,
            'view_count'      => $this->view_count,
            'tmdb_id'         => $this->tmdb_id,
            'created_by'      => $this->created_by,
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at,
        ];
    }
}
