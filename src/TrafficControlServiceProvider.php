<?php

namespace AreiaLab\TrafficControl;

use Illuminate\Support\ServiceProvider;

class TrafficControlServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/traffic.php', 'traffic');

        $this->app->singleton('traffic.control', function () {
            return new TrafficManager();
        });

        // $this->app->bind(\AreiaLab\TrafficControl\Alerts\Notifier::class, function ($app) {
        //     return new Alerts\Notifier(config('traffic.alerts'));
        // });
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadMigrationsFrom(__DIR__ . '/Database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'traffic-control');

        $this->publishes([__DIR__ . '/../config/traffic.php' => config_path('traffic.php')], 'traffic-config');

        if (!class_exists('CreateTrafficLogsTable')) {
            $this->publishes([
                __DIR__ . '/../database/migrations/2025_01_01_000000_create_traffic_logs_table.php' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_traffic_logs_table.php')
            ], 'traffic-migrations');
        }

        if ($this->app->runningInConsole()) {
            $this->commands([Console\PurgeTrafficLogs::class]);

            $this->publishes([
                __DIR__ . '/../resources/views/dashboard.blade.php' => resource_path('views/vendor/traffic-control/dashboard.blade.php')
            ], 'traffic-views');
        }

        $router = $this->app['router'];
        $router->aliasMiddleware('traffic.control', Middleware\TrafficControlMiddleware::class);
    }
}
