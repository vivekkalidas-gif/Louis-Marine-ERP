<?php

namespace App\Http\Controllers\Storekeeper;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\StockMovement;
use Illuminate\Http\Request;

use App\Models\InventoryCategory;

class InventoryItemController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryItem::query()->with('category');
        
        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('item_name', 'like', '%' . $request->search . '%')
                  ->orWhereHas('category', function($q2) use ($request) {
                      $q2->where('name', 'like', '%' . $request->search . '%');
                  })
                  ->orWhere('brand', 'like', '%' . $request->search . '%');
            });
        }
        
        // Filter by category
        if ($request->filled('category')) {
             $query->where('inventory_category_id', $request->category);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'low_stock') {
                $query->lowStock();
            } elseif ($request->status === 'out_of_stock') {
                $query->outOfStock();
            } else {
                $query->where('status', $request->status);
            }
        }
        
        $items = $query->with('creator')->latest()->paginate(15);
        $categories = InventoryCategory::all();
        
        return view('storekeeper.inventory.index', compact('items', 'categories'));
    }

    public function create()
    {
        $categories = InventoryCategory::all();
        return view('storekeeper.inventory.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'item_code' => 'nullable|string|max:50|unique:inventory_items,item_code',
            'inventory_category_id' => 'required|exists:inventory_categories,id',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'unit' => 'required|in:Nos,Kg,Set,Ltr,Mtr',
            'minimum_stock_level' => 'required|integer|min:0',
            'reorder_quantity' => 'required|integer|min:0',
            'current_stock' => 'required|integer|min:0',
            'purchase_price' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
        ]);

        $item = InventoryItem::create(array_merge($validated, [
            'status' => 'active',
            'created_by' => auth()->id()
        ]));

        // Create opening stock movement
        if ($item->current_stock > 0) {
            StockMovement::create([
                'inventory_item_id' => $item->id,
                'movement_type' => 'inward',
                'reference_type' => null,
                'quantity' => $item->current_stock,
                'balance_after' => $item->current_stock,
                'remarks' => 'Initial stock entry',
                'created_by' => auth()->id(),
            ]);
        }

        return redirect()->route('storekeeper.inventory.index')
            ->with('success', 'Inventory item created successfully.');
    }

    public function show(InventoryItem $inventory)
    {
        $inventory->load(['stockMovements' => function($q) {
            $q->latest()->limit(20)->with('creator');
        }, 'category']);
        
        return view('storekeeper.inventory.show', ['inventoryItem' => $inventory]);
    }

    public function edit(InventoryItem $inventory)
    {
        $categories = InventoryCategory::all();
        return view('storekeeper.inventory.edit', ['inventoryItem' => $inventory, 'categories' => $categories]);
    }

    public function update(Request $request, InventoryItem $inventory)
    {
        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'item_code' => 'nullable|string|max:50|unique:inventory_items,item_code,' . $inventory->id,
            'inventory_category_id' => 'required|exists:inventory_categories,id',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'unit' => 'required|in:Nos,Kg,Set,Ltr,Mtr',
            'minimum_stock_level' => 'required|integer|min:0',
            'reorder_quantity' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive',
             'purchase_price' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
        ]);

        $inventory->update($validated);

        return redirect()->route('storekeeper.inventory.index')
            ->with('success', 'Inventory item updated successfully.');
    }

    public function destroy(InventoryItem $inventory)
    {
        $inventory->update(['status' => 'inactive']);

        return redirect()->route('storekeeper.inventory.index')
            ->with('success', 'Inventory item deactivated successfully.');
    }

    public function export()
    {
        $items = InventoryItem::with('category')->get();
        $filename = "inventory-export-" . date('d-m-Y') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($items) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Category', 'Item Name', 'Brand', 'Model', 'Unit', 'Stock', 'Min Stock', 'Purchase Price', 'Selling Price']);

            foreach ($items as $item) {
                fputcsv($file, [
                    $item->category->name ?? '',
                    $item->item_name,
                    $item->brand,
                    $item->model,
                    $item->unit,
                    $item->current_stock,
                    $item->minimum_stock_level,
                    $item->purchase_price,
                    $item->selling_price
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function pdf()
    {
        $items = InventoryItem::with('category')->orderBy('item_name')->get();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('storekeeper.inventory.pdf', compact('items'));
        return $pdf->download('inventory-list.pdf');
    }

    public function importForm()
    {
        return view('storekeeper.inventory.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt'
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getPathname(), 'r');
        
        // Skip header
        fgetcsv($handle);

        $imported = 0;
        $errors = [];

        while (($row = fgetcsv($handle)) !== false) {
            // Expected Format: Category, Item Name, Brand, Model, Unit, Stock, Min Stock, Purchase Price, Selling Price
            // Indices: 0, 1, 2, 3, 4, 5, 6, 7, 8
            
            if (count($row) < 2) continue; // Skip empty rows

            $categoryName = trim($row[0]);
            $itemName = trim($row[1]);

            if (empty($itemName)) continue;

            // Find or Create Category
            $category = null;
            if (!empty($categoryName)) {
                $category = InventoryCategory::firstOrCreate(
                    ['name' => $categoryName],
                    ['slug' => \Illuminate\Support\Str::slug($categoryName), 'created_by' => auth()->id()]
                );
            }

            try {
                InventoryItem::updateOrCreate(
                    [
                        'item_name' => $itemName,
                        'inventory_category_id' => $category ? $category->id : null
                    ],
                    [
                        'brand' => $row[2] ?? null,
                        'model' => $row[3] ?? null,
                        'unit' => in_array($row[4] ?? '', ['Nos', 'Kg', 'Set', 'Ltr', 'Mtr']) ? $row[4] : 'Nos',
                        'current_stock' => (int)($row[5] ?? 0),
                        'minimum_stock_level' => (int)($row[6] ?? 0),
                        'purchase_price' => (float)($row[7] ?? 0),
                        'selling_price' => (float)($row[8] ?? 0),
                        'status' => 'active',
                        'created_by' => auth()->id()
                    ]
                );
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Error importing $itemName: " . $e->getMessage();
            }
        }
        fclose($handle);

        if (count($errors) > 0) {
            return redirect()->route('storekeeper.inventory.index')->with('warning', "Imported $imported items. Some errors occurred: " . implode(', ', array_slice($errors, 0, 5)));
        }

        return redirect()->route('storekeeper.inventory.index')->with('success', "Imported $imported items successfully.");
    }
}
