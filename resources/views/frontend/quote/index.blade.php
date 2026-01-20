<x-layouts.frontend>
    <div class="bg-gray-100 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-extrabold text-[#0b1c3c] mb-8">Request a Quote</h1>

            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p class="font-bold">Success</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p class="font-bold">Error</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <div class="flex flex-col lg:flex-row gap-12">
                <!-- Cart Items -->
                <div class="w-full lg:w-2/3">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 dark:bg-gray-700">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200">Selected Items</h3>
                        </div>
                        
                        @if(empty($cart))
                            <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                                <p>Your quote list is empty.</p>
                                <a href="{{ route('products.index') }}" class="text-red-600 font-bold hover:underline mt-2 inline-block">Browse Products</a>
                            </div>
                        @else
                            <ul class="divide-y divide-gray-100">
                                @foreach($products as $product)
                                    <li class="p-6 flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-md border border-gray-200">
                                                @if($product->image_path)
                                                    <img src="{{ Storage::url($product->image_path) }}" alt="{{ $product->name }}" class="h-full w-full object-cover object-center">
                                                @else
                                                    <div class="h-full w-full bg-gray-100 flex items-center justify-center text-gray-400">
                                                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100"><a href="{{ route('products.show', $product) }}" class="hover:text-red-600">{{ $product->name }}</a></h4>
                                                {{-- SKU removed for Website Catalog --}}
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center space-x-4">
                                            <form action="{{ route('quote.update') }}" method="POST" class="flex items-center">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                <input type="number" name="quantity" value="{{ $cart[$product->id] ?? 1 }}" min="1" class="w-16 p-1 border rounded text-center text-sm mr-2" onchange="this.form.submit()">
                                            </form>

                                            <form action="{{ route('quote.remove') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">Remove</button>
                                            </form>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>

                <!-- Request Form -->
                <div class="w-full lg:w-1/3">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 sticky top-24">
                        <h3 class="text-lg font-bold text-[#0b1c3c] mb-4">Your Details</h3>
                        
                        <form action="{{ route('quote.submit') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-bold mb-2">Company Name</label>
                                <input type="text" name="company_name" class="w-full p-2 border rounded-md focus:ring-red-500 focus:border-red-500" value="{{ old('company_name') }}">
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-bold mb-2">Contact Person *</label>
                                <input type="text" name="contact_person" class="w-full p-2 border rounded-md focus:ring-red-500 focus:border-red-500" required value="{{ old('contact_person') }}">
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-bold mb-2">Email Address *</label>
                                <input type="email" name="email" class="w-full p-2 border rounded-md focus:ring-red-500 focus:border-red-500" required value="{{ old('email') }}">
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-bold mb-2">Phone Number</label>
                                <input type="text" name="phone" class="w-full p-2 border rounded-md focus:ring-red-500 focus:border-red-500" value="{{ old('phone') }}">
                            </div>

                            <div class="mb-6">
                                <label class="block text-sm font-bold mb-2">Additional Message</label>
                                <textarea name="message" class="w-full p-2 border rounded-md focus:ring-red-500 focus:border-red-500" rows="3">{{ old('message') }}</textarea>
                            </div>

                            <button type="submit" class="w-full bg-red-600 text-white font-bold py-3 rounded-full hover:bg-red-700 transition disabled:opacity-50 disabled:cursor-not-allowed" {{ empty($cart) ? 'disabled' : '' }}>
                                Submit Quote Request
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.frontend>
