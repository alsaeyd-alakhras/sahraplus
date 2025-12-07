<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActiveDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'profile_id',
        'device_id',
        'ip_address',
        'last_activity',
        'is_active',
    ];

    protected $casts = [
        'last_activity' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function profile()
    {
        return $this->belongsTo(UserProfile::class);
    }
}

