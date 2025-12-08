<?php

namespace App\Models;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Watchlist extends Model
{
    use HasFactory;
    // use SoftDeletes;

    protected $fillable = [
        'user_id',
        'profile_id',
        'content_type',
        'content_id',
        'added_at'
    ];

    protected $casts = [
        'added_at' => 'datetime',
    ];

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
    public static function addToWatchlist($profileId, $contentType, $contentId)
    {
        return self::firstOrCreate([
            'profile_id' => $profileId,
            'content_type' => $contentType,
            'content_id' => $contentId
        ], [
            'user_id' => UserProfile::find($profileId)->user_id,
            'added_at' => now()
        ]);
    }

    public static function removeFromWatchlist($profileId, $contentType, $contentId)
    {
        return self::where([
            'profile_id' => $profileId,
            'content_type' => $contentType,
            'content_id' => $contentId
        ])->delete();
    }

    public static function isInWatchlist($profileId, $contentType, $contentId)
    {
        return self::where([
            'profile_id' => $profileId,
            'content_type' => $contentType,
            'content_id' => $contentId
        ])->exists();
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
