<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HafalanNotification extends Model
{
    use HasFactory;

    protected $table = 'hafalan_notifications';

    protected $fillable = [
        'hafalan_id',
        'santri_id',
        'ustadz_id',
        'parent_id',
        'type',
        'notification_channel',
        'status',
        'message',
        'phone_number',
        'external_id',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    /**
     * Relasi dengan Hafalan
     */
    public function hafalan()
    {
        return $this->belongsTo(Hafalan::class, 'hafalan_id');
    }

    /**
     * Relasi dengan Santri
     */
    public function santri()
    {
        return $this->belongsTo(Santri::class, 'santri_id');
    }

    /**
     * Relasi dengan User (Ustadz)
     */
    public function ustadz()
    {
        return $this->belongsTo(User::class, 'ustadz_id');
    }

    /**
     * Relasi dengan User (Parent)
     */
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    /**
     * Scope untuk mendapatkan notifikasi yang belum dikirim
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope untuk mendapatkan notifikasi berdasarkan channel
     */
    public function scopeByChannel($query, $channel)
    {
        return $query->where('notification_channel', $channel);
    }
}
