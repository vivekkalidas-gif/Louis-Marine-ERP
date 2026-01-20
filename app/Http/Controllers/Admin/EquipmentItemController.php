<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EquipmentItem;
use App\Models\EquipmentCategory;
use Illuminate\Http\Request;

class EquipmentItemController extends Controller
{
    public function index(Request $request)
{
    // Redirect to Equipment Master (Categories) page where items are shown in tabs
    return redirect()->route('admin.equipment.categories.index');
}
    public function create()
    {
        $categories = EquipmentCategory::with('attributeTemplates')->active()->ordered()->get();
        return view('admin.equipment.items.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:equipment_categories,id',
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:equipment_items,code',
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'unit' => 'required|string|max:20',
            'attributes' => 'nullable|array'
        ]);

        $validated['is_active'] = $request->has('is_active');
        
        // Auto-generate code if not provided
        if (empty($validated['code'])) {
            $category = EquipmentCategory::find($validated['category_id']);
            $prefix = strtoupper(substr($category->slug, 0, 3));
            $count = EquipmentItem::where('category_id', $validated['category_id'])->count() + 1;
            $validated['code'] = $prefix . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        }

        EquipmentItem::create($validated);

        return redirect()->route('admin.equipment.categories.index')
        ->with('success', 'Equipment item created successfully.');
    }

    public function edit(EquipmentItem $item)
    {
        $categories = EquipmentCategory::with('attributeTemplates')->active()->ordered()->get();
        $item->load('category.attributeTemplates');
        
        return view('admin.equipment.items.edit', compact('item', 'categories'));
    }

    public function update(Request $request, EquipmentItem $item)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:equipment_categories,id',
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:equipment_items,code,' . $item->id,
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'unit' => 'required|string|max:20',
            'attributes' => 'nullable|array'
        ]);

        $validated['is_active'] = $request->has('is_active');

        $item->update($validated);

        return redirect()->route('admin.equipment.categories.index')
            ->with('success', 'Equipment item updated successfully.');
    }

    public function destroy(EquipmentItem $item)
    {
        if ($item->jobEquipment()->count() > 0) {
            return back()->with('error', 'Cannot delete equipment item that is used in jobs.');
        }

        $item->delete();

        return redirect()->route('admin.equipment.items.index')
            ->with('success', 'Equipment item deleted successfully.');
    }

    // API endpoint for job creation
    public function getByCategory($categoryId)
    {
        $items = EquipmentItem::where('category_id', $categoryId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()->json($items);
    }

    /**
     * Export equipment items to CSV
     */
    public function export()
    {
        $filename = 'equipment-items-' . date('Y-m-d') . '.csv';
        
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];
        
        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Header Row
            fputcsv($file, ['Category', 'Code', 'Name', 'Brand', 'Model', 'Unit', 'Description', 'Status']);
            
            $items = EquipmentItem::with('category')->orderBy('category_id')->orderBy('name')->get();
            
            foreach ($items as $item) {
                fputcsv($file, [
                    $item->category->name ?? 'N/A',
                    $item->code,
                    $item->name,
                    $item->brand,
                    $item->model,
                    $item->unit,
                    $item->description,
                    $item->is_active ? 'Active' : 'Inactive'
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Download CSV Template for Import
     */
    public function downloadTemplate()
    {
        $filename = 'equipment-import-template.csv';
        
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];
        
        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Header Row
            fputcsv($file, ['Category', 'Name', 'Brand', 'Model', 'Unit', 'Description', 'Attributes (JSON or Key:Value)']);
            
            // Example Row
            fputcsv($file, ['Fire Extinguisher', '2kg CO2 Fire Extinguisher', 'SafetyFirst', 'CO2-2KG', 'Nos', 'Standard 2kg CO2 extinguisher', 'Capacity:2kg']);
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import Equipment Items from CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');
        
        if (($handle = fopen($file->getRealPath(), 'r')) !== FALSE) {
            $header = fgetcsv($handle); // Skip header row
            
            $row = 1;
            $success = 0;
            $errors = [];
            
            while (($data = fgetcsv($handle)) !== FALSE) {
                $row++;
                // Expecting: Category, Name, Brand, Model, Unit, Description, Attributes
                if (count($data) < 5) {
                    $errors[] = "Row $row: Insufficient columns.";
                    continue;
                }
                
                $categoryName = trim($data[0]);
                $name = trim($data[1]);
                $brand = trim($data[2]);
                $model = trim($data[3]);
                $unit = trim($data[4]);
                $desc = isset($data[5]) ? trim($data[5]) : '';
                $attrRaw = isset($data[6]) ? trim($data[6]) : '';
                
                if (empty($categoryName) || empty($name)) {
                    $errors[] = "Row $row: Category and Name are required.";
                    continue;
                }
                
                // Find or Create Category
                // Ideally we should match exact name, or fuzzy? Let's exact match for safety.
                $category = EquipmentCategory::firstOrCreate(
                    ['name' => $categoryName],
                    ['slug' => \Illuminate\Support\Str::slug($categoryName), 'is_active' => true]
                );
                
                // Parse attributes
                $attributes = [];
                if (!empty($attrRaw)) {
                    // Try JSON first
                    $json = json_decode($attrRaw, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $attributes = $json;
                    } else {
                        // Try Key:Value format (Capacity:2kg; Type:ABC)
                        $pairs = explode(';', $attrRaw);
                        foreach ($pairs as $pair) {
                            $parts = explode(':', $pair, 2);
                            if (count($parts) == 2) {
                                $attributes[trim($parts[0])] = trim($parts[1]);
                            }
                        }
                    }
                }
                
                // Generate Code
                $prefix = strtoupper(substr($category->slug, 0, 3));
                // Simple count based generation (might duplicate if concurrent import, but acceptable for this scale)
                // Better: Check if item with same Name/Model exists?
                // Let's create new item always, assuming user curated list.
                // Or check duplicates?
                $exists = EquipmentItem::where('category_id', $category->id)
                    ->where('name', $name)
                    ->where('model', $model)
                    ->exists();
                
                if ($exists) {
                    // Update? Skipping for now to avoid overwriting.
                    // $errors[] = "Row $row: Item already exists.";
                    // actually, let's update it.
                     $item = EquipmentItem::where('category_id', $category->id)
                        ->where('name', $name)
                        ->where('model', $model)
                        ->first();
                     $item->update([
                        'brand' => $brand,
                        'unit' => $unit,
                        'description' => $desc,
                        'attributes' => $attributes,
                        'is_active' => true
                     ]);
                } else {
                    $count = EquipmentItem::where('category_id', $category->id)->count() + 1 + $success; 
                    // +success to offset current batch
                    $code = $prefix . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
                    
                    // Handle code collision retry
                    while(EquipmentItem::where('code', $code)->exists()) {
                         $count++;
                         $code = $prefix . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
                    }

                    EquipmentItem::create([
                        'category_id' => $category->id,
                        'code' => $code,
                        'name' => $name,
                        'brand' => $brand,
                        'model' => $model,
                        'unit' => $unit,
                        'description' => $desc,
                        'attributes' => $attributes,
                        'is_active' => true
                    ]);
                }
                $success++;
            }
            fclose($handle);
            
            $msg = "Imported $success items successfully.";
            if (count($errors) > 0) {
                 $msg .= " Errors: " . implode(' ', array_slice($errors, 0, 3));
                 if(count($errors) > 3) $msg .= "...";
                 return redirect()->route('admin.equipment.items.index')->with('warning', $msg);
            }
            
            return redirect()->route('admin.equipment.items.index')->with('success', $msg);
        }
        
        return back()->with('error', 'Error opening file.');
    }
}
