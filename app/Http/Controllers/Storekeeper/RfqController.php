<?php

namespace App\Http\Controllers\Storekeeper;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Incoterm;
use App\Models\InventoryItem;
use App\Models\MaterialRequest;
use App\Models\Rfq;
use App\Models\RfqItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RfqController extends Controller
{
    public function index()
    {
        $rfqs = Rfq::with(['creator', 'currency', 'suppliers'])->orderBy('created_at', 'desc')->paginate(20);
        return view('storekeeper.rfq.index', compact('rfqs'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $currencies = Currency::all();
        $products = InventoryItem::all();
        $incoterms = \App\Models\Incoterm::all();
        
        // Optional: Pre-fill from Material Request if ID passed
        $materialRequest = null;
        if (request('material_request_id')) {
            $materialRequest = MaterialRequest::find(request('material_request_id'));
        }

        return view('storekeeper.rfq.create', compact('suppliers', 'currencies', 'products', 'materialRequest', 'incoterms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:local,overseas',
            'deadline_date' => 'required|date',
            'supplier_ids' => 'required|array|min:1',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.1',
        ]);

        DB::beginTransaction();
        try {
            $rfq = Rfq::create([
                'reference_number' => 'RFQ/' . date('Y') . '/' . strtoupper(Str::random(5)), // Temporary generator
                'type' => $request->type,
                'status' => 'draft',
                'currency_id' => $request->currency_id, // Nullable
                'incoterm' => $request->incoterm,
                'delivery_location' => $request->delivery_location,
                'deadline_date' => $request->deadline_date,
                'created_by' => Auth::id(),
            ]);

            // Add Items
            foreach ($request->items as $itemData) {
                RfqItem::create([
                    'rfq_id' => $rfq->id,
                    'material_request_id' => $itemData['material_request_id'] ?? null,
                    'product_id' => $itemData['product_id'] ?? null,
                    'description' => $itemData['description'],
                    'quantity' => $itemData['quantity'],
                    'unit' => $itemData['unit'] ?? 'Nos',
                ]);
            }

            // Attach Suppliers
            $rfq->suppliers()->attach($request->supplier_ids);

            DB::commit();

            return redirect()->route('storekeeper.rfq.show', $rfq)->with('success', 'RFQ Created Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating RFQ: ' . $e->getMessage());
        }
    }

    public function show(Rfq $rfq)
    {
        $rfq->load(['items.product', 'suppliers', 'quotations.supplier']);
        return view('storekeeper.rfq.show', compact('rfq'));
    }
    
    public function updateStatus(Request $request, Rfq $rfq)
    {
        $status = $request->status;
        
        // Simple status update
        $rfq->update(['status' => $status]);

        // Send Email if status is 'sent'
        if ($status == 'sent') {
            $rfq->load('suppliers'); // Ensure suppliers are loaded
            
            foreach ($rfq->suppliers as $supplier) {
                if ($supplier->email) {
                    \Illuminate\Support\Facades\Mail::to($supplier->email)
                        ->queue(new \App\Mail\SendRfqToSupplier($rfq, $supplier));
                }
            }

            return back()->with('success', 'RFQ Status updated to SENT. Emails are being queued to ' . $rfq->suppliers->count() . ' suppliers.');
        }

        return back()->with('success', 'RFQ Status Updated');
    }

    public function downloadPdf(Rfq $rfq)
    {
        $rfq->load(['items.product', 'suppliers', 'currency']);
        $supplier = $rfq->suppliers->first(); 
        if(!$supplier) {
             $supplier = new Supplier(['name' => 'General Supplier', 'email' => '', 'address' => '']);
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('storekeeper.rfq.pdf', compact('rfq', 'supplier'));
        $filename = 'RFQ-' . str_replace(['/', '\\'], '-', $rfq->reference_number) . '.pdf';
        return $pdf->download($filename);
    }

    public function streamPdf(Rfq $rfq)
    {
        $rfq->load(['items.product', 'suppliers', 'currency']);
        $supplier = $rfq->suppliers->first(); 
        if(!$supplier) {
             $supplier = new Supplier(['name' => 'General Supplier', 'email' => '', 'address' => '']);
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('storekeeper.rfq.pdf', compact('rfq', 'supplier'));
        $filename = 'RFQ-' . str_replace(['/', '\\'], '-', $rfq->reference_number) . '.pdf';
        return $pdf->stream($filename);
    }

    public function storeIncoterm(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:incoterms,code',
            'name' => 'nullable|string'
        ]);

        $incoterm = Incoterm::create([
            'code' => strtoupper($request->code),
            'name' => $request->name
        ]);

        return response()->json($incoterm);
    }

    public function edit(Rfq $rfq)
    {
        $rfq->load(['items', 'suppliers']);
        $currencies = \App\Models\Currency::all();
        $suppliers = \App\Models\Supplier::all();
        $products = \App\Models\InventoryItem::select('id', 'item_name', 'model', 'unit')->get();
        $incoterms = Incoterm::all();

        return view('storekeeper.rfq.edit', compact('rfq', 'currencies', 'suppliers', 'products', 'incoterms'));
    }

    public function update(Request $request, Rfq $rfq)
    {
        $request->validate([
            'type' => 'required|in:local,overseas',
            'deadline_date' => 'required|date',
            'supplier_ids' => 'required|array|min:1',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.1',
        ]);

        DB::beginTransaction();
        try {
            $rfq->update([
                'type' => $request->type,
                'currency_id' => $request->currency_id,
                'incoterm' => $request->incoterm,
                'delivery_location' => $request->delivery_location,
                'deadline_date' => $request->deadline_date,
            ]);

            // Sync Suppliers
            $rfq->suppliers()->sync($request->supplier_ids);

            // Sync Items: Easy way is delete all and recreate, or smart sync. 
            // For simplicity and to avoid stale IDs, we'll delete and recreate items. 
            // NOTE: If items had other relationships (unlikely for RFQ items), this would be bad.
            $rfq->items()->delete();

            foreach ($request->items as $itemData) {
                RfqItem::create([
                    'rfq_id' => $rfq->id,
                    'material_request_id' => $itemData['material_request_id'] ?? null,
                    'product_id' => $itemData['product_id'] ?? null,
                    'description' => $itemData['description'],
                    'quantity' => $itemData['quantity'],
                    'unit' => $itemData['unit'] ?? 'Nos',
                ]);
            }

            DB::commit();

            return redirect()->route('storekeeper.rfq.show', $rfq)->with('success', 'RFQ Updated Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating RFQ: ' . $e->getMessage());
        }
    }
}
