@foreach($sections as $section)
<div class="mb-8 pb-8 {{ !$loop->last ? 'border-b border-gray-200' : '' }}">
    <!-- Section Header -->
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $section->name }}</h2>
        @if($section->description)
        <p class="text-gray-600 mt-1 text-sm">{{ $section->description }}</p>
        @endif
    </div>

    <!-- Fields -->
    <div class="grid grid-cols-1 {{ $section->layout_type === 'two_column' ? 'md:grid-cols-2' : '' }} gap-6">
        @foreach($section->fields as $field)
        @php
            $value = isset($values) ? ($values[$field->id] ?? '') : '';
            // If value is JSON (e.g. checkbox group), decode it
            if(is_string($value) && (str_starts_with($value, '[') || str_starts_with($value, '{'))) {
                 $decoded = json_decode($value, true);
                 if(json_last_error() === JSON_ERROR_NONE) $value = $decoded;
            }
        @endphp
        <div class="{{ $field->width === 'full' ? 'md:col-span-full' : ($field->width === 'half' ? 'md:col-span-1' : '') }}">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                {{ $field->field_label }}
                @if($field->is_required)
                <span class="text-red-600">*</span>
                @endif
            </label>
            
            @if($field->field_type === 'textarea')
            <textarea name="fields[{{ $field->id }}]" 
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-marine-500 focus:border-marine-500" 
                      rows="3" 
                      placeholder="{{ $field->placeholder }}">{{ $value }}</textarea>
            
            @elseif($field->field_type === 'dropdown')
            <select name="fields[{{ $field->id }}]" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-marine-500 focus:border-marine-500">
                <option value="">Select...</option>
                @if($field->field_options && isset($field->field_options['options']))
                    @foreach($field->field_options['options'] as $option)
                    <option value="{{ $option }}" {{ $value == $option ? 'selected' : '' }}>{{ $option }}</option>
                    @endforeach
                @endif
            </select>
            
            @elseif($field->field_type === 'checkbox')
            <div class="flex items-center">
                <input type="hidden" name="fields[{{ $field->id }}]" value="0">
                <input type="checkbox" name="fields[{{ $field->id }}]" value="1" 
                       {{ $value ? 'checked' : '' }}
                       class="w-4 h-4 text-marine-600 border-gray-300 rounded focus:ring-marine-500">
                <span class="ml-2 text-sm text-gray-600">{{ $field->placeholder }}</span>
            </div>
            
            @elseif($field->field_type === 'radio')
            <div class="space-y-2">
                @if($field->field_options && isset($field->field_options['options']))
                    @foreach($field->field_options['options'] as $option)
                    <div class="flex items-center">
                        <input type="radio" name="fields[{{ $field->id }}]" value="{{ $option }}" 
                               {{ $value == $option ? 'checked' : '' }}
                               class="w-4 h-4 text-marine-600 border-gray-300 focus:ring-marine-500">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $option }}</span>
                    </div>
                    @endforeach
                @endif
            </div>
            
            @else
            <input type="{{ $field->field_type }}" name="fields[{{ $field->id }}]" value="{{ $value }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-marine-500 focus:border-marine-500" 
                   placeholder="{{ $field->placeholder }}">
            @endif
            
            @if($field->help_text)
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $field->help_text }}</p>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endforeach
