<?php

namespace App\Policies;

use App\Models\Returning;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReturnPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Returning $returning): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Returning $returning): bool
    {
        return $user->role == 'Admin';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Returning $returning): bool
    {
        return $user->role == 'Admin';
    }

    public function deleteAny(User $user): bool
    {
        return $user->role == 'Admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Returning $returning): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Returning $returning): bool
    {
        return true;
    }
}
