# Laravel Traffic Control 🚦

**Package Name:** `areia-lab/laravel-traffic-control`  
**Namespace:** `AreiaLab\TrafficControl`

A full-featured **traffic control & security toolkit** for Laravel.  
It provides **rate limiting, IP black/whitelisting, bot detection, request quotas, alerts, logging, and a monitoring dashboard**.

---

## ✨ Features

- 🔒 **Advanced Rate Limiting** (per-IP, per-user, per-route, per-role, per-plan)
- 🌍 **IP Control**: Whitelist, Blacklist, TOR/VPN blocking
- 🤖 **Bot & Crawler Detection** using User-Agent patterns
- 📊 **Traffic Dashboard**: Visualize blocked/allowed requests
- 📑 **Logging** of suspicious/blocked requests with purge command
- 🔔 **Alerts** (Slack, Email) on suspicious spikes
- 🚦 **API Quotas** (daily, monthly, per-user, per-plan)
- 🧩 **Custom Rules**: Extend with your own blocking logic (GeoIP, maintenance windows, etc.)
- ⚡ **Request Queueing** (optional future feature)

---

## 📦 Installation

Require the package via Composer:

```bash
composer require areia-lab/laravel-traffic-control
```

Publish configuration and migrations:

```bash
php artisan vendor:publish --provider="AreiaLab\TrafficControl\TrafficControlServiceProvider" --tag="config"
php artisan vendor:publish --provider="AreiaLab\TrafficControl\TrafficControlServiceProvider" --tag="migrations"
php artisan migrate
```

(Optional) Publish dashboard views:

```bash
php artisan vendor:publish --provider="AreiaLab\TrafficControl\TrafficControlServiceProvider" --tag="views"
```

---

## ⚙️ Configuration

Edit `config/traffic-control.php`:

```php
return [
    'enabled' => true,
    'storage' => 'redis', // redis | cache | database

    'rate_limits' => [
        'default' => ['requests' => 60, 'per' => 60],
        'api' => ['requests' => 120, 'per' => 60],
    ],

    'ip' => [
        'blacklist' => ['123.45.67.89'],
        'whitelist' => ['10.0.0.1'],
        'block_tor' => true,
    ],

    'bot_detection' => [
        'enabled' => true,
        'user_agents' => ['curl', 'scrapy', 'bad-bot'],
    ],

    'alerts' => [
        'slack' => env('TRAFFIC_CONTROL_SLACK_WEBHOOK'),
        'email' => env('TRAFFIC_CONTROL_ALERT_EMAIL'),
        'threshold' => 1000,
    ],

    'dashboard' => [
        'enabled' => true,
        'route' => 'traffic-control.dashboard',
        'middleware' => ['web', 'auth'],
    ],

    'api_quota' => [
        'default' => 10000, // requests per month per user
    ],

    'logging' => [
        'log_blocked' => true,
        'log_sample_rate' => 1,
    ],
];
```

---

## 🚀 Usage

### 1. Apply Middleware

Apply globally in `app/Http/Kernel.php` or per-route:

```php
// Default
Route::middleware(['traffic.control'])->group(function () {
    Route::get('/api/data', [ApiController::class, 'index']);
});

// Custom limits: requests,seconds (e.g., 200 req per 60 seconds)
Route::get('/heavy', [HeavyController::class, 'index'])
    ->middleware('traffic.control:200,60');
```

---

### 2. Role or Plan-Based Limits

You can extend the keying logic in middleware or via `TrafficManager`:

```php
if ($user = $request->user()) {
    $plan = $user->plan ?? 'free';
    $limit = config("traffic-control.rate_limits.$plan", config('traffic-control.rate_limits.default'));
}
```

---

### 3. API Quotas

Track per-user quotas (monthly/daily).  
In your service/controller:

```php
use AreiaLab\TrafficControl\Facades\TrafficManager;

if (!TrafficManager::checkQuota($user->id)) {
    return response()->json(['error' => 'API quota exceeded'], 429);
}
```

---

### 4. Dashboard

Quick route to view recent blocked traffic:

```php
Route::get('/admin/traffic', function () {
    $logs = \AreiaLab\TrafficControl\Models\TrafficLog::latest()->limit(50)->get();
    return view('vendor.traffic-control.dashboard', compact('logs'));
})->middleware(['web', 'auth'])->name('traffic-control.dashboard');
```

![Dashboard Example](docs/dashboard-example.png) <!-- optional screenshot -->

---

### 5. Alerts

Set `.env`:

```
TRAFFIC_CONTROL_SLACK_WEBHOOK=https://hooks.slack.com/services/XXXX
TRAFFIC_CONTROL_ALERT_EMAIL=admin@example.com
```

When requests exceed the configured threshold, Slack or email alerts will trigger.

---

### 6. Purging Old Logs

```bash
# Purge logs older than 30 days with confirmation
php artisan traffic-control:purge

# Purge logs older than 90 days without confirmation
php artisan traffic-control:purge --days=90 --force

# Purge all logs with confirmation
php artisan traffic-control:purge --all

# Purge all logs without confirmation
php artisan traffic-control:purge --all --force

```

Deletes traffic logs older than 30 days.

---

## 🧩 Extending

### Custom Rules

Create a rule under `src/Rules/`:

```php
namespace AreiaLab\TrafficControl\Rules;

use Illuminate\Http\Request;

class GeoBlockRule
{
    public function handle(Request $request)
    {
        $country = $this->lookupCountry($request->ip());
        if (in_array($country, ['CN', 'RU'])) {
            return response('Not available in your region', 403);
        }
        return true;
    }

    protected function lookupCountry($ip)
    {
        // integrate with GeoIP here
        return 'US';
    }
}
```

Register your rule in `TrafficManager` or a custom middleware.

---

## 🛠 Roadmap

- [ ] Redis sliding window / leaky bucket limiter
- [ ] GeoIP/TOR/VPN detection integration
- [ ] Charts (Chart.js / Recharts) for dashboard
- [ ] Plan-based quota & billing hooks
- [ ] AI anomaly detection

---

## 🤝 Contributing

PRs are welcome!

- Follow **PSR-12** coding style
- Add **unit/feature tests**
- Update **README.md** when adding new features

---

## 📜 License

MIT © AreiaLab
