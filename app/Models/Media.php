<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Media extends Model
{
    use HasFactory;
    protected $table = 'media';
    protected $fillable = [
        'name',
        'file_path',
        'mime_type',
        'size',
        'uploader_id',
        'alt',
        'title',
        'caption',
        'description',
    ];

    // العلاقات
    public function uploader()
    {
        return $this->belongsTo(Admin::class, 'uploader_id');
    }


    // Accessors
    public function getFileUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }

    public function getFileSizeHumanAttribute()
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    // Scopes
    public function scopeImages($query)
    {
        return $query->where('mime_type', 'like', 'image/%');
    }

    public function scopeVideos($query)
    {
        return $query->where('mime_type', 'like', 'video/%');
    }

    public function scopeDocuments($query)
    {
        return $query->whereNotIn('mime_type', function ($q) {
            $q->select('mime_type')
                ->from('media')
                ->where('mime_type', 'like', 'image/%')
                ->orWhere('mime_type', 'like', 'video/%');
        });
    }
}
