<?php

namespace App\Models;

use App\Models\Download;
use App\Models\Favorite;
use App\Models\Watchlist;
use App\Models\UserRating;
use App\Models\WatchProgres;
use App\Models\ViewingHistory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'avatar_url',
        'is_default',
        'is_child_profile',
        'pin_code',
        'language',
        'is_active'
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_child_profile' => 'boolean',
        'is_active' => 'boolean',
    ];
    protected $appends = ['avatar_full_url'];

    // العلاقات
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function watchlist()
    {
        return $this->hasMany(Watchlist::class, 'profile_id');
    }

    public function watchProgress()
    {
        return $this->hasMany(WatchProgres::class, 'profile_id');
    }

    public function viewingHistory()
    {
        return $this->hasMany(ViewingHistory::class, 'profile_id');
    }

    public function ratings()
    {
        return $this->hasMany(UserRating::class, 'profile_id');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'profile_id');
    }

    public function downloads()
    {
        return $this->hasMany(Download::class, 'profile_id');
    }

    public function getContinueWatching()
    {
        return $this->watchProgress()
            ->inProgress()
            ->recent()
            ->with('content')
            ->limit(10)
            ->get();
    }

    public function getRecentlyWatched()
    {
        return $this->viewingHistory()
            ->recent(7)
            ->with('content')
            ->limit(20)
            ->get();
    }

    // Methods
    public function verifyPin($pin)
    {
        return $this->pin_code === $pin;
    }

    public function setAsDefault()
    {
        // إلغاء الافتراضي من الملفات الأخرى
        self::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        $this->update(['is_default' => true]);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForChildren($query)
    {
        return $query->where('is_child_profile', true);
    }

    public function getAvatarFullUrlAttribute() // $user->avatar_full_url
    {
        if ($this->avatar_url) {
            return asset('storage/' . $this->avatar_url);
        }
        return asset('assets/img/avatars/1.png');
    }
}