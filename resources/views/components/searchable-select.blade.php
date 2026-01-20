@props(['options', 'name', 'label' => null, 'placeholder' => 'Select an option', 'selected' => null])

<div x-data="{
    open: false,
    search: '',
    selected: @js($selected),
    options: @js($options),
    
    get filteredOptions() {
        if (this.search === '') {
            return this.options;
        }
        return this.options.filter(option => 
            option.label.toLowerCase().includes(this.search.toLowerCase())
        );
    },
    
    get selectedLabel() {
        if (!this.selected) return null;
        const option = this.options.find(o => o.value == this.selected);
        return option ? option.label : null;
    },

    select(value) {
        this.selected = value;
        this.open = false;
        this.search = '';
    },
    
    toggle() {
        if (this.open) {
            return this.close();
        }
        this.open = true;
        this.$nextTick(() => this.$refs.searchInput.focus());
    },
    
    close() {
        this.open = false;
        this.search = '';
    }
}" 
x-modelable="selected"
class="relative" 
@click.outside="close()"
{{ $attributes }}
>
    @if($label)
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            {{ $label }}
        </label>
    @endif

    <!-- Hidden Input for Form Submission -->
    <input type="hidden" :name="'{{ $name }}'" :value="selected">

    <!-- Trigger Button -->
    <button 
        type="button" 
        @click="toggle()" 
        class="relative w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        aria-haspopup="listbox" 
        :aria-expanded="open" 
        aria-labelledby="listbox-label"
    >
        <span class="block truncate" :class="selected ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400'">
            <span x-text="selectedLabel || '{{ $placeholder }}'"></span>
        </span>
        <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
            <!-- Heroicon name: solid/selector -->
            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </span>
    </button>

    <!-- Dropdown Menu -->
    <div 
        x-show="open" 
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="absolute z-50 mt-1 w-full bg-white dark:bg-gray-700 shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
        style="display: none;"
    >
        <!-- Search Input -->
        <div class="sticky top-0 z-10 bg-white dark:bg-gray-700 px-2 py-1.5 border-b border-gray-200 dark:border-gray-600">
            <input 
                x-ref="searchInput"
                type="text" 
                x-model="search" 
                class="block w-full border-gray-300 dark:border-gray-500 dark:bg-gray-800 dark:text-white rounded-md sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 placeholder-gray-400" 
                placeholder="Search..."
                @keydown.enter.prevent
            >
        </div>

        <!-- Options List -->
        <ul class="pt-1" role="listbox">
            <template x-for="option in filteredOptions" :key="option.value">
                <li 
                    @click="select(option.value)"
                    class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-indigo-600 hover:text-white text-gray-900 dark:text-white group"
                    role="option"
                    :class="{ 'bg-indigo-600 text-white': selected == option.value }"
                >
                    <span class="block truncate font-normal" :class="{ 'font-semibold': selected == option.value }" x-text="option.label"></span>
                    
                    <span 
                        x-show="selected == option.value"
                        class="absolute inset-y-0 right-0 flex items-center pr-4 text-indigo-600 group-hover:text-white"
                        :class="{ 'text-white': selected == option.value }"
                    >
                        <!-- Heroicon name: solid/check -->
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                </li>
            </template>
            
            <li x-show="filteredOptions.length === 0" class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500 dark:text-gray-400 italic">
                No results found
            </li>
        </ul>
    </div>
</div>
