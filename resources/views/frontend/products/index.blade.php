<x-layouts.frontend>
    <style>
        /* Fallback styles to ensure grid works */
        .products-container { max-width: 1400px; margin: 0 auto; }
        .products-wrapper { display: flex; gap: 2rem; }
        .products-sidebar { width: 256px; flex-shrink: 0; }
        .products-grid-wrapper { flex: 1; }
        .products-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); 
            gap: 1.5rem; 
        }
        .product-card { 
            background: white; 
            border-radius: 0.5rem; 
            overflow: hidden; 
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: box-shadow 0.3s;
        }
        .product-card:hover { box-shadow: 0 10px 25px rgba(0,0,0,0.15); }
        .product-image-container { 
            position: relative; 
            width: 100%; 
            padding-bottom: 100%; 
            background: #f9fafb; 
            overflow: hidden;
        }
        .product-image-container img { 
            position: absolute; 
            top: 0; 
            left: 0; 
            width: 100%; 
            height: 100%; 
            object-fit: contain; 
            padding: 1rem;
        }
        @media (max-width: 1024px) {
            .products-wrapper { flex-direction: column; }
            .products-sidebar { width: 100%; }
            .products-grid { grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); }
        }
    </style>

    <!-- Hero Section -->
    <div class="relative bg-center bg-cover h-[400px]" style="background-image: url('https://louismarineqatar.com/wp-content/uploads/2024/11/IMG-20241126-WA0004.jpg');">
        <div class="absolute inset-0 bg-[#0b1c3c] bg-opacity-70 flex items-center justify-center">
            <div class="text-center text-white px-4">
                <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight mb-4 animate-fade-in-up">Our Products</h1>
                <p class="text-lg md:text-xl font-light max-w-2xl mx-auto animate-fade-in-up delay-100 text-gray-200">
                    Explore our extensive range of high-quality marine safety and fire fighting equipment.
                </p>
            </div>
        </div>
    </div>

    <div class="products-container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="products-wrapper flex flex-col lg:flex-row gap-8">
            
            <!-- Sidebar Filters -->
            <div class="products-sidebar w-full lg:w-64 flex-shrink-0">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg border border-gray-200 sticky top-24">
                    <h3 class="text-lg font-bold text-[#0b1c3c] mb-4 pb-3 border-b-2 border-red-600">Categories</h3>
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('products.index') }}" class="flex items-center justify-between py-2 px-3 rounded {{ !request('category') ? 'bg-red-600 text-white font-bold' : 'text-gray-700 hover:bg-gray-100' }} transition">
                                <span>All Products</span>
                            </a>
                        </li>
                        @foreach($categories as $category)
                            <li>
                                <a href="{{ route('products.index', ['category' => $category->name]) }}" class="flex items-center justify-between py-2 px-3 rounded {{ request('category') == $category->name ? 'bg-red-600 text-white font-bold' : 'text-gray-700 hover:bg-gray-100' }} transition">
                                    <span>{{ $category->name }}</span>
                                    <span class="bg-gray-200 text-gray-600 py-0.5 px-2 rounded-full text-xs font-semibold">{{ $category->products_count ?? $category->products->count() }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <form action="{{ route('products.index') }}" method="GET">
                            <input type="hidden" name="category" value="{{ request('category') }}">
                            <label class="block text-sm font-bold text-[#0b1c3c] mb-2">Search Products</label>
                            <div class="relative">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-600 focus:border-red-600">
                                <div class="absolute left-3 top-2.5 text-gray-400">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                            </div>
                            <button type="submit" class="w-full mt-2 bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition">Search</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Product Grid -->
            <div class="products-grid-wrapper flex-1">
                @if($products->count() > 0)
                    <div class="products-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach($products as $product)
                            <div class="product-card bg-white rounded-lg overflow-hidden shadow-md hover:shadow-2xl transition-all duration-300 border border-gray-200 flex flex-col group relative">
                                <!-- Product Image Container -->
                                <div class="product-image-container relative bg-gray-50 overflow-hidden" style="padding-bottom: 100%;">
                                    @if($product->image_path)
                                        <img src="{{ url('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="absolute inset-0 w-full h-full object-contain p-4 transition-transform duration-300 group-hover:scale-110">
                                    @else
                                        <div class="absolute inset-0 flex items-center justify-center text-gray-300">
                                            <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    @endif
                                    
                                    <!-- NEW Badge -->
                                    @if($product->created_at->diffInDays(now()) < 7)
                                        <div class="absolute top-3 left-3">
                                            <span class="bg-blue-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg">NEW</span>
                                        </div>
                                    @endif

                                    <!-- Hover Overlay with View Details -->
                                    <div class="absolute inset-0 bg-[#0b1c3c] bg-opacity-0 group-hover:bg-opacity-90 transition-all duration-300 flex items-center justify-center opacity-0 group-hover:opacity-100">
                                        <a href="{{ route('products.show', $product->slug) }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg transform translate-y-4 group-hover:translate-y-0 transition-all duration-300">
                                            View Details
                                        </a>
                                    </div>
                                </div>

                                <!-- Product Info -->
                                <div class="p-4 flex flex-col flex-1">
                                    @if($product->category)
                                        <span class="text-xs font-semibold text-red-600 uppercase tracking-wider mb-2">{{ $product->category->name }}</span>
                                    @endif
                                    <h3 class="text-base font-bold text-[#0b1c3c] mb-2 line-clamp-2 min-h-[3rem]">{{ $product->name }}</h3>
                                    
                                    @if($product->short_description)
                                        <p class="text-sm text-gray-600 line-clamp-2 mb-3">{{ $product->short_description }}</p>
                                    @endif

                                    <div class="mt-auto pt-3 border-t border-gray-100 flex items-center justify-between">
                                        <a href="{{ route('products.show', $product->slug) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 flex items-center transition-colors">
                                            Learn More <span class="ml-1">→</span>
                                        </a>
                                        <button 
                                            onclick="addToQuote('{{ $product->id }}', '{{ $product->name }}')"
                                            class="bg-gray-100 hover:bg-red-600 text-gray-700 hover:text-white p-2 rounded-full transition-all duration-300"
                                            title="Add to Quote"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-16 bg-white rounded-lg border-2 border-dashed border-gray-300 shadow-sm">
                        <svg class="h-20 w-20 text-gray-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <p class="text-xl text-gray-500 font-semibold mb-2">No products found</p>
                        <p class="text-gray-400 mb-4">Try adjusting your search or filters</p>
                        <a href="{{ route('products.index') }}" class="inline-block bg-red-600 hover:bg-red-700 text-white font-semibold px-6 py-2 rounded-lg transition">Clear all filters</a>
                    </div>
                @endif
                
                <!-- Pagination -->
                <div class="mt-8">
                     {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</x-layouts.frontend>
