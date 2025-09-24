<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
        'is_public'
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public static function get($key, $default = null)
    {
        $cacheKey = "setting_{$key}";

        return Cache::remember($cacheKey, 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();

            if (!$setting) {
                return $default;
            }

            return static::castValue($setting->value, $setting->type);
        });
    }

    public static function set($key, $value, $type = 'string', $description = null, $isPublic = false)
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_array($value) || is_object($value) ? json_encode($value) : $value,
                'type' => $type,
                'description' => $description,
                'is_public' => $isPublic
            ]
        );

        Cache::forget("setting_{$key}");

        return $setting;
    }

    public static function forget($key)
    {
        $deleted = static::where('key', $key)->delete();
        Cache::forget("setting_{$key}");

        return $deleted;
    }

    public static function getPublic()
    {
        return static::where('is_public', true)->pluck('value', 'key')->map(function ($value, $key) {
            $setting = static::where('key', $key)->first();
            return static::castValue($value, $setting->type ?? 'string');
        });
    }

    protected static function castValue($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'integer':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'array':
            case 'json':
                return is_string($value) ? json_decode($value, true) : $value;
            default:
                return $value;
        }
    }

    public function getValueAttribute($value)
    {
        return static::castValue($value, $this->type);
    }
}
