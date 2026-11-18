<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Finance Categories') }}
            </h2>
            <div x-data="{ open: false }">
                <button @click="open = true" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    Add New Category
                </button>

                <!-- Create Modal -->
                <div x-show="open" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                    <div class="flex items-center justify-center min-h-screen px-4 text-center">
                        <div class="fixed inset-0 transition-opacity bg-gray-50 dark:bg-gray-700 bg-opacity-75" @click="open = false"></div>

                        <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-2xl">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">Create New Category</h3>
                            <form action="{{ route('admin.finance.categories.store') }}" method="POST" class="mt-4">
                                @csrf
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                                    <input type="text" name="name" required class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
                                    <select name="type" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="income">Income</option>
                                        <option value="expense">Expense</option>
                                    </select>
                                </div>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Color (for Reports)</label>
                                    <select name="color" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="gray">Gray</option>
                                        <option value="red">Red</option>
                                        <option value="green">Green</option>
                                        <option value="blue">Blue</option>
                                        <option value="yellow">Yellow</option>
                                        <option value="indigo">Indigo</option>
                                    </select>
                                </div>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                    <textarea name="description" rows="2" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                                </div>
                                <div class="mt-6 flex justify-end">
                                    <button type="button" @click="open = false" class="mr-3 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300">Cancel</button>
                                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Color</th>
                                <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($categories as $category)
                            <tr x-data="{ editOpen: false }">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $category->name }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $category->description }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $category->type === 'income' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                        {{ ucfirst($category->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-block w-4 h-4 rounded-full bg-{{ $category->color }}-500 border border-gray-200"></span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400 ml-2 capitalize">{{ $category->color }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button @click="editOpen = true" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 mr-3">Edit</button>
                                    
                                    @if(!$category->is_system)
                                    <form action="{{ route('admin.finance.categories.destroy', $category) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-500 dark:hover:text-red-300" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                    @endif

                                    <!-- Edit Modal -->
                                    <div x-show="editOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                                        <div class="flex items-center justify-center min-h-screen px-4 text-center">
                                            <div class="fixed inset-0 transition-opacity bg-gray-50 dark:bg-gray-700 bg-opacity-75" @click="editOpen = false"></div>
                                            <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-2xl">
                                                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">Edit Category</h3>
                                                <form action="{{ route('admin.finance.categories.update', $category) }}" method="POST" class="mt-4">
                                                    @csrf
                                                    @method('PUT')
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                                                        <input type="text" name="name" value="{{ $category->name }}" required class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                                    </div>
                                                    <div class="mt-4">
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
                                                        <select name="type" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                                            <option value="income" {{ $category->type == 'income' ? 'selected' : '' }}>Income</option>
                                                            <option value="expense" {{ $category->type == 'expense' ? 'selected' : '' }}>Expense</option>
                                                        </select>
                                                    </div>
                                                    <div class="mt-4">
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Color</label>
                                                        <select name="color" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                                            @foreach(['gray', 'red', 'green', 'blue', 'yellow', 'indigo'] as $c)
                                                                <option value="{{ $c }}" {{ $category->color == $c ? 'selected' : '' }}>{{ ucfirst($c) }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mt-4">
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                                        <textarea name="description" rows="2" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ $category->description }}</textarea>
                                                    </div>
                                                    <div class="mt-6 flex justify-end">
                                                        <button type="button" @click="editOpen = false" class="mr-3 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300">Cancel</button>
                                                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Update</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
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
