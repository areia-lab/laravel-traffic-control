<?php
namespace AreiaLab\TrafficControl\Console;

use Illuminate\Console\Command;
use AreiaLab\TrafficControl\Models\TrafficLog;

class PurgeTrafficLogs extends Command
{
    protected $signature = 'traffic-control:purge {--days=30}';
    protected $description = 'Purge old traffic logs older than N days';

    public function handle()
    {
        $days = (int)$this->option('days');
        $cutoff = now()->subDays($days);
        $count = TrafficLog::where('created_at', '<', $cutoff)->delete();
        $this->info("Purged {$count} logs older than {$days} days");
    }
}