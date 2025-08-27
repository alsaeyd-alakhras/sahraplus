<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TmdbSyncLog extends Model
{
     use HasFactory;

    protected $fillable = [
        'content_type', 'content_id', 'tmdb_id', 'action',
        'status', 'synced_data', 'error_message', 'synced_at'
    ];

    protected $casts = [
        'synced_data' => 'array',
        'synced_at' => 'datetime',
    ];

    // Scopes
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeForContent($query, $type, $tmdbId)
    {
        return $query->where('content_type', $type)->where('tmdb_id', $tmdbId);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('synced_at', 'desc');
    }
}
