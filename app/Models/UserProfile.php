<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'avatar_url',
        'is_default',
        'is_child_profile',
        'age_restriction',
        'pin_code',
        'language',
        'is_active'
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_child_profile' => 'boolean',
        'is_active' => 'boolean',
        'age_restriction' => 'integer',
    ];

    // العلاقات
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Methods
    public function verifyPin($pin)
    {
        return $this->pin_code === $pin;
    }

    public function setAsDefault()
    {
        // إلغاء الافتراضي من الملفات الأخرى
        self::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        $this->update(['is_default' => true]);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForChildren($query)
    {
        return $query->where('is_child_profile', true);
    }
}
