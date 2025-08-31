<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Episod extends Model
{
     use HasFactory;

    protected $fillable = [
        'season_id', 'episode_number', 'title_ar', 'title_en',
        'description_ar', 'description_en', 'thumbnail_url',
        'duration_minutes', 'air_date', 'imdb_rating', 'status',
        'view_count', 'tmdb_id'
    ];

    protected $casts = [
        'episode_number' => 'integer',
        'duration_minutes' => 'integer',
        'air_date' => 'date',
        'imdb_rating' => 'decimal:1',
        'view_count' => 'integer',
    ];

    // العلاقات
    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function series()
    {
        return $this->hasOneThrough(Series::class, Season::class, 'id', 'id', 'season_id', 'series_id');
    }

    public function videoFiles()
    {
        return $this->morphMany(VideoFiles::class, 'content');
    }

    public function subtitles()
    {
        return $this->morphMany(Subtitle::class, 'content');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function watchlists()
{
    return $this->morphMany(Watchlist::class, 'content');
}

public function watchProgress()
{
    return $this->morphMany(WatchProgres::class, 'content');
}

public function viewingHistory()
{
    return $this->morphMany(ViewingHistory::class, 'content');
}

public function userRatings()
{
    return $this->morphMany(UserRating::class, 'content');
}

public function downloads()
{
    return $this->morphMany(Download::class, 'content');
}

// Methods
public function getAverageRating()
{
    return $this->userRatings()->approved()->avg('rating');
}

public function incrementViewCount()
{
    $this->increment('view_count');
}

    // Accessors
    public function getTitleAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->title_ar : $this->title_en;
    }

    public function getDescriptionAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->description_ar : $this->description_en;
    }

    public function getDurationFormattedAttribute()
    {
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;
        return $hours > 0 ? "{$hours}h {$minutes}m" : "{$minutes}m";
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('episode_number');
    }
}
