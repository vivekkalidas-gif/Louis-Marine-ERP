<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-xl text-slate-900 dark:text-white leading-tight tracking-tight flex items-center gap-2">
                    <svg class="w-6 h-6 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Add New Marine Service</span>
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Create a service entry for the public website catalog</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.cms.services.index') }}" class="btn-secondary text-xs font-bold gap-1.5 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Back to Services List</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto space-y-6">
            
            <form action="{{ route('admin.cms.services.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="theme-card space-y-6">
                    <div class="pb-3 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">General Information</h3>
                        <span class="text-xs text-slate-500">Service identification on public website</span>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <label class="theme-label mb-1.5">Service Title *</label>
                            <input type="text" name="title" value="{{ old('title') }}" required class="theme-input text-xs font-bold" placeholder="e.g. Lifeboat & Davit Inspection">
                            @error('title') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div>
                            <label class="theme-label mb-1.5">Short Description *</label>
                            <textarea name="description" rows="3" required class="theme-input text-xs" placeholder="Summary paragraph displayed on service cards...">{{ old('description') }}</textarea>
                            @error('description') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="theme-label mb-1.5">Full Content (Optional Details)</label>
                            <textarea name="content" rows="5" class="theme-input text-xs" placeholder="Detailed service description displayed on dedicated page...">{{ old('content') }}</textarea>
                            @error('content') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-200 dark:border-slate-800">
                        <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-4">Media & Visual Assets</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="theme-label mb-1.5">Icon (SVG or FontAwesome Class)</label>
                                <input type="text" name="icon_class" value="{{ old('icon_class') }}" class="theme-input text-xs font-mono" placeholder="<svg... or fa fa-anchor">
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Paste SVG markup or CSS class name.</p>
                                @error('icon_class') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="theme-label mb-1.5">Service Cover Image Upload</label>
                                <input type="file" name="image_path" class="theme-input text-xs py-2">
                                @error('image_path') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-200 dark:border-slate-800 grid grid-cols-1 md:grid-cols-2 gap-5 items-center">
                        <div>
                            <label class="theme-label mb-1.5">Catalog Display Order</label>
                            <input type="number" name="order" value="{{ old('order', 0) }}" class="theme-input text-xs font-mono w-32">
                        </div>

                        <div>
                            <label class="inline-flex items-center gap-2 cursor-pointer mt-4">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded text-brand-600 focus:ring-brand-500">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Active (Visible on Public Website)</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
                        <a href="{{ route('admin.cms.services.index') }}" class="btn-secondary text-xs font-bold">Cancel</a>
                        <button type="submit" class="btn-primary text-xs font-bold gap-1.5 px-6">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Save Service</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
