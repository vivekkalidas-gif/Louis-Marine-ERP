<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CustomerPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('customers.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Customer $customer): bool
    {
        return $user->hasPermissionTo('customers.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('customers.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Customer $customer): bool
    {
        return $user->hasPermissionTo('customers.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Customer $customer): bool
    {
        return $user->hasPermissionTo('customers.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Customer $customer): bool
    {
        return $user->hasPermissionTo('customers.delete'); // Assuming delete permission implies restore for now
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Customer $customer): bool
    {
        return $user->hasRole('super-admin'); // Only super-admin can force delete usually
    }
    
    // Sub-resources
    public function addContact(User $user, Customer $customer): bool
    {
        return $user->hasPermissionTo('customers.edit');
    }

    public function addDocument(User $user, Customer $customer): bool
    {
        return $user->hasPermissionTo('customers.edit');
    }
}
