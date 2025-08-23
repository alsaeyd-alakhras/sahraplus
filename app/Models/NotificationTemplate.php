<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    protected $fillable = [
        'name',
        'subject_ar',
        'subject_en',
        'template_ar',
        'template_en',
        'type',
        'variables',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
