<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeSectionItem extends Model
{
    protected $fillable = [
        'home_section_id',
        'content_type',
        'content_id',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */
    public function section()
    {
        return $this->belongsTo(HomeSection::class, 'home_section_id');
    }

    // علاقات المحتوى (اختياري – نستخدمها لاحقًا)
    public function movie()
    {
        return $this->belongsTo(Movie::class, 'content_id');
    }

    public function series()
    {
        return $this->belongsTo(Series::class, 'content_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */
    public function getContentAttribute()
    {
        if ($this->content_type === 'movie') {
            return $this->movie;
        } elseif ($this->content_type === 'series') {
            return $this->series;
        }
        return null;
    }
}
