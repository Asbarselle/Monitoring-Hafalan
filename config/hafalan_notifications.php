<?php

return [
    /**
     * Konfigurasi Email Notification
     */
    'email' => [
        'enabled' => env('MAIL_ENABLED', true),
    ],

    /**
     * Konfigurasi In-App Notification
     */
    'app_notification' => [
        'enabled' => env('APP_NOTIFICATION_ENABLED', true),
    ],

    /**
     * Template Pesan Notifikasi
     */
    'message_templates' => [
        'hafalan_addition' => [
            'email' => 'hafalan_addition_email',
            'app' => 'Hafalan baru untuk {student_name}: Surah {surat} ayat {ayat_dari}-{ayat_sampai}',
        ],
        'hafalan_statusupdate' => [
            'email' => 'hafalan_statusupdate_email',
            'app' => 'Status hafalan {student_name} diperbarui menjadi: {status}',
        ],
        'hafalan_reminder' => [
            'email' => 'hafalan_reminder_email',
            'app' => 'Pengingat hafalan untuk {student_name}',
        ]
    ],

    /**
     * Pengaturan Default Notifikasi per Parent
     */
    'defaults' => [
        'enable_email' => true,
        'enable_app_notification' => true,
        'send_on_hafalan_addition' => true,
        'send_on_statusupdate' => true,
        'send_on_reminder' => true,
    ],
];
