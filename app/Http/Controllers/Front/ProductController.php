<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\InventoryCategory;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Use WebsiteProduct (CMS)
        $query = \App\Models\WebsiteProduct::where('is_active', true)->with('category');

        if ($request->has('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category)->orWhere('name', $request->category);
            });
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->orderBy('sort_order')->orderBy('name')->paginate(12);
        
        // Fetch Website Categories
        $categories = \App\Models\WebsiteCategory::whereHas('products', function($q) {
            $q->where('is_active', true);
        })->withCount(['products' => function($q) {
            $q->where('is_active', true);
        }])->get();

        return view('frontend.products.index', compact('products', 'categories'));
    }

    public function show($slug)
    {
        $product = \App\Models\WebsiteProduct::where('slug', $slug)
            ->where('is_active', true)
            ->with('category')
            ->firstOrFail();

        $relatedProducts = \App\Models\WebsiteProduct::where('is_active', true)
            ->where('website_category_id', $product->website_category_id)
            ->where('id', '!=', $product->id)
            ->with('category')
            ->limit(4)
            ->get();

        return view('frontend.products.show', compact('product', 'relatedProducts'));
    }
}
