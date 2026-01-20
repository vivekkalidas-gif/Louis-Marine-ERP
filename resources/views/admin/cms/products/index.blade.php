<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Website Products') }}
            </h2>
            <a href="{{ route('admin.cms.products.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                + Add New Product
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Description</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($products as $product)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        @if($product->image_path)
                                            <img src="{{ Storage::url($product->image_path) }}" class="w-12 h-12 rounded object-cover mr-4">
                                        @else
                                            <div class="w-12 h-12 rounded bg-gray-200 mr-4 flex items-center justify-center text-gray-500 dark:text-gray-400 text-xs">No Img</div>
                                        @endif
                                        <div class="text-sm font-bold dark:text-white">{{ $product->name }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded {{ $product->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                        {{ $product->is_active ? 'Active' : 'Hidden' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ Str::limit($product->short_description, 50) }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    {{-- Actions --}}
                                    <a href="{{ route('admin.cms.products.edit', $product) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 mr-3">Edit</a>
                                    <form action="{{ route('admin.cms.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Delete this product from website?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-500 dark:hover:text-red-300">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No website products found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
