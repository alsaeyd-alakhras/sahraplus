<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'title_ar', 'title_en', 'slug', 'description_ar', 'description_en',
        'poster_url', 'backdrop_url', 'trailer_url', 'release_date',
        'duration_minutes', 'imdb_rating', 'content_rating', 'language',
        'country', 'status', 'is_featured', 'view_count', 'tmdb_id', 'created_by'
    ];

    protected $casts = [
        'release_date' => 'date',
        'duration_minutes' => 'integer',
        'imdb_rating' => 'decimal:1',
        'is_featured' => 'boolean',
        'view_count' => 'integer',
    ];

    // العلاقات
    public function categories()
    {
        return $this->belongsToMany(MovieCategory::class, 'movie_category_mapping', 'movie_id', 'category_id');
    }

    public function cast()
    {
        return $this->hasMany(MovieCat::class);
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

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
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

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->whereHas('categories', function ($q) use ($categoryId) {
            $q->where('movie_categories.id', $categoryId);
        });
    }
}
