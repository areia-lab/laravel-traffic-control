<?php

namespace AreiaLab\TrafficControl;

use AreiaLab\TrafficControl\Alerts\Notifier;
use AreiaLab\TrafficControl\Console\PurgeTrafficLogs;
use AreiaLab\TrafficControl\Http\Middleware\TrafficControlMiddleware;
use AreiaLab\TrafficControl\Models\TrafficLog;
use AreiaLab\TrafficControl\Observers\TrafficLogObserver;
use Illuminate\Support\ServiceProvider;

class TrafficControlServiceProvider extends ServiceProvider
{
    /**
     * Register package services.
     */
    public function register(): void
    {
        $this->mergeConfig();

        $this->app->singleton('traffic.control', fn(): TrafficManager => new TrafficManager());

        $this->app->bind(Notifier::class, fn(): Notifier => new Notifier());
    }

    /**
     * Bootstrap package services.
     */
    public function boot(): void
    {
        $this->registerObservers();

        $this->registerRoutes();
        $this->registerMigrations();
        $this->registerViews();
        $this->registerPublishing();
        $this->registerCommands();
        $this->registerMiddleware();
    }

    /**
     * Merge the package configuration.
     */
    protected function mergeConfig(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/traffic.php',
            'traffic'
        );
    }

    /**
     * Register package routes.
     */
    protected function registerRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
    }

    /**
     * Register package migrations.
     */
    protected function registerMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    /**
     * Register package views.
     */
    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'traffic-control');
    }

    /**
     * Register publishable resources.
     */
    protected function registerPublishing(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        // Publish config
        $this->publishes([
            __DIR__ . '/../config/traffic.php' => config_path('traffic.php'),
        ], 'traffic-config');

        // Publish migration (only if not already published)
        if (! class_exists('CreateTrafficLogsTable')) {
            $this->publishes([
                __DIR__ . '/../database/migrations/2025_01_01_000000_create_traffic_logs_table.php' =>
                database_path('migrations/' . now()->format('Y_m_d_His') . '_create_traffic_logs_table.php'),
            ], 'traffic-migrations');
        }

        // Publish views to vendor namespace
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/traffic-control'),
        ], 'traffic-views');
    }

    /**
     * Register Artisan commands.
     */
    protected function registerCommands(): void
    {
        $this->commands([
            PurgeTrafficLogs::class,
        ]);
    }

    /**
     * Register middleware alias.
     */
    protected function registerMiddleware(): void
    {
        $router = $this->app['router'];
        $router->aliasMiddleware('traffic.control', TrafficControlMiddleware::class);
    }

    /**
     * Register package observers from the model.
     */
    protected function registerObservers(): void
    {
        TrafficLog::observe(TrafficLogObserver::class);
    }
}
