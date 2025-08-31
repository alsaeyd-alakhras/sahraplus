<?php

namespace App\Models;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WatchProgres extends Model
{
     use HasFactory;

    protected $fillable = [
        'user_id', 'profile_id', 'content_type', 'content_id',
        'watched_seconds', 'total_seconds', 'progress_percentage',
        'is_completed', 'last_watched_at'
    ];

    protected $casts = [
        'watched_seconds' => 'integer',
        'total_seconds' => 'integer',
        'progress_percentage' => 'decimal:2',
        'is_completed' => 'boolean',
        'last_watched_at' => 'datetime',
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
    public static function updateProgress($profileId, $contentType, $contentId, $watchedSeconds, $totalSeconds)
    {
        $progressPercentage = ($watchedSeconds / $totalSeconds) * 100;
        $isCompleted = $progressPercentage >= 85; // يعتبر مكتمل عند 85%

        return self::updateOrCreate([
            'profile_id' => $profileId,
            'content_type' => $contentType,
            'content_id' => $contentId
        ], [
            'user_id' => UserProfile::find($profileId)->user_id,
            'watched_seconds' => $watchedSeconds,
            'total_seconds' => $totalSeconds,
            'progress_percentage' => $progressPercentage,
            'is_completed' => $isCompleted,
            'last_watched_at' => now()
        ]);
    }

    public function markAsCompleted()
    {
        $this->update([
            'is_completed' => true,
            'progress_percentage' => 100.00
        ]);
    }

    // Accessors
    public function getFormattedProgressAttribute()
    {
        return number_format($this->progress_percentage, 1) . '%';
    }

    public function getTimeRemainingAttribute()
    {
        return $this->total_seconds - $this->watched_seconds;
    }

    // Scopes
    public function scopeForProfile($query, $profileId)
    {
        return $query->where('profile_id', $profileId);
    }

    public function scopeInProgress($query)
    {
        return $query->where('is_completed', false)->where('progress_percentage', '>', 5);
    }

    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('last_watched_at', 'desc');
    }
}
