<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group_name',
        'label_ar',
        'label_en',
        'description_ar',
        'description_en',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];
}
