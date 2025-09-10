<?php

namespace AreiaLab\TrafficControl\Console;

use Illuminate\Console\Command;
use AreiaLab\TrafficControl\Models\TrafficLog;
use Illuminate\Support\Facades\DB;

class PurgeTrafficLogs extends Command
{
    protected $signature = 'traffic-control:purge 
                            {--days=30 : Purge logs older than N days} 
                            {--all : Purge all logs} 
                            {--force : Force purge without confirmation}';

    protected $description = 'Purge old traffic logs older than specified number of days or all logs';

    public function handle()
    {
        $force = $this->option('force');
        $purgeAll = $this->option('all');
        $days = (int)$this->option('days');

        // Determine cutoff
        if ($purgeAll) {
            if (!$force && !$this->confirm("Are you sure you want to purge **all traffic logs**?")) {
                $this->info('Operation cancelled.');
                return 0;
            }
            $this->info("Purging all traffic logs...");
            $query = TrafficLog::query();
        } else {
            if ($days <= 0) {
                $this->error('Invalid number of days. Must be greater than 0.');
                return 1;
            }
            $cutoff = now()->subDays($days);
            if (!$force && !$this->confirm("Are you sure you want to purge traffic logs older than {$days} days?")) {
                $this->info('Operation cancelled.');
                return 0;
            }
            $this->info("Purging traffic logs older than {$days} days...");
            $query = TrafficLog::where('created_at', '<', $cutoff);
        }

        $totalDeleted = 0;

        // Use chunking for large datasets
        $query->orderBy('id')->chunkById(1000, function ($logs) use (&$totalDeleted) {
            $count = $logs->count();
            $ids = $logs->pluck('id')->toArray();

            DB::transaction(function () use ($ids) {
                TrafficLog::whereIn('id', $ids)->delete();
            });

            $totalDeleted += $count;
            $this->info("Deleted {$count} logs...");
        });

        $this->info("✅ Purge complete. Total logs deleted: {$totalDeleted}");
        return 0;
    }
}
