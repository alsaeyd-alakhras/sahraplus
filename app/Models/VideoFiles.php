<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VideoFiles extends Model
{
    use HasFactory;

    protected $fillable = [
        'content_type', 'content_id', 'video_type', 'quality',
        'format', 'file_url', 'file_size', 'duration_seconds',
        'is_downloadable', 'is_active'
    ];

    protected $casts = [
        'file_size' => 'integer',
        'duration_seconds' => 'integer',
        'is_downloadable' => 'boolean',
        'is_active' => 'boolean',
    ];

    // العلاقات
    public function content()
    {
        return $this->morphTo();
    }

    // Accessors
    public function getFileSizeHumanAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getDurationFormattedAttribute()
    {
        $hours = floor($this->duration_seconds / 3600);
        $minutes = floor(($this->duration_seconds % 3600) / 60);
        $seconds = $this->duration_seconds % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }
        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByQuality($query, $quality)
    {
        return $query->where('quality', $quality);
    }

    public function scopeDownloadable($query)
    {
        return $query->where('is_downloadable', true);
    }
}
