<?php

namespace AreiaLab\TrafficControl\Controllers;

use Illuminate\Routing\Controller;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        return view('traffic-control::settings');
    }
}
