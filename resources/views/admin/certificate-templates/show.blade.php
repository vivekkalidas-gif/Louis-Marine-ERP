<x-app-layout>
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('admin.certificate-templates.index') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
            <i class="fas fa-arrow-left mr-2"></i>Back to Templates
        </a>
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $certificateTemplate->name }}</h1>
                <p class="text-gray-600 mt-1">{{ $certificateTemplate->equipmentCategory->name }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.certificate-templates.edit', $certificateTemplate) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold transition">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <a href="{{ route('admin.certificate-templates.preview', $certificateTemplate) }}" target="_blank" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-semibold transition">
                    <i class="fas fa-eye mr-2"></i>Preview
                </a>
            </div>
        </div>
    </div>

    <!-- Template Info -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="text-sm text-gray-600 mb-1">Status</div>
            <div class="text-2xl font-bold {{ $certificateTemplate->is_active ? 'text-green-600' : 'text-gray-600' }}">
                {{ $certificateTemplate->is_active ? 'Active' : 'Inactive' }}
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="text-sm text-gray-600 mb-1">Sections</div>
            <div class="text-2xl font-bold text-blue-600">{{ $certificateTemplate->sections->count() }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="text-sm text-gray-600 mb-1">Total Fields</div>
            <div class="text-2xl font-bold text-green-600">{{ $certificateTemplate->sections->sum(fn($s) => $s->fields->count()) }}</div>
        </div>
    </div>

    <!-- Sections -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Template Structure</h2>

        @forelse($certificateTemplate->sections as $section)
        <div class="mb-8 border-l-4 border-blue-500 pl-6">
            <div class="mb-4">
                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $section->name }}</h3>
                @if($section->description)
                <p class="text-gray-600 mt-1">{{ $section->description }}</p>
                @endif
                <div class="flex gap-2 mt-2">
                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">{{ ucfirst(str_replace('_', ' ', $section->layout_type)) }}</span>
                    @if($section->is_repeatable)
                    <span class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded">Repeatable</span>
                    @endif
                </div>
            </div>

            @if($section->fields->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($section->fields as $field)
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200">
                    <div class="font-semibold text-gray-900 dark:text-gray-100 mb-1">{{ $field->field_label }}</div>
                    <div class="text-sm text-gray-600 space-y-1">
                        <div><span class="font-medium">Type:</span> {{ ucfirst($field->field_type) }}</div>
                        <div><span class="font-medium">Width:</span> {{ ucfirst($field->width) }}</div>
                        @if($field->is_required)
                        <div class="text-red-600 font-medium">Required</div>
                        @endif
                        @if($field->placeholder)
                        <div><span class="font-medium">Placeholder:</span> {{ $field->placeholder }}</div>
                        @endif
                        @if($field->help_text)
                        <div class="text-xs text-gray-500 dark:text-gray-400 italic">{{ $field->help_text }}</div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-500 dark:text-gray-400 italic">No fields in this section</p>
            @endif
        </div>
        @empty
        <div class="text-center py-12 text-gray-500 dark:text-gray-400">
            <i class="fas fa-folder-open text-6xl mb-4"></i>
            <p class="text-lg">No sections defined yet</p>
        </div>
        @endforelse
    </div>

    <!-- Metadata -->
    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mt-6">
        <h3 class="font-bold text-gray-900 dark:text-gray-100 mb-4">Metadata</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-600">Created by:</span>
                <span class="font-medium">{{ $certificateTemplate->creator->name ?? 'System' }}</span>
            </div>
            <div>
                <span class="text-gray-600">Created at:</span>
                <span class="font-medium">{{ $certificateTemplate->created_at->format('d M Y, H:i') }}</span>
            </div>
            <div>
                <span class="text-gray-600">Last updated:</span>
                <span class="font-medium">{{ $certificateTemplate->updated_at->format('d M Y, H:i') }}</span>
            </div>
            <div>
                <span class="text-gray-600">Default template:</span>
                <span class="font-medium">{{ $certificateTemplate->is_default ? 'Yes' : 'No' }}</span>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
