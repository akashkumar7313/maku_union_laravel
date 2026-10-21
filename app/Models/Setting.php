<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

#[Fillable(['key', 'value', 'group'])]
class Setting extends Model
{
    public const CACHE_KEY = 'maku.settings';

    public static function allCached(): Collection
    {
        $values = Cache::rememberForever(self::CACHE_KEY, fn () => self::query()->pluck('value', 'key')->all());

        return collect($values);
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return self::allCached()->get($key) ?? $default;
    }

    public static function put(string $key, mixed $value, string $group = 'general'): void
    {
        self::updateOrCreate(['key' => $key], ['value' => $value, 'group' => $group]);
        Cache::forget(self::CACHE_KEY);
    }

    public static function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY));
    }
}
