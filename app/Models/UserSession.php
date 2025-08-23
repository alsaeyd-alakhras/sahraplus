<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    protected $fillable = [
        'user_id',
        'device_name',
        'device_type',
        'platform',
        'ip_address',
        'user_agent',
        'session_token',
        'is_active',
        'last_activity',
        'expires_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
