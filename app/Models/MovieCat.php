<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MovieCat extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar', 'name_en', 'slug', 'description_ar', 'description_en',
        'image_url', 'color', 'sort_order', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // العلاقات
    public function movies()
    {
        return $this->belongsToMany(Movie::class, 'movie_category_mapping', 'category_id', 'movie_id');
    }

    public function series()
    {
        return $this->belongsToMany(Series::class, 'series_category_mapping', 'category_id', 'series_id');
    }

    // Accessors
    public function getNameAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : $this->name_en;
    }

    public function getDescriptionAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->description_ar : $this->description_en;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
