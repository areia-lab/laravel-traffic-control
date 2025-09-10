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

        // Block IP
        Route::post('/block-ip', [ManageIpController::class, 'storeBlockIP'])
            ->name('traffic-control.block-ip');

        // Remove Blocked IP
        Route::delete('/block-ip/{ip}', [ManageIpController::class, 'removeBlockIP'])
            ->name('traffic-control.remove-block-ip');

        // Allow IP
        Route::post('/allow-ip', [ManageIpController::class, 'storeAllowIP'])
            ->name('traffic-control.allow-ip');

        // Remove Allowed IP
        Route::delete('/allow-ip/{ip}', [ManageIpController::class, 'removeAllowIP'])
            ->name('traffic-control.remove-allow-ip');

        // Settings
        Route::get('/settings', [SettingsController::class, 'index'])
            ->name('traffic.control.settings');
        Route::put('/settings/update', [SettingsController::class, 'update'])
            ->name('traffic.settings.update');

        // Alerts
        Route::get('/alerts', [AlertsController::class, 'index'])
            ->name('traffic.control.alerts');
    });
