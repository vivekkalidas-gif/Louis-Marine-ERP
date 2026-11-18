<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group', 'type'];

    // Clear cache on save/update/delete
    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('app_settings');
        });
        static::deleted(function () {
            Cache::forget('app_settings');
        });
    }

    /**
     * Get all settings cached.
     */
    public static function getAllCached()
    {
        return Cache::rememberForever('app_settings', function () {
            return self::all()->pluck('value', 'key');
        });
    }
}
