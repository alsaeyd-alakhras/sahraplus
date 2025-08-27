<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subtitle extends Model
{
    use HasFactory;

    protected $fillable = [
        'content_type', 'content_id', 'language', 'label',
        'file_url', 'format', 'is_default', 'is_forced', 'is_active'
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_forced' => 'boolean',
        'is_active' => 'boolean',
    ];

    // العلاقات
    public function content()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByLanguage($query, $language)
    {
        return $query->where('language', $language);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}
