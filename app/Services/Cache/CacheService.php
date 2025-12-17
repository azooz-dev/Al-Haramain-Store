<?php

namespace App\Services\Cache;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    private const DEFAULT_TTL = 3600; // 1 hour

    /**
     * Check if the current cache store supports tagging
     */
    private function supportsTags(): bool
    {
        $store = Cache::getStore();
        return method_exists($store, 'tags');
    }

    public function remember(string $key, callable $callback, ?int $ttl = null, array $tags = []): mixed
    {
        if (!empty($tags) && $this->supportsTags()) {
            $cacheInstance = Cache::tags($tags);
        } else {
            $cacheInstance = Cache::store();
        }

        return $cacheInstance->remember(
            $key,
            $ttl ?? self::DEFAULT_TTL,
            $callback
        );
    }

    public function forget(string $key, array $tags = []): void
    {
        if (!empty($tags) && $this->supportsTags()) {
            Cache::tags($tags)->forget($key);
        } else {
            Cache::forget($key);
        }
    }

    public function flush(array $tags): void
    {
        if ($this->supportsTags()) {
            Cache::tags($tags)->flush();
        } else {
            // Fallback: Use DashboardCacheHelper for dashboard-related tags
            if (in_array('dashboard', $tags)) {
                \Modules\Analytics\Services\DashboardCacheHelper::flushAll();
            } else {
                // For non-dashboard tags, clear all cache as fallback
                Cache::flush();
            }
        }
    }

    public function buildKey(string $prefix, ...$parts): string
    {
        return $prefix . ':' . implode(':', array_filter($parts));
    }
}
