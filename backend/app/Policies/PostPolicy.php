<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{

    public function before(User $user, string $ability): bool|null
    {
        \Log::info('PostPolicy before');
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
        return $user->hasPermissionTo('view-posts');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, $post): bool
    {
        // If $post is a class name, we're checking general permission
        if (is_string($post)) {
            return $user->hasPermissionTo('view-own-posts');
        }

        return $user->hasPermissionTo('view-posts') ||
            ($user->hasPermissionTo('view-own-posts') && $user->id === $post->user_id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create-posts');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Post $post): bool
    {
        return $user->hasPermissionTo('edit-posts') ||
            ($user->hasPermissionTo('edit-own-posts') && $user->id === $post->user_id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Post $post): bool
    {
        return $user->hasPermissionTo('delete-posts') ||
            ($user->hasPermissionTo('delete-own-posts') && $user->id === $post->user_id);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Post $post): bool
    {
        return $user->hasPermissionTo('delete-posts');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Post $post): bool
    {
        return $user->hasPermissionTo('delete-posts');
    }
}
