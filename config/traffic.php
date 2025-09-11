<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Package Enable/Disable
    |--------------------------------------------------------------------------
    | Toggle Traffic Control globally. Useful if you want to turn off traffic
    | monitoring temporarily without uninstalling the package.
    */
    'enabled' => env('TRAFFIC_CONTROL_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Storage Backend
    |--------------------------------------------------------------------------
    | Supported drivers: "database", "redis", "file"
    | Choose where traffic logs and rate-limiting counters are stored.
    */
    'storage' => env('TRAFFIC_CONTROL_STORAGE', 'redis'),

    /*
    |--------------------------------------------------------------------------
    | Rate Limits
    |--------------------------------------------------------------------------
    | Define request limits per time window. You can create multiple profiles
    | (e.g., default, api) and apply them via middleware.
    */
    'rate_limits' => [
        'default' => [
            'requests' => 60,
            'per'      => 60, // seconds
        ],
        'api' => [
            'requests' => 120,
            'per'      => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IP Rules
    |--------------------------------------------------------------------------
    | Control access based on IP addresses.
    | - block_tor: If true, blocks requests from Tor exit nodes.
    | - blacklist: IPs that are always blocked.
    | - whitelist: IPs that bypass all checks.
    */
    'ip' => [
        'block_tor' => true,
        'blacklist' => [],
        'whitelist' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Bot Detection
    |--------------------------------------------------------------------------
    | Enable basic bot detection using User-Agent matching.
    */
    'bot_detection' => [
        'enabled'     => true,
        'user_agents' => ['bad-bot'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Alerts & Notifications
    |--------------------------------------------------------------------------
    | Configure real-time alerts when traffic spikes occur.
    | - slack:    Incoming webhook URL for Slack.
    | - email:    Email address to notify.
    | - threshold: Number of requests per minute that triggers alerts.
    */
    'alerts' => [
        'slack'     => env('TRAFFIC_CONTROL_SLACK_WEBHOOK'),
        'email'     => env('TRAFFIC_CONTROL_ALERT_EMAIL', 'admin@example.com'),
        'threshold' => env('TRAFFIC_CONTROL_ALERT_THRESHOLD', 1000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    | Enable or disable the built-in monitoring dashboard.
    */
    'dashboard' => [
        'enabled'    => true,
        'prefix'     => 'traffic-control',
        'middleware' => ['web'],
    ],

    /*
    |--------------------------------------------------------------------------
    | API Quota
    |--------------------------------------------------------------------------
    | Define quota limits for API usage.
    */
    'api_quota' => [
        'default' => 10000,
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    | Configure how traffic events are logged.
    | - log_blocked: Whether to log blocked requests.
    | - log_sample_rate: 1 = log all, 0.5 = log 50%, etc.
    */
    'logging' => [
        'log_blocked'     => true,
        'log_sample_rate' => 1,
    ],

];
