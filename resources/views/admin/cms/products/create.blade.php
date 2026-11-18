<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-xl text-slate-900 dark:text-white leading-tight tracking-tight flex items-center gap-2">
                    <span>Add New Website Product</span>
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Add marine equipment or safety product to website catalog</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.cms.products.index') }}" class="btn-ghost text-xs gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Cancel & Return</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto">
            <div class="theme-card">
                <form action="{{ route('admin.cms.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label class="theme-label mb-1.5">Product Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="theme-input text-xs font-bold" placeholder="e.g. SOLAS Approved Life Raft 25 Person">
                        @error('name') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="theme-label mb-1.5">Short Description *</label>
                        <textarea name="short_description" rows="2" required class="theme-input text-xs" placeholder="Summary shown on product catalog card...">{{ old('short_description') }}</textarea>
                        @error('short_description') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="theme-label mb-1.5">Full Technical Specifications & Description</label>
                        <textarea name="description" rows="5" class="theme-input text-xs" placeholder="Full product specifications, certifications, and compliance details...">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label class="theme-label mb-1.5">Product Image Upload</label>
                        <input type="file" name="image_path" class="theme-input text-xs py-2">
                    </div>

                    <!-- SEO Metadata Sub-Card -->
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 space-y-4">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">SEO & Search Metadata</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="theme-label mb-1.5">Meta Title</label>
                                <input type="text" name="seo_title" value="{{ old('seo_title') }}" class="theme-input text-xs" placeholder="Custom search engine title">
                            </div>

                            <div>
                                <label class="theme-label mb-1.5">Meta Description</label>
                                <input type="text" name="seo_description" value="{{ old('seo_description') }}" class="theme-input text-xs" placeholder="Custom search meta description">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
                        <a href="{{ route('admin.cms.products.index') }}" class="btn-secondary text-xs">Cancel</a>
                        <button type="submit" class="btn-primary text-xs font-bold gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Publish Product</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
