<?php

use Illuminate\Support\Facades\Route;
use AreiaLab\TrafficControl\Models\TrafficLog;

Route::middleware(config('traffic.dashboard.middleware', ['web']))->group(function () {
    // Dashboard
    Route::get('/traffic-control/dashboard', function () {
        $logs = TrafficLog::latest()->limit(50)->get();
        return view('traffic-control::dashboard', compact('logs'));
    })->name('traffic-control.dashboard');

    // Logs page
    Route::get('/traffic-control/logs', function () {
        return view('traffic-control::logs');
    })->name('traffic-control.logs');

    // Settings page
    Route::get('/traffic-control/settings', function () {
        return view('traffic-control::settings');
    })->name('traffic-control.settings');

    // Alerts page
    Route::get('/traffic-control/alerts', function () {
        return view('traffic-control::alerts');
    })->name('traffic-control.alerts');
});
