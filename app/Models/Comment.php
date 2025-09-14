<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'commentable_type', 'commentable_id', 'user_id', 'profile_id',
        'parent_id', 'content', 'likes_count', 'replies_count',
        'is_edited', 'status', 'edited_at'
    ];

    protected $casts = [
        'likes_count' => 'integer',
        'replies_count' => 'integer',
        'is_edited' => 'boolean',
        'edited_at' => 'datetime',
    ];

    // العلاقات
    public function commentable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function profile()
    {
        return $this->belongsTo(UserProfile::class, 'profile_id');
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    // Methods
    public function addReply($content, $userId, $profileId = null)
    {
        $reply = self::create([
            'commentable_type' => $this->commentable_type,
            'commentable_id' => $this->commentable_id,
            'user_id' => $userId,
            'profile_id' => $profileId,
            'parent_id' => $this->id,
            'content' => $content,
            'status' => 'approved'
        ]);

        $this->increment('replies_count');
        return $reply;
    }

    public function incrementLikes()
    {
        $this->increment('likes_count');
    }

    public function markAsEdited()
    {
        $this->update([
            'is_edited' => true,
            'edited_at' => now()
        ]);
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeReplies($query)
    {
        return $query->whereNotNull('parent_id');
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
