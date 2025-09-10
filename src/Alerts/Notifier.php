<?php

namespace AreiaLab\TrafficControl\Alerts;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class Notifier
{
    protected ?string $slackWebhook;
    protected ?string $email;
    protected int $threshold;

    public function __construct()
    {
        $config = config('traffic.alerts', []);

        $this->slackWebhook = $config['slack'] ?? null;
        $this->email = $config['email'] ?? null;
        $this->threshold = $config['threshold'] ?? 1000;
    }

    /**
     * Check threshold and send alerts
     *
     * @param int $count
     * @param string|null $message
     */
    public function notifyIfThresholdExceeded(int $count, ?string $message = null): void
    {
        if ($count < $this->threshold) {
            return; // threshold not reached
        }

        $message = $message ?? "Traffic threshold exceeded: {$count} requests (limit: {$this->threshold})";

        // Send Slack notification
        $this->sendSlackNotification($message);

        // Send Email notification
        $this->sendEmailNotification($message);
    }

    protected function sendSlackNotification(string $message): void
    {
        if (!$this->slackWebhook) {
            return;
        }

        try {
            Http::post($this->slackWebhook, [
                'text' => $message,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send Slack notification: ' . $e->getMessage());
        }
    }

    protected function sendEmailNotification(string $message): void
    {
        if (!$this->email) {
            return;
        }

        try {
            Mail::raw($message, function ($mail) {
                $mail->to($this->email)
                    ->subject('Traffic Alert Notification');
            });
        } catch (\Exception $e) {
            Log::error('Failed to send email notification: ' . $e->getMessage());
        }
    }
}
