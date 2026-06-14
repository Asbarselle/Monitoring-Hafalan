<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Santri extends Model
{
    use HasFactory;

    protected $table = 'santri';

    protected $fillable = [
        'nama',
        'nis',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'no_hp',
        'foto',
        'orang_tua_id',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    /**
     * Get foto URL
     */
    public function getFotoUrlAttribute()
    {
        if (! $this->foto) {
            return null;
        }

        $foto = $this->foto;

        // If it's already a full URL, return as-is
        if (Str::startsWith($foto, ['http://', 'https://'])) {
            return $foto;
        }

        // If foto already contains the storage prefix, return an asset URL
        if (Str::startsWith($foto, ['storage/', '/storage/'])) {
            return asset(ltrim($foto, '/'));
        }

        // Default: treat as a path on the public disk
        return Storage::disk('public')->url($foto);
    }

    /**
     * Relasi dengan User (Orang Tua)
     */
    public function orangTua()
    {
        return $this->belongsTo(User::class, 'orang_tua_id');
    }

    /**
     * Relasi dengan Hafalan
     */
    public function hafalan()
    {
        return $this->hasMany(Hafalan::class, 'santri_id');
    }

    /**
     * Hitung total juz yang sudah dihafal
     */
    public function getTotalJuzAttribute()
    {
        return $this->hafalan()
            ->where('status', 'selesai')
            ->distinct('juz')
            ->count('juz');
    }

    /**
     * Hitung progres hafalan dalam persen
     */
    public function getProgresHafalanAttribute()
    {
        $totalJuz = 30;
        $juzSelesai = $this->total_juz;
        return $totalJuz > 0 ? round(($juzSelesai / $totalJuz) * 100, 2) : 0;
    }
    
}
