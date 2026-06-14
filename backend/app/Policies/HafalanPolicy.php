<?php

namespace App\Policies;

use App\Models\Hafalan;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class HafalanPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any hafalan (list).
     */
    public function viewAny(User $user)
    {
        // admin can view all, ustadz can view his own, orang_tua should not list
        return $user->isAdmin() || $user->isUstadz();
    }

    /**
     * Determine whether the user can view the hafalan.
     */
    public function view(User $user, Hafalan $hafalan)
    {
        if ($user->isAdmin()) {
            return true;
        }
        if ($user->isUstadz()) {
            return $hafalan->ustadz_id === $user->id;
        }
        if ($user->isOrangTua()) {
            return $hafalan->santri && $hafalan->santri->orang_tua_id === $user->id;
        }
        return false;
    }

    /**
     * Determine whether the user can create hafalan.
     */
    public function create(User $user)
    {
        return $user->isUstadz() || $user->isAdmin();
    }

    /**
     * Determine whether the user can update the hafalan.
     */
    public function update(User $user, Hafalan $hafalan)
    {
        return $user->isAdmin() || ($user->isUstadz() && $hafalan->ustadz_id === $user->id);
    }

    /**
     * Determine whether the user can delete the hafalan.
     */
    public function delete(User $user, Hafalan $hafalan)
    {
        return $user->isAdmin() || ($user->isUstadz() && $hafalan->ustadz_id === $user->id);
    }
}
