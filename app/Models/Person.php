<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Person extends Model
{
    use HasFactory;

    protected $table = 'people';

    protected $fillable = [
        'name_ar',
        'name_en',
        'bio_ar',
        'bio_en',
        'photo_url',
        'birth_date',
        'birth_place',
        'nationality',
        'gender',
        'known_for',
        'tmdb_id',
        'is_active'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'known_for' => 'array',
        'is_active' => 'boolean',
    ];

    protected $appends = ['photo_full_url', 'age', 'is_favorite'];

    //relationships
    public function movieRoles()
    {
        return $this->hasMany(MovieCast::class);
    }

    public function movies()
    {
        return $this->belongsToMany(Movie::class, 'movie_cast', 'person_id', 'movie_id')
            ->withPivot(['role_type', 'character_name', 'sort_order'])
            ->withTimestamps();
    }

    public function seriesRoles()
    {
        return $this->hasMany(SeriesCast::class);
    }

    public function getIsFavoriteAttribute()
    {
        return Favorite::where([
            'content_type' => 'person',
            'content_id' => $this->id,
        ])->exists();
    }

    public function series()
    {
        return $this->belongsToMany(Series::class, 'series_cast', 'person_id', 'series_id')
            ->withPivot('role_type', 'character_name', 'sort_order')
            ->withTimestamps();
    }

    // Accessors
    public function getNameAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : $this->name_en;
    }

    public function getBioAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->bio_ar : $this->bio_en;
    }

    public function getAgeAttribute()
    {
        return $this->birth_date ? $this->birth_date->age : null;
    }
    public function getPhotoFullUrlAttribute() // $this->photo_full_url
    {
        if (Str::startsWith($this->photo_url, ['http', 'https'])) {
            return $this->photo_url;
        }
        if (empty($this->photo_url)) {
            return null;
        }
        return asset('storage/' . $this->photo_url);
    }


    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeActors($query)
    {
        return $query->whereHas('movieRoles', function ($q) {
            $q->where('role_type', 'actor');
        })->orWhereHas('seriesRoles', function ($q) {
            $q->where('role_type', 'actor');
        });
    }

    public function scopeDirectors($query)
    {
        return $query->whereHas('movieRoles', function ($q) {
            $q->where('role_type', 'director');
        })->orWhereHas('seriesRoles', function ($q) {
            $q->where('role_type', 'director');
        });
    }
}