<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\WebsiteProduct; // Updated
use App\Models\QuoteRequest;
use App\Models\QuoteRequestItem; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuoteCartController extends Controller
{
    // View Cart / Request Form
    public function index()
    {
        $cart = session()->get('quote_cart', []);
        $products = [];
        
        if (!empty($cart)) {
            // Fetch Website Products
            $products = WebsiteProduct::whereIn('id', array_keys($cart))->get();
        }

        return view('frontend.quote.index', compact('cart', 'products'));
    }

    // Add to Cart
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:website_products,id', // Validated against cms products
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = session()->get('quote_cart', []);
        
        $id = $request->product_id;
        $qty = $request->quantity;

        if (isset($cart[$id])) {
            $cart[$id] += $qty;
        } else {
            $cart[$id] = $qty;
        }

        session()->put('quote_cart', $cart);

        return response()->json([
            'success' => true, 
            'message' => 'Added to quote list',
            'count' => count($cart)
        ]);
    }

    // Update Quantity
    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = session()->get('quote_cart', []);
        $id = $request->product_id;
        
        if (isset($cart[$id])) {
            $cart[$id] = $request->quantity;
            session()->put('quote_cart', $cart);
        }

        return back()->with('success', 'Cart updated');
    }

    // Remove Item
    public function remove(Request $request)
    {
        $request->validate(['product_id' => 'required']);
        
        $cart = session()->get('quote_cart', []);
        unset($cart[$request->product_id]);
        session()->put('quote_cart', $cart);

        return back()->with('success', 'Item removed');
    }

    // Submit Quote
    public function submit(Request $request)
    {
        $request->validate([
            'contact_person' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'nullable|string',
        ]);

        $cart = session()->get('quote_cart', []);
        
        if (empty($cart)) {
            return back()->with('error', 'Your quote list is empty.');
        }

        DB::transaction(function() use ($request, $cart) {
            // Create Request
            $quoteRequest = QuoteRequest::create([
                'reference_no' => 'QR-' . date('Y') . '-' . strtoupper(Str::random(5)),
                'contact_person' => $request->contact_person,
                'company_name' => $request->company_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'message' => $request->message,
                'status' => 'new'
            ]);

            // Create Items
            foreach ($cart as $productId => $qty) {
                QuoteRequestItem::create([
                    'quote_request_id' => $quoteRequest->id,
                    'website_product_id' => $productId, // Updated Column
                    'quantity' => $qty
                ]);
            }

            // Clear Cart
            session()->forget('quote_cart');

            // Send Emails (Queued ideally, but sync for now)
            try {
                \Illuminate\Support\Facades\Mail::to('admin@louismarine.com')->send(new \App\Mail\NewQuoteRequestAdmin($quoteRequest));
                \Illuminate\Support\Facades\Mail::to($request->email)->send(new \App\Mail\QuoteRequestReceivedCustomer($quoteRequest));
            } catch (\Exception $e) {
                // Log error but don't fail the request
                \Illuminate\Support\Facades\Log::error('Quote Email Error: ' . $e->getMessage());
            }
        });

        return redirect()->route('products.index')->with('success', 'Quote request submitted successfully! We will contact you soon.');
    }
}
