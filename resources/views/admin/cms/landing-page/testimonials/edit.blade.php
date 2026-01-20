<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Edit Testimonial') }}</h2></x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('admin.cms.landing-page.testimonials.update', $testimonial) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="space-y-6">
                        <div><label class="block text-sm font-medium mb-2">Client Name *</label><input type="text" name="client_name" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-marine-500 focus:ring-marine-500" value="{{ old('client_name', $testimonial->client_name) }}"></div>
                        <div><label class="block text-sm font-medium mb-2">Company Name</label><input type="text" name="company_name" class="w-full rounded-md border-gray-300 shadow-sm focus:border-marine-500 focus:ring-marine-500" value="{{ old('company_name', $testimonial->company_name) }}"></div>
                        <div><label class="block text-sm font-medium mb-2">Testimonial Text *</label><textarea name="testimonial_text" rows="4" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-marine-500 focus:ring-marine-500">{{ old('testimonial_text', $testimonial->testimonial_text) }}</textarea></div>
                        <div><label class="block text-sm font-medium mb-2">Client Photo</label>@if($testimonial->client_photo)<div class="mb-2"><img src="{{ Storage::url($testimonial->client_photo) }}" alt="Current" class="h-20 w-20 rounded-full object-cover"></div>@endif<input type="file" name="client_photo" accept="image/*" class="w-full"></div>
                        <div class="grid grid-cols-3 gap-4">
                            <div><label class="block text-sm font-medium mb-2">Rating (1-5) *</label><select name="rating" required class="w-full rounded-md border-gray-300 shadow-sm">@for($i=5; $i>=1; $i--)<option value="{{ $i }}" {{ old('rating', $testimonial->rating) == $i ? 'selected' : '' }}>{{ $i }} Stars</option>@endfor</select></div>
                            <div><label class="block text-sm font-medium mb-2">Sort Order</label><input type="number" name="sort_order" value="{{ old('sort_order', $testimonial->sort_order) }}" class="w-full rounded-md border-gray-300 shadow-sm"></div>
                            <div class="flex items-center"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $testimonial->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-marine-600"><label class="ml-2 text-sm font-medium">Active</label></div>
                        </div>
                    </div>
                    <div class="mt-6 flex gap-4">
                        <button type="submit" class="bg-marine-600 hover:bg-marine-700 text-white px-6 py-2 rounded-lg">Update</button>
                        <a href="{{ route('admin.cms.landing-page.testimonials.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
