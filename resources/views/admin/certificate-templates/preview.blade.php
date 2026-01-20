<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $certificateTemplate->name }} - Preview</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $certificateTemplate->title }}</h1>
                    <p class="text-gray-600 mt-1">Template Preview</p>
                </div>
                <button onclick="window.close()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                    Close
                </button>
            </div>
            <div class="bg-blue-50 border border-blue-200 rounded p-4">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    This is a preview of how the certificate form will appear. Actual certificates will include dynamic data.
                </p>
            </div>
        </div>

        <!-- Certificate Preview -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-8">
            @foreach($certificateTemplate->sections as $section)
            <div class="mb-8 pb-8 {{ !$loop->last ? 'border-b border-gray-200' : '' }}">
                <!-- Section Header -->
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $section->name }}</h2>
                    @if($section->description)
                    <p class="text-gray-600 mt-1">{{ $section->description }}</p>
                    @endif
                    @if($section->is_repeatable)
                    <span class="inline-block bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded mt-2">
                        Repeatable Section
                    </span>
                    @endif
                </div>

                <!-- Fields -->
                @if($section->layout_type === 'table' && $section->is_repeatable)
                <!-- Table Layout -->
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-300">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                @foreach($section->fields as $field)
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-300 border-b">
                                    {{ $field->field_label }}
                                    @if($field->is_required)<span class="text-red-600">*</span>@endif
                                </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b">
                                @foreach($section->fields as $field)
                                <td class="px-4 py-2 text-sm text-gray-600">
                                    [{{ ucfirst($field->field_type) }}]
                                </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
                @else
                <!-- Grid Layout -->
                <div class="grid grid-cols-1 {{ $section->layout_type === 'two_column' ? 'md:grid-cols-2' : '' }} gap-6">
                    @foreach($section->fields as $field)
                    <div class="{{ $field->width === 'full' ? 'md:col-span-full' : ($field->width === 'half' ? 'md:col-span-1' : '') }}">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            {{ $field->field_label }}
                            @if($field->is_required)
                            <span class="text-red-600">*</span>
                            @endif
                        </label>
                        
                        @if($field->field_type === 'textarea')
                        <textarea class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 dark:bg-gray-700" rows="3" placeholder="{{ $field->placeholder }}"></textarea>
                        @elseif($field->field_type === 'dropdown')
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 dark:bg-gray-700">
                            <option value="">Select...</option>
                            @if($field->field_options && isset($field->field_options['options']))
                                @foreach($field->field_options['options'] as $option)
                                <option>{{ $option }}</option>
                                @endforeach
                            @endif
                        </select>
                        @elseif($field->field_type === 'checkbox')
                        <div class="flex items-center">
                            <input type="checkbox" class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-600">{{ $field->placeholder }}</span>
                        </div>
                        @elseif($field->field_type === 'radio')
                        <div class="space-y-2">
                            @if($field->field_options && isset($field->field_options['options']))
                                @foreach($field->field_options['options'] as $option)
                                <div class="flex items-center">
                                    <input type="radio" name="{{ $field->field_name }}" class="w-4 h-4 text-blue-600 border-gray-300">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $option }}</span>
                                </div>
                                @endforeach
                            @endif
                        </div>
                        @else
                        <input type="{{ $field->field_type }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 dark:bg-gray-700" placeholder="{{ $field->placeholder }}">
                        @endif
                        
                        @if($field->help_text)
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $field->help_text }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach

            @if($certificateTemplate->sections->count() === 0)
            <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                <i class="fas fa-folder-open text-6xl mb-4"></i>
                <p class="text-lg">No sections defined yet</p>
                <p class="text-sm">Add sections and fields to see the preview</p>
            </div>
            @endif
        </div>
    </div>
</body>
</html>
