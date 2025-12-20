<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViewingHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'profile_id', 'content_type', 'content_id',
        'watch_duration_seconds', 'completion_percentage', 'device_type',
        'quality_watched', 'watched_at',
    ];

    protected $casts = [
        'watch_duration_seconds' => 'integer',
        'completion_percentage' => 'decimal:2',
        'watched_at' => 'datetime',
    ];

    // العلاقات
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function profile()
    {
        return $this->belongsTo(UserProfile::class);
    }

    public function content()
    {
        return $this->morphTo();
    }

    // Methods
    public static function recordView($profileId, $contentType, $contentId, $watchDuration, $completionPercentage, $deviceType = null, $quality = null)
    {
        return self::create([
            'user_id' => UserProfile::find($profileId)->user_id,
            'profile_id' => $profileId,
            'content_type' => $contentType,
            'content_id' => $contentId,
            'watch_duration_seconds' => $watchDuration,
            'completion_percentage' => $completionPercentage,
            'device_type' => $deviceType,
            'quality_watched' => $quality,
            'watched_at' => now(),
        ]);
    }

    // Accessors
    public function getFormattedDurationAttribute()
    {
        $hours = floor($this->watch_duration_seconds / 3600);
        $minutes = floor(($this->watch_duration_seconds % 3600) / 60);
        $seconds = $this->watch_duration_seconds % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }

        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    // Scopes
    public function scopeForProfile($query, $profileId)
    {
        return $query->where('profile_id', $profileId);
    }

    public function scopeByContentType($query, $type)
    {
        return $query->where('content_type', $type);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('watched_at', '>=', now()->subDays($days));
    }

    public function scopeToday($query)
    {
        return $query->whereDate('watched_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('watched_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public static function mostViewedMixed(int $limit = 12, bool $isChild = false)
    {
        // 1️⃣ المحاولة الأولى: اعتمادًا على ViewingHistory
        $movies = Movie::selectBasic()
            ->withCount('viewingHistory')
            ->when($isChild, callback: fn ($q) => $q->where('is_kids', true))
            ->orderByDesc('viewing_history_count')
            ->limit($limit)
            ->get();

        $series = Series::selectBasic()
            ->when($isChild, fn ($q) => $q->where('is_kids', true))
            ->with(['episodes' => function ($q) {
                $q->select([
                    'episods.id',
                    'episods.season_id',
                    'episods.view_count',
                ]);
            }])            
            ->get()
            ->map(function ($s) {
                $s->viewing_history_count = $s->episodes->sum('view_count');
                return $s;
            })
            ->sortByDesc('viewing_history_count')
            ->take($limit)
            ->values();
        

        $merged = $movies
            ->map(fn ($m) => ['type' => 'movie', 'data' => $m])
            ->merge(
                $series->map(fn ($s) => ['type' => 'series', 'data' => $s])
            )
            ->sortByDesc(fn ($i) => $i['data']->viewing_history_count)
            ->values();

        // 2️⃣ إذا في نتائج فعلية من ViewingHistory
        if ($merged->where('data.viewing_history_count', '>', 0)->count() > 0) {
            return $merged->take($limit);
        }

        // 3️⃣ Fallback: الاعتماد على view_count من الجداول نفسها
        $moviesFallback = Movie::selectBasic()
            ->when($isChild, fn ($q) => $q->where('is_kids', true))
            ->orderByDesc('view_count')
            ->limit($limit)
            ->get()
            ->map(fn ($m) => ['type' => 'movie', 'data' => $m]);

        $seriesFallback = Series::selectBasic()
            ->when($isChild, fn ($q) => $q->where('is_kids', true))
            ->orderByDesc('view_count')
            ->limit($limit)
            ->get()
            ->map(fn ($s) => ['type' => 'series', 'data' => $s]);

        return $moviesFallback
            ->merge($seriesFallback)
            ->sortByDesc(fn ($i) => $i['data']->view_count ?? 0)
            ->take($limit)
            ->values();
    }
}
