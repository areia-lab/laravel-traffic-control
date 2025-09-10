<?php

namespace AreiaLab\TrafficControl\Controllers;

use Illuminate\Routing\Controller;

class ManageIpController extends Controller
{
    /**
     * Display the IPs.
     */
    public function index()
    {
        $blockedIps = config('traffic.ip.blacklist');
        $allowedIps = config('traffic.ip.whitelist');

        return view('traffic-control::manage-ip', compact(['blockedIps', 'allowedIps']));
    }
}
