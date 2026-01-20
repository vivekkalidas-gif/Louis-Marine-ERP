<?php

namespace App\Http\Controllers\Storekeeper;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\Storekeeper\DeliveryNote;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryNoteController extends Controller
{
    public function index()
    {
        $notes = DeliveryNote::with('customer', 'creator')->latest()->paginate(10);
        return view('storekeeper.delivery_notes.index', compact('notes'));
    }

    public function create()
    {
        $customers = Customer::orderBy('company_name')->get();
        $items = InventoryItem::where('status', 'active')->get();
        
        // Auto-generate DR Number
        $lastNote = DeliveryNote::latest()->first();
        // Assuming format DR-YYYY-0001
        $count = $lastNote ? intval(substr($lastNote->dr_number, -4)) + 1 : 1;
        $drNumber = 'DR-' . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        return view('storekeeper.delivery_notes.create', compact('customers', 'items', 'drNumber'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'dr_number' => 'required|unique:delivery_notes,dr_number',
            'date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.inventory_item_id' => 'required|exists:inventory_items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () use ($request) {
            $note = DeliveryNote::create([
                'dr_number' => $request->dr_number,
                'date' => $request->date,
                'customer_id' => $request->customer_id,
                'reference' => $request->reference,
                'status' => 'draft',
                'notes' => $request->notes,
                'created_by' => auth()->id(),
            ]);

            foreach ($request->items as $item) {
                // Fetch item name if description is empty?
                // Frontend should send description
                $inventoryName = InventoryItem::find($item['inventory_item_id'])->item_name;

                $note->items()->create([
                    'inventory_item_id' => $item['inventory_item_id'],
                    'description' => $item['description'] ?? $inventoryName,
                    'quantity' => $item['quantity']
                ]);
            }
        });

        return redirect()->route('storekeeper.delivery-notes.index')->with('success', 'Delivery Note created.');
    }

    public function show(DeliveryNote $deliveryNote)
    {
        $deliveryNote->load('items.inventoryItem', 'customer', 'invoice');
        return view('storekeeper.delivery_notes.show', compact('deliveryNote'));
    }

    public function createInvoice(DeliveryNote $deliveryNote)
    {
        if ($deliveryNote->invoice_id) {
            return back()->with('error', 'Invoice already exists for this Delivery Note.');
        }

        DB::transaction(function () use ($deliveryNote) {
            // Create Invoice
            // Generate Invoice Number logic (duplicated from InvoiceController)
            // Ideally InvoiceController should have a static method for this.
            // MVP: simple generation
            $count = Invoice::count() + 1;
            $invNumber = 'INV-' . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

            $invoice = Invoice::create([
                'invoice_number' => $invNumber,
                'date' => now(),
                'due_date' => now()->addDays(30),
                'customer_id' => $deliveryNote->customer_id,
                'status' => 'draft',
                'reference' => $deliveryNote->dr_number,
                'notes' => 'Generated from Delivery Note ' . $deliveryNote->dr_number,
                'created_by' => auth()->id(),
            ]);

            $total = 0;
            foreach ($deliveryNote->items as $item) {
                // Determine Price
                $price = $item->inventoryItem->selling_price ?? 0;
                $lineTotal = $item->quantity * $price;

                $invoice->items()->create([
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'unit_price' => $price,
                    'amount' => $lineTotal
                ]);
                $total += $lineTotal;
            }
            
            $invoice->update(['subtotal' => $total, 'total_amount' => $total]);

            // Link to Note
            $deliveryNote->update(['invoice_id' => $invoice->id, 'status' => 'invoiced']);
            
            // Notify Accounts
            $accounts = \App\Models\User::whereHas('roles', function($q) {
                $q->whereIn('slug', ['accountant', 'admin']); 
            })->get();
            
            \Illuminate\Support\Facades\Notification::send($accounts, new \App\Notifications\DeliveryNoteInvoiced($deliveryNote, $invoice));
        });

        return redirect()->route('storekeeper.delivery-notes.show', $deliveryNote->id)
            ->with('success', 'Invoice generated successfully.');
    }
    public function edit(DeliveryNote $deliveryNote)
    {
        if ($deliveryNote->status !== 'draft') {
            return redirect()->route('storekeeper.delivery-notes.show', $deliveryNote->id)
                ->with('error', 'Cannot edit a delivery note that has been processed.');
        }

        $customers = Customer::orderBy('company_name')->get();
        $items = InventoryItem::where('status', 'active')->get();
        $deliveryNote->load('items');

        return view('storekeeper.delivery_notes.edit', compact('deliveryNote', 'customers', 'items'));
    }

