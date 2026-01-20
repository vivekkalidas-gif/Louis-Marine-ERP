<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Testimonials') }}</h2>
            <a href="{{ route('admin.cms.landing-page.testimonials.create') }}" class="bg-marine-600 hover:bg-marine-700 text-white px-4 py-2 rounded-lg transition">+ Add Testimonial</a>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6">{{ session('success') }}</div>@endif
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead><tr><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rating</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th><th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th></tr></thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($testimonials as $testimonial)
                            <tr>
                                <td class="px-6 py-4 text-sm">{{ $testimonial->sort_order }}</td>
                                <td class="px-6 py-4"><div class="text-sm font-bold">{{ $testimonial->client_name }}</div><div class="text-xs text-gray-500">{{ $testimonial->company_name }}</div></td>
                                <td class="px-6 py-4 text-sm">{{ str_repeat('⭐', $testimonial->rating) }}</td>
                                <td class="px-6 py-4"><span class="px-2 py-1 text-xs rounded {{ $testimonial->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $testimonial->is_active ? 'Active' : 'Inactive' }}</span></td>
                                <td class="px-6 py-4 text-right text-sm"><a href="{{ route('admin.cms.landing-page.testimonials.edit', $testimonial) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a><form action="{{ route('admin.cms.landing-page.testimonials.destroy', $testimonial) }}" method="POST" class="inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button type="submit" class="text-red-600 hover:text-red-900">Delete</button></form></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">No testimonials found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
