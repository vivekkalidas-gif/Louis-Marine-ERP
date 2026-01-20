@extends('layouts.app')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-full mx-auto">

    <!-- Page Header -->
    <!-- Warning for Expiring Equipment -->
    @if(isset($expiringCount) && $expiringCount > 0)
    <div class="mb-6 bg-orange-50 dark:bg-orange-900/20 border-l-4 border-orange-500 p-4 rounded shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-orange-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-orange-700 dark:text-orange-300">
                        <span class="font-bold">Attention Needed:</span> 
                        {{ $expiringCount }} equipment item{{ $expiringCount > 1 ? 's are' : ' is' }} due for calibration within the next 30 days.
                    </p>
                </div>
            </div>
            <div class="ml-4 flex-shrink-0">
                <a href="{{ route('engineer.equipment.index', ['status' => 'expiring_soon']) }}" class="font-medium text-orange-600 dark:text-orange-400 hover:text-orange-500 underline text-sm">
                    View Items
                </a>
            </div>
        </div>
    </div>
    @endif

    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-[#0b1c3c] dark:text-gray-100 font-bold">Equipment List</h1>
        </div>
        <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
            <!-- Export -->
            <a href="{{ route('engineer.equipment.export') }}" class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 text-gray-600 dark:text-gray-300 font-medium py-2 px-4 rounded shadow flex items-center gap-2" title="Export to CSV">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                <span class="hidden sm:inline">Export</span>
            </a>

            <!-- PDF -->
            <a href="{{ route('engineer.equipment.pdf') }}" class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 text-gray-600 dark:text-gray-300 font-medium py-2 px-4 rounded shadow flex items-center gap-2" title="Download PDF">
                <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                <span class="hidden sm:inline">PDF</span>
            </a>

            <!-- Import -->
            <button onclick="document.getElementById('import-modal').showModal()" class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 text-gray-600 dark:text-gray-300 font-medium py-2 px-4 rounded shadow flex items-center gap-2" title="Import from CSV">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                <span class="hidden sm:inline">Import</span>
            </button>

            <!-- Add Equipment -->
            <a href="{{ route('engineer.equipment.create') }}" class="btn bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded shadow flex items-center gap-2">
                <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16">
                    <path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                </svg>
                <span>Add Equipment</span>
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
        <form action="{{ route('engineer.equipment.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4 w-full">
            <!-- Search -->
            <div class="relative flex-grow">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </div>
                <input class="form-input pl-10 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md sm:text-sm py-2" type="search" name="search" placeholder="Search by name, code, brand..." value="{{ request('search') }}" />
            </div>

            <!-- Type Filter -->
            <div class="sm:w-48 text-left">
                <select name="type_id" class="form-select w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md sm:text-sm py-2" onchange="this.form.submit()">
                    <option value="all">All Types</option>
                    @foreach($types as $type)
                    <option value="{{ $type->id }}" {{ request('type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div class="sm:w-48 text-left">
                <select name="status" class="form-select w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md sm:text-sm py-2" onchange="this.form.submit()">
                    <option value="all">All Status</option>
                    <option value="valid" {{ request('status') == 'valid' ? 'selected' : '' }}>Valid</option>
                    <option value="expiring_soon" {{ request('status') == 'expiring_soon' ? 'selected' : '' }}>Expiring Soon</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                    <option value="not_required" {{ request('status') == 'not_required' ? 'selected' : '' }}>Not Required</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Main Content -->
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg border border-gray-200 dark:border-gray-700">
        @if($equipment->isEmpty())
            <!-- Empty State -->
            <div class="flex flex-col items-center justify-center py-16 px-4">
                <div class="bg-gray-50 dark:bg-gray-700 rounded-full p-4 mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-1">No equipment found</h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm mb-6 text-center max-w-sm">
                    Get started by adding your first piece of equipment to track calibration and maintenance.
                </p>
                <a href="{{ route('engineer.equipment.create') }}" class="btn bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded shadow transition duration-150 ease-in-out">
                    + Add New Equipment
                </a>
            </div>
        @else
            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="table-auto w-full">
                    <thead class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-4 py-3 whitespace-nowrap"><div class="font-semibold text-left">Equipment Name</div></th>
                            <th class="px-4 py-3 whitespace-nowrap"><div class="font-semibold text-left">Type</div></th>
                            <th class="px-4 py-3 whitespace-nowrap"><div class="font-semibold text-left">Serial No</div></th>
                            <th class="px-4 py-3 whitespace-nowrap"><div class="font-semibold text-left">Calibration Expiry</div></th>
                            <th class="px-4 py-3 whitespace-nowrap"><div class="font-semibold text-left">Status</div></th>
                            <th class="px-4 py-3 whitespace-nowrap"><div class="font-semibold text-center">Actions</div></th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($equipment as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-700/50 transition duration-150">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="font-medium text-gray-800 dark:text-gray-200">{{ $item->name }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">
                                    {{ $item->type->name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-gray-600 font-mono text-xs">
                                {{ $item->code ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($item->calibration_required)
                                <div class="text-gray-800 dark:text-gray-200">{{ $item->calibration_expiry_date ? \Carbon\Carbon::parse($item->calibration_expiry_date)->format('d M, Y') : 'Not Set' }}</div>
                                @else
                                    <span class="text-gray-400 dark:text-gray-500 dark:text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if(!$item->calibration_required)
                                    <div class="inline-flex font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-full text-center px-2.5 py-0.5 text-xs">Not Required</div>
                                @elseif($item->status === 'valid')
                                    <div class="inline-flex font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full text-center px-2.5 py-0.5 text-xs border border-green-200 dark:border-green-800">Valid</div>
                                @elseif($item->status === 'expiring_soon')
                                    <div class="inline-flex font-medium bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 rounded-full text-center px-2.5 py-0.5 text-xs border border-orange-200 dark:border-orange-800">Expiring Soon</div>
                                @elseif($item->status === 'expired')
                                    <div class="inline-flex font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-full text-center px-2.5 py-0.5 text-xs border border-red-200 dark:border-red-800">Expired</div>
                                @else 
                                    <div class="inline-flex font-medium bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded-full text-center px-2.5 py-0.5 text-xs">Pending</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-3">
                                    <!-- View Details -->
                                    <button class="text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition" title="View Details" onclick="document.getElementById('details-modal-{{ $item->id }}').showModal()">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>

                                    <!-- Edit -->
                                    <a href="{{ route('engineer.equipment.edit', $item->id) }}" class="text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>

                                    <!-- Delete -->
                                    <form action="{{ route('engineer.equipment.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this equipment?');" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition pt-1" title="Delete">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>

                                    <!-- Prepare Dispatch -->
                                    @if($item->calibration_required)
                                    <div class="w-px h-4 bg-gray-300 mx-1"></div>
                                    
                                    <a href="{{ route('admin.dispatch.create', ['equipment_id' => $item->id, 'type' => 'calibration']) }}" class="text-gray-500 dark:text-gray-400 hover:text-orange-600 dark:hover:text-orange-400 transition" title="Prepare Dispatch">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    </a>
                                    @endif
                                </div>

                                <!-- Details Modal -->
                                <dialog id="details-modal-{{ $item->id }}" class="modal p-0 rounded-lg shadow-xl w-full max-w-lg backdrop:bg-gray-900/50">
                                    <div class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden">
                                        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-700/50">
                                            <h3 class="font-semibold text-gray-800 dark:text-gray-200">Equipment Details</h3>
                                            <button onclick="document.getElementById('details-modal-{{ $item->id }}').close()" class="text-gray-400 hover:text-gray-500 dark:text-gray-400 dark:hover:text-gray-300">
                                                <svg class="w-4 h-4 fill-current" viewBox="0 0 16 16"><path d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.414 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z" /></svg>
                                            </button>
                                        </div>
                                        <div class="p-5 space-y-4">
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Name</label>
                                                    <div class="text-sm font-medium text-gray-800 dark:text-gray-200 text-wrap">{{ $item->name }}</div>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Serial No</label>
                                                    <div class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $item->code ?? '-' }}</div>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Type</label>
                                                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ $item->type->name ?? '-' }}</div>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Status</label>
                                                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ ucfirst(str_replace('_', ' ', $item->status)) }}</div>
                                                </div>
                                                <div class="col-span-2">
                                                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Description</label>
                                                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ $item->description ?? 'No description provided.' }}</div>
                                                </div>
                                            </div>

                                            @if($item->calibration_required)
                                            <div class="border-t border-gray-100 pt-4">
                                                <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200 mb-2">Calibration Certificate</h4>
                                                @if($item->certificate_path)
                                                    <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700 p-3 rounded border border-gray-200">
                                                        <div class="flex items-center">
                                                            <svg class="w-6 h-6 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                                            <div class="text-sm">
                                                                <div class="font-medium text-gray-800 dark:text-gray-200">Certificate.pdf</div>
                                                                <div class="text-xs text-gray-500 dark:text-gray-400">Expires: {{ \Carbon\Carbon::parse($item->calibration_expiry_date)->format('d M, Y') }}</div>
                                                            </div>
                                                        </div>
                                                        <a href="{{ Storage::url($item->certificate_path) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm font-medium underline">View PDF</a>
                                                    </div>
                                                @else
                                                    <div class="text-sm text-gray-500 dark:text-gray-400 italic mb-2">No certificate uploaded.</div>
                                                @endif

                                                <form action="{{ route('engineer.equipment.upload-certificate', $item->id) }}" method="POST" enctype="multipart/form-data" class="mt-3 bg-blue-50 p-3 rounded border border-blue-100">
                                                    @csrf
                                                    <div class="text-xs font-semibold text-blue-800 mb-2">Upload New Certificate</div>
                                                    <div class="flex flex-col gap-2">
                                                        <input type="file" name="certificate" accept=".pdf,.jpg,.png" class="block w-full text-xs text-gray-500 dark:text-gray-400 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700" required>
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-xs text-gray-600 whitespace-nowrap">New Expiry:</span>
                                                            <input type="date" name="expiry_date" class="form-input py-1 px-2 text-xs w-full border-gray-300 rounded" required>
                                                        </div>
                                                    </div>
                                                    <button type="submit" class="mt-2 w-full btn-xs bg-blue-600 hover:bg-blue-700 text-white py-1 rounded shadow-sm text-xs uppercase tracking-wide">Update Certificate</button>
                                                </form>
                                            </div>
                                            @endif
                                            
                                            <div class="flex justify-end pt-2">
                                                <button onclick="document.getElementById('details-modal-{{ $item->id }}').close()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 dark:text-gray-200 rounded text-sm font-medium">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </dialog>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($equipment->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                {{ $equipment->links() }}
            </div>
            @endif
        @endif
    </div>
</div>
    <!-- Import Modal -->
    <dialog id="import-modal" class="modal p-0 rounded-lg shadow-xl w-full max-w-md backdrop:bg-gray-900/50">
        <div class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-700/50">
                <h3 class="font-semibold text-gray-800 dark:text-gray-200">Import Equipment</h3>
                <button onclick="document.getElementById('import-modal').close()" class="text-gray-400 hover:text-gray-500 dark:text-gray-400 dark:hover:text-gray-300">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 16 16"><path d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.414 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z" /></svg>
                </button>
            </div>
            <form action="{{ route('engineer.equipment.import') }}" method="POST" enctype="multipart/form-data" class="p-5">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select CSV File</label>
                    <input type="file" name="file" accept=".csv,.txt" class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-gray-700 dark:file:text-gray-300" required>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Upload a CSV file with columns: Category, Name, Brand, Model, Unit...</p>
                    <a href="{{ route('engineer.equipment.template') }}" class="text-sm text-blue-600 hover:underline mt-1 inline-block">Download Template</a>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('import-modal').close()" class="btn-sm bg-gray-100 hover:bg-gray-200 text-gray-800 dark:text-gray-200">Cancel</button>
                    <button type="submit" class="btn-sm bg-blue-600 hover:bg-blue-700 text-white">Import Items</button>
                </div>
            </form>
        </div>
    </dialog>
@endsection
