<?php

namespace App\Policies;

use App\Models\ServiceJob;
use App\Models\User;

class ServiceJobPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('jobs.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ServiceJob $serviceJob): bool
    {
        return $user->hasPermissionTo('jobs.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('jobs.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ServiceJob $serviceJob): bool
    {
        if (!$user->hasPermissionTo('jobs.edit')) {
            return false;
        }

        // Engineers can only edit jobs they created or are assigned to
        if ($user->hasRole('engineer')) {
            // Check if user created the job
            if ($serviceJob->created_by === $user->id) {
                return true;
            }

            // Check if user is assigned to the job
            $isAssigned = $serviceJob->assignments()
                ->where('user_id', $user->id)
                ->exists();

            return $isAssigned;
        }

        // Admins, Managers, Super-Admins (if they have permissions)
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ServiceJob $serviceJob): bool
    {
        return $user->hasPermissionTo('jobs.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ServiceJob $serviceJob): bool
    {
        return $user->hasRole('super-admin'); // Typically strictly restricted
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ServiceJob $serviceJob): bool
    {
        return $user->hasRole('super-admin');
    }
}
