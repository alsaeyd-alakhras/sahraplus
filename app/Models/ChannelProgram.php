<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChannelProgram extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel_id',
        'title_ar',
        'title_en',
        'description_ar',
        'description_en',
        'start_time',
        'end_time',
        'duration_minutes',
        'genre',
        'is_live',
        'is_repeat',
        'poster_url',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_live' => 'boolean',
        'is_repeat' => 'boolean',
        'duration_minutes' => 'integer',
    ];

    public function channel()
    {
        return $this->belongsTo(LiveTvChannel::class);
    }

    public function scopeCurrent($query)
    {
        return $query
            ->where('start_time', '<=', now())
            ->where('end_time', '>', now());
    }

    public function scopeUpcoming($query)
    {
        return $query
            ->where('start_time', '>', now())
            ->orderBy('start_time');
    }
}
