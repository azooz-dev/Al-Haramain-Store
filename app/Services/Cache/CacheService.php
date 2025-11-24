<?php

namespace App\Services\Cache;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    private const DEFAULT_TTL = 3600; // 1 hour

    public function remember(string $key, callable $callback, ?int $ttl = null, array $tags = []): mixed
    {
        $cacheInstance = empty($tags) ? Cache::store() : Cache::tags($tags);
        
        return $cacheInstance->remember(
            $key, 
            $ttl ?? self::DEFAULT_TTL, 
            $callback
        );
    }

    public function forget(string $key, array $tags = []): void
    {
        if (empty($tags)) {
            Cache::forget($key);
        } else {
            Cache::tags($tags)->forget($key);
        }
    }

    public function flush(array $tags): void
    {
        Cache::tags($tags)->flush();
    }

    public function buildKey(string $prefix, ...$parts): string
    {
        return $prefix . ':' . implode(':', array_filter($parts));
    }
}


