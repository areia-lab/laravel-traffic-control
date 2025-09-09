<?php

namespace AreiaLab\TrafficControl\Controllers;

use Illuminate\Routing\Controller;
use AreiaLab\TrafficControl\Models\TrafficLog;

class DashboardController extends Controller
{
    /**
     * Display the main dashboard with recent traffic logs.
     */
    public function index()
    {
        $logs = TrafficLog::latest()->limit(50)->get();
        return view('traffic-control::dashboard', compact('logs'));
    }
}
