<?php

namespace AreiaLab\TrafficControl\Observers;

use AreiaLab\TrafficControl\Models\TrafficLog;
use AreiaLab\TrafficControl\Alerts\Notifier;

class TrafficLogObserver
{
    /**
     * Handle the TrafficLog "created" event.
     */
    public function created(TrafficLog $log)
    {
        // Resolve Notifier from the container
        $notifier = app(Notifier::class);

        // Count recent logs in the last minute (to prevent excessive alerts)
        $timeWindow = now()->subMinute();
        $count = TrafficLog::where('created_at', '>=', $timeWindow)->count();

        // Trigger notifier if threshold exceeded
        $notifier->notifyIfThresholdExceeded(
            $count,
            "Traffic spike detected! {$count} requests in the last minute."
        );
    }

    public function updated(TrafficLog $log): void
    {
        //
    }

    public function deleted(TrafficLog $log): void
    {
        //
    }

    public function restored(TrafficLog $log): void
    {
        //
    }

    public function forceDeleted(TrafficLog $log): void
    {
        //
    }
}
