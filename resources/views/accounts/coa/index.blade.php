<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                {{ __('Chart of Accounts') }}
            </h2>
            <button @click="$dispatch('open-modal', 'create-account-modal')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Add New Account
            </button>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ editingAccount: null }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
             @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    
                    @foreach($types as $typeKey => $typeLabel)
                        <div class="mb-8" x-data="{ open: true }">
                            <div class="flex items-center justify-between bg-gray-100 dark:bg-gray-700 px-4 py-2 cursor-pointer" @click="open = !open">
                                <h3 class="text-lg font-bold text-gray-800 dark:text-white">{{ $typeLabel }}</h3>
                                <svg :class="{'rotate-180': open, 'rotate-0': !open}" class="w-5 h-5 transition-transform text-gray-600 transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                            
                            <div x-show="open" class="mt-2" x-transition>
                                <table class="min-w-full">
                                    <thead>
                                        <tr>
                                            <th class="text-left py-2 px-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Code</th>
                                            <th class="text-left py-2 px-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Name</th>
                                            <th class="text-left py-2 px-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Parent</th>
                                            <th class="text-left py-2 px-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Status</th>
                                            <th class="text-right py-2 px-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                        @foreach($accounts->where('type', $typeKey) as $account)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-700">
                                                <td class="px-4 py-2 text-sm font-mono text-gray-600 dark:text-gray-300">{{ $account->code }}</td>
                                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-white font-medium">
                                                    {{ $account->name }}
                                                </td>
                                                <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $account->parent ? $account->parent->code . ' - ' . $account->parent->name : '-' }}
                                                </td>
                                                <td class="px-4 py-2">
                                                    <span class="inline-flex text-xs leading-5 font-semibold rounded-full {{ $account->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                                        {{ $account->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-2 text-right text-sm">
                                                    <button 
                                                        x-data=""
                                                        x-on:click="$dispatch('open-modal', 'edit-account-modal-{{ $account->id }}')"
                                                        class="text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 mr-3">Edit</button>
                                                </td>
                                            </tr>
                                            
                                            <!-- Edit Modal (Inside Loop for simplicity, optimized apps would load dynamic component) -->
                                              <x-modal name="edit-account-modal-{{ $account->id }}" :show="false">
                                                <form method="POST" action="{{ route('accounts.coa.update', $account) }}" class="p-6">
                                                    @csrf
                                                    @method('PUT')
                                                    
                                                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                                        Edit Account: {{ $account->name }}
                                                    </h2>
                                        
                                                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                                                        <div>
                                                            <x-input-label for="code_{{ $account->id }}" value="Account Code" />
                                                            <x-text-input id="code_{{ $account->id }}" name="code" type="text" class="mt-1 block w-full" value="{{ $account->code }}" required />
                                                        </div>
                                                        <div>
                                                            <x-input-label for="name_{{ $account->id }}" value="Account Name" />
                                                            <x-text-input id="name_{{ $account->id }}" name="name" type="text" class="mt-1 block w-full" value="{{ $account->name }}" required />
                                                        </div>
                                                        <div>
                                                            <x-input-label for="type_{{ $account->id }}" value="Account Type" />
                                                            <select id="type_{{ $account->id }}" name="type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                                                @foreach($types as $key => $label)
                                                                    <option value="{{ $key }}" {{ $account->type == $key ? 'selected' : '' }}>{{ $label }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <x-input-label for="parent_id_{{ $account->id }}" value="Parent Account" />
                                                            <select id="parent_id_{{ $account->id }}" name="parent_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                                                <option value="">None</option>
                                                                @foreach($accounts->where('id', '!=', $account->id) as $parent)
                                                                    <option value="{{ $parent->id }}" {{ $account->parent_id == $parent->id ? 'selected' : '' }}>{{ $parent->code }} - {{ $parent->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-span-2">
                                                            <x-input-label for="description_{{ $account->id }}" value="Description" />
                                                            <textarea id="description_{{ $account->id }}" name="description" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ $account->description }}</textarea>
                                                        </div>
                                                         <div class="flex items-center space-x-4">
                                                            <label class="flex items-center">
                                                                <input type="checkbox" name="is_reconcilable" value="1" {{ $account->is_reconcilable ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                                                <span class="ml-2 text-sm text-gray-600">Reconcilable</span>
                                                            </label>
                                                             <label class="flex items-center">
                                                                <input type="hidden" name="is_active" value="0">
                                                                <input type="checkbox" name="is_active" value="1" {{ $account->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                                                <span class="ml-2 text-sm text-gray-600">Active</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                        
                                                    <div class="mt-6 flex justify-between">
                                                        @if(!$account->journalItems()->exists() && !$account->children()->exists())
                                                            <button type="submit" formaction="{{ route('accounts.coa.destroy', $account) }}" name="_method" value="DELETE" class="text-red-600 hover:text-red-900 font-bold" onclick="return confirm('Are you sure?')">
                                                                Delete
                                                            </button>
                                                        @else
                                                            <span class="text-xs text-gray-500 dark:text-gray-400 italic">Cannot delete (in use)</span>
                                                        @endif

                                                        <div class="flex justify-end">
                                                            <x-secondary-button x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                                                            <x-primary-button class="ml-3">Update Account</x-primary-button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </x-modal>

                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                    
                </div>
            </div>
        </div>
        
        <!-- Create Modal -->
        <x-modal name="create-account-modal" :show="false">
             <form method="POST" action="{{ route('accounts.coa.store') }}" class="p-6">
                @csrf
                
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Add New Account
                </h2>
    
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="create_code" value="Account Code" />
                        <x-text-input id="create_code" name="code" type="text" class="mt-1 block w-full" required placeholder="e.g. 1001" />
                    </div>
                    <div>
                        <x-input-label for="create_name" value="Account Name" />
                        <x-text-input id="create_name" name="name" type="text" class="mt-1 block w-full" required placeholder="e.g. Petty Cash" />
                    </div>
                    <div>
                        <x-input-label for="create_type" value="Account Type" />
                        <select id="create_type" name="type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @foreach($types as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="create_parent_id" value="Parent Account" />
                         <select id="create_parent_id" name="parent_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">None</option>
                            @foreach($accounts as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->code }} - {{ $parent->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-2">
                        <x-input-label for="create_description" value="Description" />
                        <textarea id="create_description" name="description" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    </div>
                    <div class="flex items-center space-x-4">
                         <label class="flex items-center">
                            <input type="checkbox" name="is_reconcilable" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-600">Reconcilable</span>
                        </label>
                            <label class="flex items-center">
                                <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-600">Active</span>
                        </label>
                    </div>
                </div>
    
                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        Cancel
                    </x-secondary-button>
    
                    <x-primary-button class="ml-3">
                        Create Account
                    </x-primary-button>
                </div>
            </form>
        </x-modal>

    </div>
</x-app-layout>
