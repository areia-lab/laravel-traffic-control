<?php

namespace AreiaLab\TrafficControl\Models;

use Illuminate\Database\Eloquent\Model;

class TrafficLog extends Model
{
    protected $table = 'traffic_logs';

    protected $fillable = [
        'ip',
        'path',
        'method',
        'user_agent',
        'reason',
        'meta'
    ];

    protected $casts = [
        'meta' => 'array'
    ];
}