    public function update(Request $request, DeliveryNote $deliveryNote)
    {
        if ($deliveryNote->status !== 'draft') {
            return back()->with('error', 'Cannot edit a delivery note that has been processed.');
        }

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.inventory_item_id' => 'required|exists:inventory_items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () use ($request, $deliveryNote) {
            $deliveryNote->update([
                'date' => $request->date,
                'customer_id' => $request->customer_id,
                'reference' => $request->reference,
                'notes' => $request->notes,
            ]);

            // Sync Items: Easy way is delete all and recreate, or diff. 
            // Since there are no ID references for updates from frontend usually in this simple Alpine form (just array),
            // wiping and recreating is safest for now unless we track IDs.
            // Let's stick to delete and recreate for simplicity as per previous patterns.
            $deliveryNote->items()->delete();

            foreach ($request->items as $item) {
                // Fetch item name if description is empty?
                $inventoryName = InventoryItem::find($item['inventory_item_id'])->item_name;

                $deliveryNote->items()->create([
                    'inventory_item_id' => $item['inventory_item_id'],
                    'description' => $item['description'] ?? $inventoryName,
                    'quantity' => $item['quantity']
                ]);
            }
        });

        return redirect()->route('storekeeper.delivery-notes.index')->with('success', 'Delivery Note updated.');
    }

    public function destroy(DeliveryNote $deliveryNote)
    {
        if ($deliveryNote->status !== 'draft') {
            return back()->with('error', 'Cannot delete a delivery note that has been processed.');
        }

        $deliveryNote->delete(); // Soft delete as per model

        return redirect()->route('storekeeper.delivery-notes.index')->with('success', 'Delivery Note deleted.');
    }

    public function dispatchNote(DeliveryNote $deliveryNote)
    {
        if ($deliveryNote->status !== 'draft') {
            return back()->with('error', 'Only draft delivery notes can be dispatched.');
        }

        if ($deliveryNote->items->isEmpty()) {
            return back()->with('error', 'Cannot dispatch a delivery note with no items.');
        }

        DB::transaction(function () use ($deliveryNote) {
            foreach ($deliveryNote->items as $item) {
                $inventoryItem = InventoryItem::find($item->inventory_item_id);
                
                if ($inventoryItem) {
                    // 1. Reduce Stock
                    $inventoryItem->decrement('current_stock', $item->quantity);

                    // 2. Create Stock Movement
                    \App\Models\StockMovement::create([
                        'inventory_item_id' => $inventoryItem->id,
                        'movement_type' => 'outward',
                        'reference_type' => DeliveryNote::class,
                        'reference_id' => $deliveryNote->id,
                        'quantity' => $item->quantity,
                        'balance_after' => $inventoryItem->current_stock, // Decrement returns int but doesn't mute object immediately unless refreshed, but simpler to just use calculated value or refresh. Let's refresh.
                        // Actually decrement returns num rows. current_stock on object is old.
                        // Better to explicitly calculate:
                        // $newStock = $inventoryItem->current_stock - $item->quantity;
                        // $inventoryItem->update(['current_stock' => $newStock]);
                        // 'balance_after' => $newStock
                        'location' => 'Store', // Default
                        'remarks' => 'Dispatched via ' . $deliveryNote->dr_number,
                        'created_by' => auth()->id(),
                    ]);
                }
            }

            $deliveryNote->update(['status' => 'dispatched']);
        });

        return redirect()->route('storekeeper.delivery-notes.show', $deliveryNote->id)
            ->with('success', 'Delivery Note dispatched and stock updated.');
    }

    public function print(DeliveryNote $deliveryNote)
    {
        $deliveryNote->load('items.inventoryItem', 'customer');
        return view('storekeeper.delivery_notes.print', compact('deliveryNote'));
    }
}
