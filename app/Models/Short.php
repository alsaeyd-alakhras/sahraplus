<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Short extends Model
{
     use HasFactory;

    protected $fillable = [
        'title', 'description', 'video_path', 'poster_path',
        'aspect_ratio', 'likes_count', 'comments_count', 'shares_count',
        'share_url', 'is_featured', 'status', 'created_by'
    ];

    protected $casts = [
        'likes_count' => 'integer',
        'comments_count' => 'integer',
        'shares_count' => 'integer',
        'is_featured' => 'boolean',
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

    // Accessors
    public function getVideoUrlAttribute()
    {
        return asset('storage/' . $this->video_path);
    }

    public function getPosterUrlAttribute()
    {
        return $this->poster_path ? asset('storage/' . $this->poster_path) : null;
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
}
