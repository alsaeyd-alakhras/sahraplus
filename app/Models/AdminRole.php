<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminRole extends Model
{
    use HasFactory;

    protected $table = 'admin_roles';

    public $timestamps = false;
    protected $fillable = [
        'role_name','admin_id','ability'
    ];

    public function admin(){
        return $this->belongsTo(Admin::class);
    }
}
