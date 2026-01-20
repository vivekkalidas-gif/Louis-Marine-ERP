<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-marine-900 dark:text-marine-100 leading-tight">
            {{ __('Request Material') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <form action="{{ route('engineer.material-requests.store') }}" method="POST">
                        @csrf
                        @if(isset($job))
                            <input type="hidden" name="service_job_id" value="{{ $job->id }}">
                            <div class="mb-6 bg-blue-50 border-l-4 border-blue-400 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-blue-700">
                                            Requesting materials for Job: <span class="font-bold">{{ $job->job_number }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div x-data="{ items: [{ product_id: '', quantity: 1 }] }">
                            <div class="space-y-4">
                                <template x-for="(item, index) in items" :key="index">
                                    <div class="flex items-end gap-4 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg relative">
                                        <div class="flex-1">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Select Item</label>
                                            <x-searchable-select 
                                                :options="$products->map(fn($p) => ['value' => $p->id, 'label' => $p->item_name . ' (Stock: ' . $p->current_stock . ')'])->values()->all()"
                                                name="items[' + index + '][product_id]"
                                                x-model="item.product_id"
                                                placeholder="Select Item from Inventory"
                                            />
                                        </div>
                                        
                                        <div class="w-32">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantity</label>
                                            <input type="number" :name="'items[' + index + '][quantity]'" x-model="item.quantity" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                                        </div>

                                        <button type="button" @click="items.splice(index, 1)" class="text-red-500 hover:text-red-700 mb-2" x-show="items.length > 1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            </div>

                            <div class="mt-4">
                                <button type="button" @click="items.push({ product_id: '', quantity: 1 })" class="flex items-center text-indigo-600 hover:text-indigo-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                    </svg>
                                    Add Another Item
                                </button>
                            </div>

                            <div class="border-t border-gray-200 dark:border-gray-600 my-6 pt-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Urgency</label>
                                    <select name="urgency" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="normal">Normal</option>
                                        <option value="high">High</option>
                                        <option value="critical">Critical</option>
                                    </select>
                                </div>
                                
                                <div class="flex justify-end mt-6">
                                    <button type="submit" class="bg-orange-600 text-white px-4 py-2 rounded-md hover:bg-orange-700">Submit Request</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
