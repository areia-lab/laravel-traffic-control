<?php
namespace AreiaLab\TrafficControl\Middleware;

use Closure;
use Illuminate\Http\Request;
use AreiaLab\TrafficControl\RateLimiter\AdvancedThrottle;
use AreiaLab\TrafficControl\Models\TrafficLog;

class TrafficControlMiddleware
{
    protected AdvancedThrottle $throttle;

    public function __construct(AdvancedThrottle $throttle)
    {
        $this->throttle = $throttle;
    }

    public function handle(Request $request, Closure $next, $limit = null)
    {
        if (!config('traffic-control.enabled')) return $next($request);

        $ip = $request->ip();
        $cfg = config('traffic-control.ip');

        if (in_array($ip, $cfg['whitelist'] ?? [])) return $next($request);
        if (in_array($ip, $cfg['blacklist'] ?? [])) {
            $this->logBlocked($request, 'blacklist');
            return $this->deny($request, 'IP blocked');
        }

        if (config('traffic-control.bot_detection.enabled')) {
            $ua = $request->userAgent() ?: '';
            foreach (config('traffic-control.bot_detection.user_agents', []) as $bad) {
                if (stripos($ua, $bad) !== false) {
                    $this->logBlocked($request, 'bot');
                    return $this->deny($request, 'Bot detected');
                }
            }
        }

        $limitConfig = $limit ? explode(',', $limit) : null;
        $requests = $limitConfig[0] ?? config('traffic-control.rate_limits.default.requests');
        $per = $limitConfig[1] ?? config('traffic-control.rate_limits.default.per');

        $key = $this->key($request);
        if (!$this->throttle->allowRequest($key, (int)$requests, (int)$per)) {
            $this->logBlocked($request, 'rate_limit');
            return $this->deny($request, 'Too Many Requests', 429, ['Retry-After' => $per]);
        }

        return $next($request);
    }

    protected function key(Request $request)
    {
        return $request->user()
            ? 'tc:user:'.$request->user()->getAuthIdentifier()
            : 'tc:ip:'.$request->ip();
    }

    protected function deny(Request $request, $message = 'Blocked', $status = 403, array $headers = [])
    {
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => $message], $status, $headers);
        }
        return response($message, $status, $headers);
    }

    protected function logBlocked(Request $request, $reason = null)
    {
        if (!class_exists(TrafficLog::class)) return;
        TrafficLog::create([
            'ip' => $request->ip(),
            'path' => $request->path(),
            'method' => $request->method(),
            'user_agent' => $request->userAgent(),
            'reason' => $reason,
        ]);
    }
}