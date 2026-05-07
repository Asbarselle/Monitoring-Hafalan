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
        'audio_path',
        'audio_filename',
        'audio_duration',
        // Cloud Storage Fields
        's3_audio_path',
        's3_audio_url',
        'uploaded_to_cloud_at',

        // Audio Analysis Fields
        'audio_quality_score',
        'audio_bitrate',
        'audio_codec',
        'is_audio_analyzed',
        // Analytics Fields
        'progress_percentage',
        'days_to_completion',
        'completion_status',
        'analytics_insights',
        'last_analyzed_at',
    ];

    protected $casts = [
        'tanggal_setoran' => 'date',
        'uploaded_to_cloud_at' => 'datetime',
        'last_analyzed_at' => 'datetime',
        'analytics_insights' => 'json',
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
     * Relasi dengan Notifications
     */
    public function notifications()
    {
        return $this->hasMany(HafalanNotification::class, 'hafalan_id');
    }

    /**
     * Get total verses dalam hafalan ini
     */
    public function getTotalVersesAttribute()
    {
        return ($this->ayat_sampai - $this->ayat_dari) + 1;
    }

    /**
     * Get audio URL (dari Firebase, S3, atau local - prioritas urutan)
     */
    public function getAudioUrlAttribute()
    {
        // Priority 1: Firebase URL
        if ($this->firebase_audio_url && $this->firebase_sync_status) {
            return $this->firebase_audio_url;
        }
        
        // Priority 2: S3 URL
        if ($this->s3_audio_url) {
            return $this->s3_audio_url;
        }
        
        // Priority 3: Local storage
        if ($this->audio_path) {
            return asset('storage/' . $this->audio_path);
        }
        
        return null;
    }

    /**
     * Check apakah audio sudah di-cloud (S3 atau Firebase)
     */
    public function isCloudStorage(): bool
    {
        return !is_null($this->s3_audio_path) || ($this->firebase_sync_status && !is_null($this->firebase_audio_path));
    }

    /**
     * Check apakah audio sudah di-firebase
     */
    public function isFirebaseStored(): bool
    {
        return $this->firebase_sync_status && !is_null($this->firebase_audio_path) && !is_null($this->firebase_audio_url);
    }

    /**
     * Get Firebase URL langsung
     */
    public function getFirebaseAudioUrl(): ?string
    {
        return $this->firebase_sync_status ? $this->firebase_audio_url : null;
    }

    /**
     * Get quality level dari score
     */
    public function getAudioQualityLevelAttribute()
    {
        if ($this->audio_quality_score === null) {
            return 'Not Analyzed';
        }
        
        return match (true) {
            $this->audio_quality_score >= 80 => 'Excellent',
            $this->audio_quality_score >= 60 => 'Good',
            $this->audio_quality_score >= 40 => 'Fair',
            default => 'Poor',
        };
    }

    /**
     * Get status badge color
     */
    public function getCompletionStatusColorAttribute()
    {
        return match ($this->completion_status) {
            'in_progress' => 'info',
            'on_track' => 'success',
            'at_risk' => 'warning',
            'completed' => 'success',
            default => 'secondary',
        };
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
