<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Website Content Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Quick Stats --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <div class="text-gray-500 dark:text-gray-400 text-sm uppercase tracking-wide">Website Products</div>
                    <div class="text-2xl font-bold dark:text-white">{{ $stats['total_products'] }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <div class="text-gray-500 dark:text-gray-400 text-sm uppercase tracking-wide">Active Listings</div>
                    <div class="text-2xl font-bold dark:text-white">{{ $stats['active_products'] }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-purple-500">
                    <div class="text-gray-500 dark:text-gray-400 text-sm uppercase tracking-wide">Services Offered</div>
                    <div class="text-2xl font-bold dark:text-white">{{ $stats['services'] }}</div>
                </div>
                 <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-red-500">
                    <div class="text-gray-500 dark:text-gray-400 text-sm uppercase tracking-wide">New Enquiries</div>
                    <div class="text-2xl font-bold dark:text-white">{{ $stats['enquiries'] }}</div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Content Actions -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                     <h3 class="text-lg font-bold mb-4 dark:text-white">Content Management</h3>
                     <div class="space-y-4">
                         <a href="{{ route('admin.cms.services.index') }}" class="block p-4 border rounded hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-700 dark:border-gray-600 transition flex justify-between items-center group">
                             <div>
                                 <span class="font-bold text-gray-800 dark:text-gray-200 block group-hover:text-blue-600">Manage Services</span>
                                 <span class="text-sm text-gray-500 dark:text-gray-400">Edit service listings, icons, and descriptions.</span>
                             </div>
                             <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                         </a>
                         
                         <a href="{{ route('admin.cms.products.index') }}" class="block p-4 border rounded hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-700 dark:border-gray-600 transition flex justify-between items-center group">
                             <div>
                                 <span class="font-bold text-gray-800 dark:text-gray-200 block group-hover:text-blue-600">Website Products</span>
                                 <span class="text-sm text-gray-500 dark:text-gray-400">Manage public product catalog (Separated from Inventory).</span>
                             </div>
                             <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                         </a>

                         <a href="{{ route('admin.cms.pages.index') }}" class="block p-4 border rounded hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-700 dark:border-gray-600 transition flex justify-between items-center group">
                             <div>
                                 <span class="font-bold text-gray-800 dark:text-gray-200 block group-hover:text-blue-600">Edit Pages</span>
                                 <span class="text-sm text-gray-500 dark:text-gray-400">Home, About Us, Contact information.</span>
                             </div>
                             <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                         </a>
                     </div>
                </div>

                <!-- Inbox -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4 dark:text-white">Recent Enquiries</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mb-4">You have {{ $stats['enquiries'] }} new messages from the contact form.</p>
                    <a href="{{ route('admin.cms.contact-submissions.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 inline-block font-bold">View Message Box</a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
