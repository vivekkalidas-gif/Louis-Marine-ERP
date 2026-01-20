<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class SuperAdminRefinementSeeder extends Seeder
{
    public function run()
    {
        // 1. Get Roles
        $superAdminRole = Role::firstOrCreate(['slug' => 'super-admin'], ['name' => 'Super Admin']);
        
        // 2. Assign ALL Permissions (Full System Access)
        // Super Admin gets everything: CMS, Settings, Security, Operations, everything.
        $allPermissions = Permission::all();
        $superAdminRole->permissions()->sync($allPermissions);

        // 3. Ensure admin@louis-marine.com exists and has Super Admin role
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@louis-marine.com'],
            ['name' => 'Super Administrator', 'password' => Hash::make('password')]
        );
        
        // Assign Role
        $adminUser->roles()->syncWithoutDetaching([$superAdminRole->id]);

        // 4. Cleanup (Remove temporary system user and role if they exist)
        User::where('email', 'system@louis-marine.com')->delete();
        Role::where('slug', 'cms-manager')->delete();

        $this->command->info('Super Admin (admin@louis-marine.com) granted FULL SYSTEM ACCESS.');
    }
}
