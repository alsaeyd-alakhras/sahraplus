<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TmdbSyncLogResource extends JsonResource
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
            'content_type'  => $this->content_type,
            'content_id'    => $this->content_id,
            'tmdb_id'       => $this->tmdb_id,
            'action'        => $this->action,
            'status'        => $this->status,
            'synced_data'   => $this->synced_data,   // JSON data
            'error_message' => $this->error_message,
            'synced_at'     => $this->synced_at,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
