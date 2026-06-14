<?php

namespace App\Mail;

use App\Models\HafalanNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HafalanNotificationEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $notification;

    /**
     * Create a new message instance.
     */
    public function __construct(HafalanNotification $notification)
    {
        $this->notification = $notification;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $hafalan = $this->notification->hafalan;
        $santri = $hafalan->santri;

        return new Envelope(
            subject: "Update Hafalan Al-Qur'an: {$santri->nama} - {$hafalan->surat}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.hafalan_notification',
            with: [
                'notification' => $this->notification,
                'hafalan' => $this->notification->hafalan,
                'santri' => $this->notification->hafalan->santri,
                'ustadz' => $this->notification->hafalan->ustadz,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
