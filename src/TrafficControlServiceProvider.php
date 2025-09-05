<?php

namespace AreiaLab\TrafficControl;

use Illuminate\Support\ServiceProvider;

class TrafficControlServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/traffic-control.php', 'traffic-control');

        $this->app->singleton('traffic.control', function ($app) {
            return new TrafficManager($app);
        });

        $this->app->bind(\AreiaLab\TrafficControl\Alerts\Notifier::class, function ($app) {
            return new Alerts\Notifier(config('traffic-control.alerts'));
        });
    }

    public function boot()
    {
        $this->publishes([__DIR__ . '/../config/traffic-control.php' => config_path('traffic-control.php')], 'config');

        if (!class_exists('CreateTrafficLogsTable')) {
            $this->publishes([
                __DIR__ . '/../database/migrations/2025_01_01_000000_create_traffic_logs_table.php' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_traffic_logs_table.php')
            ], 'migrations');
        }

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'traffic-control');

        if ($this->app->runningInConsole()) {
            $this->commands([Console\PurgeTrafficLogs::class]);
            $this->publishes([
                __DIR__ . '/../resources/views/dashboard.blade.php' => resource_path('views/vendor/traffic-control/dashboard.blade.php')
            ], 'views');
        }

        $router = $this->app['router'];
        $router->aliasMiddleware('traffic.control', Middleware\TrafficControlMiddleware::class);
    }
}
