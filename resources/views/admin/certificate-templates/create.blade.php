<x-app-layout>
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('admin.certificate-templates.index') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
            <i class="fas fa-arrow-left mr-2"></i>Back to Templates
        </a>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Create Certificate Template</h1>
        <p class="text-gray-600 mt-1">Define a new certificate template for an equipment category</p>
    </div>

    <!-- Form -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-8">
        <form action="{{ route('admin.certificate-templates.store') }}" method="POST">
            @csrf

            <!-- Template Name -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Template Name *</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="e.g., Fire Extinguisher Certificate">
                @error('name')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Equipment Category -->
            <div class="mb-6">
                <label for="equipment_category_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Equipment Category *</label>
                <select name="equipment_category_id" id="equipment_category_id" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('equipment_category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
                @error('equipment_category_id')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Certificate Title -->
            <div class="mb-6">
                <label for="title" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Certificate Title *</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="e.g., Fire Extinguisher Service Certificate">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">This will appear as the main title on the certificate PDF</p>
                @error('title')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status Toggles -->
            <div class="mb-6 space-y-4">
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="is_active" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Active (template can be used for new certificates)
                    </label>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_default" id="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}
                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="is_default" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Set as default template for this category
                    </label>
                </div>
            </div>

            <!-- Info Box -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-1">Next Steps</p>
                        <p>After creating the template, you'll be able to add sections and fields to define the certificate structure.</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                    <i class="fas fa-save mr-2"></i>Create Template
                </button>
                <a href="{{ route('admin.certificate-templates.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 dark:text-gray-300 px-6 py-3 rounded-lg font-semibold transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
</x-app-layout>
