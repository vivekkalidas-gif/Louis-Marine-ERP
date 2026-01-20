<?php

namespace App\Http\Controllers\Storekeeper;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class QuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = \App\Models\Quotation::with('customer');

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('quotation_number', 'like', '%' . $request->search . '%')
                  ->orWhere('reference_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('customer', function($c) use ($request) {
                      $c->where('company_name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sort
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'oldest':
                    $query->oldest();
                    break;
                case 'amount_high':
                    $query->orderByDesc('total');
                    break;
                case 'amount_low':
                    $query->orderBy('total');
                    break;
                default: // latest
                    $query->latest();
                    break;
            }
        } else {
            $query->latest();
        }

        $quotations = $query->paginate(10)->withQueryString();
        return view('storekeeper.quotations.index', compact('quotations'));
    }

    public function create()
    {
        $customers = \App\Models\Customer::orderBy('company_name')->get();
        return view('storekeeper.quotations.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'quotation_date' => 'required|date',
            'reference_number' => 'nullable|string|max:255',
            'valid_until' => 'nullable|date',
        ]);

        // Generate Quotation Number (Simple Auto-increment logic or UUID)
        $count = \App\Models\Quotation::count() + 1;
        $prefix = config('settings.prefix_quotation', 'QTE');
        $quotationNumber = $prefix . '-' . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        $quotation = \App\Models\Quotation::create([
            'quotation_number' => $quotationNumber,
            'customer_id' => $validated['customer_id'],
            'date' => $validated['quotation_date'],
            'reference_number' => $validated['reference_number'],
            'valid_until' => $validated['valid_until'],
            'status' => 'draft',
            'total' => 0,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('storekeeper.quotations.edit', $quotation->id)
            ->with('success', 'Quotation created. Please add items.');
    }

    public function storeCustomer(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'type' => 'nullable|string|in:owner,manager,agent,other',
        ]);

        $customer = \App\Models\Customer::create([
            'company_name' => $validated['company_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'type' => $validated['type'] ?? 'owner',
            'status' => 'active',
        ]);

        return response()->json([
            'success' => true,
            'customer' => [
                'id' => $customer->id,
                'name' => $customer->company_name
            ]
        ]);
    }

    public function edit($id)
    {
        $quotation = \App\Models\Quotation::with(['items.product', 'customer'])->findOrFail($id);
        $customers = \App\Models\Customer::orderBy('company_name')->get();
        $items = \App\Models\InventoryItem::where('status', 'active')->get(); // For adding items dropdown
        return view('storekeeper.quotations.edit', compact('quotation', 'items', 'customers'));
    }

    public function update(Request $request, $id)
    {
        $quotation = \App\Models\Quotation::findOrFail($id);
        
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'quotation_date' => 'required|date',
            'reference_number' => 'nullable|string|max:255',
            'valid_until' => 'nullable|date',
            'status' => 'required|in:draft,sent,approved,rejected',
            'items' => 'nullable|array',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.inventory_item_id' => 'nullable|exists:inventory_items,id',
        ]);

        // Update header
        $quotation->update([
            'customer_id' => $validated['customer_id'],
            'date' => $validated['quotation_date'],
            'reference_number' => $validated['reference_number'],
            'valid_until' => $validated['valid_until'],
            'status' => $validated['status'],
        ]);

        // Process Items
        // Strategy: Delete all and recreate (MVP). Ideally sync.
        $quotation->items()->delete();
        
        $total = 0;
        if (!empty($validated['items'])) {
            foreach ($validated['items'] as $item) {
                $lineTotal = $item['quantity'] * $item['unit_price'];
                $quotation->items()->create([
                    'inventory_item_id' => $item['inventory_item_id'] ?? null,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $lineTotal,
                ]);
                $total += $lineTotal;
            }
        }

        // Update Total
        $quotation->update([
            'subtotal' => $total,
            'total' => $total, // Tax handling can be added later
        ]);

        return redirect()->route('storekeeper.quotations.edit', $quotation->id)->with('success', 'Quotation and items updated.');
    }

    public function pdf($id)
    {
        $quotation = \App\Models\Quotation::with(['items', 'customer'])->findOrFail($id);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('storekeeper.quotations.pdf', compact('quotation'));
        return $pdf->stream('Quotation-' . $quotation->quotation_number . '.pdf');
    }

    public function destroy($id)
    {
        //
    }
    public function deliveryNote($id)
    {
        $quotation = \App\Models\Quotation::with(['items.product', 'customer'])->findOrFail($id);
        return view('storekeeper.quotations.delivery-note', compact('quotation'));
    }

    public function requestInvoice($id)
    {
        $quotation = \App\Models\Quotation::findOrFail($id);
        if ($quotation->status !== 'approved') {
            return back()->with('error', 'Only approved quotations can be sent for billing.');
        }
        
        // In a real scenario, this might trigger a notification or change status.
        return back()->with('success', 'Finance team has been notified for billing.');
    }
}
