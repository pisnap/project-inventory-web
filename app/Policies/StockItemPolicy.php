<?php

namespace App\Policies;

use App\Models\Stock_item;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class StockItemPolicy
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
    public function view(User $user, Stock_item $stockItem): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role == 'Admin';
    }


    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Stock_item $stockItem): bool
    {
        return $user->role == 'Admin';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function deleteAny(User $user): bool
    {
        return $user->role == 'Admin';
    }

    public function delete(User $user, Stock_item $stockItem): bool
    {
        return $user->role == 'Admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Stock_item $stockItem): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Stock_item $stockItem): bool
    {
        return $user->role == 'Admin';
    }

    public function replicate(User $user): bool
    {
        return $user->role == 'Admin';
    }

    public function export(User $user): bool
    {
        // Contoh: Hanya pengguna dengan role "Admin" yang dapat eksport data
        return $user->role === 'Admin';
    }
}
