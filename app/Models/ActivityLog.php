<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;


    // تعطيل Timestamps
    public $timestamps = false;

    protected $fillable = [
        'admin_id',
        'admin_name',
        'ip_request',
        'ip_address',
        'event_type',
        'model_name',
        'message',
        'old_data',
        'new_data',
        'created_at',
    ];

    // Relationships
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    // Scopes
    public function scopeForModel($query, $modelName)
    {
        return $query->where('model_name', $modelName);
    }

    public function scopeForEvent($query, $eventType)
    {
        return $query->where('event_type', $eventType);
    }
}
