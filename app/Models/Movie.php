<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'title_ar',
        'title_en',
        'slug',
        'description_ar',
        'description_en',
        'poster_url',
        'backdrop_url',
        'trailer_url',
        'release_date',
        'duration_minutes',
        'imdb_rating',
        'content_rating',
        'language',
        'country',
        'status',
        'is_featured',
        'view_count',
        'tmdb_id',
        'created_by'
    ];

    protected $casts = [
        'release_date' => 'date',
        'duration_minutes' => 'integer',
        'imdb_rating' => 'decimal:1',
        'is_featured' => 'boolean',
        'view_count' => 'integer',
    ];

    protected $appends = ['poster_full_url', 'backdrop_full_url'];

    // العلاقات
    public function categories()
{
    return $this->belongsToMany(MovieCategory::class, 'category_movie_pivot', 'movie_id', 'category_id');
}

public function people()
{
    return $this->belongsToMany(
        Person::class,
        'movie_person_pivot',  // اسم الجدول الوسيط
        'movie_id',            // FK الخاص بـ Movie
        'person_id'            // FK الخاص بـ Person
    )->withTimestamps();    
     
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
    public function getPosterFullUrlAttribute() // $this->poster_full_url
    {
        if(Str::startsWith($this->poster_url, ['http', 'https'])) {
            return $this->poster_url;
        }
        if (empty($this->poster_url)) {
            return null;
        }
        return asset('storage/' . $this->poster_url);
    }
    public function getBackdropFullUrlAttribute() // $this->backdrop_full_url
    {
        if(Str::startsWith($this->backdrop_url, ['http', 'https'])) {
            return $this->backdrop_url;
        }
        if (empty($this->backdrop_url)) {
            return null;
        }
        return asset('storage/' . $this->backdrop_url);
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
