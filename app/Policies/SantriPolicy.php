<?php

namespace App\Policies;

use App\Models\Santri;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SantriPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any santri.
     */
    public function viewAny(User $user)
    {
        // admins and ustadz can list
        return $user->isAdmin() || $user->isUstadz();
    }

    /**
     * Determine whether the user can view the santri.
     */
    public function view(User $user, Santri $santri)
    {
        if ($user->isAdmin()) {
            return true;
        }
        if ($user->isUstadz()) {
            return true; // ustadz may view all santri
        }
        if ($user->isOrangTua()) {
            return $santri->orang_tua_id === $user->id;
        }
        return false;
    }

    /**
     * Determine whether the user can create santri.
     */
    public function create(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the santri.
     */
    public function update(User $user, Santri $santri)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the santri.
     */
    public function delete(User $user, Santri $santri)
    {
        return $user->isAdmin();
    }
}
