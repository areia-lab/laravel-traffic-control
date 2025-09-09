<?php

namespace AreiaLab\TrafficControl\Controllers;

use Illuminate\Routing\Controller;

class AlertsController extends Controller
{
    /**
     * Display the alerts page.
     */
    public function index()
    {
        return view('traffic-control::alerts');
    }
}
