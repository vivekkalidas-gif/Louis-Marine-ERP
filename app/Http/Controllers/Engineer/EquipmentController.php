<?php

namespace App\Http\Controllers\Engineer;

use App\Http\Controllers\Controller;
use App\Models\EquipmentItem;
use App\Models\EquipmentCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;

class EquipmentController extends Controller
{
    public function index(Request $request)
    {
        $query = EquipmentItem::with(['type', 'assignedEngineer'])
            ->where('is_active', true)
            ->where('record_type', 'asset');

        // Filters
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        if ($request->has('type_id') && $request->type_id != 'all') {
            $query->where('category_id', $request->type_id);
        }

        if ($request->has('status') && $request->status != 'all') {
            switch ($request->status) {
                case 'valid':
                    $query->where('calibration_expiry_date', '>', now()->addDays(30));
                    break;
                case 'expiring_soon':
                    $query->whereDate('calibration_expiry_date', '>', now())
                          ->whereDate('calibration_expiry_date', '<=', now()->addDays(30));
                    break;
                case 'expired':
                    $query->whereDate('calibration_expiry_date', '<', now());
                    break;
                case 'not_required':
                    $query->where('calibration_required', false);
                    break;
            }
        }

        // Sorting
        $sort = $request->get('sort', 'name');
        $direction = $request->get('direction', 'asc');
        
        if ($sort === 'expiry') {
            $query->orderBy('calibration_expiry_date', $direction);
        } else {
            $query->orderBy($sort, $direction);
        }

        $equipment = $query->paginate(15)->withQueryString();
        $types = EquipmentCategory::all();

        // Check for expiring equipment count (global check for warning)
        $expiringCount = EquipmentItem::where('is_active', true)
            ->where('record_type', 'asset')
            ->whereDate('calibration_expiry_date', '>', now())
            ->whereDate('calibration_expiry_date', '<=', now()->addDays(30))
            ->count();

        return view('engineer.equipment.index', compact('equipment', 'types', 'expiringCount'));
    }

    public function create()
    {
        $types = EquipmentCategory::all();
        $engineers = User::whereHas('roles', function($q) {
            $q->where('slug', 'engineer');
        })->get();
        return view('engineer.equipment.form', compact('types', 'engineers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:equipment_categories,id',
            'code' => 'nullable|string|max:100', // Serial No
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'calibration_required' => 'boolean',
            'calibration_expiry_date' => 'nullable|required_if:calibration_required,1|date',
            'assigned_engineer_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
        ]);

        $item = EquipmentItem::create($validated + ['is_active' => true]);

        return redirect()->route('engineer.equipment.index')
            ->with('success', 'Equipment added successfully.');
    }

    public function edit(EquipmentItem $equipment)
    {
        $types = EquipmentCategory::all();
        $engineers = User::whereHas('roles', function($q) {
            $q->where('slug', 'engineer');
        })->get();
        return view('engineer.equipment.form', compact('equipment', 'types', 'engineers'));
    }

