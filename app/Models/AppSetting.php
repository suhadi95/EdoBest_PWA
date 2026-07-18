<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AppSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function getValue(string $key, ?string $default = null): ?string
    {
        $settings = static::allCached();

        return array_key_exists($key, $settings)
            ? $settings[$key]
            : $default;
    }

    public static function setValue(string $key, ?string $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        Cache::forget('app_settings');
    }

    public static function getMany(array $keys): array
    {
        $settings = static::allCached();
        $result = [];

        foreach ($keys as $key => $default) {
            if (is_int($key)) {
                $result[$default] = $settings[$default] ?? null;
            } else {
                $result[$key] = $settings[$key] ?? $default;
            }
        }

        return $result;
    }

    public static function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            static::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        Cache::forget('app_settings');
    }

    protected static function allCached(): array
    {
        return Cache::remember('app_settings', 300, function () {
            return static::query()->pluck('value', 'key')->toArray();
        });
    }
}
