<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'password',
        'email',
        'avatar',
        'super_admin',
        'is_active',
        'last_activity'
    ];

    protected $hidden = [
        'password', 'remember_token'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_activity' => 'datetime',
        'super_admin' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected $appends = ['avatar_url'];
    // العلاقات
    public function roles()
    {
        return $this->hasMany(AdminRole::class, 'admin_id', 'id');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    // Methods
    public function hasRole($roleName)
    {
        return $this->roles()->where('role_name', $roleName)->exists();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Accessor
    public function getAvatarUrlAttribute() // $user->avatar_url
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return asset('imgs/user.jpg');
    }
}
