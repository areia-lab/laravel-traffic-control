<?php

use Illuminate\Support\Facades\Route;
use AreiaLab\TrafficControl\Models\TrafficLog;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/traffic-control/dashboard', function () {
        $logs = TrafficLog::latest()->limit(50)->get();
        return view('dashboard', compact('logs'));
    })->name('traffic-control.dashboard');
});
