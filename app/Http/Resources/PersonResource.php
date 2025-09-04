<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PersonResource extends JsonResource
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
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'bio_ar' => $this->bio_ar,
            'bio_en' => $this->bio_en,
            'photo_url' => $this->photo_full_url,
            'birth_date' => $this->birth_date,
            'birth_place' => $this->birth_place,
            'nationality' => $this->nationality,
            'gender' => $this->gender,
            'known_for' => $this->known_for,
            'tmdb_id' => $this->tmdb_id,
            'is_active' => $this->is_active
        ];
    }
}
