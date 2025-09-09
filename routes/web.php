<?php

use Illuminate\Support\Facades\Route;
use AreiaLab\TrafficControl\Models\TrafficLog;

Route::middleware(config('traffic.dashboard.middleware'))->group(function () {
    Route::get('/traffic-control/dashboard', function () {
        $logs = TrafficLog::latest()->limit(50)->get();
        return view('traffic-control::dashboard', compact('logs'));
    })->name('traffic-control.dashboard');
});
