<x-layouts.frontend>
    <!-- Breadcrumb -->
    <div class="bg-gray-100 py-4 border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex text-sm text-gray-500 dark:text-gray-400">
                <a href="{{ route('home') }}" class="hover:text-red-600">Home</a>
                <span class="mx-2">/</span>
                <a href="{{ route('products.index') }}" class="hover:text-red-600">Products</a>
                <span class="mx-2">/</span>
                @if($product->category)
                    <a href="{{ route('products.index', ['category' => $product->category->name]) }}" class="hover:text-red-600">{{ $product->category->name }}</a>
                    <span class="mx-2">/</span>
                @endif
                <span class="text-gray-800 dark:text-gray-200 font-semibold">{{ $product->name }}</span>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="lg:grid lg:grid-cols-2 lg:gap-x-12 lg:items-start">
            
            <!-- Image Gallery -->
            <div class="flex flex-col-reverse">
                <!-- Thumbnail List (if multiple) -->
                @php
                    $images = $product->gallery_images ?? [];
                    // Ensure main image is included if not in gallery? Usually gallery has all. 
                    // Or just use gallery. 
                @endphp
                
                @if(count($images) > 1)
                <div class="hidden mt-6 w-full max-w-2xl mx-auto sm:block lg:max-w-none">
                    <div class="grid grid-cols-4 gap-6" x-data>
                        @foreach($images as $image)
                            <button @click="$dispatch('change-image', '{{ Storage::url($image) }}')" class="relative h-24 bg-white dark:bg-gray-800 rounded-md flex items-center justify-center text-sm font-medium uppercase text-gray-900 dark:text-gray-100 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 focus:outline-none focus:ring focus:ring-offset-4 focus:ring-opacity-50 border border-gray-200">
                                <span class="sr-only">Image {{ $loop->iteration }}</span>
                                <span class="absolute inset-0 rounded-md overflow-hidden">
                                    <img src="{{ Storage::url($image) }}" alt="" class="w-full h-full object-center object-contain">
                                </span>
                            </button>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Main Image -->
                <div class="w-full aspect-w-1 aspect-h-1 rounded-lg overflow-hidden sm:aspect-w-2 sm:aspect-h-3" x-data="{ mainSrc: '{{ $product->image_path ? Storage::url($product->image_path) : '' }}' }" @change-image.window="mainSrc = $event.detail">
                    @if($product->image_path)
                        <img :src="mainSrc" alt="{{ $product->name }}" class="w-full h-[500px] object-center object-contain bg-white dark:bg-gray-800 shadow-lg rounded-lg border border-gray-100">
                    @else
                        <div class="w-full h-[500px] bg-gray-100 flex items-center justify-center text-gray-400 rounded-lg border border-gray-200">
                            <svg class="h-24 w-24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Product Info -->
            <div class="mt-10 px-2 sm:px-0 sm:mt-16 lg:mt-0">
                <div class="mb-6">
                    <h1 class="text-3xl font-extrabold tracking-tight text-[#0b1c3c]">{{ $product->name }}</h1>
                    <div class="mt-3">
                        <h2 class="sr-only">Product information</h2>
                        @if($product->category)
                            <a href="{{ route('products.index', ['category' => $product->category->name]) }}" class="inline-block bg-gray-100 text-gray-600 text-xs px-3 py-1 rounded-full uppercase tracking-wide font-bold hover:bg-red-50 hover:text-red-600 transition">{{ $product->category->name }}</a>
                        @endif
                    </div>
                </div>

                <div class="mt-6">
                    <h3 class="sr-only">Description</h3>
                    <div class="text-base text-gray-700 dark:text-gray-300 space-y-6 leading-relaxed">
                        <div>{!! $product->short_description ?? Str::limit($product->description, 200) !!}</div>
                    </div>
                </div>
                
                @if($product->specifications)
                    <div class="mt-8 border-t border-gray-200 pt-8">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100 uppercase tracking-wide">Specifications</h3>
                        <div class="mt-4 prose prose-sm text-gray-500 dark:text-gray-400">
                            <ul role="list" class="list-disc pl-5 space-y-2">
                                @foreach($product->specifications as $key => $value)
                                    <li><strong>{{ $key }}:</strong> {{ $value }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
                
                <div class="mt-10 flex gap-4">
                    {{-- Add to Quote Button - Temporarily disabled until quote system is implemented
                    <button type="button" 
                        @click="
                            adding = true;
                            fetch('{{ route('quote.add') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                },
                                body: JSON.stringify({
                                    product_id: '{{ $product->id }}',
                                    quantity: 1
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                adding = false;
                                if(data.success) {
                                    if(confirm('Added to quote list! View list now?')) {
                                        window.location.href = '{{ route('quote.index') }}';
                                    } else {
                                        window.location.reload();
                                    }
                                }
                            })
                            .catch(error => {
                                adding = false;
                                alert('Error adding to quote list.');
                                console.error(error);
                            });
                        "
                        class="flex-1 bg-red-600 border border-transparent rounded-md py-3 px-8 flex items-center justify-center text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 shadow-lg transform active:scale-95 transition disabled:opacity-50"
                        :disabled="adding">
                        <span x-show="!adding">Add to Quote List</span>
                        <span x-show="adding">Adding...</span>
                    </button>
                    --}}
                    
                    <a href="{{ route('contact') }}" class="w-full bg-red-600 text-white border border-transparent rounded-md py-3 px-8 flex items-center justify-center text-base font-medium hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 shadow-lg transition">
                        Request a Quote
                    </a>
                    
                    <a href="{{ route('contact') }}" class="flex-1 bg-white dark:bg-gray-800 border border-[#0b1c3c] rounded-md py-3 px-8 flex items-center justify-center text-base font-medium text-[#0b1c3c] hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                        Contact Us
                    </a>
                </div>
                
                <div class="mt-8 border-t border-gray-200 pt-8">
                     {{-- SKU/Stock removed for Website Catalog --}}
                </div>
            </div>
        </div>
        
        <!-- Detailed Description Tab (if long description exists) -->
        @if($product->description && $product->short_description)
            <div class="mt-16 bg-white dark:bg-gray-800 p-8 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-xl font-bold text-[#0b1c3c] mb-6 pb-4 border-b">Product Description</h3>
                <div class="prose max-w-none text-gray-600">
                    {!! $product->description !!}
                </div>
            </div>
        @endif

        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
            <section class="mt-16 border-t pt-16">
                <h2 class="text-2xl font-bold text-[#0b1c3c] mb-8">Related Products</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($relatedProducts as $related)
                        <div class="group relative bg-white dark:bg-gray-800 border border-gray-100 rounded-lg overflow-hidden hover:shadow-lg transition">
                            <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden bg-gray-100 h-48">
                                @if($related->image_path)
                                    <img src="{{ Storage::url($related->image_path) }}" alt="{{ $related->name }}" class="object-cover object-center group-hover:opacity-75 h-full w-full">
                                @else
                                     <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100 hover:text-red-600 transition">
                                    <a href="{{ route('products.show', $related) }}">
                                        <span aria-hidden="true" class="absolute inset-0"></span>
                                        {{ $related->name }}
                                    </a>
                                </h3>
                                @if($related->category)
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $related->category->name }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-layouts.frontend>
