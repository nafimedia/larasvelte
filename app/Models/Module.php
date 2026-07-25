<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Module extends Model
{
    use HasFactory, LogsActivity;

    public const CACHE_KEY = 'active_cms_modules';

    protected $fillable = [
        'key',
        'name',
        'group',
        'description',
        'icon',
        'is_active',
        'is_system',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_system' => 'boolean',
        'order' => 'integer',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['key', 'name', 'is_active'])
            ->logOnlyDirty();
    }

    /**
     * Get cached map of module active statuses [key => boolean].
     */
    public static function getCachedActiveModules(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            if (! \Illuminate\Support\Facades\Schema::hasTable('modules')) {
                return [];
            }
            return static::pluck('is_active', 'key')->all();
        });
    }

    /**
     * Check if a module is currently active.
     */
    public static function isActive(string $key): bool
    {
        $activeModules = static::getCachedActiveModules();
        return $activeModules[$key] ?? true;
    }

    /**
     * Clear active module cache.
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
