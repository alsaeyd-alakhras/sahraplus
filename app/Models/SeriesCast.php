<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeriesCast extends Model
{
    use HasFactory;

    protected $table = 'series_cast';

    protected $fillable = [
        'series_id', 'person_id', 'role_type', 'character_name', 'sort_order'
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    // العلاقات
    public function series()
    {
        return $this->belongsTo(Series::class);
    }

    public function person()
    {
        return $this->belongsTo(People::class);
    }

    // Scopes
    public function scopeActors($query)
    {
        return $query->where('role_type', 'actor');
    }

    public function scopeDirectors($query)
    {
        return $query->where('role_type', 'director');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
