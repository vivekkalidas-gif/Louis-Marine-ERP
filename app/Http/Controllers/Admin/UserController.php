<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', User::class);
        $users = User::with(['roles.permissions', 'employeeDetail'])->latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $this->authorize('create', User::class);
        $roles = Role::all();
        // Get potential managers (e.g., Admins, Managers, Engineers)
        $managers = User::with(['employeeDetail', 'roles'])->whereHas('roles', function($q) {
            $q->whereIn('slug', ['admin', 'manager', 'engineer']);
        })->get();

        // Auto-generate next Employee Code
        $lastEmployee = \App\Models\EmployeeDetail::latest('id')->first();
        $nextId = $lastEmployee ? (intval(preg_replace('/[^0-9]/', '', $lastEmployee->employee_code)) + 1) : 1001;
        $suggestedCode = 'EMP-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
        
        return view('admin.users.create', compact('roles', 'managers', 'suggestedCode'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', User::class);
        // 1. Validate All Inputs
        $validated = $request->validate([
            // Basic
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string'], // Single role selection primarily
            'mobile' => ['nullable', 'string', 'max:20'],
            'avatar' => ['nullable', 'image', 'max:1024'], // 1MB Max
            
            // Employee Details
            'employee_code' => ['required', 'string', 'unique:employee_details,employee_code'],
            'nationality' => ['nullable', 'string'],
            'dob' => ['nullable', 'date'],
            'gender' => ['nullable', 'string'],
            'designation' => ['nullable', 'string'],
            'department' => ['nullable', 'string'],
            'employment_type' => ['nullable', 'string'],
            'work_location' => ['nullable', 'string'],
            'reporting_manager_id' => ['nullable', 'uuid'],
            'join_date' => ['nullable', 'date'],
            'status' => ['nullable', 'string'],
            
            // Financial
            'payment_mode' => ['nullable', 'string'],
            'basic_salary' => ['nullable', 'numeric', 'min:0'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'allowance_transport' => ['nullable', 'numeric', 'min:0'],
            'allowance_food' => ['nullable', 'numeric', 'min:0'],
            'allowance_other' => ['nullable', 'numeric', 'min:0'],
            'bank_name' => ['nullable', 'string'],
            'bank_account_number' => ['nullable', 'string'],
            'bank_iban' => ['nullable', 'string'],

            // Documents (Array validation)
            'documents' => ['nullable', 'array'],
            'documents.*.type' => ['required', 'string'],
            'documents.*.number' => ['nullable', 'string'],
            'documents.*.issue_date' => ['nullable', 'date'],
            'documents.*.expiry_date' => ['nullable', 'date'],
            'documents.*.file' => ['required', 'file', 'max:5120'], // 5MB
        ]);

        DB::transaction(function () use ($request, $validated) {
            // 2. Create User
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'mobile' => $validated['mobile'] ?? null,
                'password' => Hash::make($validated['password'] ?? 'password'), // Default password if empty
                'is_active' => ($validated['status'] ?? 'Active') === 'Active',
            ]);

            // Assign Role
            // Assuming 'role' input is a slug. We might need to fetch ID or use specialized method.
            // For now, let's assume we find role by slug.
            $role = Role::where('slug', $validated['role'])->first();
            if ($role) {
                $user->roles()->attach($role);
            }

            // Avatar Upload
            if ($request->hasFile('avatar')) {
                $path = $request->file('avatar')->store('avatars', 'public');
                $user->update(['avatar' => $path]);
            }

            // 3. Create Employee Details
            $bankDetails = [
                'bank_name' => $validated['bank_name'] ?? null,
                'account_number' => $validated['bank_account_number'] ?? null,
                'iban' => $validated['bank_iban'] ?? null,
            ];

            $user->employeeDetail()->create([
                'employee_code' => $validated['employee_code'],
                'nationality' => $validated['nationality'] ?? null,
                'dob' => $validated['dob'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'designation' => $validated['designation'] ?? null,
                'department' => $validated['department'] ?? null,
                'employment_type' => $validated['employment_type'] ?? 'Permanent',
                'work_location' => $validated['work_location'] ?? null,
                'reporting_manager_id' => $validated['reporting_manager_id'] ?? null,
                'join_date' => $validated['join_date'] ?? null,
                'status' => $validated['status'] ?? 'Active',
                'payment_mode' => $validated['payment_mode'] ?? 'Bank Transfer',
                'basic_salary' => $validated['basic_salary'] ?? 0,
                'hourly_rate' => $validated['hourly_rate'] ?? 0,
                'allowance_transport' => $validated['allowance_transport'] ?? 0,
                'allowance_food' => $validated['allowance_food'] ?? 0,
                'allowance_other' => $validated['allowance_other'] ?? 0,
                'bank_details' => $bankDetails,
            ]);

            // 4. Handle Documents
            if ($request->has('documents')) {
                foreach ($request->documents as $index => $docData) {
                    if (isset($docData['file']) && $docData['file'] instanceof \Illuminate\Http\UploadedFile) {
                        $path = $docData['file']->store("employees/{$user->id}/documents", 'public');
                        
                        $user->documents()->create([
                            'document_type' => $docData['type'],
                            'document_number' => $docData['number'] ?? null,
                            'issue_date' => $docData['issue_date'] ?? null,
                            'expiry_date' => $docData['expiry_date'] ?? null,
                            'file_path' => $path,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('admin.users.index')->with('success', 'Employee created successfully.');
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);
        $user->load(['roles', 'employeeDetail', 'documents']);
        $roles = Role::all();
        $managers = User::with(['employeeDetail', 'roles'])->where('id', '!=', $user->id) // Prevent reporting to self
            ->whereHas('roles', function($q) {
                $q->whereIn('slug', ['admin', 'manager', 'engineer']);
            })->get();

        return view('admin.users.edit', compact('user', 'roles', 'managers'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);
         $validated = $request->validate([
            // Basic
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'avatar' => ['nullable', 'image', 'max:1024'],
            
            // Employee Details
            'employee_code' => ['required', 'string', 'unique:employee_details,employee_code,'.$user->employeeDetail->id],
            'nationality' => ['nullable', 'string'],
            'dob' => ['nullable', 'date'],
            'gender' => ['nullable', 'string'],
            'designation' => ['nullable', 'string'],
            'department' => ['nullable', 'string'],
            'employment_type' => ['nullable', 'string'],
            'work_location' => ['nullable', 'string'],
            'reporting_manager_id' => ['nullable', 'uuid'],
            'join_date' => ['nullable', 'date'],
            'status' => ['nullable', 'string'],
            
            // Financial
            'payment_mode' => ['nullable', 'string'],
            'basic_salary' => ['nullable', 'numeric', 'min:0'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'allowance_transport' => ['nullable', 'numeric', 'min:0'],
            'allowance_food' => ['nullable', 'numeric', 'min:0'],
            'allowance_other' => ['nullable', 'numeric', 'min:0'],
            'bank_name' => ['nullable', 'string'],
            'bank_account_number' => ['nullable', 'string'],
            'bank_iban' => ['nullable', 'string'],

            // Documents (New uploads)
            'documents' => ['nullable', 'array'],
            'documents.*.type' => ['required', 'string'],
            'documents.*.file' => ['required', 'file', 'max:5120'],
        ]);

        DB::transaction(function () use ($request, $validated, $user) {
            // Update User
            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'mobile' => $validated['mobile'] ?? null,
                'is_active' => ($validated['status'] ?? 'Active') === 'Active',
            ];
            
            if (!empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }
            
            if ($request->hasFile('avatar')) {
                $userData['avatar'] = $request->file('avatar')->store('avatars', 'public');
            }
            
            $user->update($userData);

            // Sync Role
            $role = Role::where('slug', $validated['role'])->first();
            if ($role) {
                $user->roles()->sync([$role->id]);
            }

            // Update Employee Details
            $bankDetails = [
                'bank_name' => $validated['bank_name'] ?? null,
                'account_number' => $validated['bank_account_number'] ?? null,
                'iban' => $validated['bank_iban'] ?? null,
            ];

            $user->employeeDetail()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'employee_code' => $validated['employee_code'],
                    'nationality' => $validated['nationality'] ?? null,
                    'dob' => $validated['dob'] ?? null,
                    'gender' => $validated['gender'] ?? null,
                    'designation' => $validated['designation'] ?? null,
                    'department' => $validated['department'] ?? null,
                    'employment_type' => $validated['employment_type'] ?? 'Permanent',
                    'work_location' => $validated['work_location'] ?? null,
                    'reporting_manager_id' => $validated['reporting_manager_id'] ?? null,
                    'join_date' => $validated['join_date'] ?? null,
                    'status' => $validated['status'] ?? 'Active',
                    'payment_mode' => $validated['payment_mode'] ?? 'Bank Transfer',
                    'basic_salary' => $validated['basic_salary'] ?? 0,
                    'hourly_rate' => $validated['hourly_rate'] ?? 0,
                    'allowance_transport' => $validated['allowance_transport'] ?? 0,
                    'allowance_food' => $validated['allowance_food'] ?? 0,
                    'allowance_other' => $validated['allowance_other'] ?? 0,
                    'bank_details' => $bankDetails,
                ]
            );

            // Handle New Documents
            if ($request->has('documents')) {
                 foreach ($request->documents as $index => $docData) {
                    if (isset($docData['file']) && $docData['file'] instanceof \Illuminate\Http\UploadedFile) {
                        $path = $docData['file']->store("employees/{$user->id}/documents", 'public');
                        
                        $user->documents()->create([
                            'document_type' => $docData['type'],
                            'document_number' => $docData['number'] ?? null,
                            'issue_date' => $docData['issue_date'] ?? null,
                            'expiry_date' => $docData['expiry_date'] ?? null,
                            'file_path' => $path,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('admin.users.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }
        
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
    
    public function toggleStatus(User $user)
    {
        $this->authorize('update', $user);
        // Toggle active status logic
         $user->update(['is_active' => !$user->is_active]);
         return back()->with('success', 'Status updated.');
    }

    public function forceLogout(User $user)
    {
        $this->authorize('update', $user);
         $user->update(['remember_token' => \Illuminate\Support\Str::random(60)]);
         return back()->with('success', 'User logged out.');
    }
}
