<?php

namespace App\Services;

use App\Models\HafalanNotification;
use App\Models\Hafalan;
use App\Models\User;

class NotificationService
{
    /**
     * Buat dan kirim notifikasi untuk hafalan baru
     */
    public static function notifyHafalanAddition(Hafalan $hafalan)
    {
        try {
            \Log::info('🚀 Starting notification process', [
                'hafalan_id' => $hafalan->id,
                'santri_id' => $hafalan->santri_id,
                'ustadz_id' => $hafalan->ustadz_id,
            ]);

            // Dapatkan parent dari santri
            $parent = $hafalan->santri->orangTua;

            // Jika tidak ada parent, skip
            if (!$parent) {
                \Log::warning('Parent not found for santri', ['santri_id' => $hafalan->santri_id]);
                return;
            }

            \Log::info('Parent found', [
                'parent_id' => $parent->id,
                'parent_name' => $parent->name,
                'parent_email' => $parent->email,
                'notify_email' => $parent->notify_email,
                'notify_app' => $parent->notify_app,
            ]);

            // Buat array untuk loop (untuk kompatibilitas kode di bawah)
            $parents = collect([$parent]);

            foreach ($parents as $parent) {
                // Check if parent has any notifications enabled
                if (!$parent->notify_email && !$parent->notify_app) {
                    \Log::info('Parent has all notifications disabled', ['parent_id' => $parent->id]);
                    continue;
                }

                // Generate pesan terpisah untuk email dan in-app
                $emailMessage = self::createMessage('hafalan_addition', $hafalan, $parent, 'email');
                $appMessage = self::createMessage('hafalan_addition', $hafalan, $parent, 'app');

                \Log::info('Messages created', [
                    'parent_id' => $parent->id,
                    'email_message_length' => strlen($emailMessage),
                    'app_message_length' => strlen($appMessage),
                ]);

                // Kirim Email
                if ($parent->notify_email && $parent->email) {
                    \Log::info('Sending Email notification', [
                        'parent_id' => $parent->id,
                        'email' => $parent->email,
                    ]);
                    self::sendEmail($hafalan, $parent, $emailMessage, 'hafalan_addition');
                } elseif ($parent->notify_email && !$parent->email) {
                    \Log::warning('Email enabled but no email address', ['parent_id' => $parent->id]);
                }

                // Kirim App Notification
                if ($parent->notify_app) {
                    \Log::info('Sending App notification', ['parent_id' => $parent->id]);
                    self::sendAppNotification($hafalan, $parent, $appMessage, 'hafalan_addition');
                }
            }

            \Log::info('✅ Notification process completed', ['hafalan_id' => $hafalan->id]);
        } catch (\Exception $e) {
            \Log::error('❌ Error in notifyHafalanAddition', [
                'hafalan_id' => $hafalan->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Buat template pesan berdasarkan tipe notifikasi
     */
    private static function createMessage($type, Hafalan $hafalan, User $parent, string $channel = 'email')
    {
        $templates = config('hafalan_notifications.message_templates')[$type] ?? [];
        $template = $templates[$channel] ?? $templates['app'] ?? $templates['email'] ?? '';

        // Replace placeholders
        $replacements = [
            '{student_name}' => $hafalan->santri->nama,
            '{surat}' => $hafalan->surat ?? 'Juz ' . $hafalan->juz,
            '{ayat_dari}' => $hafalan->ayat_dari ?? '',
            '{ayat_sampai}' => $hafalan->ayat_sampai ?? '',
            '{status}' => $hafalan->status,
            '{nilai}' => $hafalan->nilai ?? '-',
            '{tanggal}' => optional($hafalan->tanggal_setoran)->format('d-m-Y') ?? date('d-m-Y'),
            '{catatan}' => $hafalan->catatan ?? '-',
            '{audio_link}' => $hafalan->audio_path ? route('parent.hafalan.listen', $hafalan->id) : 'Audio belum tersedia',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }

    /**
     * Buat record notifikasi Email
     */
    private static function sendEmail(Hafalan $hafalan, User $parent, $message, $type)
    {
        if (!config('hafalan_notifications.email.enabled')) {
            return;
        }

        $notification = HafalanNotification::create([
            'hafalan_id' => $hafalan->id,
            'santri_id' => $hafalan->santri_id,
            'ustadz_id' => $hafalan->ustadz_id,
            'parent_id' => $parent->id,
            'type' => $type,
            'notification_channel' => 'email',
            'message' => $message,
            'status' => 'pending',
        ]);

        // Queue job untuk mengirim email secara async
        \App\Jobs\SendEmailNotification::dispatch($notification);
    }

    /**
     * Buat record notifikasi In-App
     */
    private static function sendAppNotification(Hafalan $hafalan, User $parent, $message, $type)
    {
        if (!config('hafalan_notifications.app_notification.enabled')) {
            return;
        }

        HafalanNotification::create([
            'hafalan_id' => $hafalan->id,
            'santri_id' => $hafalan->santri_id,
            'ustadz_id' => $hafalan->ustadz_id,
            'parent_id' => $parent->id,
            'type' => $type,
            'notification_channel' => 'app_notification',
            'message' => $message,
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }
}
