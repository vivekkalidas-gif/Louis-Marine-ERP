<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Str;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // 1. Create Roles
        $roles = ['super-admin', 'admin', 'manager', 'engineer', 'technician', 'stockist', 'accounts', 'customer'];
        foreach ($roles as $slug) {
            Role::firstOrCreate(['slug' => $slug], ['name' => Str::title(str_replace('-', ' ', $slug))]);
        }

        // 2. Define Permissions
        $modules = ['users', 'customers', 'jobs', 'certificates', 'inventory', 'accounts', 'reports', 'settings'];
        $actions = ['view', 'create', 'edit', 'delete', 'manage'];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(
                    ['slug' => "{$module}.{$action}"],
                    ['name' => Str::title("{$action} {$module}")]
                );
            }
        }

        // 3. Assign Permissions (Simplified Matrix)
        $adminRole = Role::where('slug', 'admin')->first();
        
        // Ensure Admin User Exists
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@louis-marine.com'],
            [
                'name' => 'Administrator',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $adminUser->roles()->syncWithoutDetaching([$adminRole->id]);

        $adminPermissions = Permission::all(); // Admin gets everything except maybe some super-critical stuff? 
        // Actually Super Admin gets everything via bypass. Admin gets most.
        $adminRole->permissions()->sync($adminPermissions);

        $engineerRole = Role::where('slug', 'engineer')->first();
        $engineerPerms = Permission::whereIn('slug', [
            'jobs.view', 'jobs.edit', 'jobs.manage', // Can manage jobs
            'certificates.view', 'certificates.create',
            'inventory.view',
            'reports.view', // Own stats
        ])->get();
        $engineerRole->permissions()->sync($engineerPerms);
        
        $stockistRole = Role::where('slug', 'stockist')->first();
        $stockistPerms = Permission::whereIn('slug', [
            'inventory.view', 'inventory.create', 'inventory.edit',
        ])->get();
        $stockistRole->permissions()->sync($stockistPerms);

        $accountsRole = Role::where('slug', 'accounts')->first();
        $accountsPerms = Permission::whereIn('slug', [
            'accounts.view', 'accounts.create', 'accounts.edit', 'accounts.delete',
            'customers.view', 'customers.create', 'customers.edit',
            'reports.view',
        ])->get();
        $accountsRole->permissions()->sync($accountsPerms);
    }
}
