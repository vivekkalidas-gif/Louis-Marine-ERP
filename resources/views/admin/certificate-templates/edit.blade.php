<x-app-layout>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .drag-handle { cursor: grab; }
        .drag-handle:active { cursor: grabbing; }
        .sortable-ghost { opacity: 0.4; background-color: #f3f4f6; }
    </style>
<div class="container mx-auto px-4 py-8">
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
                <a href="{{ route('admin.certificate-templates.preview', $certificateTemplate) }}" target="_blank" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-semibold transition">
                    <i class="fas fa-eye mr-2"></i>Preview
                </a>
                <form action="{{ route('admin.certificate-templates.destroy', $certificateTemplate) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this template? This cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold transition">
                        <i class="fas fa-trash mr-2"></i>Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        {{ session('error') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Template Settings -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">Template Settings</h2>
                <form action="{{ route('admin.certificate-templates.update', $certificateTemplate) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Template Name</label>
                            <input type="text" name="name" value="{{ old('name', $certificateTemplate->name) }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Equipment Category</label>
                            <select name="equipment_category_id" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $certificateTemplate->equipment_category_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Certificate Title</label>
                            <input type="text" name="title" value="{{ old('title', $certificateTemplate->title) }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="flex gap-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1" {{ $certificateTemplate->is_active ? 'checked' : '' }}
                                    class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                                <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Active</span>
                            </label>

                            <label class="flex items-center">
                                <input type="checkbox" name="is_default" value="1" {{ $certificateTemplate->is_default ? 'checked' : '' }}
                                    class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                                <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Default</span>
                            </label>
                        </div>

                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition">
                            Update Settings
                        </button>
                    </div>
                </form>
            </div>

            <!-- Sections -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">Sections & Fields</h2>
                    <button onclick="showAddSectionModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold transition text-sm">
                        <i class="fas fa-plus mr-2"></i>Add Section
                    </button>
                </div>

                <div id="sections-container">
                @forelse($certificateTemplate->sections as $section)
                <div class="border border-gray-200 rounded-lg mb-4 overflow-hidden section-item" data-section-id="{{ $section->id }}">
                    <!-- Section Header -->
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 flex justify-between items-center">
                        <div class="text-gray-400 mr-3 cursor-move section-drag-handle">
                            <i class="fas fa-grip-vertical"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-900 dark:text-gray-100">{{ $section->name }}</h3>
                            <p class="text-sm text-gray-600">{{ $section->description }}</p>
                            <div class="flex gap-2 mt-2">
                                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">{{ ucfirst(str_replace('_', ' ', $section->layout_type)) }}</span>
                                @if($section->is_repeatable)
                                <span class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded">Repeatable</span>
                                @endif
                                <span class="text-xs bg-gray-100 text-gray-800 dark:text-gray-200 px-2 py-1 rounded">{{ $section->fields->count() }} fields</span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button onclick='editSection(@json($section, JSON_HEX_APOS | JSON_HEX_QUOT))' class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </button>
                            <button onclick="showAddFieldModal('{{ $section->id }}')" class="text-green-600 hover:text-green-800 text-sm font-semibold">
                                <i class="fas fa-plus mr-1"></i>Add Field
                            </button>
                            <form action="{{ route('admin.template-sections.destroy', $section) }}" method="POST" onsubmit="return confirm('Delete this section and all its fields?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-semibold">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Fields -->
                    @if($section->fields->count() > 0)
                    <div class="p-4 space-y-2 sortable-fields" data-section-id="{{ $section->id }}">
                        @foreach($section->fields as $field)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded border border-gray-200" data-field-id="{{ $field->id }}">
                            <div class="flex-1 flex items-center">
                                <div class="text-gray-400 mr-3 drag-handle">
                                    <i class="fas fa-grip-vertical"></i>
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $field->field_label }}</div>
                                <div class="text-sm text-gray-600">
                                    <span class="text-xs bg-gray-200 px-2 py-1 rounded">{{ $field->field_type }}</span>
                                    <span class="text-xs bg-gray-200 px-2 py-1 rounded ml-1">{{ $field->width }}</span>
                                    @if($field->is_required)
                                    <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded ml-1">Required</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                            <div class="flex gap-2">
                                <button onclick='editField(@json($field, JSON_HEX_APOS | JSON_HEX_QUOT))' type="button" class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </button>
                                <form action="{{ route('admin.template-fields.destroy', $field) }}" method="POST" onsubmit="return confirm('Delete this field?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-semibold">
                                    <i class="fas fa-trash mr-1"></i>Delete
                                </button>
                            </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                @empty
                <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                    <i class="fas fa-folder-open text-4xl mb-4"></i>
                    <p>No sections yet. Add your first section to get started.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Stats -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h3 class="font-bold text-gray-900 dark:text-gray-100 mb-4">Template Stats</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Sections:</span>
                        <span class="font-bold">{{ $certificateTemplate->sections->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Fields:</span>
                        <span class="font-bold">{{ $certificateTemplate->sections->sum(fn($s) => $s->fields->count()) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Status:</span>
                        <span class="font-bold {{ $certificateTemplate->is_active ? 'text-green-600' : 'text-gray-600' }}">
                            {{ $certificateTemplate->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Help -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="font-bold text-blue-900 mb-2">Need Help?</h3>
                <ul class="text-sm text-blue-800 space-y-2">
                    <li><i class="fas fa-check mr-2"></i>Sections group related fields</li>
                    <li><i class="fas fa-check mr-2"></i>Fields define form inputs</li>
                    <li><i class="fas fa-check mr-2"></i>Repeatable sections create tables</li>
                    <li><i class="fas fa-check mr-2"></i>Preview shows final layout</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Add Section Modal -->
<div id="addSectionModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg max-w-2xl w-full p-6">
        <h3 id="sectionModalTitle" class="text-xl font-bold mb-4">Add New Section</h3>
        <form id="addSectionForm" action="{{ route('admin.template-sections.store', $certificateTemplate) }}" method="POST">
            @csrf
            <input type="hidden" name="_method" id="sectionFormMethod" value="POST">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold mb-2">Section Name *</label>
                    <input type="text" name="name" required class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-2">Description</label>
                    <textarea name="description" rows="2" class="w-full px-4 py-2 border rounded-lg"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-2">Layout Type *</label>
                    <select name="layout_type" required class="w-full px-4 py-2 border rounded-lg">
                        <option value="single_column">Single Column</option>
                        <option value="two_column">Two Column</option>
                        <option value="table">Table (for repeatable data)</option>
                    </select>
                </div>
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_repeatable" value="1" class="w-4 h-4">
                        <span class="ml-2 text-sm">Repeatable Section (for multiple entries)</span>
                    </label>
                </div>
                <div class="flex gap-4">
                    <button type="submit" id="sectionSubmitBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold">
                        Add Section
                    </button>
                    <button type="button" onclick="hideAddSectionModal()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 dark:text-gray-300 px-6 py-2 rounded-lg font-semibold">
                        Cancel
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Add Field Modal -->
<div id="addFieldModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg max-w-2xl w-full p-6 max-h-screen overflow-y-auto">
        <h3 id="fieldModalTitle" class="text-xl font-bold mb-4">Add New Field</h3>
        <form id="addFieldForm" method="POST">
            @csrf
            <input type="hidden" name="_method" id="fieldFormMethod" value="POST">
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-2">Field Name (ID) *</label>
                        <input type="text" name="field_name" required class="w-full px-4 py-2 border rounded-lg" placeholder="e.g., serial_number">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2">Field Label *</label>
                        <input type="text" name="field_label" required class="w-full px-4 py-2 border rounded-lg" placeholder="e.g., Serial Number">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-2">Field Type *</label>
                        <select name="field_type" id="field_type" required class="w-full px-4 py-2 border rounded-lg" onchange="toggleFieldOptions()">
                            <option value="text">Text</option>
                            <option value="textarea">Textarea</option>
                            <option value="number">Number</option>
                            <option value="date">Date</option>
                            <option value="dropdown">Dropdown</option>
                            <option value="checkbox">Checkbox</option>
                            <option value="radio">Radio</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2">Width *</label>
                        <select name="width" required class="w-full px-4 py-2 border rounded-lg">
                            <option value="full">Full Width</option>
                            <option value="half">Half Width</option>
                            <option value="third">Third Width</option>
                        </select>
                    </div>
                </div>
                <div id="field_options_container" class="hidden">
                    <label class="block text-sm font-semibold mb-2">Options (one per line)</label>
                    <textarea name="field_options_text" rows="4" class="w-full px-4 py-2 border rounded-lg" placeholder="Option 1&#10;Option 2&#10;Option 3"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-2">Placeholder</label>
                    <input type="text" name="placeholder" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-2">Help Text</label>
                    <input type="text" name="help_text" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_required" value="1" class="w-4 h-4">
                        <span class="ml-2 text-sm font-semibold">Required Field</span>
                    </label>
                </div>
                <div class="flex gap-4">
                    <button type="submit" id="fieldSubmitBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold">
                        Add Field
                    </button>
                    <button type="button" onclick="hideAddFieldModal()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 dark:text-gray-300 px-6 py-2 rounded-lg font-semibold">
                        Cancel
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function showAddSectionModal() {
    resetSectionModal();
    document.getElementById('addSectionModal').classList.remove('hidden');
}

function hideAddSectionModal() {
    document.getElementById('addSectionModal').classList.add('hidden');
}

function editSection(section) {
    resetSectionModal();
    const modal = document.getElementById('addSectionModal');
    const form = document.getElementById('addSectionForm');
    
    // Update Modal UI
    document.getElementById('sectionModalTitle').innerText = 'Edit Section';
    document.getElementById('sectionSubmitBtn').innerText = 'Update Section';
    document.getElementById('sectionFormMethod').value = 'PUT';
    
    // Set Action URL
    form.action = `{{ url('admin/template-sections') }}/${section.id}`;
    
    // Populate Data
    form.querySelector('[name="name"]').value = section.name;
    form.querySelector('[name="description"]').value = section.description || '';
    form.querySelector('[name="layout_type"]').value = section.layout_type;
    form.querySelector('[name="is_repeatable"]').checked = !!section.is_repeatable;
    
    modal.classList.remove('hidden');
}

function resetSectionModal() {
    const form = document.getElementById('addSectionForm');
    form.reset();
    form.action = "{{ route('admin.template-sections.store', $certificateTemplate) }}";
    document.getElementById('sectionModalTitle').innerText = 'Add New Section';
    document.getElementById('sectionSubmitBtn').innerText = 'Add Section';
    document.getElementById('sectionFormMethod').value = 'POST';
}

function showAddFieldModal(sectionId) {
    resetFieldModal();
    const form = document.getElementById('addFieldForm');
    form.action = `{{ url('admin/template-sections') }}/${sectionId}/fields`;
    document.getElementById('addFieldModal').classList.remove('hidden');
}

function editField(field) {
    resetFieldModal();
    const modal = document.getElementById('addFieldModal');
    const form = document.getElementById('addFieldForm');
    
    // Update Modal UI
    document.getElementById('fieldModalTitle').innerText = 'Edit Field';
    document.getElementById('fieldSubmitBtn').innerText = 'Update Field';
    document.getElementById('fieldFormMethod').value = 'PUT';
    
    // Set Action URL
    form.action = `{{ url('admin/template-fields') }}/${field.id}`;
    
    // Populate Data
    form.querySelector('[name="field_name"]').value = field.field_name;
    form.querySelector('[name="field_label"]').value = field.field_label;
    form.querySelector('[name="field_type"]').value = field.field_type;
    form.querySelector('[name="width"]').value = field.width;
    form.querySelector('[name="placeholder"]').value = field.placeholder || '';
    form.querySelector('[name="help_text"]').value = field.help_text || '';
    form.querySelector('[name="is_required"]').checked = !!field.is_required;
    
    // Handle Options
    if (field.field_options) {
        let options = [];
        if (field.field_options.options) {
            options = field.field_options.options;
        } else if (Array.isArray(field.field_options)) {
            options = field.field_options;
        }
        
        if (options.length > 0) {
            document.querySelector('[name="field_options_text"]').value = options.join('\n');
            // Ensure options container is shown if type requires it
            toggleFieldOptions(); 
        }
    } else {
         toggleFieldOptions();
    }
    
    modal.classList.remove('hidden');
}

function resetFieldModal() {
    const form = document.getElementById('addFieldForm');
    form.reset();
    document.getElementById('fieldModalTitle').innerText = 'Add New Field';
    document.getElementById('fieldSubmitBtn').innerText = 'Add Field';
    document.getElementById('fieldFormMethod').value = 'POST';
    document.getElementById('field_options_container').classList.add('hidden');
    // Remove dynamic inputs added by previous submissions
    const dynamicInputs = form.querySelectorAll('input[type="hidden"][name^="field_options["]');
    dynamicInputs.forEach(input => input.remove());
}

function hideAddFieldModal() {
    document.getElementById('addFieldModal').classList.add('hidden');
}

function toggleFieldOptions() {
    const fieldType = document.getElementById('field_type').value;
    const container = document.getElementById('field_options_container');
    if (fieldType === 'dropdown' || fieldType === 'radio') {
        container.classList.remove('hidden');
    } else {
        container.classList.add('hidden');
    }
}

// Handle field options conversion
document.getElementById('addFieldForm').addEventListener('submit', function(e) {
    const fieldType = document.getElementById('field_type').value;
    if (fieldType === 'dropdown' || fieldType === 'radio') {
        const optionsText = document.querySelector('[name="field_options_text"]').value;
        const options = optionsText.split('\n').filter(o => o.trim());
        
        // Create hidden inputs for each option
        options.forEach((option, index) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `field_options[${index}]`;
            input.value = option.trim();
            this.appendChild(input);
        });
    }
});


// Initialize SortableJS
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Sortable === 'undefined') {
        console.error('SortableJS not loaded!');
        alert('Error: Sorting library could not check internet connection.');
        return;
    }
    
    // Initialize field sorting within sections
    const containers = document.querySelectorAll('.sortable-fields');
    console.log('Found sortable containers:', containers.length);

    containers.forEach(container => {
        new Sortable(container, {
            filter: 'button, a',
            preventOnFilter: false,
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: function (evt) {
                console.log('Drag ended', evt);
                const sectionId = evt.to.getAttribute('data-section-id');
                const items = evt.to.querySelectorAll('[data-field-id]');
                const fields = [];
                
                items.forEach((item, index) => {
                    fields.push({
                        id: item.getAttribute('data-field-id'),
                        order: index + 1
                    });
                });

                // Send update to server
                fetch('{{ route("admin.template-fields.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ fields: fields })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Optional: Show success toast
                        console.log('Order updated');
                    } else {
                        alert('Failed to update order');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error update field order');
                });
            }
        });
    });

    // Initialize section sorting
    const sectionsContainer = document.getElementById('sections-container');
    if (sectionsContainer) {
        new Sortable(sectionsContainer, {
            filter: 'button, a, input, select, textarea',
            preventOnFilter: false,
            handle: '.section-drag-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: function (evt) {
                console.log('Section drag ended', evt);
                const items = sectionsContainer.querySelectorAll('[data-section-id]');
                const sections = [];
                
                items.forEach((item, index) => {
                    sections.push({
                        id: item.getAttribute('data-section-id'),
                        order: index + 1
                    });
                });

                // Send update to server
                fetch('{{ route("admin.template-sections.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ sections: sections })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Section order updated');
                    } else {
                        alert('Failed to update section order');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating section order');
                });
            }
        });
    }
});


function saveFieldOrder(container) {
    const sectionId = container.getAttribute('data-section-id');
    const items = container.querySelectorAll('[data-field-id]');
    const fields = [];
    
    items.forEach((item, index) => {
        fields.push({
            id: item.getAttribute('data-field-id'),
            order: index + 1
        });
    });

    // Send update to server
    fetch('{{ route("admin.template-fields.reorder") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ fields: fields })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Order updated via manual move');
        } else {
            alert('Failed to update order');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error update field order');
    });
}
</script>
</x-app-layout>
