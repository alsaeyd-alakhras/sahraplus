<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Series extends Model
{
    use HasFactory;

    protected $table = 'series';

    protected $fillable = [
        'title_ar',
        'title_en',
        'slug',
        'description_ar',
        'description_en',
        'poster_url',
        'backdrop_url',
        'trailer_url',
        'first_air_date',
        'last_air_date',
        'seasons_count',
        'episodes_count',
        'imdb_rating',
        'content_rating',
        'language',
        'country',
        'status',
        'series_status',
        'is_featured',
        'view_count',
        'tmdb_id',
        'created_by'
    ];

    protected $casts = [
        'first_air_date' => 'date',
        'last_air_date' => 'date',
        'seasons_count' => 'integer',
        'episodes_count' => 'integer',
        'imdb_rating' => 'decimal:1',
        'is_featured' => 'boolean',
        'view_count' => 'integer',
    ];

    protected $appends = ['poster_full_url', 'backdrop_full_url'];

    // العلاقات
    public function categories()
    {
        return $this->belongsToMany(MovieCategory::class, 'series_category_mapping', 'series_id', 'category_id');
    }

    public function seasons()
    {
        return $this->hasMany(Season::class);
    }

    public function episodes()
    {
        return $this->hasManyThrough(
            Episode::class,   // الموديل النهائي
            Season::class,    // الموديل الوسيط
            'series_id',      // مفتاح الربط في جدول seasons
            'season_id',      // مفتاح الربط في جدول episodes
            'id',             // مفتاح primary لـ series
            'id'              // مفتاح primary لـ seasons
        );
    }

    public function cast()
    {
        return $this->hasMany(SeriesCast::class);
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

    public function getPosterFullUrlAttribute()
    {
        if(Str::startsWith($this->poster_url, ['http', 'https'])) {
            return $this->poster_url;
        }
        if (empty($this->poster_url)) {
            return null;
        }
        return asset('storage/' . $this->poster_url);
    }

    public function getBackdropFullUrlAttribute()
    {
        if(Str::startsWith($this->backdrop_url, ['http', 'https'])) {
            return $this->backdrop_url;
        }
        if (empty($this->backdrop_url)) {
            return null;
        }
        return asset('storage/' . $this->backdrop_url);
    }

    // Methods
    public function updateCounts()
    {
        $this->seasons_count = $this->seasons()->count();
        $this->episodes_count = $this->seasons()->withCount('episodes')->get()->sum('episodes_count');
        $this->save();
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
}
