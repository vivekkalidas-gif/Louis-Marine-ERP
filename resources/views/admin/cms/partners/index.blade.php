<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-marine-900 dark:text-marine-100 leading-tight">
            {{ __('Manage Partners & Clients') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 bg-white dark:bg-marine-900 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Add Client / Partner</h3>
                <form action="{{ route('admin.cms.partners.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                            <input type="text" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-marine-500 focus:ring-marine-500 dark:bg-marine-800 dark:border-marine-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Website URL</label>
                            <input type="url" name="website_url" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-marine-500 focus:ring-marine-500 dark:bg-marine-800 dark:border-marine-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Logo</label>
                            <input type="file" name="logo_path" class="mt-1 block w-full">
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="w-full bg-marine-600 hover:bg-marine-700 text-white font-bold py-2 px-4 rounded">
                                Add Partner
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="bg-white dark:bg-marine-900 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-marine-800">
                        <thead class="bg-gray-50 dark:bg-marine-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Partner</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Logo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-marine-800">
                            @foreach($partners as $partner)
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $partner->name }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $partner->website_url }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($partner->logo_path)
                                            <img src="{{ asset('storage/' . $partner->logo_path) }}" class="h-10 w-auto object-contain">
                                        @else
                                            <span class="text-gray-400">No Logo</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium">
                                        <form action="{{ route('admin.cms.partners.destroy', $partner) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-500 dark:hover:text-red-300">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
