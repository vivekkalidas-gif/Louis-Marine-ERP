<x-app-layout>
    <x-slot name="header">
        <h2 class="theme-text-header">
            {{ __('Edit Employee: ') . $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="theme-card" x-data="{ activeTab: 'personal' }">
                
                <!-- Errors -->
                @if ($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <strong class="font-bold">Error!</strong>
                        <span class="block sm:inline">Please check the form below for errors.</span>
                        <ul class="mt-2 list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.users.update', $user->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Tabs Navigation -->
                    <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
                        <nav class="-mb-px flex space-x-8 overflow-x-auto" aria-label="Tabs">
                            <button type="button" @click="activeTab = 'personal'" :class="activeTab === 'personal' ? 'border-marine-500 text-marine-600 dark:text-marine-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                1. Personal Details
                            </button>
                            <button type="button" @click="activeTab = 'employment'" :class="activeTab === 'employment' ? 'border-marine-500 text-marine-600 dark:text-marine-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                2. Employment
                            </button>
                            <button type="button" @click="activeTab = 'salary'" :class="activeTab === 'salary' ? 'border-marine-500 text-marine-600 dark:text-marine-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                3. Payroll & Salary
                            </button>
                            <button type="button" @click="activeTab = 'documents'" :class="activeTab === 'documents' ? 'border-marine-500 text-marine-600 dark:text-marine-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                4. Documents
                            </button>
                            <button type="button" @click="activeTab = 'access'" :class="activeTab === 'access' ? 'border-marine-500 text-marine-600 dark:text-marine-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                5. System Access
                            </button>
                        </nav>
                    </div>

                    <!-- TAB 1: Personal Details -->
                    <div x-show="activeTab === 'personal'" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="theme-label">Full Name *</label>
                                <input type="text" name="name" class="theme-input" value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div>
                                <label class="theme-label">Email Address *</label>
                                <input type="email" name="email" class="theme-input" value="{{ old('email', $user->email) }}" required>
                            </div>
                            <div>
                                <label class="theme-label">Mobile Number</label>
                                <input type="text" name="mobile" class="theme-input" value="{{ old('mobile', $user->mobile) }}">
                            </div>
                            <div>
                                <label class="theme-label">Date of Birth</label>
                                <input type="date" name="dob" class="theme-input" value="{{ old('dob', optional($user->employeeDetail)->dob?->format('Y-m-d')) }}">
                            </div>
                            <div>
                                <label class="theme-label">Nationality</label>
                                <input type="text" name="nationality" class="theme-input" value="{{ old('nationality', optional($user->employeeDetail)->nationality) }}">
                            </div>
                            <div>
                                <label class="theme-label">Gender</label>
                                <select name="gender" class="theme-input">
                                    <option value="">Select Gender</option>
                                    <option value="Male" {{ old('gender', optional($user->employeeDetail)->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender', optional($user->employeeDetail)->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                            <div class="col-span-1 md:col-span-2">
                                <label class="theme-label">Profile Photo</label>
                                @if($user->avatar)
                                    <div class="mb-2">
                                        <img src="{{ Storage::url($user->avatar) }}" class="h-16 w-16 rounded-full object-cover">
                                    </div>
                                @endif
                                <input type="file" name="avatar" class="theme-input p-2" accept="image/*">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Upload passport size photo. Max 1MB.</p>
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button type="button" @click="activeTab = 'employment'" class="bg-marine-700 text-white px-4 py-2 rounded">Next: Employment &rarr;</button>
                        </div>
                    </div>

                    <!-- TAB 2: Employment Details -->
                    <div x-show="activeTab === 'employment'" class="space-y-6" style="display: none;">
                        <input type="hidden" name="employee_detail_id" value="{{ optional($user->employeeDetail)->id }}">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="theme-label">Employee Code *</label>
                                <input type="text" name="employee_code" class="theme-input" value="{{ old('employee_code', optional($user->employeeDetail)->employee_code) }}" required>
                            </div>
                            <div>
                                <label class="theme-label">Date of Joining</label>
                                <input type="date" name="join_date" class="theme-input" value="{{ old('join_date', optional($user->employeeDetail)->join_date?->format('Y-m-d')) }}">
                            </div>
                            <div>
                                <label class="theme-label">Designation</label>
                                <input type="text" name="designation" class="theme-input" value="{{ old('designation', optional($user->employeeDetail)->designation) }}">
                            </div>
                            <div>
                                <label class="theme-label">Department</label>
                                <input type="text" name="department" class="theme-input" value="{{ old('department', optional($user->employeeDetail)->department) }}">
                            </div>
                            <div>
                                <label class="theme-label">Employment Type</label>
                                <select name="employment_type" class="theme-input">
                                    <option value="Permanent" {{ old('employment_type', optional($user->employeeDetail)->employment_type) == 'Permanent' ? 'selected' : '' }}>Permanent</option>
                                    <option value="Contract" {{ old('employment_type', optional($user->employeeDetail)->employment_type) == 'Contract' ? 'selected' : '' }}>Contract</option>
                                    <option value="Temporary" {{ old('employment_type', optional($user->employeeDetail)->employment_type) == 'Temporary' ? 'selected' : '' }}>Temporary</option>
                                </select>
                            </div>
                            <div>
                                <label class="theme-label">Work Location</label>
                                <input type="text" name="work_location" class="theme-input" value="{{ old('work_location', optional($user->employeeDetail)->work_location) }}">
                            </div>
                            <div>
                                <label class="theme-label">Reporting Manager</label>
                                <select name="reporting_manager_id" class="theme-input">
                                    <option value="">None</option>
                                    @foreach($managers as $manager)
                                        <option value="{{ $manager->id }}" {{ old('reporting_manager_id', optional($user->employeeDetail)->reporting_manager_id) == $manager->id ? 'selected' : '' }}>
                                            {{ $manager->name }} ({{ $manager->employeeDetail->designation ?? $manager->roles->first()->name }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="theme-label">Status</label>
                                <select name="status" class="theme-input">
                                    <option value="Active" {{ old('status', optional($user->employeeDetail)->status) == 'Active' ? 'selected' : '' }}>Active</option>
                                    <option value="On Leave" {{ old('status', optional($user->employeeDetail)->status) == 'On Leave' ? 'selected' : '' }}>On Leave</option>
                                    <option value="Resigned" {{ old('status', optional($user->employeeDetail)->status) == 'Resigned' ? 'selected' : '' }}>Resigned</option>
                                    <option value="Terminated" {{ old('status', optional($user->employeeDetail)->status) == 'Terminated' ? 'selected' : '' }}>Terminated</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex justify-between">
                            <button type="button" @click="activeTab = 'personal'" class="text-gray-600 underline">&larr; Back</button>
                            <button type="button" @click="activeTab = 'salary'" class="bg-marine-700 text-white px-4 py-2 rounded">Next: Salary &rarr;</button>
                        </div>
                    </div>

                    <!-- TAB 3: Payroll & Salary -->
                    <div x-show="activeTab === 'salary'" class="space-y-6" style="display: none;">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Earnings</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="theme-label">Basic Salary</label>
                                <input type="number" step="0.01" name="basic_salary" class="theme-input" value="{{ old('basic_salary', optional($user->employeeDetail)->basic_salary) }}">
                            </div>
                            <div>
                                <label class="theme-label">Hourly Rate (for OT)</label>
                                <input type="number" step="0.01" name="hourly_rate" class="theme-input" value="{{ old('hourly_rate', optional($user->employeeDetail)->hourly_rate) }}">
                            </div>
                        </div>
                        
                        <h4 class="font-semibold mt-4 text-gray-700 dark:text-gray-300">Allowances</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="theme-label">Transport Allowance</label>
                                <input type="number" step="0.01" name="allowance_transport" class="theme-input" value="{{ old('allowance_transport', optional($user->employeeDetail)->allowance_transport) }}">
                            </div>
                            <div>
                                <label class="theme-label">Food Allowance</label>
                                <input type="number" step="0.01" name="allowance_food" class="theme-input" value="{{ old('allowance_food', optional($user->employeeDetail)->allowance_food) }}">
                            </div>
                            <div>
                                <label class="theme-label">Other Allowance</label>
                                <input type="number" step="0.01" name="allowance_other" class="theme-input" value="{{ old('allowance_other', optional($user->employeeDetail)->allowance_other) }}">
                            </div>
                        </div>

                        <div class="border-t border-gray-200 dark:border-gray-700 my-4"></div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Bank Details</h3>
                         @php
                            $bankDetails = optional($user->employeeDetail)->bank_details ?? [];
                        @endphp
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="theme-label">Payment Mode</label>
                                <select name="payment_mode" class="theme-input">
                                    <option value="Bank Transfer" {{ old('payment_mode', optional($user->employeeDetail)->payment_mode) == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="Cash" {{ old('payment_mode', optional($user->employeeDetail)->payment_mode) == 'Cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="Cheque" {{ old('payment_mode', optional($user->employeeDetail)->payment_mode) == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                </select>
                            </div>
                             <div>
                                <label class="theme-label">Bank Name</label>
                                <input type="text" name="bank_name" class="theme-input" value="{{ old('bank_name', $bankDetails['bank_name'] ?? '') }}">
                            </div>
                            <div>
                                <label class="theme-label">Account Number</label>
                                <input type="text" name="bank_account_number" class="theme-input" value="{{ old('bank_account_number', $bankDetails['account_number'] ?? '') }}">
                            </div>
                            <div>
                                <label class="theme-label">IBAN</label>
                                <input type="text" name="bank_iban" class="theme-input" value="{{ old('bank_iban', $bankDetails['iban'] ?? '') }}">
                            </div>
                        </div>
                        <div class="flex justify-between">
                            <button type="button" @click="activeTab = 'employment'" class="text-gray-600 underline">&larr; Back</button>
                            <button type="button" @click="activeTab = 'documents'" class="bg-marine-700 text-white px-4 py-2 rounded">Next: Documents &rarr;</button>
                        </div>
                    </div>

                    <!-- TAB 4: Documents -->
                    <div x-show="activeTab === 'documents'" class="space-y-6" style="display: none;" x-data="{ docs: [] }">
                        <!-- Existing Documents -->
                        @if($user->documents->count() > 0)
                            <h4 class="font-bold text-gray-700 dark:text-gray-300">Uploaded Documents</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($user->documents as $doc)
                                    <div class="flex items-center justify-between border p-3 rounded bg-white dark:bg-marine-900">
                                        <div>
                                            <p class="font-semibold text-sm">{{ $doc->document_type }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Exp: {{ $doc->expiry_date ? $doc->expiry_date->format('d/m/Y') : 'N/A' }}</p>
                                        </div>
                                        <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="text-blue-600 text-sm hover:underline">View</a>
                                    </div>
                                @endforeach
                            </div>
                            <div class="border-t border-gray-200 dark:border-gray-700 my-4"></div>
                        @endif

                        <h4 class="font-bold text-gray-700 dark:text-gray-300">Add New Documents</h4>
                        <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded text-sm text-blue-800 dark:text-blue-100 mb-4">
                            Mandatory Uploads: Passport, Visa/Work Permit, National ID.
                        </div>

                        <template x-for="(doc, index) in docs" :key="index">
                            <div class="border border-gray-200 dark:border-gray-700 rounded p-4 mb-4 bg-gray-50 dark:bg-marine-800">
                                <div class="flex justify-between mb-2">
                                    <h4 class="font-bold text-gray-700 dark:text-gray-300" x-text="'New Document ' + (index + 1)"></h4>
                                    <button type="button" @click="docs.splice(index, 1)" class="text-red-500 hover:text-red-700 text-sm">Remove</button>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                                    <div class="lg:col-span-1">
                                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400">Type *</label>
                                        <select :name="'documents['+index+'][type]'" class="theme-input text-sm" required>
                                            <option value="Passport">Passport</option>
                                            <option value="Visa">Visa / Work Permit</option>
                                            <option value="QID">National ID / QID</option>
                                            <option value="Medical">Medical Certificate</option>
                                            <option value="Contract">Employment Contract</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400">Doc Number</label>
                                        <input type="text" :name="'documents['+index+'][number]'" class="theme-input text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400">Issue Date</label>
                                        <input type="date" :name="'documents['+index+'][issue_date]'" class="theme-input text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400">Expiry Date</label>
                                        <input type="date" :name="'documents['+index+'][expiry_date]'" class="theme-input text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400">File *</label>
                                        <input type="file" :name="'documents['+index+'][file]'" class="theme-input text-sm p-1" required>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <button type="button" @click="docs.push({})" class="w-full py-2 border-2 border-dashed border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 font-bold rounded hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-marine-800">
                            + Add Document
                        </button>

                         <div class="flex justify-between mt-6">
                            <button type="button" @click="activeTab = 'salary'" class="text-gray-600 underline">&larr; Back</button>
                            <button type="button" @click="activeTab = 'access'" class="bg-marine-700 text-white px-4 py-2 rounded">Next: Access &rarr;</button>
                        </div>
                    </div>

                    <!-- TAB 5: System Access -->
                    <div x-show="activeTab === 'access'" class="space-y-6" style="display: none;">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="theme-label">System Role *</label>
                                <select name="role" class="theme-input" required>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->slug }}" {{ $user->hasRole($role->slug) ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="md:col-start-1">
                                <label class="theme-label">New Password</label>
                                <input type="text" name="password" class="theme-input" placeholder="Leave blank to keep current">
                            </div>
                            <div>
                                <label class="theme-label">Confirm New Password</label>
                                <input type="text" name="password_confirmation" class="theme-input" placeholder="Confirm New Password">
                            </div>
                        </div>

                         <div class="flex justify-between mt-6">
                            <button type="button" @click="activeTab = 'documents'" class="text-gray-600 underline">&larr; Back</button>
                            <div class="space-x-3">
                                <a href="{{ route('admin.users.index') }}" class="bg-gray-50 dark:bg-gray-7000 hover:bg-gray-600 text-white px-4 py-2 rounded">Cancel</a>
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded font-bold shadow-lg">Update Employee</button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>
