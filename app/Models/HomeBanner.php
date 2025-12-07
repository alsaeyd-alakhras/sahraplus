<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeBanner extends Model
{
    use HasFactory;

    protected $fillable = [
        'content_type',
        'content_id',
        'placement',
        'is_kids',
        'is_active',
        'sort_order',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'is_kids' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    // العلاقات
    public function movie()
    {
        return $this->belongsTo(Movie::class, 'content_id');
    }

    public function series()
    {
        return $this->belongsTo(Series::class, 'content_id');
    }

    // Accessor للحصول على المحتوى بشكل موحد
    public function getContentAttribute()
    {
        if ($this->content_type === 'movie') {
            return $this->movie;
        } elseif ($this->content_type === 'series') {
            return $this->series;
        }
        return null;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForPlacement($query, $placement)
    {
        return $query->where('placement', $placement);
    }

    public function scopeKids($query)
    {
        return $query->where('is_kids', true);
    }

    public function scopeAdults($query)
    {
        return $query->where('is_kids', false);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderByDesc('id');
    }

    // Helper methods
    public function getContentTitleAttribute()
    {
        $content = $this->content;
        if (!$content) return 'N/A';
        
        return app()->getLocale() === 'ar' 
            ? ($content->title_ar ?? $content->title_en ?? 'N/A')
            : ($content->title_en ?? $content->title_ar ?? 'N/A');
    }
}

