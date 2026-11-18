<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-xl text-slate-900 dark:text-white leading-tight tracking-tight">
                    {{ __('Edit Dispatch Note') }}: {{ $dispatchNote->dispatch_number }}
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Modify gate pass details, recipient information, and dispatched equipment items</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.dispatch.index') }}" class="btn-ghost text-xs gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Back to List</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6" x-data="dispatchForm()">
        <div class="max-w-7xl mx-auto space-y-6">

            <!-- Flash Error / Validation Banner -->
            @if($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800/80 text-rose-800 dark:text-rose-200 text-xs space-y-1">
                <p class="font-bold flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Please correct the following errors:</span>
                </p>
                <ul class="list-disc pl-6 space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- 70/30 LEFT-RIGHT SAAS SPLIT ARCHITECTURE -->
            <form action="{{ route('admin.dispatch.update', ['dispatch_note' => $dispatchNote->id]) }}" method="POST" id="dispatchNoteEditForm">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    
                    <!-- LEFT COLUMN (70%): Dispatch Form Controls -->
                    <div class="lg:col-span-8 space-y-6">
                        
                        <!-- Core Details Card -->
                        <div class="theme-card">
                            <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-200 dark:border-slate-700/80">
                                <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <svg class="w-5 h-5 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    <span>Dispatch Details</span>
                                </h3>
                                <span class="badge-neutral text-[10px]">Gate Pass</span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <!-- Dispatch Number -->
                                <div>
                                    <label class="theme-label mb-1.5">Dispatch Number</label>
                                    <input type="text" value="{{ $dispatchNote->dispatch_number }}" readonly 
                                        class="theme-input text-xs py-2.5 font-mono font-bold bg-slate-100 dark:bg-slate-900/80 text-slate-500 cursor-not-allowed">
                                </div>

                                <!-- Date -->
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="theme-label mb-0">Dispatch Date *</label>
                                        <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800/50 px-1.5 py-0.5 rounded">Required</span>
                                    </div>
                                    <input type="date" name="date" value="{{ optional($dispatchNote->date)->format('Y-m-d') }}" x-model="dispatchDate" required
                                        class="theme-input text-xs py-2.5 font-bold">
                                </div>

                                <!-- Dispatch Type -->
                                <div class="md:col-span-2" x-data="{ isCustom: {{ !in_array($dispatchNote->type, ['Calibration', 'Site Transfer', 'Return to Vendor', 'Delivery']) ? 'true' : 'false' }} }">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="theme-label mb-0">Dispatch Type *</label>
                                        <button type="button" @click="isCustom = !isCustom; if(!isCustom) $nextTick(() => $refs.select.focus()); else $nextTick(() => $refs.input.focus());" 
                                            class="text-xs text-brand-600 dark:text-brand-400 font-bold hover:underline">
                                            <span x-text="isCustom ? '← Select from List' : '+ Add Custom Type'"></span>
                                        </button>
                                    </div>
                                    
                                    <div x-show="!isCustom">
                                        <select name="type" x-ref="select" :disabled="isCustom" x-model="dispatchType"
                                            class="theme-input text-xs py-2.5 font-bold">
                                            <option value="Calibration" {{ $dispatchNote->type == 'Calibration' ? 'selected' : '' }}>Calibration</option>
                                            <option value="Site Transfer" {{ $dispatchNote->type == 'Site Transfer' ? 'selected' : '' }}>Site Transfer</option>
                                            <option value="Return to Vendor" {{ $dispatchNote->type == 'Return to Vendor' ? 'selected' : '' }}>Return to Vendor</option>
                                            <option value="Delivery" {{ $dispatchNote->type == 'Delivery' ? 'selected' : '' }}>Delivery</option>
                                        </select>
                                    </div>

                                    <div x-show="isCustom" style="display: none;">
                                        <input type="text" name="type" value="{{ $dispatchNote->type }}" x-ref="input" :disabled="!isCustom" x-model="dispatchType"
                                            class="theme-input text-xs py-2.5 font-bold" 
                                            placeholder="Enter custom dispatch type e.g. Repair / Offloading">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recipient Card -->
                        <div class="theme-card">
                            <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-200 dark:border-slate-700/80">
                                <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    <span>Recipient Information</span>
                                </h3>
                                <span class="badge-neutral text-[10px]">Destination</span>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="theme-label mb-0">Receiver Name / Company *</label>
                                        <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800/50 px-1.5 py-0.5 rounded">Required</span>
                                    </div>
                                    <input type="text" name="receiver_name" value="{{ $dispatchNote->receiver_name }}" x-model="receiverName"
                                        class="theme-input text-xs py-2.5 font-bold" 
                                        placeholder="e.g. ABC Calibration Labs / Qatar Petroleum Site" required>
                                </div>

                                <div>
                                    <label class="theme-label mb-1.5">Address / Location</label>
                                    <textarea name="receiver_address" rows="3" 
                                        class="theme-input text-xs py-2.5" 
                                        placeholder="Detailed address, workshop location, or site contact">{{ $dispatchNote->receiver_address }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Items to Dispatch Table Card -->
                        <div class="theme-card space-y-4">
                            <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-700/80 flex-wrap gap-2">
                                <div>
                                    <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                        <span>Items to Dispatch</span>
                                    </h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">List equipment items included in this gate pass</p>
                                </div>

                                <div class="flex items-center gap-2">
                                    <button type="button" @click="openExcelModal()" class="btn-primary btn-sm text-xs gap-1.5 font-bold shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18M9 4v16M15 4v16"/></svg>
                                        <span>📊 Excel Grid Mode</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Table -->
                            <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700/80">
                                <table class="w-full text-xs text-left">
                                    <thead class="bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold border-b border-slate-200 dark:border-slate-700">
                                        <tr>
                                            <th class="p-3 w-10 text-center">#</th>
                                            <th class="p-3 min-w-[200px]">Description *</th>
                                            <th class="p-3 min-w-[150px]">Serial Number / Ref</th>
                                            <th class="p-3 w-24">Qty *</th>
                                            <th class="p-3 min-w-[160px]">Remarks</th>
                                            <th class="p-3 w-14 text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800 bg-white dark:bg-slate-900">
                                        <template x-for="(item, index) in items" :key="index">
                                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                                                <td class="p-3 text-center font-bold text-slate-400" x-text="index + 1"></td>
                                                
                                                <td class="p-2">
                                                    <input type="text" :name="'items['+index+'][description]'" x-model="item.description" 
                                                        class="theme-input text-xs py-1.5 font-medium" 
                                                        required placeholder="Item description / Equipment name">
                                                </td>

                                                <td class="p-2">
                                                    <input type="text" :name="'items['+index+'][serial_number]'" x-model="item.serial_number" 
                                                        class="theme-input text-xs py-1.5 font-mono" 
                                                        placeholder="Serial/Ref #">
                                                </td>

                                                <td class="p-2">
                                                    <input type="number" :name="'items['+index+'][quantity]'" x-model="item.quantity" 
                                                        class="theme-input text-xs py-1.5 font-bold" 
                                                        min="1" required>
                                                </td>

                                                <td class="p-2">
                                                    <input type="text" :name="'items['+index+'][remarks]'" x-model="item.remarks" 
                                                        class="theme-input text-xs py-1.5" 
                                                        placeholder="Sent for calibration / repair">
                                                </td>

                                                <td class="p-2 text-center">
                                                    <button type="button" @click="removeItem(index)" x-show="items.length > 1"
                                                        class="btn-ghost btn-sm p-1.5 text-rose-500 hover:text-rose-700" title="Remove Row">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>

                            <button type="button" @click="addItem()" class="btn-secondary text-xs font-bold gap-1.5">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                <span>+ Add Another Item</span>
                            </button>
                        </div>

                    </div>

                    <!-- RIGHT COLUMN (30% Sticky Panel): Actions & Live Summary -->
                    <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-24">
                        
                        <!-- Primary Action Panel -->
                        <div class="theme-card space-y-4">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white pb-3 border-b border-slate-200 dark:border-slate-700/80 flex items-center gap-2">
                                <svg class="w-4 h-4 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Update Dispatch Note</span>
                            </h3>

                            <button type="submit" 
                                    class="btn-primary w-full text-center text-xs py-3 font-bold gap-2 shadow-md flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Update Dispatch Note</span>
                            </button>

                            <a href="{{ route('admin.dispatch.index') }}" class="btn-secondary w-full text-center text-xs py-2.5 font-bold gap-1.5 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                                <span>Cancel & Return</span>
                            </a>
                        </div>

                        <!-- Live Dispatch Metadata Summary Card -->
                        <div class="theme-card space-y-3 bg-slate-50/50 dark:bg-slate-900/40">
                            <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider pb-2 border-b border-slate-200 dark:border-slate-700/80">
                                Dispatch Summary
                            </h4>

                            <div class="flex items-center justify-between text-xs py-1">
                                <span class="text-slate-500 dark:text-slate-400">Dispatch Number</span>
                                <span class="font-mono font-bold text-brand-600 dark:text-brand-400">{{ $dispatchNote->dispatch_number }}</span>
                            </div>

                            <div class="flex items-center justify-between text-xs py-1">
                                <span class="text-slate-500 dark:text-slate-400">Dispatch Type</span>
                                <span class="font-bold text-slate-900 dark:text-white truncate max-w-[140px]" x-text="dispatchType || '{{ $dispatchNote->type }}'"></span>
                            </div>

                            <div class="flex items-center justify-between text-xs py-1">
                                <span class="text-slate-500 dark:text-slate-400">Receiver</span>
                                <span class="font-bold text-slate-900 dark:text-white truncate max-w-[140px]" x-text="receiverName || '{{ $dispatchNote->receiver_name }}'"></span>
                            </div>

                            <div class="flex items-center justify-between text-xs py-1">
                                <span class="text-slate-500 dark:text-slate-400">Total Items</span>
                                <span class="badge-info text-[10px] font-bold" x-text="items.length + ' item(s)'"></span>
                            </div>
                        </div>

                    </div>

                </div>
            </form>
        </div>

        <!-- FULL-SCREEN EXCEL SPREADSHEET GRID MODAL -->
        <div x-show="excelModalOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="fixed inset-0 bg-slate-900/80 backdrop-blur-md z-50 flex flex-col p-4 md:p-6 overflow-hidden"
             style="display: none;">
            
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl flex-1 flex flex-col overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-950/60 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-500 text-white font-bold flex items-center justify-center shadow-md">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18M9 4v16M15 4v16"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <span>Dispatch Items - Excel Grid Editor</span>
                                <span class="badge-emerald text-[10px]">Excel Grid Spreadsheet</span>
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Use <kbd class="px-1.5 py-0.5 bg-slate-200 dark:bg-slate-800 rounded text-[10px] font-mono font-bold">TAB</kbd> key to navigate between cells. Tab on last cell inserts a new row.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="button" @click="addItem()" class="btn-secondary text-xs font-bold gap-1.5">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>+ Add Row</span>
                        </button>
                        <button type="button" @click="excelModalOpen = false" class="btn-primary text-xs font-bold gap-1 px-5 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Done & Save Grid</span>
                        </button>
                    </div>
                </div>

                <div class="flex-1 overflow-auto p-6" id="dispatch-excel-grid-container-edit">
                    <table class="w-full border-collapse border border-slate-200 dark:border-slate-800 text-left text-xs">
                        <thead class="bg-slate-100 dark:bg-slate-800 sticky top-0 z-10">
                            <tr>
                                <th class="p-3 border border-slate-200 dark:border-slate-700/80 font-bold text-slate-700 dark:text-slate-300 w-12 text-center bg-slate-200/70 dark:bg-slate-800">#</th>
                                <th class="p-3 border border-slate-200 dark:border-slate-700/80 font-bold text-slate-800 dark:text-slate-200 min-w-[220px]">Description *</th>
                                <th class="p-3 border border-slate-200 dark:border-slate-700/80 font-bold text-slate-800 dark:text-slate-200 min-w-[160px]">Serial Number</th>
                                <th class="p-3 border border-slate-200 dark:border-slate-700/80 font-bold text-slate-800 dark:text-slate-200 w-28">Quantity *</th>
                                <th class="p-3 border border-slate-200 dark:border-slate-700/80 font-bold text-slate-800 dark:text-slate-200 min-w-[180px]">Remarks</th>
                                <th class="p-3 border border-slate-200 dark:border-slate-700/80 font-bold text-slate-700 dark:text-slate-300 w-16 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800 bg-white dark:bg-slate-900">
                            <template x-for="(item, index) in items" :key="index">
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                                    <td class="p-2 border border-slate-200 dark:border-slate-800 text-center font-bold text-slate-500 dark:text-slate-400 bg-slate-50/50 dark:bg-slate-950/40" x-text="index + 1"></td>
                                    
                                    <td class="p-1.5 border border-slate-200 dark:border-slate-800">
                                        <input type="text" x-model="item.description" 
                                               class="theme-input text-xs py-1.5 px-2.5 w-full bg-transparent focus:bg-white dark:focus:bg-slate-800 border-transparent focus:border-brand-500 font-medium" 
                                               placeholder="Description">
                                    </td>

                                    <td class="p-1.5 border border-slate-200 dark:border-slate-800">
                                        <input type="text" x-model="item.serial_number" 
                                               class="theme-input text-xs py-1.5 px-2.5 w-full bg-transparent focus:bg-white dark:focus:bg-slate-800 border-transparent focus:border-brand-500 font-mono" 
                                               placeholder="Serial Ref">
                                    </td>

                                    <td class="p-1.5 border border-slate-200 dark:border-slate-800">
                                        <input type="number" x-model="item.quantity" min="1"
                                               class="theme-input text-xs py-1.5 px-2.5 w-full bg-transparent focus:bg-white dark:focus:bg-slate-800 border-transparent focus:border-brand-500 font-bold">
                                    </td>

                                    <td class="p-1.5 border border-slate-200 dark:border-slate-800">
                                        <input type="text" x-model="item.remarks" 
                                               @keydown.tab="handleExcelTab($event, index)"
                                               class="theme-input text-xs py-1.5 px-2.5 w-full bg-transparent focus:bg-white dark:focus:bg-slate-800 border-transparent focus:border-brand-500" 
                                               placeholder="Remarks">
                                    </td>

                                    <td class="p-1.5 border border-slate-200 dark:border-slate-800 text-center">
                                        <button type="button" x-show="items.length > 1" @click="removeItem(index)" 
                                                class="btn-ghost btn-sm p-1 text-rose-500 hover:text-rose-700" title="Delete Row">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-950/60 text-xs font-semibold text-slate-500 shrink-0">
                    <div>
                        <span>Total Items: </span>
                        <strong class="text-slate-900 dark:text-white" x-text="items.length"></strong>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('dispatchForm', () => ({
            items: @json($dispatchNote->items),
            dispatchType: '{{ $dispatchNote->type }}',
            dispatchDate: '{{ optional($dispatchNote->date)->format("Y-m-d") }}',
            receiverName: '{{ $dispatchNote->receiver_name }}',
            excelModalOpen: false,

            autoIncrementSerial(val) {
                if (!val) return '';
                const match = val.toString().match(/^(.*?)(\d+)$/);
                if (match) {
                    const prefix = match[1];
                    const numStr = match[2];
                    const nextNum = (parseInt(numStr, 10) + 1).toString().padStart(numStr.length, '0');
                    return prefix + nextNum;
                }
                return val;
            },

            addItem() {
                const prev = this.items.length > 0 ? this.items[this.items.length - 1] : null;
                let newDesc = '';
                let newSerial = '';
                let newQty = 1;
                let newRemarks = '';

                if (prev) {
                    newDesc = prev.description || '';
                    newSerial = this.autoIncrementSerial(prev.serial_number || '');
                    newQty = prev.quantity || 1;
                    newRemarks = prev.remarks || '';
                }

                this.items.push({ description: newDesc, serial_number: newSerial, quantity: newQty, remarks: newRemarks });
            },

            removeItem(index) {
                if (this.items.length > 1) {
                    this.items.splice(index, 1);
                }
            },

            openExcelModal() {
                this.excelModalOpen = true;
            },

            handleExcelTab(e, index) {
                if (index === this.items.length - 1 && !e.shiftKey) {
                    e.preventDefault();
                    this.addItem();
                    setTimeout(() => {
                        const inputs = document.querySelectorAll('#dispatch-excel-grid-container-edit table tbody tr:last-child input');
                        if (inputs.length > 0) {
                            inputs[0].focus();
                            if (inputs[0].select) inputs[0].select();
                        }
                    }, 150);
                }
            },

            init() {
                if (!this.items || this.items.length === 0) {
                    this.items = [{ description: '', serial_number: '', quantity: 1, remarks: '' }];
                }
            }
        }));
    });
    </script>
</x-app-layout>
