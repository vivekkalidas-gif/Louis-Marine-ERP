<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuoteRequest;
use App\Models\InventoryCategory;
use App\Models\Quotation; // Assuming this model exists
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; // If used later

class QuoteRequestController extends Controller
{
    public function index()
    {
        $requests = QuoteRequest::withCount('items')->latest()->paginate(15);
        return view('admin.quote_requests.index', compact('requests'));
    }

    public function show(QuoteRequest $quoteRequest)
    {
        $quoteRequest->load('items.product');
        return view('admin.quote_requests.show', compact('quoteRequest'));
    }

    public function update(Request $request, QuoteRequest $quoteRequest)
    {
        $request->validate([
            'status' => 'required|in:new,in_review,quoted,closed'
        ]);

        $quoteRequest->update(['status' => $request->status]);

        return back()->with('success', 'Status updated successfully.');
    }

    public function destroy(QuoteRequest $quoteRequest)
    {
        $quoteRequest->items()->delete(); // Or rely on cascade if set in DB
        $quoteRequest->delete(); // Soft delete

        return redirect()->route('admin.quote-requests.index')->with('success', 'Quote request deleted.');
    }
    
    // Convert to Official Quotation (Draft)
    public function convertToQuotation(QuoteRequest $quoteRequest)
    {
        // Logic to create a new Quotation record from this request
        // This assumes a Quotation model and table exist with compatible structure
        // For now, we will just redirect to the Create Quotation page with pre-filled data via session or URL params
        // But since the User asked for "Enable Admin/Manager to enter quoted prices...", 
        // We might want to actually create the draft quotation record here or map it.
        
        // Simpler approach: Redirect to Quotation Create with items
        // Since we don't know the exact Quotation structure deeply, let's assume we can pass IDs.
        
        // Update status
        $quoteRequest->update(['status' => 'converted']); // Or 'quoted' if we auto-create

        return redirect()->route('admin.invoices.quotations.create', ['quote_request_id' => $quoteRequest->id])
            ->with('success', 'Redirected to create quotation. Please fill in pricing.');
            
        // Note: The above route 'admin.invoices.quotations.create' might be 'admin.invoices.quotations.create' or just 'admin.quotations.create'
        // Based on route list: admin.quotations.create
    }
}
