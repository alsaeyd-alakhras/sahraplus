<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class HomeSection extends Model
{
    protected $fillable = [
        'title_ar',
        'title_en',
        'platform',
        'is_kids',
        'is_active',
        'sort_order',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'is_kids' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */
    public function items()
    {
        return $this->hasMany(HomeSectionItem::class)
            ->orderBy('sort_order');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */
    public function getTitleAttribute()
    {
        return app()->getLocale() === 'ar'
            ? $this->title_ar
            : ($this->title_en ?? $this->title_ar);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */
    public function scopeActive(Builder $query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForPlatform(Builder $query, string $platform)
    {
        return $query->whereIn('platform', [$platform, 'both']);
    }

    public function scopeForKids(Builder $query, bool $isChildProfile)
    {
        if ($isChildProfile) {
            return $query->where('is_kids', true);
        }

        return $query;
    }

    public function scopeCurrentlyVisible(Builder $query)
    {
        $now = Carbon::now();

        return $query->where(function ($q) use ($now) {
            $q->whereNull('starts_at')
                ->orWhere('starts_at', '<=', $now);
        })->where(function ($q) use ($now) {
            $q->whereNull('ends_at')
                ->orWhere('ends_at', '>=', $now);
        });
    }
}
