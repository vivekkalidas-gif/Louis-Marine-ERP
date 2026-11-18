@extends('layouts.app')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <div class="mb-4 sm:mb-0">
        <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
            {{ isset($equipment) ? 'Edit Equipment' : 'Add New Equipment' }}
        </h1>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 mt-4 p-5">
        <form action="{{ isset($equipment) ? route('engineer.equipment.update', $equipment->id) : route('engineer.equipment.store') }}" method="POST">
            @csrf
            @if(isset($equipment))
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-800 dark:text-gray-300" for="name">Equipment Name <span class="text-rose-500">*</span></label>
                    <input id="name" class="form-input w-full bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 focus:border-blue-500 dark:focus:border-blue-400" type="text" name="name" value="{{ old('name', $equipment->name ?? '') }}" required />
                </div>

                <!-- Code / Serial -->
                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-800 dark:text-gray-300" for="code">Serial No / Code</label>
                    <input id="code" class="form-input w-full bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 focus:border-blue-500 dark:focus:border-blue-400" type="text" name="code" value="{{ old('code', $equipment->code ?? '') }}" />
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-800 dark:text-gray-300" for="category_id">Equipment Type <span class="text-rose-500">*</span></label>
                    <div class="flex gap-2">
                        <select id="category_id" class="form-select w-full bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 focus:border-blue-500 dark:focus:border-blue-400" name="category_id" required>
                            <option value="">Select Type</option>
                            @foreach($types as $type)
                                <option value="{{ $type->id }}" {{ old('category_id', $equipment->category_id ?? '') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                        <button type="button" onclick="addType()" class="btn bg-blue-600 hover:bg-blue-700 text-white px-3" title="Add New Type">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 16 16">
                                <path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Brand -->
                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-800 dark:text-gray-300" for="brand">Brand</label>
                    <input id="brand" class="form-input w-full bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 focus:border-blue-500 dark:focus:border-blue-400" type="text" name="brand" value="{{ old('brand', $equipment->brand ?? '') }}" />
                </div>

                <!-- Model -->
                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-800 dark:text-gray-300" for="model">Model</label>
                    <input id="model" class="form-input w-full bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 focus:border-blue-500 dark:focus:border-blue-400" type="text" name="model" value="{{ old('model', $equipment->model ?? '') }}" />
                </div>

                <!-- Assigned Engineer -->
                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-800 dark:text-gray-300" for="assigned_engineer_id">Assigned Engineer</label>
                    <select id="assigned_engineer_id" class="form-select w-full bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 focus:border-blue-500 dark:focus:border-blue-400" name="assigned_engineer_id">
                        <option value="">Unassigned</option>
                        @foreach($engineers as $engineer)
                            <option value="{{ $engineer->id }}" {{ old('assigned_engineer_id', $equipment->assigned_engineer_id ?? '') == $engineer->id ? 'selected' : '' }}>
                                {{ $engineer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1 text-gray-800 dark:text-gray-300" for="description">Description</label>
                    <textarea id="description" class="form-textarea w-full bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 focus:border-blue-500 dark:focus:border-blue-400" rows="3" name="description">{{ old('description', $equipment->description ?? '') }}</textarea>
                </div>

                <!-- Calibration Section -->
                <div class="md:col-span-2 border-t border-gray-100 dark:border-gray-700 pt-4 mt-2">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Calibration Details</h3>
                    
                    <div class="flex items-center mb-4">
                        <input type="checkbox" id="calibration_required" name="calibration_required" value="1" class="form-checkbox bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-blue-600" 
                            {{ old('calibration_required', $equipment->calibration_required ?? false) ? 'checked' : '' }}
                            onchange="toggleCalibrationField()">
                        <label for="calibration_required" class="ml-2 text-sm text-gray-600 dark:text-gray-400">Calibration Required</label>
                    </div>

                    <div id="calibration_field" class="{{ old('calibration_required', $equipment->calibration_required ?? false) ? '' : 'hidden' }}">
                        <label class="block text-sm font-medium mb-1 text-gray-800 dark:text-gray-300" for="calibration_expiry_date">Current Calibration Expiry Date</label>
                        <input id="calibration_expiry_date" class="form-input w-full md:w-1/2 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 focus:border-blue-500 dark:focus:border-blue-400" type="date" name="calibration_expiry_date" 
                            value="{{ old('calibration_expiry_date', isset($equipment->calibration_expiry_date) ? \Carbon\Carbon::parse($equipment->calibration_expiry_date)->format('Y-m-d') : '') }}" />
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Note: Uploading a new certificate will automatically update this date history.</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end mt-8 gap-4">
                <a href="{{ route('engineer.equipment.index') }}" class="btn border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-700 transition">Cancel</a>
                <button type="submit" class="btn bg-blue-600 hover:bg-blue-700 text-white shadow-sm transition">
                    {{ isset($equipment) ? 'Update Equipment' : 'Save Equipment' }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleCalibrationField() {
        const checkbox = document.getElementById('calibration_required');
        const field = document.getElementById('calibration_field');
        const input = document.getElementById('calibration_expiry_date');
        
        if (checkbox.checked) {
            field.classList.remove('hidden');
            input.required = true;
        } else {
            field.classList.add('hidden');
            input.required = false;
            input.value = '';
        }
    }

    function addType() {
        Swal.fire({
            title: 'Add New Equipment Type',
            input: 'text',
            inputLabel: 'Type Name',
            inputPlaceholder: 'e.g. Multimeter',
            showCancelButton: true,
            confirmButtonText: 'Add',
            showLoaderOnConfirm: true,
            customClass: {
                popup: 'dark:bg-gray-800 dark:text-gray-100',
                input: 'dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600',
                confirmButton: 'bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded',
                cancelButton: 'bg-gray-50 dark:bg-gray-700 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded ml-2'
            },
            preConfirm: (name) => {
                if (!name) {
                    Swal.showValidationMessage('Please enter a type name');
                    return;
                }
                return fetch('{{ route('engineer.equipment.categories.quick-store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ name: name })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw new Error(err.message || response.statusText);
                        });
                    }
                    return response.json();
                })
                .catch(error => {
                    Swal.showValidationMessage(
                        `Request failed: ${error}`
                    );
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed && result.value && result.value.success) {
                // Add to select and select it
                const select = document.getElementById('category_id');
                const option = new Option(result.value.category.name, result.value.category.id);
                select.add(option, undefined); // Add to end
                select.value = result.value.category.id;
                
                Swal.fire({
                    title: 'Success!',
                    text: result.value.message,
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false,
                    customClass: {
                         popup: 'dark:bg-gray-800 dark:text-gray-100',
                    }
                });
            }
        });
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
