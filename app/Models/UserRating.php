<?php

namespace App\Models;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'profile_id',
        'content_type',
        'content_id',
        'rating',
        'review',
        'is_spoiler',
        'helpful_count',
        'status',
        'reviewed_at'
    ];
    protected $appends = ['stars', 'status_trans', 'created',  'content_type_trans','user_name'];


    protected $casts = [
        'rating' => 'decimal:1  ',
        'is_spoiler' => 'boolean',
        'helpful_count' => 'integer',
        'reviewed_at' => 'datetime',
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
    public static function addRating($userId, $contentType, $contentId, $rating, $review = null, $isSpoiler = false)
    {
        return self::updateOrCreate([
            'user_id' => $userId,
            'content_type' => $contentType,
            'content_id' => $contentId
        ], [
            'rating' => $rating,
            'review' => $review,
            'is_spoiler' => $isSpoiler,
            'status' => 'approved',
            'reviewed_at' => now()
        ]);
    }

    public function markAsHelpful()
    {
        $this->increment('helpful_count');
    }

    public function approve()
    {
        $this->update(['status' => 'approved']);
    }

    public function reject()
    {
        $this->update(['status' => 'rejected']);
    }

    // Accessors
    public function getStarsAttribute()
    {
        return str_repeat('★', floor($this->rating)) . str_repeat('☆', 5 - floor($this->rating));
    }
    public function getUserNameAttribute()
    {
        return $this->user
            ? trim($this->user->first_name . ' ' . $this->user->last_name)
            : 'غير معروف';
    }
    public function getCreatedAttribute()
    {
        return $this->created_at ? $this->created_at->format('Y-m-d') : null;
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeWithReview($query)
    {
        return $query->whereNotNull('review');
    }

    public function scopeHighRating($query, $minRating = 4.0)
    {
        return $query->where('rating', '>=', $minRating);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('reviewed_at', 'desc');
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
    public function getStatusTransAttribute()
    {
        //'pending','downloading','completed','failed','expired'
        if ($this->status == 'pending') {
            return __('admin.pending');
        } elseif ($this->status == 'approved') {
            return __('admin.approved');
        } elseif ($this->status == 'rejected') {
            return __('admin.rejected');
        }
    }
}
