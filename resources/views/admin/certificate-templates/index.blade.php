<x-app-layout>
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Certificate Templates</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Manage certificate templates for different equipment categories</p>
        </div>
        <div class="flex gap-2" x-data>
            <a href="{{ route('admin.certificate-templates.export') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold flex items-center gap-2" title="Export to CSV">
                <span>Export</span>
            </a>
            
            <button @click="$dispatch('open-import-modal')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold flex items-center gap-2" title="Import from CSV">
                <span>Import</span>
            </button>

            <a href="{{ route('admin.certificate-templates.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-semibold transition flex items-center gap-2">
                <i class="fas fa-plus"></i>
                <span>New Template</span>
            </a>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 px-4 py-3 rounded mb-6">
        {{ session('success') }}
    </div>
    @endif

    <!-- Error Message -->
    @if(session('error'))
    <div class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-200 px-4 py-3 rounded mb-6">
        {{ session('error') }}
    </div>
    @endif

    <!-- Templates Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($templates as $template)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-lg transition border {{ $template->is_active ? 'border-green-200 dark:border-green-700' : 'border-gray-200 dark:border-gray-700' }}">
            <div class="p-6">
                <!-- Header -->
                <div class="flex justify-between items-start mb-4">
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1">{{ $template->name }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $template->equipmentCategory->name }}</p>
                    </div>
                    <div class="flex gap-2">
                        @if($template->is_default)
                        <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs px-2 py-1 rounded">Default</span>
                        @endif
                        @if($template->is_active)
                        <span class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-xs px-2 py-1 rounded">Active</span>
                        @else
                        <span class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 text-xs px-2 py-1 rounded">Inactive</span>
                        @endif
                    </div>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-2 gap-4 mb-4 py-4 border-t border-b border-gray-200 dark:border-gray-700">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $template->sections_count }}</div>
                        <div class="text-xs text-gray-600 dark:text-gray-400">Sections</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $template->sections->sum(fn($s) => $s->fields->count()) }}</div>
                        <div class="text-xs text-gray-600 dark:text-gray-400">Fields</div>
                    </div>
                </div>

                <!-- Meta -->
                <div class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                    <div>Updated: {{ $template->updated_at->diffForHumans() }}</div>
                </div>

                <!-- Actions -->
                <div class="flex gap-2">
                    <a href="{{ route('admin.certificate-templates.edit', $template) }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center px-4 py-2 rounded text-sm font-semibold transition">
                        Edit
                    </a>
                    <a href="{{ route('admin.certificate-templates.show', $template) }}" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white text-center px-4 py-2 rounded text-sm font-semibold transition">
                        View
                    </a>
                </div>

                <!-- More Actions -->
                <div class="mt-3 flex gap-2">
                    <form action="{{ route('admin.certificate-templates.toggle', $template) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full bg-{{ $template->is_active ? 'yellow' : 'green' }}-100 dark:bg-{{ $template->is_active ? 'yellow' : 'green' }}-900 hover:bg-{{ $template->is_active ? 'yellow' : 'green' }}-200 dark:hover:bg-{{ $template->is_active ? 'yellow' : 'green' }}-800 text-{{ $template->is_active ? 'yellow' : 'green' }}-800 dark:text-{{ $template->is_active ? 'yellow' : 'green' }}-200 px-4 py-2 rounded text-sm font-semibold transition">
                            {{ $template->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                    
                    @if(!$template->is_default)
                    <form action="{{ route('admin.certificate-templates.set-default', $template) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full bg-blue-100 dark:bg-blue-900 hover:bg-blue-200 dark:hover:bg-blue-800 text-blue-800 dark:text-blue-200 px-4 py-2 rounded text-sm font-semibold transition">
                            Set Default
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
            <i class="fas fa-file-alt text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
            <h3 class="text-xl font-bold text-gray-700 dark:text-gray-300 mb-2">No Templates Yet</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">Get started by creating your first certificate template</p>
            <a href="{{ route('admin.certificate-templates.create') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                <i class="fas fa-plus mr-2"></i>Create Template
            </a>
        </div>
        @endforelse
    </div>
    </div>
    
    <!-- Import Modal -->
    <div x-data="{ open: false }"
         @open-import-modal.window="open = true"
         x-show="open" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        
        <div class="fixed inset-0 bg-gray-50 dark:bg-gray-7000 bg-opacity-75 transition-opacity" @click="open = false"></div>

        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <form action="{{ route('admin.certificate-templates.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="bg-white dark:bg-gray-800 px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white" id="modal-title">Import Certificate Templates</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                        Upload a CSV file to import certificate templates. 
                                        <a href="{{ route('admin.certificate-templates.template') }}" class="text-blue-600 dark:text-blue-400 hover:underline">Download Template</a>
                                    </p>
                                    
                                    <div class="mt-4">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Choose CSV File</label>
                                        <input type="file" name="file" accept=".csv" required class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400
                                            file:mr-4 file:py-2 file:px-4
                                            file:rounded-full file:border-0
                                            file:text-sm file:font-semibold
                                            file:bg-blue-50 file:text-blue-700
                                            hover:file:bg-blue-100
                                            dark:file:bg-gray-700 dark:file:text-gray-300
                                        "/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="submit" class="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:ml-3 sm:w-auto">Import</button>
                        <button type="button" @click="open = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-700 sm:mt-0 sm:w-auto">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
