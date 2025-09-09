<?php

namespace AreiaLab\TrafficControl\Facades;

use Illuminate\Support\Facades\Facade;

class TrafficManager extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'traffic-manager';
    }
}
