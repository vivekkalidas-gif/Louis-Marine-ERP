<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Models\WebsiteProduct;
use App\Models\WebsiteCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WebsiteProductController extends Controller
{
    public function index()
    {
        $products = WebsiteProduct::with('category')->orderBy('sort_order')->paginate(10);
        return view('admin.cms.products.index', compact('products'));
    }

    public function create()
    {
        $categories = WebsiteCategory::all();
        return view('admin.cms.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'website_category_id' => 'nullable|exists:website_categories,id',
            'short_description' => 'required',
            'description' => 'nullable',
            'image_path' => 'nullable|image|max:2048',
            'seo_title' => 'nullable',
            'seo_description' => 'nullable',
        ]);

        $validated['slug'] = Str::slug($request->name);
        
        if ($request->hasFile('image_path')) {
            $validated['image_path'] = $request->file('image_path')->store('website/products', 'public');
        }

        WebsiteProduct::create($validated);
        return redirect()->route('admin.cms.products.index')->with('success', 'Product published to website.');
    }

    public function edit(WebsiteProduct $product)
    {
        $categories = WebsiteCategory::all();
        return view('admin.cms.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, WebsiteProduct $product)
    {
        $validated = $request->validate([
            'name' => 'required',
            'website_category_id' => 'nullable|exists:website_categories,id',
            'short_description' => 'required',
            'description' => 'nullable',
            'image_path' => 'nullable|image|max:2048',
             'seo_title' => 'nullable',
            'seo_description' => 'nullable',
            'is_active' => 'boolean'
        ]);

        if ($request->hasFile('image_path')) {
            $validated['image_path'] = $request->file('image_path')->store('website/products', 'public');
        }

        $product->update($validated);
        return redirect()->route('admin.cms.products.index')->with('success', 'Product updated.');
    }

    public function destroy(WebsiteProduct $product)
    {
        $product->delete();
        return back()->with('success', 'Product removed from website.');
    }
}
