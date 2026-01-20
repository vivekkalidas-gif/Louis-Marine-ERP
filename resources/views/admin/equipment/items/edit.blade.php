<x-app-layout>
    <x-slot name="header">
        <h2 class="theme-text-header">Edit Equipment Item</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="theme-card">
                <form method="POST" action="{{ route('admin.equipment.items.update', $item->id) }}" x-data="equipmentForm()">
                    @csrf
                    @method('PUT')

                    <!-- Validation Errors -->
                    @if ($errors->any())
                        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <strong class="font-bold">Validation Error!</strong>
                            <span class="block sm:inline">Please check the form below for errors.</span>
                            <ul class="mt-2 list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Basic Info -->
                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-marine-900 dark:text-marine-100 mb-4 pb-2 border-b dark:border-marine-800">
                            Basic Information
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="theme-label">Category *</label>
                                <select name="category_id" x-model="categoryId" @change="loadAttributes()" class="theme-input" required>
                                    <option value="">-- Select Category --</option>
                                    @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" 
                                            data-templates="{{ $cat->attributeTemplates->toJson() }}"
                                            {{ old('category_id', $item->category_id) == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="theme-label">Equipment Code</label>
                                <input type="text" name="code" class="theme-input bg-gray-100 dark:bg-gray-700" value="{{ $item->code }}" readonly>
                                @error('code')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="theme-label">Equipment Name *</label>
                                <input type="text" name="name" class="theme-input" required value="{{ old('name', $item->name) }}">
                                @error('name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="theme-label">Brand</label>
                                <input type="text" name="brand" class="theme-input" value="{{ old('brand', $item->brand) }}">
                            </div>

                            <div>
                                <label class="theme-label">Model</label>
                                <input type="text" name="model" class="theme-input" value="{{ old('model', $item->model) }}">
                            </div>

                            <div>
                                <label class="theme-label">Unit *</label>
                                <select name="unit" class="theme-input" required>
                                    <option value="Nos" {{ old('unit', $item->unit) == 'Nos' ? 'selected' : '' }}>Nos (Numbers)</option>
                                    <option value="Sets" {{ old('unit', $item->unit) == 'Sets' ? 'selected' : '' }}>Sets</option>
                                    <option value="Ltrs" {{ old('unit', $item->unit) == 'Ltrs' ? 'selected' : '' }}>Ltrs (Liters)</option>
                                    <option value="Pairs" {{ old('unit', $item->unit) == 'Pairs' ? 'selected' : '' }}>Pairs</option>
                                    <option value="Kgs" {{ old('unit', $item->unit) == 'Kgs' ? 'selected' : '' }}>Kgs (Kilograms)</option>
                                </select>
                            </div>

                            <div>
                                <label class="flex items-center mt-6">
                                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $item->is_active) ? 'checked' : '' }} class="rounded">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active</span>
                                </label>
                            </div>

                            <div class="md:col-span-2">
                                <label class="theme-label">Description</label>
                                <textarea name="description" rows="3" class="theme-input">{{ old('description', $item->description) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Attributes -->
                    <div x-show="attributeTemplates.length > 0" x-transition>
                        <h3 class="text-lg font-bold text-marine-900 dark:text-marine-100 mb-4 pb-2 border-b dark:border-marine-800">
                            Equipment Attributes
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <template x-for="(template, index) in attributeTemplates" :key="index">
                                <div>
                                    <label class="theme-label" x-text="template.attribute_label + (template.is_required ? ' *' : '')"></label>
                                    
                                    <!-- Text Input -->
                                    <template x-if="template.attribute_type === 'text'">
                                        <input type="text" 
                                               :name="'attributes[' + template.attribute_key + ']'" 
                                               class="theme-input"
                                               :value="getAttributeValue(template.attribute_key)"
                                               :required="template.is_required">
                                    </template>
                                    
                                    <!-- Number Input -->
                                    <template x-if="template.attribute_type === 'number'">
                                        <input type="number" 
                                               :name="'attributes[' + template.attribute_key + ']'" 
                                               class="theme-input"
                                               step="any"
                                               :value="getAttributeValue(template.attribute_key)"
                                               :required="template.is_required">
                                    </template>
                                    
                                    <!-- Select Input -->
                                    <template x-if="template.attribute_type === 'select'">
                                        <select :name="'attributes[' + template.attribute_key + ']'" 
                                                class="theme-input"
                                                :required="template.is_required">
                                            <option value="">-- Select --</option>
                                            <template x-for="option in template.options" :key="option">
                                                <option :value="option" 
                                                        :selected="getAttributeValue(template.attribute_key) == option"
                                                        x-text="option"></option>
                                            </template>
                                        </select>
                                    </template>
                                    
                                    <!-- Boolean Input -->
                                    <template x-if="template.attribute_type === 'boolean'">
                                        <label class="flex items-center">
                                            <input type="checkbox" 
                                                   :name="'attributes[' + template.attribute_key + ']'" 
                                                   value="1"
                                                   :checked="getAttributeValue(template.attribute_key) == '1'"
                                                   class="rounded">
                                            <span class="ml-2 text-sm">Yes</span>
                                        </label>
                                    </template>
                                    
                                    <!-- Date Input -->
                                    <template x-if="template.attribute_type === 'date'">
                                        <input type="date" 
                                               :name="'attributes[' + template.attribute_key + ']'" 
                                               class="theme-input"
                                               :value="getAttributeValue(template.attribute_key)"
                                               :required="template.is_required">
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="mt-8 flex justify-end space-x-3">
                        <a href="{{ route('admin.equipment.categories.index') }}" class="bg-gray-50 dark:bg-gray-7000 hover:bg-gray-600 text-white px-6 py-2 rounded">
                            Cancel
                        </a>
                        <button type="submit" class="bg-marine-700 hover:bg-marine-600 text-white px-6 py-2 rounded">
                            Update Equipment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function equipmentForm() {
            return {
                categoryId: '{{ old('category_id', $item->category_id) }}',
                attributeTemplates: [],
                existingAttributes: @json($item->attributes ?? []),
                
                init() {
                    this.loadAttributes();
                },
                
                loadAttributes() {
                    const select = document.querySelector('select[name="category_id"]');
                    const selected = select.options[select.selectedIndex];
                    
                    if (selected && selected.dataset.templates) {
                        this.attributeTemplates = JSON.parse(selected.dataset.templates);
                    } else {
                        this.attributeTemplates = [];
                    }
                },
                
                getAttributeValue(key) {
                    return this.existingAttributes[key] || '';
                }
            }
        }
    </script>
</x-app-layout>
