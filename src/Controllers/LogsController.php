<?php

namespace AreiaLab\TrafficControl\Controllers;

use Illuminate\Routing\Controller;

class LogsController extends Controller
{
    /**
     * Display the logs page.
     */
    public function index()
    {
        return view('traffic-control::logs');
    }
}
