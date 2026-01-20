<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            {{ __('System Settings') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ activeTab: 'company' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Tabs Navigation -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4">
                <nav class="flex space-x-4 border-b border-gray-200 dark:border-gray-700" aria-label="Tabs">
                    <button @click="activeTab = 'company'" :class="{ 'border-blue-500 text-blue-600': activeTab === 'company', 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300 hover:border-gray-300': activeTab !== 'company' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Company Profile
                    </button>
                    <button @click="activeTab = 'branding'" :class="{ 'border-blue-500 text-blue-600': activeTab === 'branding', 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300 hover:border-gray-300': activeTab !== 'branding' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Branding & Theme
                    </button>
                    <button @click="activeTab = 'email'" :class="{ 'border-blue-500 text-blue-600': activeTab === 'email', 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300 hover:border-gray-300': activeTab !== 'email' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Email & Notifications
                    </button>
                    <button @click="activeTab = 'numbering'" :class="{ 'border-blue-500 text-blue-600': activeTab === 'numbering', 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300 hover:border-gray-300': activeTab !== 'numbering' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Numbering Format
                    </button>
                    <button @click="activeTab = 'security'" :class="{ 'border-blue-500 text-blue-600': activeTab === 'security', 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300 hover:border-gray-300': activeTab !== 'security' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Security & System
                    </button>
                </nav>
            </div>

            <!-- Settings Form -->
            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- Company Profile Tab -->
                <div x-show="activeTab === 'company'" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b dark:border-gray-700 pb-2">Company Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="company_name" :value="__('Company Name')" />
                            <x-text-input id="company_name" name="company_name" type="text" class="mt-1 block w-full" :value="$settings->get('company')?->where('key', 'company_name')->first()->value ?? ''" required />
                        </div>
                        <div>
                            <x-input-label for="company_email" :value="__('Company Email')" />
                            <x-text-input id="company_email" name="company_email" type="email" class="mt-1 block w-full" :value="$settings->get('company')?->where('key', 'company_email')->first()->value ?? ''" required />
                        </div>
                         <div>
                            <x-input-label for="company_phone" :value="__('Phone Number')" />
                            <x-text-input id="company_phone" name="company_phone" type="text" class="mt-1 block w-full" :value="$settings->get('company')?->where('key', 'company_phone')->first()->value ?? ''" />
                        </div>
                         <div>
                            <x-input-label for="company_website" :value="__('Website')" />
                            <x-text-input id="company_website" name="company_website" type="url" class="mt-1 block w-full" :value="$settings->get('company')?->where('key', 'company_website')->first()->value ?? ''" />
                        </div>
                        <div class="md:col-span-2">
                             <x-input-label for="company_address" :value="__('Address')" />
                             <textarea id="company_address" name="company_address" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block w-full mt-1" rows="3">{{ $settings->get('company')?->where('key', 'company_address')->first()->value ?? '' }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Branding Tab -->
                <div x-show="activeTab === 'branding'" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-6" style="display: none;">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b dark:border-gray-700 pb-2">Branding & Theme</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                         <div>
                            <x-input-label for="company_logo" :value="__('Company Logo')" />
                            <input type="file" name="company_logo" class="mt-1 block w-full border border-gray-300 rounded p-2">
                            @if($logo = $settings->get('branding')?->where('key', 'company_logo')->first())
                                <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">Current: <a href="{{ asset($logo->value) }}" target="_blank" class="text-blue-600 underline">View Logo</a></div>
                            @endif
                        </div>
                        <div>
                             <x-input-label for="site_favicon" :value="__('Favicon')" />
                             <input type="file" name="site_favicon" class="mt-1 block w-full border border-gray-300 rounded p-2">
                        </div>
                        <div>
                            <x-input-label for="branding_primary_color" :value="__('Primary Color')" />
                            <div class="flex items-center gap-2 mt-1">
                                <input type="color" id="branding_primary_color" name="branding_primary_color" class="h-10 w-20" value="{{ $settings->get('branding')?->where('key', 'branding_primary_color')->first()->value ?? '#1e3a8a' }}">
                                <x-text-input type="text" class="block w-full" :value="$settings->get('branding')?->where('key', 'branding_primary_color')->first()->value ?? '#1e3a8a'" readonly />
                            </div>
                        </div>
                        <div>
                             <x-input-label for="branding_logo_height" :value="__('Logo Height (px)')" />
                             <x-text-input id="branding_logo_height" name="branding_logo_height" type="number" class="mt-1 block w-full" :value="$settings->get('branding')?->where('key', 'branding_logo_height')->first()->value ?? '40'" min="20" max="100" />
                             <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Default is 40px. Adjust to fit your logo aspect ratio.</p>
                        </div>
                    </div>
                </div>

                <!-- Email Tab -->
                <div x-show="activeTab === 'email'" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-6" style="display: none;">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b dark:border-gray-700 pb-2">Email Configuration</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="mail_from_name" :value="__('From Name')" />
                            <x-text-input id="mail_from_name" name="mail_from_name" type="text" class="mt-1 block w-full" :value="$settings->get('email')?->where('key', 'mail_from_name')->first()->value ?? config('app.name')" />
                        </div>
                         <div>
                            <x-input-label for="mail_from_address" :value="__('From Email Address')" />
                            <x-text-input id="mail_from_address" name="mail_from_address" type="email" class="mt-1 block w-full" :value="$settings->get('email')?->where('key', 'mail_from_address')->first()->value ?? ''" />
                        </div>
                        <div>
                            <label class="inline-flex items-center mt-6">
                                <input type="hidden" name="mail_notify_expiry" value="0">
                                <input type="checkbox" name="mail_notify_expiry" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ ($settings->get('email')?->where('key', 'mail_notify_expiry')->first()->value ?? '0') == '1' ? 'checked' : '' }}>
                                <span class="ml-2 text-gray-700 dark:text-gray-300">Enable Certificate Expiry Notifications</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Numbering Tab -->
                 <div x-show="activeTab === 'numbering'" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-6" style="display: none;">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b dark:border-gray-700 pb-2">Prefix & Numbering</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div>
                            <x-input-label for="prefix_invoice" :value="__('Invoice Prefix')" />
                            <x-text-input id="prefix_invoice" name="prefix_invoice" type="text" class="mt-1 block w-full font-mono uppercase" :value="$settings->get('numbering')?->where('key', 'prefix_invoice')->first()->value ?? 'INV'" />
                        </div>
                        <div>
                            <x-input-label for="prefix_quotation" :value="__('Quotation Prefix')" />
                            <x-text-input id="prefix_quotation" name="prefix_quotation" type="text" class="mt-1 block w-full font-mono uppercase" :value="$settings->get('numbering')?->where('key', 'prefix_quotation')->first()->value ?? 'QTE'" />
                        </div>
                        <div>
                            <x-input-label for="prefix_job" :value="__('Job Prefix')" />
                            <x-text-input id="prefix_job" name="prefix_job" type="text" class="mt-1 block w-full font-mono uppercase" :value="$settings->get('numbering')?->where('key', 'prefix_job')->first()->value ?? 'JOB'" />
                        </div>
                        <div>
                            <x-input-label for="prefix_certificate" :value="__('Certificate Prefix')" />
                            <x-text-input id="prefix_certificate" name="prefix_certificate" type="text" class="mt-1 block w-full font-mono uppercase" :value="$settings->get('numbering')?->where('key', 'prefix_certificate')->first()->value ?? 'CERT'" />
                        </div>
                        <div>
                             <x-input-label for="prefix_dispatch" :value="__('Dispatch Prefix')" />
                             <x-text-input id="prefix_dispatch" name="prefix_dispatch" type="text" class="mt-1 block w-full font-mono uppercase" :value="$settings->get('numbering')?->where('key', 'prefix_dispatch')->first()->value ?? 'DN'" />
                        </div>
                        <div>
                             <x-input-label for="prefix_purchase_order" :value="__('PO Prefix')" />
                             <x-text-input id="prefix_purchase_order" name="prefix_purchase_order" type="text" class="mt-1 block w-full font-mono uppercase" :value="$settings->get('numbering')?->where('key', 'prefix_purchase_order')->first()->value ?? 'PO'" />
                        </div>
                        <div>
                             <x-input-label for="prefix_inventory" :value="__('Inventory/SKU Prefix')" />
                             <x-text-input id="prefix_inventory" name="prefix_inventory" type="text" class="mt-1 block w-full font-mono uppercase" :value="$settings->get('numbering')?->where('key', 'prefix_inventory')->first()->value ?? 'INV'" />
                        </div>
                    </div>
                </div>

                <!-- Security Tab -->
                 <div x-show="activeTab === 'security'" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-6" style="display: none;">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b dark:border-gray-700 pb-2">System Security</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="security_session_lifetime" :value="__('Session Timeout (Minutes)')" />
                            <x-text-input id="security_session_lifetime" name="security_session_lifetime" type="number" class="mt-1 block w-full" :value="$settings->get('security')?->where('key', 'security_session_lifetime')->first()->value ?? '120'" />
                        </div>
                        <div>
                            <label class="inline-flex items-center mt-8">
                                <input type="hidden" name="security_maintenance_mode" value="0">
                                <input type="checkbox" name="security_maintenance_mode" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ ($settings->get('security')?->where('key', 'security_maintenance_mode')->first()->value ?? '0') == '1' ? 'checked' : '' }}>
                                <span class="ml-2 text-red-600 font-bold">Enable Maintenance Mode</span>
                            </label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 ml-6">Only admins will be able to access the system.</p>
                        </div>
                    </div>
                </div>

                <!-- Save Action -->
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-lg mt-6">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Save Changes
                    </button>
                </div>

            </form>
        </div>
    </div>
</x-app-layout>
