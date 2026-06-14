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
            'email' => 'Anak Anda, {student_name}, telah menambahkan hafalan baru: Surah {surat} ayat {ayat_dari}-{ayat_sampai}.',
            'app' => 'Hafalan baru untuk {student_name}: Surah {surat} ayat {ayat_dari}-{ayat_sampai}',
        ],
        'hafalan_statusupdate' => [
            'email' => 'Status hafalan {student_name} telah diperbarui menjadi: {status}.',
            'app' => 'Status hafalan {student_name} diperbarui menjadi: {status}',
        ],
        'hafalan_reminder' => [
            'email' => 'Pengingat hafalan untuk {student_name}: silakan cek perkembangan terbaru.',
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
