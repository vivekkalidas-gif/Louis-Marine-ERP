<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EquipmentCategory;
use App\Models\EquipmentItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EquipmentCategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = EquipmentCategory::withCount(['templates', 'certificateTemplates'])
            ->ordered()
            ->get();
        
        // Fetch items for the second tab
        $query = EquipmentItem::with('category');

        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        $items = $query->orderBy('name')->paginate(20);
        
        return view('admin.equipment.categories.index', compact('categories', 'items'));
    }

    public function store(Request $request)
    {
        $request->merge(['is_active' => $request->has('is_active')]);

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:equipment_categories,name',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        EquipmentCategory::create($validated);

        return redirect()->route('admin.equipment.categories.index')
            ->with('success', 'Equipment category created successfully.');
    }

    public function update(Request $request, EquipmentCategory $category)
    {
        $request->merge(['is_active' => $request->has('is_active')]);

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:equipment_categories,name,' . $category->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $category->update($validated);

        return redirect()->route('admin.equipment.categories.index')
            ->with('success', 'Equipment category updated successfully.');
    }

    public function destroy(EquipmentCategory $category)
    {
        if ($category->allItems()->count() > 0) {
            return back()->with('error', 'Cannot delete category with existing equipment items.');
        }

        $category->delete();

        return redirect()->route('admin.equipment.categories.index')
            ->with('success', 'Equipment category deleted successfully.');
    }

    public function toggle(EquipmentCategory $category)
    {
        $category->update(['is_active' => !$category->is_active]);

        return back()->with('success', 'Category status updated.');
    }

    public function export()
    {
        $filename = 'equipment-categories-export-' . date('Y-m-d') . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];
        
        $columns = array('Name', 'Description', 'Slug', 'Status');
        
        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            $categories = EquipmentCategory::all();
            foreach ($categories as $category) {
                fputcsv($file, array(
                    $category->name, 
                    $category->description, 
                    $category->slug,
                    $category->is_active ? 'Active' : 'Inactive'
                ));
            }
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    public function downloadTemplate()
    {
        $filename = 'equipment-category-import-template.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];
        
        $columns = array('name', 'description', 'status');
        
        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            // Example row
            fputcsv($file, array('Safety Equipment', 'General safety gear', 'active'));
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');
        
        try {
            \Illuminate\Support\Facades\DB::beginTransaction();
            
            $handle = fopen($file->getPathname(), 'r');
            $header = fgetcsv($handle); // Skip header
            
            $count = 0;
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 1) continue;
                
                $name = $row[0] ?? null;
                if (!$name) continue;
                
                EquipmentCategory::updateOrCreate(
                    ['slug' => Str::slug($name)],
                    [
                        'name' => $name,
                        'description' => $row[1] ?? null,
                        'is_active' => strtolower($row[2] ?? 'active') === 'active',
                        'slug' => Str::slug($name)
                    ]
                );
                $count++;
            }
            
            fclose($handle);
            \Illuminate\Support\Facades\DB::commit();
            
            return redirect()->route('admin.equipment.categories.index')->with('success', "Successfully imported $count categories.");
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Error importing file: ' . $e->getMessage());
        }
    }
}
