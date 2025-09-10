<?php

namespace AreiaLab\TrafficControl\Http\Controllers;

use AreiaLab\TrafficControl\Models\TrafficLog;
use Illuminate\Routing\Controller;

class LogsController extends Controller
{
    /**
     * Display the logs page.
     */
    public function index()
    {
        $logs = TrafficLog::latest()->limit(50)->get();
        return view('traffic-control::logs', compact('logs'));
    }
}
