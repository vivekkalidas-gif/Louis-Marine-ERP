<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Str;

class ManagerRoleSeeder extends Seeder
{
    public function run()
    {
        // 1. Create Manager Role
        $role = Role::firstOrCreate(
            ['slug' => 'manager'],
            ['name' => 'Manager', 'label' => 'Operations Manager']
        );

        // 2. Define Permissions (Ensure they exist)
        $permissions = [
            'users.view', 'users.create', 'users.edit', // Manage Staff
            'jobs.view', 'jobs.create', 'jobs.edit', 'jobs.delete', 'jobs.assign', // Operations
            'customers.view', 'customers.create', 'customers.edit', // CRM
            'vessels.view', 'vessels.create', 'vessels.edit',
            'inventory.view', 'inventory.create', 'inventory.edit', // Stock
            'accounts.view', 'accounts.create', 'accounts.edit', // Finance
            'certificates.view', 'certificates.approve', 'certificates.reject', // Compliance
            'reports.view', // Analytics
        ];

        // 3. Sync Permissions
        $permissionIds = [];
        foreach ($permissions as $slug) {
            $perm = Permission::firstOrCreate(
                ['slug' => $slug],
                ['name' => Str::title(str_replace('.', ' ', $slug))]
            );
            $permissionIds[] = $perm->id;
        }

        $role->permissions()->sync($permissionIds);

        // 4. Create a Demo Manager User
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'manager@louis-marine.com'],
            ['name' => 'Operations Manager', 'password' => \Illuminate\Support\Facades\Hash::make('password')]
        );
        $user->roles()->syncWithoutDetaching([$role->id]);

        $this->command->info('Manager role and demo user created.');
    }
}
