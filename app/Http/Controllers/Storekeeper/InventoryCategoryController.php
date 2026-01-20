<?php

namespace App\Http\Controllers\Storekeeper;

use App\Http\Controllers\Controller;
use App\Models\InventoryCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InventoryCategoryController extends Controller
{
    public function index()
    {
        $categories = InventoryCategory::latest()->paginate(10);
        return view('storekeeper.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('storekeeper.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:inventory_categories,name',
            'description' => 'nullable|string',
        ]);

        InventoryCategory::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('storekeeper.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(InventoryCategory $category)
    {
        return view('storekeeper.categories.edit', compact('category'));
    }

    public function update(Request $request, InventoryCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:inventory_categories,name,' . $category->id,
            'description' => 'nullable|string',
        ]);

        $category->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
        ]);

        return redirect()->route('storekeeper.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(InventoryCategory $category)
    {
        // Optional: Check if used by items before delete? 
        // Or SoftDelete handles it (but items would have null category_id eventually if forced)
        $category->delete();
        return redirect()->route('storekeeper.categories.index')
            ->with('success', 'Category deleted successfully.');
    }

    public function export()
    {
        $categories = InventoryCategory::all();
        $filename = "categories-export-" . date('d-m-Y') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($categories) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Name', 'Description']);

            foreach ($categories as $category) {
                fputcsv($file, [
                    $category->name,
                    $category->description
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function pdf()
    {
        $categories = InventoryCategory::orderBy('name')->get();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('storekeeper.categories.pdf', compact('categories'));
        return $pdf->download('categories-list.pdf');
    }

    public function importForm()
    {
        return view('storekeeper.categories.import');
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
            // Expected Format: Name, Description
            
            if (count($row) < 1) continue;

            $name = trim($row[0]);
            if (empty($name)) continue;

            try {
                InventoryCategory::updateOrCreate(
                    ['name' => $name],
                    [
                        'slug' => \Illuminate\Support\Str::slug($name),
                        'description' => $row[1] ?? null,
                        'created_by' => auth()->id()
                    ]
                );
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Error importing $name: " . $e->getMessage();
            }
        }
        fclose($handle);

        if (count($errors) > 0) {
            return redirect()->route('storekeeper.categories.index')->with('warning', "Imported $imported categories. Errors: " . implode(', ', array_slice($errors, 0, 5)));
        }

        return redirect()->route('storekeeper.categories.index')->with('success', "Imported $imported categories successfully.");
    }
}
