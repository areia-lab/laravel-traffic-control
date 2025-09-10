<?php

use Illuminate\Support\Facades\Route;
use AreiaLab\TrafficControl\Controllers\DashboardController;
use AreiaLab\TrafficControl\Controllers\LogsController;
use AreiaLab\TrafficControl\Controllers\SettingsController;
use AreiaLab\TrafficControl\Controllers\AlertsController;
use AreiaLab\TrafficControl\Controllers\ManageIpController;

Route::middleware(config('traffic.dashboard.middleware', ['web']))
    ->prefix(config('traffic.dashboard.prefix', 'traffic-control'))
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('traffic.control.dashboard');

        // Logs
        Route::get('/logs', [LogsController::class, 'index'])
            ->name('traffic.control.logs');

        // Manage Ips
        Route::get('/manage-ips', [ManageIpController::class, 'index'])
            ->name('traffic.control.manageIp');

        // Settings
        Route::get('/settings', [SettingsController::class, 'index'])
            ->name('traffic.control.settings');
        Route::put('/settings/update', [SettingsController::class, 'update'])
            ->name('traffic.settings.update');

        // Alerts
        Route::get('/alerts', [AlertsController::class, 'index'])
            ->name('traffic.control.alerts');
    });
