<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Favorite extends Model
{
     use HasFactory;

    protected $fillable = [
        'user_id', 'profile_id', 'content_type', 'content_id', 'added_at'
    ];

    protected $casts = [
        'added_at' => 'datetime',
    ];
    protected $appends=['is_favorite'];

    // العلاقات
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function profile()
    {
        return $this->belongsTo(UserProfile::class);
    }

    public function content()
    {
        return $this->morphTo();
    }

    // Methods
    public static function addToFavorites($profileId, $contentType, $contentId , $user_id)
    {
        return self::firstOrCreate([
            'profile_id' => $profileId,
            'content_type' => $contentType,
            'content_id' => $contentId
        ], [
            'user_id' => $user_id,
            'added_at' => now()
        ]);
    }

    public static function removeFromFavorites($profileId, $contentType, $contentId, $user_id)
    {
        return self::where([
            'profile_id' => $profileId,
            'content_type' => $contentType,
            'content_id' => $contentId,
            'user_id' => $user_id
        ])->delete();
    }

    public function getIsFavoriteAttribute()
    {
        return self::where([
            'profile_id' => $this->profile_id,
            'content_type' => $this->content_type,
            'content_id' => $this->content_id,
            'user_id' => $this->user_id
        ])->exists();
    }
    public static function IsFavorite($profileId, $contentType, $contentId, $user_id)
    {
        return self::where([
            'profile_id' => $profileId,
            'content_type' => $contentType,
            'content_id' => $contentId,
            'user_id' => $user_id
        ])->exists();
    }

    public static function toggleFavorite($profileId, $contentType, $contentId,$user_id)
    {
        if (self::isFavorite($profileId, $contentType, $contentId,$user_id)) {
            return self::removeFromFavorites($profileId, $contentType, $contentId, $user_id);
        } else {
            return self::addToFavorites($profileId, $contentType, $contentId, $user_id);
        }
    }

    // Scopes
    public function scopeForProfile($query, $profileId)
    {
        return $query->where('profile_id', $profileId);
    }

    public function scopeByContentType($query, $type)
    {
        return $query->where('content_type', $type);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('added_at', 'desc');
    }
}
