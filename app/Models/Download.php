<?php

namespace App\Models;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Download extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'profile_id',
        'content_type',
        'content_id',
        'quality',
        'format',
        'file_size',
        'status',
        'progress_percentage',
        'device_id',
        'download_token',
        'expires_at',
        'completed_at'
    ];
    protected $appends = ['created', 'duration', 'status_trans', 'user_name', 'expired', 'content_type_trans'];

    protected $casts = [
        'file_size' => 'integer',
        'progress_percentage' => 'integer',
        'expires_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($download) {
            $download->download_token = Str::random(40);
            $download->expires_at = now()->addDays(7); // انتهاء صلاحية بعد أسبوع
        });
    }

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
    public static function requestDownload($profileId, $contentType, $contentId, $quality, $format, $deviceId, $fileSize = null)
    {
        return self::create([
            'user_id' => UserProfile::find($profileId)->user_id,
            'profile_id' => $profileId,
            'content_type' => $contentType,
            'content_id' => $contentId,
            'quality' => $quality,
            'format' => $format,
            'file_size' => $fileSize,
            'device_id' => $deviceId,
            'status' => 'pending'
        ]);
    }

    public function startDownload()
    {
        $this->update(['status' => 'downloading']);
    }

    public function updateProgress($percentage)
    {
        $this->update(['progress_percentage' => $percentage]);
    }
    public function getUserNameAttribute()
    {
        return $this->user
            ? trim($this->user->first_name . ' ' . $this->user->last_name)
            : 'غير معروف';
    }

    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'progress_percentage' => 100,
            'completed_at' => now()
        ]);
    }

    public function markAsFailed()
    {
        $this->update(['status' => 'failed']);
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    // Accessors
    public function getFileSizeHumanAttribute()
    {
        if (!$this->file_size) return 'Unknown';

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    // Scopes
    public function scopeForProfile($query, $profileId)
    {
        return $query->where('profile_id', $profileId);
    }

    public function scopeForDevice($query, $deviceId)
    {
        return $query->where('device_id', $deviceId);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'downloading');
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'downloading', 'completed'])
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    // دالة لحساب مدة التحميل (من الإنشاء إلى الإكمال)
    public function getDurationAttribute()
    {
        if ($this->completed_at && $this->created_at) {
            return $this->created_at->diffForHumans($this->completed_at, true);
        }
        return null;
    }

    public function getCreatedAttribute()
    {
        return $this->created_at ? $this->created_at->format('Y-m-d') : null;
    }
    public function getExpiredAttribute()
    {
        return $this->expires_at ? $this->expires_at->format('Y-m-d') : null;
    }

    public function getStatusTransAttribute()
    {
        //'pending','downloading','completed','failed','expired'
        if ($this->status == 'pending') {
            return __('admin.pending');
        } elseif ($this->status == 'downloading') {
            return __('admin.downloading');
        } elseif ($this->status == 'completed') {
            return __('admin.completed');
        } elseif ($this->status == 'failed') {
            return __('admin.failed');
        } elseif ($this->status == 'expired') {
            return __('admin.expired');
        }
    }
    public function getContentTypeTransAttribute()
    {
        //'pending','downloading','completed','failed','expired'
        if ($this->content_type == 'movie') {
            return __('admin.movie');
        } elseif ($this->content_type == 'series') {
            return __('admin.series');
        } elseif ($this->content_type == 'episode') {
            return __('admin.episode');
        }
    }
}
