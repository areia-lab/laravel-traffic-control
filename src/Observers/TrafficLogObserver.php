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

        // Count all logs (or adjust to a time window if needed)
        $count = TrafficLog::count();

        // Trigger notifier if threshold exceeded
        $notifier->notifyIfThresholdExceeded(
            $count,
            "Traffic spike detected! Total logs: {$count}"
        );
    }

    /**
     * Handle the TrafficLog "updated" event.
     */
    public function updated(TrafficLog $log): void
    {
        //
    }

    /**
     * Handle the TrafficLog "deleted" event.
     */
    public function deleted(TrafficLog $log): void
    {
        //
    }

    /**
     * Handle the TrafficLog "restored" event.
     */
    public function restored(TrafficLog $log): void
    {
        //
    }

    /**
     * Handle the TrafficLog "force deleted" event.
     */
    public function forceDeleted(TrafficLog $log): void
    {
        //
    }
}
