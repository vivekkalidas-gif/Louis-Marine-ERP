{{-- Individual Field Renderer --}}
@props(['field', 'namePrefix', 'useInstance' => false, 'value' => null])

@php
    $fieldName = $useInstance ? $namePrefix : $namePrefix;
    $fieldValue = $value ?? old($fieldName, $field->default_value);
@endphp

@if($field->field_type === 'textarea')
    <textarea 
        :name="'{{ $namePrefix }}' + ({{ $useInstance ? 'instance' : '""' }})"
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        rows="3"
        placeholder="{{ $field->placeholder }}"
        {{ $field->is_required ? 'required' : '' }}
    >{{ $fieldValue }}</textarea>

@elseif($field->field_type === 'dropdown')
    <select 
        :name="'{{ $namePrefix }}' + ({{ $useInstance ? 'instance' : '""' }})"
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        {{ $field->is_required ? 'required' : '' }}
    >
        <option value="">Select...</option>
        @if($field->field_options && isset($field->field_options['options']))
            @foreach($field->field_options['options'] as $option)
            <option value="{{ $option }}" {{ $fieldValue == $option ? 'selected' : '' }}>{{ $option }}</option>
            @endforeach
        @endif
    </select>

@elseif($field->field_type === 'checkbox')
    <div class="flex items-center">
        <input 
            type="checkbox"
            :name="'{{ $namePrefix }}' + ({{ $useInstance ? 'instance' : '""' }})"
            value="1"
            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
            {{ $fieldValue ? 'checked' : '' }}
        >
        <span class="ml-2 text-sm text-gray-600">{{ $field->placeholder }}</span>
    </div>

@elseif($field->field_type === 'radio')
    <div class="space-y-2">
        @if($field->field_options && isset($field->field_options['options']))
            @foreach($field->field_options['options'] as $option)
            <div class="flex items-center">
                <input 
                    type="radio"
                    :name="'{{ $namePrefix }}' + ({{ $useInstance ? 'instance' : '""' }})"
                    value="{{ $option }}"
                    class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                    {{ $fieldValue == $option ? 'checked' : '' }}
                    {{ $field->is_required ? 'required' : '' }}
                >
                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $option }}</span>
            </div>
            @endforeach
        @endif
    </div>

@else
    {{-- Default: text, number, date --}}
    <input 
        type="{{ $field->field_type }}"
        :name="'{{ $namePrefix }}' + ({{ $useInstance ? 'instance' : '""' }})"
        value="{{ $fieldValue }}"
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        placeholder="{{ $field->placeholder }}"
        {{ $field->is_required ? 'required' : '' }}
    >
@endif
