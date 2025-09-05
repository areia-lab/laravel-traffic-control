<?php

return [
    'enabled' => env('TRAFFIC_CONTROL_ENABLED', true),
    'storage' => env('TRAFFIC_CONTROL_STORAGE', 'redis'),
    'rate_limits' => [
        'default' => ['requests' => 60, 'per' => 60],
        'api' => ['requests' => 120, 'per' => 60],
    ],
    'ip' => ['blacklist' => [], 'whitelist' => [], 'block_tor' => true],
    'bot_detection' => ['enabled' => true, 'user_agents' => ['bad-bot']],
    'alerts' => [
        'slack' => env('TRAFFIC_CONTROL_SLACK_WEBHOOK'),
        'email' => env('TRAFFIC_CONTROL_ALERT_EMAIL'),
        'threshold' => 1000,
    ],
    'dashboard' => ['enabled' => true, 'route' => 'traffic-control.dashboard', 'middleware' => ['web']],
    'api_quota' => ['default' => 10000],
    'logging' => ['log_blocked' => true, 'log_sample_rate' => 1],
];
