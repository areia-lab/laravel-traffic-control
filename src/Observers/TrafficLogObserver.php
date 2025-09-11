<?php

namespace AreiaLab\TrafficControl\Observers;

use AreiaLab\TrafficControl\Models\TrafficLog;
use AreiaLab\TrafficControl\Alerts\Notifier;

class TrafficLogObserver
{
    public function __construct(private ?Notifier $notifier = null)
    {
        $this->notifier ??= app(Notifier::class);
    }

    /**
     * Handle the TrafficLog "created" event.
     */
    public function created(TrafficLog $log)
    {
        // Count recent logs in the last minute
        $timeWindow = now()->subMinute();
        $count = TrafficLog::where('created_at', '>=', $timeWindow)->count();

        // Trigger notifier if threshold exceeded
        $this->notifier->notifyIfThresholdExceeded(
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
