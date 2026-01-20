<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceType;
use Illuminate\Http\Request;

class ServiceTypeController extends Controller
{
    public function index()
    {
        $services = ServiceType::latest()->paginate(20);
        return view('admin.service-types.index', compact('services'));
    }

    public function create()
    {
        return view('admin.service-types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:service_types',
            'code' => 'required|unique:service_types',
            'default_price' => 'nullable|numeric|min:0',
            'quantity' => 'nullable|integer|min:0',
        ]);

        $data = $request->except('_token');
        if (!isset($data['default_price'])) {
            $data['default_price'] = 0;
        }

        ServiceType::create($data);

        return redirect()->route('admin.service-types.index')->with('success', 'Service Type created.');
    }

    public function edit(ServiceType $serviceType)
    {
        return view('admin.service-types.edit', compact('serviceType'));
    }

    public function update(Request $request, ServiceType $serviceType)
    {
        $request->validate([
            'name' => 'required|unique:service_types,name,' . $serviceType->id,
            'code' => 'required|unique:service_types,code,' . $serviceType->id,
            'default_price' => 'nullable|numeric|min:0',
            'quantity' => 'nullable|integer|min:0',
        ]);

        $data = $request->except(['_token', '_method']);
        // Keep existing price if not provided (safety) or set to 0? Logic: if hidden, it sends nothing? No, I'll use hidden input in view.
        // But for safety:
        if (!isset($data['default_price']) && !$request->has('default_price')) {
             // If field is missing entirely (not just null), maybe preserve old? 
             // Actually, the hidden input in view handles this. This validation change is enough.
        }

        $serviceType->update($data);

        return redirect()->route('admin.service-types.index')->with('success', 'Service Type updated.');
    }

    public function destroy(ServiceType $serviceType)
    {
        $serviceType->delete();
        return redirect()->route('admin.service-types.index')->with('success', 'Service Type deleted.');
    }

    public function export()
    {
        $filename = 'service-types-export-' . date('Y-m-d') . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];
        
        $columns = array('Name', 'Code', 'Default Price');
        
        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            $services = ServiceType::all();
            foreach ($services as $service) {
                fputcsv($file, array(
                    $service->name, 
                    $service->code, 
                    $service->default_price
                ));
            }
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    public function downloadTemplate()
    {
        $filename = 'service-type-import-template.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];
        
        $columns = array('name', 'code', 'default_price');
        
        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            // Example row
            fputcsv($file, array('Annual Inspection', 'ANN-INSP-001', '150.00'));
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
                if (count($row) < 2) continue; // Need at least name and code
                
                $name = $row[0] ?? null;
                $code = $row[1] ?? null;
                $price = isset($row[2]) ? (float)$row[2] : 0;
                
                if (!$name || !$code) continue;
                
                ServiceType::updateOrCreate(
                    ['code' => $code],
                    [
                        'name' => $name,
                        'default_price' => $price
                    ]
                );
                $count++;
            }
            
            fclose($handle);
            \Illuminate\Support\Facades\DB::commit();
            
            return redirect()->route('admin.service-types.index')->with('success', "Successfully imported $count service types.");
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Error importing file: ' . $e->getMessage());
        }
    }
}
