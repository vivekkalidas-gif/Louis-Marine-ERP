<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="theme-text-header">
                {{ __('Employee Profile') }}
            </h2>
            <a href="{{ route('admin.users.edit', $user->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded font-bold text-sm">
                Edit Profile
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Header Card -->
            <div class="theme-card flex flex-col md:flex-row items-center md:items-start space-y-4 md:space-y-0 md:space-x-6">
                <div class="flex-shrink-0">
                    @if($user->avatar)
                        <img src="{{ Storage::url($user->avatar) }}" class="h-32 w-32 rounded-full object-cover border-4 border-gray-200 dark:border-gray-700">
                    @else
                        <div class="h-32 w-32 rounded-full bg-marine-100 dark:bg-marine-800 flex items-center justify-center text-4xl font-bold text-marine-700 dark:text-marine-300">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                    @endif
                </div>
                <div class="flex-grow text-center md:text-left">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h1>
                    <p class="text-lg text-gray-500 dark:text-gray-400">{{ optional($user->employeeDetail)->designation ?? 'No Designation' }}</p>
                    <div class="mt-2 flex flex-wrap justify-center md:justify-start gap-2">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                            {{ optional($user->employeeDetail)->employee_code ?? 'N/A' }}
                        </span>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        @foreach($user->roles as $role)
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                {{ $role->name }}
                            </span>
                        @endforeach
                    </div>
                </div>
                <div class="text-center md:text-right space-y-1">
                    <p class="text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Joined:</span> {{ optional($user->employeeDetail)->join_date ? $user->employeeDetail->join_date->format('d M, Y') : '-' }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Department:</span> {{ optional($user->employeeDetail)->department ?? '-' }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Location:</span> {{ optional($user->employeeDetail)->work_location ?? '-' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Personal Details -->
                <div class="theme-card md:col-span-1">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">Personal Details</h3>
                    <ul class="space-y-3 text-sm">
                        <li class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Email:</span>
                            <span class="text-gray-900 dark:text-gray-200">{{ $user->email }}</span>
                        </li>
                        <li class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Mobile:</span>
                            <span class="text-gray-900 dark:text-gray-200">{{ $user->mobile ?? '-' }}</span>
                        </li>
                        <li class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">DOB:</span>
                            <span class="text-gray-900 dark:text-gray-200">{{ optional($user->employeeDetail)->dob ? $user->employeeDetail->dob->format('d M, Y') : '-' }}</span>
                        </li>
                        <li class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Nationality:</span>
                            <span class="text-gray-900 dark:text-gray-200">{{ optional($user->employeeDetail)->nationality ?? '-' }}</span>
                        </li>
                        <li class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Gender:</span>
                            <span class="text-gray-900 dark:text-gray-200">{{ optional($user->employeeDetail)->gender ?? '-' }}</span>
                        </li>
                    </ul>
                </div>

                <!-- Employment & Financial -->
                <div class="theme-card md:col-span-2">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">Employment & Financial</h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-semibold text-gray-700 dark:text-gray-300 mb-2">Employment</h4>
                            <ul class="space-y-2 text-sm">
                                <li class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Type:</span> <span>{{ optional($user->employeeDetail)->employment_type ?? '-' }}</span></li>
                                <li class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Reporting To:</span> <span>{{ optional($user->employeeDetail->manager)->name ?? '-' }}</span></li>
                                <li class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Status:</span> <span>{{ optional($user->employeeDetail)->status ?? 'Active' }}</span></li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-700 dark:text-gray-300 mb-2">Financial</h4>
                            <ul class="space-y-2 text-sm">
                                <li class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Basic Salary:</span> <span>{{ number_format(optional($user->employeeDetail)->basic_salary ?? 0, 2) }}</span></li>
                                <li class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Bank Name:</span> <span>{{ optional($user->employeeDetail)->bank_details['bank_name'] ?? '-' }}</span></li>
                                <li class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">IBAN:</span> <span>{{ optional($user->employeeDetail)->bank_details['iban'] ?? '-' }}</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents Gallery -->
            <div class="theme-card">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">Documents</h3>
                
                @if($user->documents->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($user->documents as $doc)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 flex flex-col justify-between bg-gray-50 dark:bg-marine-800">
                                <div class="mb-4">
                                    <h5 class="font-bold text-marine-700 dark:text-marine-300">{{ $doc->document_type }}</h5>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Number: {{ $doc->document_number ?? 'N/A' }}</p>
                                    
                                    @if($doc->expiry_date)
                                        @php
                                            $isExpired = $doc->expiry_date->isPast();
                                            $isExpiringSoon = !$isExpired && $doc->expiry_date->diffInDays(now()) < 30;
                                        @endphp
                                        <p class="text-xs mt-1 {{ $isExpired ? 'text-red-600 font-bold' : ($isExpiringSoon ? 'text-yellow-600 font-bold' : 'text-green-600') }}">
                                            Expires: {{ $doc->expiry_date->format('d M, Y') }}
                                            @if($isExpired) (EXPIRED) @endif
                                            @if($isExpiringSoon) (Expiring Soon) @endif
                                        </p>
                                    @endif
                                </div>
                                <div class="mt-auto pt-2 border-t border-gray-200 dark:border-gray-700">
                                    <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="w-full block text-center bg-white dark:bg-marine-700 border border-gray-300 dark:border-gray-600 rounded py-1 text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-marine-600">
                                        View Document
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 dark:text-gray-400 text-center py-6">No documents uploaded.</p>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
