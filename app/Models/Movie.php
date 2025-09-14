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

    protected $appends = ['poster_full_url', 'backdrop_full_url','trailer_full_url'];

    // العلاقات
    public function categories()
    {
        return $this->belongsToMany(MovieCategory::class, 'category_movie_pivot', 'movie_id', 'category_id')
        ->withTimestamps();
    }

    /** الطاقم (pivot: movie_cast) */
    public function people()
    {
        // pivot يحتوي حقول إضافية اختيارية: role, character_name, job, ordering
        return $this->belongsToMany(Person::class, 'movie_cast', 'movie_id', 'person_id')
            ->withPivot(['role_type', 'character_name', 'sort_order'])
            ->withTimestamps();
    }

    public function cast()
    {
        return $this->hasMany(MovieCast::class);
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

    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'content');
    }

    public function downloads()
    {
        return $this->morphMany(Download::class, 'content');
    }

    /** ملفات الفيديو (Morph) */
    public function videoFiles()
    {
        return $this->morphMany(VideoFiles::class, 'content');
    }

    /** الترجمات (Morph) */
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

    // Methods
    public function getAverageRating()
    {
        return $this->userRatings()->approved()->avg('rating');
    }

    public function getRatingCount()
    {
        return $this->userRatings()->approved()->count();
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
    public function getPosterFullUrlAttribute() // $this->poster_full_url
    {
        if (Str::startsWith($this->poster_url, ['http', 'https'])) {
            return $this->poster_url;
        }
        if (empty($this->poster_url)) {
            return null;
        }
        return asset('storage/' . $this->poster_url);
    }
    public function getBackdropFullUrlAttribute() // $this->backdrop_full_url
    {
        if (Str::startsWith($this->backdrop_url, ['http', 'https'])) {
            return $this->backdrop_url;
        }
        if (empty($this->backdrop_url)) {
            return null;
        }
        return asset('storage/' . $this->backdrop_url);
    }
    public function getTrailerFullUrlAttribute() // $this->trailer_full_url
    {
        if (Str::startsWith($this->trailer_url, ['http', 'https'])) {
            return $this->trailer_url;
        }
        if (empty($this->trailer_url)) {
            return null;
        }
        return asset($this->trailer_url);
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
