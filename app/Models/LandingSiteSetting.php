<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingSiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'json_value',
    ];

    protected $casts = [
        'json_value' => 'array',
    ];

    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        if (!$setting) return $default;
        return $setting->json_value ?? $setting->value ?? $default;
    }

    public static function set($key, $value)
    {
        if (is_array($value) || is_object($value)) {
            return static::updateOrCreate(
                ['key' => $key],
                ['json_value' => $value, 'value' => null]
            );
        }

        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'json_value' => null]
        );
    }
}
