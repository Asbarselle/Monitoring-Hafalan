<?php

namespace App\Jobs;

use App\Models\HafalanNotification;
use App\Mail\HafalanNotificationEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $notification;
    protected $maxAttempts = 3;
    protected $backoff = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(HafalanNotification $notification)
    {
        $this->notification = $notification;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $parent = $this->notification->parent;
            
            if (!$parent || !$parent->email) {
                $this->notification->update([
                    'status' => 'failed',
                    'error_message' => 'Parent email not found',
                ]);
                return;
            }

            Mail::to($parent->email)->send(
                new HafalanNotificationEmail($this->notification)
            );

            $this->notification->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

        } catch (\Exception $e) {
            \Log::error('SendEmailNotification job failed', [
                'notification_id' => $this->notification->id,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
            ]);

            if ($this->attempts() < $this->maxAttempts) {
                $this->release($this->backoff);
            } else {
                $this->notification->update([
                    'status' => 'failed',
                    'error_message' => 'Max retry attempts reached: ' . $e->getMessage(),
                ]);
            }
        }
    }
}
