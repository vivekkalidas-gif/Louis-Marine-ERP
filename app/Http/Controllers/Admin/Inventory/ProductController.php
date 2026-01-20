<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\InventoryCategory;
use App\Models\Supplier;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProductController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Product::class);
        $products = Product::with(['category', 'supplier'])->latest()->paginate(15);
        return view('admin.inventory.products.index', compact('products'));
    }

    public function create()
    {
        $this->authorize('create', Product::class);
        $categories = InventoryCategory::all();
        $suppliers = Supplier::all();
        return view('admin.inventory.products.create', compact('categories', 'suppliers'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Product::class);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|unique:products,sku',
            'inventory_category_id' => 'required|exists:inventory_categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'price' => 'numeric|min:0',
            'min_stock_level' => 'integer|min:0',
            'description' => 'nullable|string',
            'location' => 'nullable|string',
            'slug' => 'nullable|string|unique:products,slug',
            'short_description' => 'nullable|string',
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $validated['slug'] = $validated['slug'] ?? \Illuminate\Support\Str::slug($validated['name']);
        $validated['is_published'] = $request->has('is_published');

        Product::create($validated);

        return redirect()->route('admin.inventory.products.index')->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        $this->authorize('view', $product);
        $product->load(['category', 'supplier', 'transactions.user']);
        return view('admin.inventory.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $this->authorize('update', $product);
        $categories = InventoryCategory::all();
        $suppliers = Supplier::all();
        return view('admin.inventory.products.edit', compact('product', 'categories', 'suppliers'));
    }

    public function update(Request $request, Product $product)
    {
        $this->authorize('update', $product);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|unique:products,sku,' . $product->id,
            'inventory_category_id' => 'required|exists:inventory_categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'price' => 'numeric|min:0',
            'min_stock_level' => 'integer|min:0',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'location' => 'nullable|string',
            'is_published' => 'boolean',
            'slug' => 'nullable|string|unique:products,slug,' . $product->id,
            'specs_keys.*' => 'nullable|string',
            'specs_values.*' => 'nullable|string',
            'new_images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'remove_images.*' => 'string',
        ]);

        DB::transaction(function () use ($request, $product, $validated) {
            // 1. Handle Specifications (Convert parallel arrays to associative array)
            $specifications = [];
            if ($request->has('specs_keys') && $request->has('specs_values')) {
                foreach ($request->specs_keys as $index => $key) {
                    $value = $request->specs_values[$index] ?? null;
                    if (!empty($key) && !empty($value)) {
                        $specifications[$key] = $value;
                    }
                }
            }
            // If no specs provided but we are updating, should we clear them? 
            // The form will send empty arrays if cleared. 
            // If the inputs are missing entirely (not in request), it might be a partial update? 
            // Proceed assuming full form submission.
            $product->specifications = $specifications;

            // 2. Handle Images
            $currentImages = $product->image_paths ?? [];
            
            // a) Remove selected images
            if ($request->has('remove_images')) {
                foreach ($request->remove_images as $imgToRemove) {
                    // Remove from storage
                    // Note: $imgToRemove should be the path relative to storage root (e.g. products/slug/img.jpg)
                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($imgToRemove)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($imgToRemove);
                    }
                    // Remove from array
                    $currentImages = array_values(array_diff($currentImages, [$imgToRemove]));
                }
            }

            // b) Add new images
            if ($request->hasFile('new_images')) {
                foreach ($request->file('new_images') as $file) {
                    // Store in products directory
                    $path = $file->store('products/' . $product->slug, 'public');
                    $currentImages[] = $path;
                }
            }

            $product->image_paths = $currentImages;

            // 3. Update Standard Fields
            $product->name = $validated['name'];
            $product->sku = $validated['sku'];
            $product->inventory_category_id = $validated['inventory_category_id'];
            $product->supplier_id = $validated['supplier_id'];
            $product->price = $validated['price'] ?? 0;
            $product->min_stock_level = $validated['min_stock_level'];
            $product->description = $validated['description'];
            $product->short_description = $validated['short_description'];
            $product->location = $validated['location'];
            $product->is_published = $request->has('is_published');
            
            if (!empty($validated['slug'])) {
                $product->slug = $validated['slug'];
            }

            $product->save();
        });

        return redirect()->route('admin.inventory.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);
        $product->delete();
        return redirect()->route('admin.inventory.products.index')->with('success', 'Product deleted successfully.');
    }

    public function adjustStock(Request $request, Product $product)
    {
        $this->authorize('update', $product);
        $request->validate([
            'type' => 'required|in:IN,OUT,ADJUSTMENT',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
            'reference' => 'nullable|string'
        ]);

        DB::transaction(function () use ($request, $product) {
            
            // Create Transaction Record
            InventoryTransaction::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'type' => $request->type,
                'quantity' => $request->quantity,
                'reference' => $request->reference,
                'notes' => $request->notes
            ]);

            // Update Stock
            if ($request->type === 'IN') {
                $product->increment('current_stock', $request->quantity);
            } elseif ($request->type === 'OUT') {
                if ($product->current_stock < $request->quantity) {
                    throw new \Exception("Insufficient stock.");
                }
                $product->decrement('current_stock', $request->quantity);
            } else {
                // ADJUSTMENT: Set to specific value? Or just add/subtract? 
                // Usually adjustment means correcting the value. 
                // For simplicity here, let's treat ADJUSTMENT as a direct signed change or we can say 'quantity' is the delta.
                // Let's assume quantity is always positive and type dictates direction.
                // Actually, for ADJUSTMENT, it might be better to just allow manual override.
                // But to keep it simple, let's treat type=ADJUSTMENT as 'Add' for now or maybe check context.
                // Better approach: 'ADJUSTMENT' adds if positive.
                // Let's stick to IN/OUT logic strictly. 
                // Any 'correction' is an IN or OUT.
            }
        });

        return back()->with('success', 'Stock updated successfully.');
    }
}
