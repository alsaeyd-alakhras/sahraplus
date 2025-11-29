<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveTvChannel extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name_ar',
        'name_en',
        'slug',
        'description_ar',
        'description_en',
        'logo_url',
        'poster_url',
        'stream_url',
        'epg_id',
        'stream_type',
        'stream_health_status',
        'stream_health_last_check',
        'stream_health_details',
        'viewer_count',
        'sort_order',
        'is_featured',
        'is_active',
        'language',
        'country',
    ];

    protected $casts = [
        'stream_health_last_check' => 'datetime',
        'stream_health_details' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(LiveTvCategory::class);
    }

    public function programs()
    {
        return $this->hasMany(ChannelProgram::class, 'channel_id');
    }

    public function currentProgram()
    {
        return $this->hasOne(ChannelProgram::class)
            ->where('start_time', '<=', now())
            ->where('end_time', '>', now());
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query
            ->orderBy('sort_order')
            ->orderByDesc('viewer_count');
    }
}


