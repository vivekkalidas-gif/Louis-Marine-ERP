<?php

namespace App\Http\Controllers\Storekeeper;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with('supplier')->latest()->paginate(10);
        return view('storekeeper.purchase-orders.index', compact('purchaseOrders'));
    }

    public function create(Request $request)
    {
        $suppliers = Supplier::all(); // Should likely paginate or select needed fields in real app
        $inventoryItems = InventoryItem::all(); // For item selection modal/dropdown
        
        // Generate a draft PO Number
        $prefix = config('settings.prefix_purchase_order', 'PO');
        $poNumber = $prefix . '-' . strtoupper(Str::random(8));

        $prefilledData = [];

        // Check for RFQ Conversion
        if ($request->has('rfq_id') && $request->has('quotation_id')) {
            $rfq = \App\Models\Rfq::with('items')->find($request->rfq_id);
            $quotation = \App\Models\SupplierQuotation::find($request->quotation_id);

            if ($rfq && $quotation) {
                $prefilledData['supplier_id'] = $quotation->supplier_id;
                $prefilledData['notes'] = "Converted from RFQ " . $rfq->reference_number;
                $prefilledData['items'] = [];

                foreach ($rfq->items as $item) {
                    if ($item->product_id) {
                        $prefilledData['items'][] = [
                            'inventory_item_id' => $item->product_id,
                            'quantity' => $item->quantity,
                            'unit_price' => 0, // Quote doesn't strictly track unit price per line, so 0
                            'total_price' => 0
                        ];
                    }
                }
            }
        }

        return view('storekeeper.purchase-orders.create', compact('suppliers', 'inventoryItems', 'poNumber', 'prefilledData'));
    }

    public function store(Request $request)
    {
         $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'po_number' => 'required|unique:purchase_orders,po_number',
            'po_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:po_date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.inventory_item_id' => 'required|exists:inventory_items,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'transport_charges' => 'nullable|numeric|min:0',
            'customs_charges' => 'nullable|numeric|min:0',
            'other_charges' => 'nullable|numeric|min:0',
        ]);

        $po = new PurchaseOrder();
        $po->supplier_id = $validated['supplier_id'];
        $po->po_number = $validated['po_number'];
        $po->po_date = $validated['po_date'];
        $po->expected_delivery_date = $validated['expected_delivery_date'];
        $po->notes = $validated['notes'];
        $po->transport_charges = $validated['transport_charges'] ?? 0;
        $po->customs_charges = $validated['customs_charges'] ?? 0;
        $po->other_charges = $validated['other_charges'] ?? 0;
        $po->status = 'sent'; // Or draft, if you add 'Save as Draft' button
        $po->created_by = auth()->id();
        $po->save();

        $totalAmount = 0;

        foreach ($validated['items'] as $itemData) {
            $itemTotal = $itemData['quantity'] * $itemData['unit_price'];
            $totalAmount += $itemTotal;

            $po->items()->create([
                'inventory_item_id' => $itemData['inventory_item_id'],
                'quantity' => $itemData['quantity'],
                'unit_price' => $itemData['unit_price'],
                'total_price' => $itemTotal,
            ]);
        }

        $po->total_amount = $totalAmount + $po->transport_charges + $po->customs_charges + $po->other_charges;
        $po->save();

        return redirect()->route('storekeeper.purchase-orders.index')
            ->with('success', 'Purchase Order created successfully.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'items.inventoryItem']);
        return view('storekeeper.purchase-orders.show', compact('purchaseOrder'));
    }

    public function receive(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status === 'received' || $purchaseOrder->status === 'cancelled') {
            return redirect()->back()->with('error', 'Purchase Order already processed.');
        }

        // DB Transaction for safety
        \DB::transaction(function () use ($purchaseOrder) {
            foreach ($purchaseOrder->items as $item) {
                $inventoryItem = $item->inventoryItem;
                
                // 1. Create Stock Movement
                \App\Models\StockMovement::create([
                    'inventory_item_id' => $inventoryItem->id,
                    'movement_type' => 'inward',
                    'reference_type' => PurchaseOrder::class,
                    'reference_id' => $purchaseOrder->id,
                    'quantity' => $item->quantity,
                    'balance_after' => $inventoryItem->current_stock + $item->quantity,
                    'remarks' => 'Received from PO #' . $purchaseOrder->po_number,
                    'created_by' => auth()->id(),
                ]);

                // 2. Update Inventory Stock & Price
                $inventoryItem->increment('current_stock', $item->quantity);
                $inventoryItem->update(['purchase_price' => $item->unit_price]);
            }

            $purchaseOrder->update(['status' => 'received']);
        });

        return redirect()->back()->with('success', 'Purchase Order received and stock updated.');
    }
    public function edit(PurchaseOrder $purchaseOrder)
    {
        // Prevent editing if received or cancelled (optional, but good practice)
        if (in_array($purchaseOrder->status, ['received', 'cancelled'])) {
            return redirect()->route('storekeeper.purchase-orders.show', $purchaseOrder)
                ->with('error', 'Cannot edit a processed Purchase Order.');
        }

        $purchaseOrder->load(['items.inventoryItem']);
        $suppliers = Supplier::all();
        $inventoryItems = InventoryItem::all();

        return view('storekeeper.purchase-orders.edit', compact('purchaseOrder', 'suppliers', 'inventoryItems'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        // prevent update if locked
        if (in_array($purchaseOrder->status, ['received', 'cancelled'])) {
             return back()->with('error', 'Cannot update a processed Purchase Order.');
        }

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'po_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:po_date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.inventory_item_id' => 'required|exists:inventory_items,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'transport_charges' => 'nullable|numeric|min:0',
            'customs_charges' => 'nullable|numeric|min:0',
            'other_charges' => 'nullable|numeric|min:0',
        ]);

        \DB::transaction(function () use ($purchaseOrder, $validated) {
            $purchaseOrder->update([
                'supplier_id' => $validated['supplier_id'],
                'po_date' => $validated['po_date'],
                'expected_delivery_date' => $validated['expected_delivery_date'],
                'notes' => $validated['notes'],
                'transport_charges' => $validated['transport_charges'] ?? 0,
                'customs_charges' => $validated['customs_charges'] ?? 0,
                'other_charges' => $validated['other_charges'] ?? 0,
            ]);

            // Sync Items: Delete all and recreate
            $purchaseOrder->items()->delete();

            $totalAmount = 0;

            foreach ($validated['items'] as $itemData) {
                $itemTotal = $itemData['quantity'] * $itemData['unit_price'];
                $totalAmount += $itemTotal;

                $purchaseOrder->items()->create([
                    'inventory_item_id' => $itemData['inventory_item_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'total_price' => $itemTotal,
                ]);
            }

            $purchaseOrder->total_amount = $totalAmount + $purchaseOrder->transport_charges + $purchaseOrder->customs_charges + $purchaseOrder->other_charges;
            $purchaseOrder->save();
        });

        return redirect()->route('storekeeper.purchase-orders.show', $purchaseOrder)
            ->with('success', 'Purchase Order updated successfully.');
    }

    public function pdf(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'items.inventoryItem']);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('storekeeper.purchase-orders.pdf', compact('purchaseOrder'));
        return $pdf->download('PO-'.$purchaseOrder->po_number.'.pdf');
    }
}
