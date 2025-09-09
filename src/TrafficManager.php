<?php

namespace AreiaLab\TrafficControl;

use Illuminate\Support\Facades\Cache;
use AreiaLab\TrafficControl\Models\TrafficLog;

class TrafficManager
{
    public static function checkQuota($userId, $limit = null)
    {
        $key = 'traffic_quota_' . $userId . '_' . date('Ym');
        $count = Cache::get($key, 0);
        $limit = $limit ?: config('traffic.api_quota.default', 10000);

        if ($count >= $limit) {
            return false;
        }

        Cache::put($key, $count + 1, now()->addMonth());
        return true;
    }

    public static function blockIfIpBlacklisted($ip)
    {
        $blacklist = config('traffic.ip.blacklist', []);
        return in_array($ip, $blacklist);
    }

    public static function logRequest($ip, $path, $blocked = false)
    {
        if (!config('traffic.logging.log_blocked') && $blocked) {
            return;
        }

        TrafficLog::create([
            'ip' => $ip,
            'path' => $path,
            'blocked' => $blocked,
        ]);
    }
}
