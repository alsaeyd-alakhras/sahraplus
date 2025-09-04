<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Season extends Model
{
    use HasFactory;

    protected $fillable = [
        'series_id',
        'season_number',
        'title_ar',
        'title_en',
        'description_ar',
        'description_en',
        'poster_url',
        'air_date',
        'episode_count',
        'status',
        'tmdb_id'
    ];

    protected $casts = [
        'season_number' => 'integer',
        'episode_count' => 'integer',
        'air_date' => 'date',
    ];

    protected $appends = ['poster_full_url'];

    // العلاقات
    public function series()
    {
        return $this->belongsTo(Series::class);
    }

    public function episodes()
    {
        return $this->hasMany(Episode::class);
    }

    // Accessors
    public function getTitleAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->title_ar : $this->title_en;
    }

    public function getDescriptionAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->description_ar : $this->description_en;
    }

    public function getPosterFullUrlAttribute()
    {
        if(Str::startsWith($this->poster_url, ['http', 'https'])) {
            return $this->poster_url;
        }
        if (empty($this->poster_url)) {
            return null;
        }
        return asset('storage/' . $this->poster_url);
    }

    // Methods
    public function updateEpisodeCount()
    {
        $this->episode_count = $this->episodes()->count();
        $this->save();

        // تحديث عدد الحلقات في المسلسل
        $this->series->updateCounts();
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('season_number');
    }
}
