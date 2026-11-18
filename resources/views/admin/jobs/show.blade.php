<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs text-brand-600 dark:text-brand-400 font-semibold mb-1">
                    <a href="{{ route('admin.jobs.index') }}" class="hover:underline">Service Jobs</a>
                    <span>/</span>
                    <span class="text-slate-500 dark:text-slate-400">Job Details</span>
                </div>
                <div class="flex items-center gap-3">
                    <h2 class="font-black text-2xl text-slate-900 dark:text-white leading-tight tracking-tight">
                        {{ $job->job_number }} - {{ $job->customer->company_name ?? 'N/A' }}
                    </h2>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold
                        {{ $job->status === 'Completed' || $job->status === 'completed' ? 'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800/60' : '' }}
                        {{ $job->status === 'Assigned' || $job->status === 'assigned' ? 'bg-amber-100 dark:bg-amber-950/80 text-amber-700 dark:text-amber-300 border border-amber-300 dark:border-amber-800/60' : '' }}
                        {{ $job->status === 'Open' || $job->status === 'open' ? 'bg-blue-100 dark:bg-blue-950/80 text-blue-700 dark:text-blue-300 border border-blue-300 dark:border-blue-800/60' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700' }}">
                        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                        {{ ucfirst($job->status) }}
                    </span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                 <a href="{{ route('admin.jobs.pdf', $job->id) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs rounded-xl shadow-md shadow-blue-500/20 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <span>View PDF</span>
                 </a>
                 <a href="{{ route('admin.jobs.pdf', [$job->id, 'download' => 1]) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 font-bold text-xs rounded-xl shadow-xs transition-all">
                    <svg class="w-4 h-4 text-brand-500 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Download PDF</span>
                 </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ tab: 'overview' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Job Header Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Scope of Work</h3>
                            <p class="mt-1 text-lg text-gray-900 dark:text-gray-100">{{ $job->scope_of_work }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Vessel</h3>
                            <p class="mt-1 text-md text-gray-900 dark:text-gray-100">{{ $job->vessel->name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $job->vessel->type ?? '' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Assigned Engineer</h3>
                            <div x-data="{ editingEngineer: false }">
                                @if($job->engineer)
                                    <div x-show="!editingEngineer" class="flex items-center justify-between mt-1">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold mr-2">
                                                {{ substr($job->engineer->name, 0, 1) }}
                                            </div>
                                            <span class="font-bold">{{ $job->engineer->name }}</span>
                                        </div>
                                        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
                                            <button @click="editingEngineer = true" class="text-xs text-blue-600 hover:underline ml-2">Change</button>
                                        @endif
                                    </div>
                                @endif

                                <div x-show="editingEngineer || !{{ $job->engineer ? 'true' : 'false' }}" class="mt-1">
                                    <form action="{{ route('admin.jobs.update', $job) }}" method="POST" class="flex">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="{{ $job->status }}"> 
                                        <select name="assigned_engineer_id" class="text-sm border-gray-300 rounded-l-md w-full">
                                            <option value="">Select Engineer...</option>
                                            @foreach($engineers as $eng)
                                                <option value="{{ $eng->id }}" {{ $job->assigned_engineer_id == $eng->id ? 'selected' : '' }}>{{ $eng->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="bg-indigo-600 text-white px-3 py-1 rounded-r-md text-sm">Save</button>
                                        <button type="button" @click="editingEngineer = false" x-show="{{ $job->engineer ? 'true' : 'false' }}" class="ml-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300 text-sm">&times;</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </div>

            <!-- Received Items (Delivery Note) Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Received Items (Delivery Note)</h3>
                    @if($job->items->count() > 0 || $job->equipment->count() > 0)
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Item Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Quantity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description / Serial</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                                <!-- Equipment (Master) -->
                                @foreach($job->equipment as $equip)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $equip->equipmentItem->name ?? 'Unknown Item' }}
                                            <span class="text-xs text-blue-600 bg-blue-100 px-1 rounded ml-2">Equipment</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $equip->quantity }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $equip->condition_notes ?? '-' }}</td>
                                    </tr>
                                @endforeach
                                
                                <!-- Custom Items -->
                                @foreach($job->items as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $item->name }}
                                            <span class="text-xs text-green-600 bg-green-100 px-1 rounded ml-2">Manual</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $item->quantity }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $item->description }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-gray-500 dark:text-gray-400 italic">No items recorded.</p>
                    @endif
                </div>
            </div>

            <!-- Tabs -->
            <div class="border-b border-gray-200 mb-6">
                <nav class="-mb-px flex space-x-8">
                    <button @click="tab = 'overview'" :class="{'border-indigo-500 text-indigo-600': tab === 'overview', 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300 hover:border-gray-300': tab !== 'overview'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Reports & Timeline
                    </button>
                    <button @click="tab = 'documents'" :class="{'border-indigo-500 text-indigo-600': tab === 'documents', 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300 hover:border-gray-300': tab !== 'documents'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Documents
                    </button>
                </nav>
            </div>

            <!-- Overview / Reports Tab -->
            <div x-show="tab === 'overview'">
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Service Reports</h3>
                    </div>
                    
                    @if($job->reports->count() > 0)
                        <ul class="space-y-4">
                            @foreach($job->reports as $report)
                                <li class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md border border-gray-200">
                                    <div class="flex justify-between">
                                        <span class="font-bold text-gray-700 dark:text-gray-300">{{ $report->report_date->format('d M Y') }}</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $report->status }}</span>
                                    </div>
                                    <p class="mt-2 text-gray-600">{{ $report->work_description }}</p>
                                    <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                        <strong>Technician:</strong> {{ $report->engineer->name }} | <strong>Hours:</strong> {{ $report->hours_worked }}
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500 dark:text-gray-400 italic">No field reports filed yet.</p>
                    @endif
                </div>
            </div>

            <!-- Documents Tab -->
            <div x-show="tab === 'documents'" style="display: none;">
                 <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Job Documents</h3>
                    
                    <!-- Upload Form -->
                    <form action="{{ route('admin.jobs.documents.store', $job) }}" method="POST" enctype="multipart/form-data" class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded border border-dashed border-gray-300">
                        @csrf
                        <div class="flex gap-4 items-end">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Document Title</label>
                                <input type="text" name="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">File</label>
                                <input type="file" name="file" class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400" required>
                            </div>
                            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-md hover:bg-gray-700">Upload</button>
                        </div>
                    </form>

                    <ul class="divide-y divide-gray-200">
                        @foreach($job->documents as $doc)
                            <li class="py-3 flex justify-between items-center">
                                <div class="flex items-center">
                                    <svg class="h-6 w-6 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $doc->title }}</span>
                                    <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">({{ strtoupper(pathinfo($doc->file_path, PATHINFO_EXTENSION)) }})</span>
                                </div>
                                <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 text-sm">Download</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
