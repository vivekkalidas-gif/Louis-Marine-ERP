<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <h2 class="font-black text-2xl text-slate-900 dark:text-white leading-tight tracking-tight">
                    View Job: {{ $job->job_number }}
                </h2>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold
                    {{ $job->status === 'completed' || $job->status === 'Completed' ? 'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800/60' : '' }}
                    {{ $job->status === 'in-progress' || $job->status === 'In Progress' ? 'bg-amber-100 dark:bg-amber-950/80 text-amber-700 dark:text-amber-300 border border-amber-300 dark:border-amber-800/60' : '' }}
                    {{ $job->status === 'requested' || $job->status === 'Requested' ? 'bg-sky-100 dark:bg-sky-950/80 text-sky-700 dark:text-sky-300 border border-sky-300 dark:border-sky-800/60' : '' }}
                    {{ $job->status === 'overdue' || $job->status === 'Overdue' ? 'bg-rose-100 dark:bg-rose-950/80 text-rose-700 dark:text-rose-300 border border-rose-300 dark:border-rose-800/60' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700' }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                    {{ ucfirst($job->status) }}
                </span>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('engineer.jobs.pdf', $job->id) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs rounded-xl shadow-md shadow-blue-500/20 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <span>View PDF</span>
                </a>

                <a href="{{ route('engineer.jobs.pdf', [$job->id, 'download' => 1]) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 font-bold text-xs rounded-xl shadow-xs transition-all">
                    <svg class="w-4 h-4 text-brand-500 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Download PDF</span>
                </a>

                @if($job->status !== 'Completed' && $job->status !== 'completed')
                    <a href="{{ route('engineer.jobs.report.create', $job->id) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs rounded-xl shadow-md shadow-emerald-500/20 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>CREATE SERVICE REPORT</span>
                    </a>
                @endif

                @if($job->activeReport)
                    <a href="{{ route('engineer.reports.show', $job->activeReport->id) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-xl shadow-xs border border-slate-700 transition-all">
                        <span>View Report</span>
                    </a>

                    @if($job->status === 'Completed' || $job->status === 'completed') 
                        <a href="{{ route('engineer.reports.edit', $job->activeReport->id) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs rounded-xl shadow-xs transition-all">
                            <span>Edit Report</span>
                        </a>
                    @endif

                    <a href="{{ route('engineer.reports.download', $job->activeReport->id) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs rounded-xl shadow-xs transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Report PDF</span>
                    </a>
                @endif

                @if($job->job_type === 'Workshop Service' && $job->delivery_note_number)
                    <a href="{{ route('engineer.jobs.delivery-note', $job->id) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs rounded-xl shadow-xs transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Delivery Note</span>
                    </a>
                @endif

                <a href="{{ route('admin.jobs.edit', $job->id) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-amber-600/10 hover:bg-amber-600 text-amber-600 dark:text-amber-400 hover:text-white border border-amber-500/30 font-bold text-xs rounded-xl shadow-xs transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>Edit Job</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Job Overview Card -->
            <div class="theme-card">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-bold text-marine-900 dark:text-marine-100 mb-4">{{ $job->title }}</h3>
                        <div class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                            <p><span class="font-medium">Job Type:</span> {{ $job->job_type }}</p>
                            <p><span class="font-medium">Priority:</span> <span class="{{ $job->priority === 'Emergency' ? 'text-red-600 font-bold' : '' }}">{{ $job->priority }}</span></p>
                            <p><span class="font-medium">Service Category:</span> {{ $job->service_category }}</p>
                            <p><span class="font-medium">Scheduled:</span> {{ $job->scheduled_at ? $job->scheduled_at->format('d M Y, H:i') : 'Not Scheduled' }}</p>
                        </div>
                    </div>
                    <div>
                         <h4 class="font-bold text-marine-900 dark:text-marine-100 mb-2">Client Details</h4>
                         <div class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                            <p><span class="font-medium">Customer:</span> {{ $job->customer->company_name }}</p>
                            <p><span class="font-medium">Vessel:</span> {{ $job->vessel->name ?? 'N/A' }}</p>
                            <p><span class="font-medium">Contact:</span> {{ $job->requestedBy->name ?? 'N/A' }}</p>
                            <p><span class="font-medium">Location:</span> {{ $job->location_details ?? 'N/A' }}</p>
                         </div>
                    </div>
                </div>

                <div class="mt-6 border-t dark:border-marine-800 pt-6">
                    <h4 class="font-bold text-marine-900 dark:text-marine-100 mb-2">Scope of Work</h4>
                    <p class="text-gray-700 dark:text-gray-300 text-sm whitespace-pre-line">{{ $job->scope_of_work }}</p>
                </div>

                @if($job->special_instructions)
                <div class="mt-4 bg-yellow-50 dark:bg-yellow-900 p-4 rounded border dark:border-yellow-700">
                    <h4 class="font-bold text-yellow-800 dark:text-yellow-100 mb-1">Special Instructions</h4>
                    <p class="text-yellow-700 dark:text-yellow-200 text-sm">{{ $job->special_instructions }}</p>
                </div>
                @endif

                
                @if($job->items->count() > 0 || $job->equipment->count() > 0)
                <div class="mt-6 border-t dark:border-marine-800 pt-6">
                    <h4 class="font-bold text-marine-900 dark:text-marine-100 mb-2">Received Items (Delivery Note)</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-gray-50 dark:bg-marine-800 dark:text-gray-200">
                                <tr>
                                    <th class="px-4 py-2">Item Name</th>
                                    <th class="px-4 py-2">Qty</th>
                                    <th class="px-4 py-2">Description / Serial</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Delivery Note Equipment (Master Data) -->
                                @foreach($job->equipment as $equip)
                                <tr class="bg-white dark:bg-gray-800 border-b dark:bg-marine-900 dark:border-marine-800 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-marine-800">
                                    <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">
                                        {{ $equip->equipmentItem->name ?? 'Unknown Item' }}
                                        <span class="text-xs text-blue-600 bg-blue-100 px-1 rounded ml-2">Equipment</span>
                                    </td>
                                    <td class="px-4 py-2">{{ $equip->quantity }}</td>
                                    <td class="px-4 py-2">{{ $equip->condition_notes ?? '-' }}</td>
                                </tr>
                                @endforeach

                                <!-- Custom Job Items (Ad-hoc) -->
                                @foreach($job->items as $item)
                                <tr class="bg-white dark:bg-gray-800 border-b dark:bg-marine-900 dark:border-marine-800 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-marine-800">
                                    <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">
                                        {{ $item->name }}
                                        <span class="text-xs text-green-600 bg-green-100 px-1 rounded ml-2">Manual</span>
                                    </td>
                                    <td class="px-4 py-2">{{ $item->quantity }}</td>
                                    <td class="px-4 py-2">{{ $item->description }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Assignments -->
                <div class="theme-card">
                    <h3 class="text-lg font-bold text-marine-900 dark:text-marine-100 mb-4">Team Assignment</h3>
                    <ul class="space-y-3">
                         @foreach($job->assignments as $assignment)
                        <li class="flex items-center justify-between p-3 bg-gray-50 dark:bg-marine-800 rounded">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-marine-200 dark:bg-marine-600 rounded-full flex items-center justify-center text-marine-800 dark:text-marine-100 font-bold text-xs">
                                    {{ substr($assignment->user->name, 0, 2) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $assignment->user->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ ucfirst($assignment->user?->roles?->first()?->name ?? 'Staff') }}</p>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Materials -->
                <div class="theme-card">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-marine-900 dark:text-marine-100">Materials</h3>
                         <a href="{{ route('engineer.jobs.material-request', $job->id) }}" class="text-sm bg-blue-600 hover:bg-blue-700 text-white py-1 px-3 rounded shadow-sm">
                            + Request Material
                        </a>
                    </div>
                    @if($job->materials->count() > 0)
                        <ul class="space-y-2">
                             @foreach($job->materials as $mat)
                            <li class="flex justify-between text-sm p-2 bg-gray-50 dark:bg-marine-800 rounded">
                                <span class="text-gray-800 dark:text-gray-200">{{ $mat->item_name }}</span>
                                <span class="font-bold text-gray-600 dark:text-gray-400">x{{ $mat->quantity }}</span>
                            </li>
                             @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400 italic">No materials requested.</p>
                    @endif
                </div>
            </div>

            <!-- Documents Loop -->
            <div class="theme-card">
                <h3 class="text-lg font-bold text-marine-900 dark:text-marine-100 mb-4">Attachments & Documents</h3>
                 <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($job->documents as $doc)
                    <div class="flex items-center p-3 border dark:border-marine-800 rounded hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-marine-800 transition">
                        <svg class="w-8 h-8 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        <div class="overflow-hidden">
                            <p class="text-sm font-medium text-marine-700 dark:text-marine-300 truncate">{{ $doc->title }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ strtoupper($doc->file_type) }} • {{ round($doc->file_size / 1024) }} KB</p>
                        </div>
                    </div>
                    @endforeach
                    @if($job->documents->count() === 0)
                        <p class="text-sm text-gray-500 dark:text-gray-400 italic col-span-3">No documents attached.</p>
                    @endif
                 </div>
            </div>
            
             <div class="flex justify-end">
                <a href="{{ route('engineer.dashboard') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded dark:bg-gray-700 dark:hover:bg-gray-600">
                    Back to Dashboard
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
