<?php

namespace App\Listeners;

use App\Events\HafalanCreated;
use App\Services\NotificationService;

class SendHafalanNotification
{
    /**
     * Handle the event.
     */
    public function handle(HafalanCreated $event): void
    {
        try {
            // Kirim notifikasi ke parents
            NotificationService::notifyHafalanAddition($event->hafalan);
        } catch (\Exception $e) {
            \Log::error('Error in SendHafalanNotification listener', [
                'event' => get_class($event),
                'hafalan_id' => $event->hafalan->id ?? null,
                'error' => $e->getMessage(),
            ]);
            // Don't throw - we want to continue even if notifications fail
        }
    }
}
