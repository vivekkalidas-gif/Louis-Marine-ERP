<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="font-black text-xl text-slate-900 dark:text-white leading-tight tracking-tight">
                        {{ $item->name }}
                    </h2>
                    <span class="badge-success text-xs">{{ $item->is_active ? 'Active' : 'Inactive' }}</span>
                    <span class="badge-neutral font-mono text-xs">{{ $item->code }}</span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Update equipment item code, master category, brand, and specifications</p>
            </div>
            
            <a href="{{ route('admin.equipment.categories.index') }}" class="btn-ghost text-xs gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Back to Equipment Master</span>
            </a>
        </div>
    </x-slot>

    <div class="py-6" x-data="equipmentForm()">
        <div class="max-w-7xl mx-auto">
            <form method="POST" action="{{ route('admin.equipment.items.update', $item->id) }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    
                    <!-- LEFT COLUMN (70%): Form Sections -->
                    <div class="lg:col-span-8 space-y-6">
                        
                        <!-- SECTION 1: Identity & Category -->
                        <div class="theme-card">
                            <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-200 dark:border-slate-700/80">
                                <div class="w-9 h-9 rounded-xl bg-brand-50 dark:bg-brand-950/60 text-brand-600 dark:text-brand-400 border border-brand-200 dark:border-brand-800/50 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-slate-900 dark:text-white">1. Master Classification & Equipment Code</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Select equipment category, custom equipment code, and item title</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="theme-label mb-0">Master Category</label>
                                        <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800/50 px-1.5 py-0.5 rounded">Required</span>
                                    </div>
                                    <select name="category_id" x-model="categoryId" class="theme-input text-xs py-2.5" required>
                                        <option value="">-- Select Category --</option>
                                        @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id', $item->category_id) == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <p class="text-[11px] text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="theme-label mb-0">Equipment Code</label>
                                        <span class="text-[10px] font-bold text-sky-600 dark:text-sky-400 bg-sky-50 dark:bg-sky-950/60 border border-sky-200 dark:border-sky-800/50 px-1.5 py-0.5 rounded">Editable</span>
                                    </div>
                                    <input type="text" name="code" class="theme-input text-xs py-2.5 font-mono uppercase font-bold tracking-wider" value="{{ old('code', $item->code) }}" placeholder="e.g. FIR-0004">
                                    @error('code')
                                        <p class="text-[11px] text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="theme-label mb-0">Equipment Name</label>
                                        <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800/50 px-1.5 py-0.5 rounded">Required</span>
                                    </div>
                                    <input type="text" name="name" class="theme-input text-xs py-2.5 font-bold" value="{{ old('name', $item->name) }}" required placeholder="e.g. AFFF Foam Fire Extinguisher 9Ltr">
                                    @error('name')
                                        <p class="text-[11px] text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 2: Technical Specifications & Brand -->
                        <div class="theme-card">
                            <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-200 dark:border-slate-700/80">
                                <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/50 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-slate-900 dark:text-white">2. Brand, Model & Unit Defaults</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Manufacturer brand name, model number, unit of measure, and active status</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="theme-label">Brand / Manufacturer</label>
                                    <input type="text" name="brand" class="theme-input text-xs py-2.5" value="{{ old('brand', $item->brand) }}" placeholder="e.g. Bavaria, Eversafe">
                                </div>

                                <div>
                                    <label class="theme-label">Model Number</label>
                                    <input type="text" name="model" class="theme-input text-xs py-2.5" value="{{ old('model', $item->model) }}" placeholder="e.g. F9-AFFF">
                                </div>

                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="theme-label mb-0">Unit of Measure</label>
                                        <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800/50 px-1.5 py-0.5 rounded">Required</span>
                                    </div>
                                    <select name="unit" class="theme-input text-xs py-2.5" required>
                                        <option value="Nos" {{ old('unit', $item->unit) == 'Nos' ? 'selected' : '' }}>Nos (Numbers)</option>
                                        <option value="Sets" {{ old('unit', $item->unit) == 'Sets' ? 'selected' : '' }}>Sets</option>
                                        <option value="Ltrs" {{ old('unit', $item->unit) == 'Ltrs' ? 'selected' : '' }}>Ltrs (Liters)</option>
                                        <option value="Pairs" {{ old('unit', $item->unit) == 'Pairs' ? 'selected' : '' }}>Pairs</option>
                                        <option value="Kgs" {{ old('unit', $item->unit) == 'Kgs' ? 'selected' : '' }}>Kgs (Kilograms)</option>
                                    </select>
                                </div>

                                <div class="flex items-end pb-2">
                                    <label class="flex items-center gap-2.5 cursor-pointer">
                                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $item->is_active) ? 'checked' : '' }} class="rounded border-slate-300 text-brand-600 focus:ring-brand-500 w-4 h-4">
                                        <span class="text-xs font-bold text-slate-900 dark:text-white">Active Master Record</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 3: Description & Inspection Scope -->
                        <div class="theme-card">
                            <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-200 dark:border-slate-700/80">
                                <div class="w-9 h-9 rounded-xl bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 border border-sky-200 dark:border-sky-800/50 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-slate-900 dark:text-white">3. Item Description & Operational Scope</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Detailed item specifications, maintenance guidelines, or safety compliance</p>
                                </div>
                            </div>

                            <div>
                                <label class="theme-label">Detailed Description</label>
                                <textarea name="description" rows="4" class="theme-input text-xs" placeholder="Specify technical specs, pressure ratings, capacity...">{{ old('description', $item->description) }}</textarea>
                            </div>
                        </div>

                    </div>

                    <!-- RIGHT COLUMN (30% Sticky Panel): Actions & Metadata -->
                    <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-24">
                        
                        <!-- Primary Action Panel Card -->
                        <div class="theme-card space-y-4">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white pb-3 border-b border-slate-200 dark:border-slate-700/80 flex items-center gap-2">
                                <svg class="w-4 h-4 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                                <span>Publish Changes</span>
                            </h3>

                            <button type="submit" class="btn-primary w-full text-center text-xs py-3 font-bold gap-2 shadow-md">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Update Equipment Item</span>
                            </button>

                            <a href="{{ route('admin.equipment.categories.index') }}" class="btn-secondary w-full text-center text-xs py-2.5 font-bold gap-1.5 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Cancel</span>
                            </a>
                        </div>

                        <!-- Audit Metadata Card -->
                        <div class="theme-card space-y-3 bg-slate-50/50 dark:bg-slate-900/40">
                            <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider pb-2 border-b border-slate-200 dark:border-slate-700/80">
                                Equipment Record Metadata
                            </h4>

                            <div class="flex items-center justify-between text-xs py-1">
                                <span class="text-slate-500 dark:text-slate-400">Item ID</span>
                                <span class="font-mono font-bold text-slate-900 dark:text-white">#{{ $item->id }}</span>
                            </div>

                            <div class="flex items-center justify-between text-xs py-1">
                                <span class="text-slate-500 dark:text-slate-400">Current Code</span>
                                <span class="font-mono font-bold text-brand-600 dark:text-brand-400">{{ $item->code }}</span>
                            </div>

                            <div class="flex items-center justify-between text-xs py-1">
                                <span class="text-slate-500 dark:text-slate-400">Created Date</span>
                                <span class="font-semibold text-slate-900 dark:text-white">{{ $item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('d M, Y') : 'N/A' }}</span>
                            </div>
                        </div>

                    </div>

                </div>
            </form>
        </div>
    </div>

    <script>
        function equipmentForm() {
            return {
                categoryId: '{{ old('category_id', $item->category_id) }}',
            }
        }
    </script>
</x-app-layout>
