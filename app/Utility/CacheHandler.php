<?php

namespace App\Utility;

use Illuminate\Support\Facades\Cache;

class CacheHandler
{
    public static function rememberData($cacheKey, $minutes, $callback)
    {
        return Cache::remember($cacheKey, $minutes, function () use ($callback) {
            return $callback();
        });
    }
}
