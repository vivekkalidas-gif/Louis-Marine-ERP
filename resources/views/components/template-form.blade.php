{{-- Dynamic Template Form Renderer Component --}}
@props(['template', 'certificate' => null])

@if($template)
<div class="space-y-8">
    <input type="hidden" name="template_id" value="{{ $template->id }}">
    
    @foreach($template->sections as $section)
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-blue-500">
        <!-- Section Header -->
        <div class="mb-6">
            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $section->name }}</h3>
            @if($section->description)
            <p class="text-gray-600 text-sm mt-1">{{ $section->description }}</p>
            @endif
            @if($section->is_repeatable)
            <span class="inline-block bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded mt-2">
                <i class="fas fa-repeat mr-1"></i>Repeatable Section
            </span>
            @endif
        </div>

        @if($section->is_repeatable)
        <!-- Repeatable Section -->
        <div x-data="{ instances: 1 }">
            <template x-for="instance in instances" :key="instance">
                <div class="mb-6 pb-6 border-b border-gray-200 last:border-0">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="font-semibold text-gray-700 dark:text-gray-300">Entry <span x-text="instance"></span></h4>
                        <button type="button" x-show="instance > 1" @click="instances--" class="text-red-600 hover:text-red-800 text-sm">
                            <i class="fas fa-trash mr-1"></i>Remove
                        </button>
                    </div>
                    
                    @if($section->layout_type === 'table')
                    <!-- Table Layout for Repeatable -->
                    <div class="grid grid-cols-{{ count($section->fields) }} gap-4">
                        @foreach($section->fields as $field)
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                {{ $field->field_label }}
                                @if($field->is_required)<span class="text-red-600">*</span>@endif
                            </label>
                            @include('components.template-field', ['field' => $field, 'namePrefix' => "field_{$field->id}_instance_", 'useInstance' => true])
                        </div>
                        @endforeach
                    </div>
                    @else
                    <!-- Grid Layout for Repeatable -->
                    <div class="grid grid-cols-1 {{ $section->layout_type === 'two_column' ? 'md:grid-cols-2' : '' }} gap-4">
                        @foreach($section->fields as $field)
                        <div class="{{ $field->width === 'full' ? 'md:col-span-full' : '' }}">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                {{ $field->field_label }}
                                @if($field->is_required)<span class="text-red-600">*</span>@endif
                            </label>
                            @include('components.template-field', ['field' => $field, 'namePrefix' => "field_{$field->id}_instance_", 'useInstance' => true])
                            @if($field->help_text)
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $field->help_text }}</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </template>
            
            <input type="hidden" :name="'section_{{ $section->id }}_instances'" x-model="instances">
            <button type="button" @click="instances++" class="bg-green-100 hover:bg-green-200 text-green-800 px-4 py-2 rounded font-semibold text-sm transition">
                <i class="fas fa-plus mr-2"></i>Add Another Entry
            </button>
        </div>
        @else
        <!-- Regular Section -->
        <div class="grid grid-cols-1 {{ $section->layout_type === 'two_column' ? 'md:grid-cols-2' : '' }} gap-6">
            @foreach($section->fields as $field)
            <div class="{{ $field->width === 'full' ? 'md:col-span-full' : ($field->width === 'half' ? 'md:col-span-1' : '') }}">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    {{ $field->field_label }}
                    @if($field->is_required)<span class="text-red-600">*</span>@endif
                </label>
                @include('components.template-field', ['field' => $field, 'namePrefix' => "field_{$field->id}", 'useInstance' => false])
                @if($field->help_text)
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $field->help_text }}</p>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>
    @endforeach
</div>
@else
<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
    <i class="fas fa-exclamation-triangle text-yellow-600 text-3xl mb-3"></i>
    <p class="text-yellow-800 font-semibold">No template available for this equipment category</p>
    <p class="text-yellow-700 text-sm mt-2">Please contact administrator to create a template for this category.</p>
</div>
@endif
