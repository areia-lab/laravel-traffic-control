<?php

use Illuminate\Support\Facades\Route;
use AreiaLab\TrafficControl\Controllers\DashboardController;
use AreiaLab\TrafficControl\Controllers\LogsController;
use AreiaLab\TrafficControl\Controllers\SettingsController;
use AreiaLab\TrafficControl\Controllers\AlertsController;

Route::middleware(config('traffic.dashboard.middleware', ['web']))
    ->prefix(config('traffic.dashboard.prefix', 'traffic-control'))
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('traffic-control.dashboard');

        // Logs
        Route::get('/logs', [LogsController::class, 'index'])
            ->name('traffic-control.logs');

        // Settings
        Route::get('/settings', [SettingsController::class, 'index'])
            ->name('traffic-control.settings');

        // Alerts
        Route::get('/alerts', [AlertsController::class, 'index'])
            ->name('traffic-control.alerts');
    });
