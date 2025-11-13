<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Short extends Model
{
     use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'video_path',
        'poster_path',
        'aspect_ratio',
        'likes_count',
        'comments_count',
        'shares_count',
        'share_url',
        'is_featured',
        'status',
        'created_by',
        'video_basic_url'
    ];

    protected $casts = [
        'likes_count' => 'integer',
        'comments_count' => 'integer',
        'shares_count' => 'integer',
        'is_featured' => 'boolean',
    ];

    protected $appends = [
        'poster_full_path',
        'video_full_url',
    ];

    // العلاقات
    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
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

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_short_pivot', 'short_id', 'category_id')
            ->withTimestamps();
    }

    // Accessors
    public function getVideoUrlAttribute()
    {
        return asset('storage/' . $this->video_path);
    }

    public function getPosterUrlAttribute()
    {
        return $this->poster_path ? asset('storage/' . $this->poster_path) : null;
    }

     public function getPosterFullPathAttribute() // $this->poster_full_path
    {
        if (Str::startsWith($this->poster_path, ['http', 'https'])) {
            return $this->poster_path;
        }
        if (empty($this->poster_path)) {
            return null;
        }
        return asset('storage/' . $this->poster_path);
    }

    public function getVideoFullUrlAttribute()
    {
        if (!$this->video_path) return null;
        if (Str::startsWith($this->video_path, ['http', 'https'])) {
            return $this->video_path;
        }
        return asset('storage/' . ltrim($this->video_path, '/'));
    }








    // Methods
    public function incrementLikes()
    {
        $this->increment('likes_count');
    }

    public function incrementComments()
    {
        $this->increment('comments_count');
    }

    public function incrementShares()
    {
        $this->increment('shares_count');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeVertical($query)
    {
        return $query->where('aspect_ratio', 'vertical');
    }

    public function scopeHorizontal($query)
    {
        return $query->where('aspect_ratio', 'horizontal');
    }

    public function scopeByCategory($q, $categoryId)
    {
        return $q->whereHas('categories', fn($qq) => $qq->where('movie_categories.id', $categoryId));
    }
}
