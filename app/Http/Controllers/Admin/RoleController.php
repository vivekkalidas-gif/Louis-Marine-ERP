<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->where('slug', '!=', 'super-admin')->get(); // Hide Super Admin from editing
        $permissions = Permission::all()->groupBy(function($item) {
            return explode('.', $item->slug)[0]; // Group by module (users, customers...)
        });

        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        // Validation? Basic array check
        $role->permissions()->sync($request->input('permissions', []));
        
        return back()->with('success', 'Permissions updated for ' . $role->name);
    }
}
