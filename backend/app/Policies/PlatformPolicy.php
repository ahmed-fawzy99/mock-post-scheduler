<?php

namespace App\Policies;

use App\Models\Platform;
use App\Models\User;

class PlatformPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('admin')) {
            return true;  // Automatically allow admins to do everything
        }
        return null;  // Continue with normal policy checks
    }

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
    public function view(User $user, Platform $platform): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create-platforms');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Platform $platform): bool
    {
        return $user->hasPermissionTo('edit-platforms');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Platform $platform): bool
    {
        return $user->hasPermissionTo('delete-platforms');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Platform $platform): bool
    {
        return $user->hasPermissionTo('delete-platforms');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Platform $platform): bool
    {
        return $user->hasPermissionTo('delete-platforms');
    }
}
