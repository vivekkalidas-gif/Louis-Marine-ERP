<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CmsManagerRoleSeeder extends Seeder
{
    public function run()
    {
        // 1. Create New Super Admin (System Owner) to prevent lockout
        $systemUser = User::firstOrCreate(
            ['email' => 'system@louis-marine.com'],
            ['name' => 'System Owner', 'password' => Hash::make('password')]
        );
        // Assign Super Admin to System User
        $superAdminRole = Role::where('slug', 'super-admin')->first();
        if ($superAdminRole) {
            $systemUser->roles()->syncWithoutDetaching([$superAdminRole->id]);
        }

        // 2. Create CMS Manager Role
        $cmsRole = Role::firstOrCreate(
            ['slug' => 'cms-manager'],
            ['name' => 'CMS Manager']
        );

        // 3. Define CMS Permissions
        // We assume 'services.view', 'pages.view' etc exist or we create them.
        // Based on previous web.php, CMS routes are protected by 'admin' role. 
        // We will need to update web.php to allow 'cms-manager' too.
        $permissions = [
            'cms.manage', // General CMS access
            'enquiries.view', // Contact form submissions
            'quote_requests.view', // Quote requests (maybe?)
        ];

        // Create these permissions if they don't exist
        $permissionIds = [];
        foreach ($permissions as $slug) {
            $perm = Permission::firstOrCreate(
                ['slug' => $slug],
                ['name' => Str::title(str_replace('.', ' ', $slug))]
            );
            $permissionIds[] = $perm->id;
        }
        $cmsRole->permissions()->sync($permissionIds);

        // 4. Demote admin@louis-marine.com to CMS Manager
        $oldAdmin = User::where('email', 'admin@louis-marine.com')->first();
        if ($oldAdmin) {
            // Remove Super Admin and Admin roles
            // Note: If we just detach all, it's cleanest.
            $oldAdmin->roles()->sync([$cmsRole->id]);
            $this->command->info('Demoted admin@louis-marine.com to CMS Manager.');
        }

        $this->command->info('System Owner (system@louis-marine.com) created.');
        $this->command->info('CMS Manager Role created.');
    }
}