    public function update(Request $request, EquipmentItem $equipment)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:equipment_categories,id',
            'code' => 'nullable|string|max:100', 
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'calibration_required' => 'boolean',
            'calibration_expiry_date' => 'nullable|required_if:calibration_required,1|date',
            'assigned_engineer_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
        ]);

        $equipment->update($validated);

        return redirect()->route('engineer.equipment.index')
            ->with('success', 'Equipment updated successfully.');
    }

    public function destroy(EquipmentItem $equipment)
    {
        // Check active jobs integration here when JobEquipment is fully implemented
        // For now, allow soft delete
        $equipment->delete();

        return redirect()->route('engineer.equipment.index')
            ->with('success', 'Equipment deleted successfully.');
    }

    public function uploadCertificate(Request $request, EquipmentItem $equipment)
    {
        $request->validate([
            'certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB
            'expiry_date' => 'required|date|after:today'
        ]);

        try {
            if ($request->hasFile('certificate')) {
                // Delete old certificate if exists
                if ($equipment->certificate_path) {
                    Storage::disk('public')->delete($equipment->certificate_path);
                }

                $path = $request->file('certificate')->store('certificates', 'public');
                
                $equipment->update([
                    'certificate_path' => $path,
                    'calibration_expiry_date' => $request->expiry_date,
                    'calibration_required' => true // Logic implies it is required if uploading
                ]);

                return back()->with('success', 'Certificate uploaded and expiry date updated.');
            }

            return back()->with('error', 'No file uploaded.');

        } catch (Exception $e) {
            return back()->with('error', 'Upload failed: ' . $e->getMessage());
        }
    }

    public function storeType(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:equipment_categories,name',
        ]);

        $category = EquipmentCategory::create([
            'name' => $validated['name'],
            'slug' => \Illuminate\Support\Str::slug($validated['name']),
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'category' => $category,
            'message' => 'Category added successfully.'
        ]);
    }

    /**
     * Export equipment items to CSV
     */
    public function export()
    {
        $filename = 'equipment-list-' . date('Y-m-d') . '.csv';
        
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
            
            $items = EquipmentItem::with('type')
                ->where('record_type', 'asset')
                ->orderBy('category_id')->orderBy('name')->get();
            
            foreach ($items as $item) {
                fputcsv($file, [
                    $item->type->name ?? 'N/A',
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
            fputcsv($file, ['Drill', 'Cordless Drill 18V', 'Makita', 'DDF485', 'Nos', 'Battery operated drill', 'Voltage:18V']);
            
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
                
                $category = EquipmentCategory::firstOrCreate(
                    ['name' => $categoryName],
                    ['slug' => \Illuminate\Support\Str::slug($categoryName), 'is_active' => true]
                );
                
                $attributes = [];
                if (!empty($attrRaw)) {
                    $json = json_decode($attrRaw, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $attributes = $json;
                    } else {
                        $pairs = explode(';', $attrRaw);
                        foreach ($pairs as $pair) {
                            $parts = explode(':', $pair, 2);
                            if (count($parts) == 2) {
                                $attributes[trim($parts[0])] = trim($parts[1]);
                            }
                        }
                    }
                }
                
                // Auto Code Generation
                $prefix = strtoupper(substr($category->slug, 0, 3));
                
                $item = EquipmentItem::updateOrCreate(
                    [
                        'category_id' => $category->id,
                        'name' => $name,
                        'model' => $model,
                        'record_type' => 'asset' // Ensure strictly checking asset type
                    ],
                    [
                        'brand' => $brand,
                        'unit' => $unit,
                        'description' => $desc,
                        'attributes' => $attributes,
                        'is_active' => true,
                        'code' => \Illuminate\Support\Facades\DB::raw("IFNULL(code, CONCAT('$prefix-', LPAD((SELECT count(*)+1 FROM equipment_items as e2 WHERE e2.category_id = '$category->id'), 4, '0')))"), // Simple SQL generation if code missing
                        'record_type' => 'asset'
                    ]
                );
                
                // Fix code if it was null (newly created) - actually the raw SQL above might verify complex. 
                // Let's stick to simpler logic:
                if (!$item->code) {
                     $count = EquipmentItem::where('category_id', $category->id)->count(); 
                     $item->code = $prefix . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
                     $item->save();
                }

                $success++;
            }
            fclose($handle);
            
            $msg = "Imported $success items successfully.";
            if (count($errors) > 0) {
                 return redirect()->route('engineer.equipment.index')->with('warning', $msg . " With errors: " . implode(', ', array_slice($errors, 0, 3)));
            }
            
            return redirect()->route('engineer.equipment.index')->with('success', $msg);
        }
        
        return back()->with('error', 'Error opening file.');
    }

    public function __downloadPdf()
    {
        $items = EquipmentItem::with('type')
            ->where('record_type', 'asset')
            ->orderBy('category_id')->orderBy('name')->get();
            
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('engineer.equipment.pdf', compact('items'));
        return $pdf->download('equipment-list.pdf');
    }

    public function downloadPdf()
    {
        // Check if PDF facade is alias or class
        // Implementation might depend on setup. Assuming standard loading.
        $items = EquipmentItem::with('type')
            ->where('record_type', 'asset')
            ->orderBy('category_id')->orderBy('name')->get();
            
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('engineer.equipment.pdf', compact('items'));
        return $pdf->download('equipment-list.pdf');
    }
}
