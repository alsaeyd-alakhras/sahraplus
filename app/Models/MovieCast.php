<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MovieCast extends Model
{
    use HasFactory;

    protected $table = 'movie_cast';

    protected $fillable = [
        'movie_id', 'person_id', 'role_type', 'character_name', 'sort_order'
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    // العلاقات
    public function movie()
    {
        return $this->belongsTo(Movie::class);
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
