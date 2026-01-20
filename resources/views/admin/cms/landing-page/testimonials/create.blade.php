<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Create Testimonial') }}</h2></x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('admin.cms.landing-page.testimonials.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-6">
                        <div><label class="block text-sm font-medium mb-2">Client Name *</label><input type="text" name="client_name" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-marine-500 focus:ring-marine-500" value="{{ old('client_name') }}"></div>
                        <div><label class="block text-sm font-medium mb-2">Company Name</label><input type="text" name="company_name" class="w-full rounded-md border-gray-300 shadow-sm focus:border-marine-500 focus:ring-marine-500" value="{{ old('company_name') }}"></div>
                        <div><label class="block text-sm font-medium mb-2">Testimonial Text *</label><textarea name="testimonial_text" rows="4" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-marine-500 focus:ring-marine-500">{{ old('testimonial_text') }}</textarea></div>
                        <div><label class="block text-sm font-medium mb-2">Client Photo</label><input type="file" name="client_photo" accept="image/*" class="w-full"></div>
                        <div class="grid grid-cols-3 gap-4">
                            <div><label class="block text-sm font-medium mb-2">Rating (1-5) *</label><select name="rating" required class="w-full rounded-md border-gray-300 shadow-sm"><option value="5">5 Stars</option><option value="4">4 Stars</option><option value="3">3 Stars</option><option value="2">2 Stars</option><option value="1">1 Star</option></select></div>
                            <div><label class="block text-sm font-medium mb-2">Sort Order</label><input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" class="w-full rounded-md border-gray-300 shadow-sm"></div>
                            <div class="flex items-center"><input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-marine-600"><label class="ml-2 text-sm font-medium">Active</label></div>
                        </div>
                    </div>
                    <div class="mt-6 flex gap-4">
                        <button type="submit" class="bg-marine-600 hover:bg-marine-700 text-white px-6 py-2 rounded-lg">Create</button>
                        <a href="{{ route('admin.cms.landing-page.testimonials.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
