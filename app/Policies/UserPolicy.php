<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('users.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return $user->hasPermissionTo('users.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('users.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Prevent editing Super Admin unless you are one
        if ($model->hasRole('super-admin') && !$user->hasRole('super-admin')) {
            return false;
        }
        
        return $user->hasPermissionTo('users.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Prevent deleting yourself
        if ($user->id === $model->id) {
            return false;
        }

        // Prevent deleting Super Admin
        if ($model->hasRole('super-admin')) {
            return false;
        }

        return $user->hasPermissionTo('users.delete');
    }
}
