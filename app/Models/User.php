<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'date_of_birth',
        'gender',
        'country_code',
        'language',
        'avatar_url',
        'is_active',
        'is_banned',
        'email_notifications',
        'push_notifications',
        'parental_controls',
        'last_activity'
    ];

    protected $hidden = [
        'password', 'remember_token'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth' => 'date',
        'last_activity' => 'datetime',
        'is_active' => 'boolean',
        'is_banned' => 'boolean',
        'email_notifications' => 'boolean',
        'push_notifications' => 'boolean',
        'parental_controls' => 'boolean',
    ];
    protected $appends = ['avatar_full_url', 'full_name'];

    // العلاقات
    public function profiles()
    {
        return $this->hasMany(UserProfile::class);
    }

    public function sessions()
    {
        return $this->hasMany(UserSession::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class,'notifiable_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_code', 'code');
    }

    // Accessors & Mutators
    public function getFullNameAttribute() // $user->full_name
    {
        return $this->first_name . ' ' . $this->last_name;
    }
    public function getAvatarFullUrlAttribute() // $user->avatar_full_url
    {
        if ($this->avatar_url) {
            return asset('storage/' . $this->avatar_url);
        }
        return asset('assets/img/avatars/1.png');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeNotBanned($query)
    {
        return $query->where('is_banned', false);
    }
}
