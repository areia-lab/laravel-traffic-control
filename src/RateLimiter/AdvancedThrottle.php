<?php

namespace AreiaLab\TrafficControl\RateLimiter;

use Illuminate\Contracts\Cache\Repository as CacheRepository;

class AdvancedThrottle
{
    protected CacheRepository $cache;

    public function __construct(CacheRepository $cache)
    {
        $this->cache = $cache;
    }

    public function allowRequest(string $key, int $maxRequests, int $perSeconds): bool
    {
        $cacheKey = "traffic:limit:" . $key;
        $now = time();
        $data = $this->cache->get($cacheKey, ['count' => 0, 'start' => $now]);

        if ($now - $data['start'] >= $perSeconds) {
            $data = ['count' => 0, 'start' => $now];
        }

        if ($data['count'] >= $maxRequests) {
            $this->cache->put($cacheKey, $data, $perSeconds);
            return false;
        }

        $data['count']++;
        $this->cache->put($cacheKey, $data, $perSeconds);
        return true;
    }
}
