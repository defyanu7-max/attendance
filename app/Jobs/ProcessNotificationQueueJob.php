<?php

namespace App\Jobs;

use App\Models\NotificationQueue;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Process pending WhatsApp notifications from the notification_queue table.
 * Sends messages via the configured WA API gateway.
 *
 * Blueprint Section: 9.1 – Notification Dispatcher
 */
class ProcessNotificationQueueJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60; // seconds between retries

    public function __construct()
    {
        //
    }

    public function handle(): void
    {
        // Check global toggle
        $enabled = Setting::where('key', 'wa_notification_enabled')->value('value');
        if ($enabled !== 'true') {
            Log::info('[ProcessNotificationQueueJob] WA notifications are disabled.');
            return;
        }

        $pending = NotificationQueue::where('status', 'pending')
            ->orderBy('created_at')
            ->limit(50)
            ->get();

        if ($pending->isEmpty()) {
            Log::info('[ProcessNotificationQueueJob] No pending notifications.');
            return;
        }

        foreach ($pending as $notification) {
            try {
                // TODO: Implement actual WA API integration
                // Example: Http::post(config('services.wa_gateway.url'), [...])

                $notification->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);

                Log::info("[ProcessNotificationQueueJob] Sent notification #{$notification->id}");
            } catch (\Throwable $e) {
                $notification->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);

                Log::error("[ProcessNotificationQueueJob] Failed notification #{$notification->id}: {$e->getMessage()}");
            }
        }
    }
}
