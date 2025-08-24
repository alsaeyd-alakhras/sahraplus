<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = ['key', 'value'];

   public static function getValue(string $key, $default = null)
    {
        $row = static::where('key', $key)->first();
        return $row ? $row->value : $default;
    }

    public static function setValue(string $key, $value): self
    {
        return static::updateOrCreate(['key' => $key], ['value' => $value ?? '']);
    }
}
