<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hafalan extends Model
{
    use HasFactory;

    protected $table = 'hafalan';

    protected $fillable = [
        'santri_id',
        'ustadz_id',
        'juz',
        'surat',
        'ayat_dari',
        'ayat_sampai',
        'status',
        'catatan',
        'tanggal_setoran',
        'nilai',
    ];

    protected $casts = [
        'tanggal_setoran' => 'date',
    ];

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
     * Scope untuk filter berdasarkan status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk filter berdasarkan juz
     */
    public function scopeJuz($query, $juz)
    {
        return $query->where('juz', $juz);
    }
}
