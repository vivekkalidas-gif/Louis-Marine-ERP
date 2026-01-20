<?php

namespace App\Policies;

use App\Models\Certificate;
use App\Models\User;

class CertificatePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('certificates.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Certificate $certificate): bool
    {
        return $user->hasPermissionTo('certificates.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('certificates.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Certificate $certificate): bool
    {
        if ($user->hasPermissionTo('certificates.edit')) {
            return true;
        }

        // Allow creators to edit their own DRAFT certificates if they have create permission
        if ($user->hasPermissionTo('certificates.create') && $certificate->created_by === $user->id && $certificate->status === 'DRAFT') {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Certificate $certificate): bool
    {
        return $user->hasPermissionTo('certificates.delete');
    }
}
