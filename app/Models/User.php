<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /**
     * role definitions used throughout the app (avoids magic strings)
     */
    public const ROLE_ADMIN    = 'admin';
    public const ROLE_USTADZ   = 'ustadz';
    public const ROLE_ORANG_TUA = 'orang_tua';

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone_number',
        'notify_email',
        'notify_app',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'notify_email' => 'boolean',
            'notify_app' => 'boolean',
        ];
    }

    /**
     * Get notification preferences with defaults
     */
    public function getNotificationPreferences()
    {
        return [
            'notify_email' => (bool)($this->notify_email ?? true),
            'notify_app' => (bool)($this->notify_app ?? true),
        ];
    }

    /**
     * Relasi dengan Santri (jika role adalah orang_tua)
     */
    public function santri()
    {
        return $this->hasMany(Santri::class, 'orang_tua_id');
    }

    /**
     * Relasi dengan Hafalan (jika role adalah ustadz)
     */
    public function hafalan()
    {
        return $this->hasMany(Hafalan::class, 'ustadz_id');
    }

    /**
     * Cek apakah user adalah admin
     */
    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Cek apakah user adalah ustadz
     */
    public function isUstadz()
    {
        return $this->role === self::ROLE_USTADZ;
    }

    /**
     * Cek apakah user adalah orang tua
     */
    public function isOrangTua()
    {
        return $this->role === self::ROLE_ORANG_TUA;
    }

    /**
     * Scope a query to only users with given role(s).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|array $roles
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRole($query, $roles)
    {
        if (is_array($roles)) {
            return $query->whereIn('role', $roles);
        }

        return $query->where('role', $roles);
    }
}
