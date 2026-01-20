<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vessel;
use Illuminate\Http\Request;

class VesselController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Vessel::class, 'vessel');
    }
    public function index()
    {
        $vessels = Vessel::with(['customer', 'companyContact'])->latest()->paginate(10);
        return view('admin.vessels.index', compact('vessels'));
    }

    public function create()
    {
        $customers = \App\Models\Customer::with('contacts')->get();
        // Fetch existing types + defaults
        $existingTypes = Vessel::select('type')->distinct()->whereNotNull('type')->where('type', '!=', '')->pluck('type')->toArray();
        $defaultTypes = ['Tanker', 'Cargo', 'Yacht', 'Other'];
        $vesselTypes = array_unique(array_merge($defaultTypes, $existingTypes));
        sort($vesselTypes);

        return view('admin.vessels.create', compact('customers', 'vesselTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'imo_number' => 'nullable|unique:vessels,imo_number',
            'type' => 'nullable',
            'status' => 'required',
            'customer_id' => 'required|exists:customers,id',
            'company_contact_id' => 'nullable|exists:customer_contacts,id'
        ]);

        // Validate that contact belongs to customer
        if ($validated['company_contact_id']) {
            $contact = \App\Models\CustomerContact::find($validated['company_contact_id']);
            if ($contact->customer_id != $validated['customer_id']) {
                 return back()->withErrors(['company_contact_id' => 'Contact must belong to the selected customer.'])->withInput();
            }
        }

        Vessel::create($validated);
        return redirect()->route('admin.vessels.index')->with('success', 'Vessel added successfully.');
    }

    public function edit(Vessel $vessel)
    {
        $customers = \App\Models\Customer::with('contacts')->get();
        // Fetch existing types + defaults
        $existingTypes = Vessel::select('type')->distinct()->whereNotNull('type')->where('type', '!=', '')->pluck('type')->toArray();
        $defaultTypes = ['Tanker', 'Cargo', 'Yacht', 'Other'];
        $vesselTypes = array_unique(array_merge($defaultTypes, $existingTypes));
        sort($vesselTypes);

        return view('admin.vessels.edit', compact('vessel', 'customers', 'vesselTypes'));
    }

    public function update(Request $request, Vessel $vessel)
    {
        $validated = $request->validate([
            'name' => 'required',
            'imo_number' => 'nullable|unique:vessels,imo_number,' . $vessel->id,
            'status' => 'required',
            'type' => 'nullable',
            'customer_id' => 'required|exists:customers,id',
            'company_contact_id' => 'nullable|exists:customer_contacts,id'
        ]);
        
        // Validate contact belongs to customer
        if ($validated['company_contact_id']) {
             $contact = \App\Models\CustomerContact::find($validated['company_contact_id']);
             if ($contact->customer_id != $validated['customer_id']) {
                  return back()->withErrors(['company_contact_id' => 'Contact must belong to the selected customer.'])->withInput();
             }
        }

        $vessel->update($validated);
        return redirect()->route('admin.vessels.index')->with('success', 'Vessel updated successfully.');
    }

    public function destroy(Vessel $vessel)
    {
        $vessel->delete();
        return redirect()->route('admin.vessels.index')->with('success', 'Vessel deleted successfully.');
    }
    public function export()
    {
        $this->authorize('viewAny', Vessel::class);
        $filename = 'vessels-export-' . date('Y-m-d') . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];
        
        $columns = array('Name', 'Customer', 'Customer Email', 'Contact Name', 'IMO Number', 'Type', 'Call Sign', 'Status');
        
        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            $vessels = Vessel::with(['customer', 'companyContact'])->get();
            foreach ($vessels as $vessel) {
                fputcsv($file, array(
                    $vessel->name, 
                    $vessel->customer?->company_name, 
                    $vessel->customer?->email,
                    $vessel->companyContact?->name,
                    $vessel->imo_number,
                    $vessel->type,
                    $vessel->call_sign,
                    $vessel->status
                ));
            }
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    public function downloadTemplate()
    {
        $this->authorize('create', Vessel::class);
        $filename = 'vessel-import-template.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];
        
        $columns = array('name', 'customer_email', 'contact_email', 'imo_number', 'type', 'call_sign', 'status');
        
        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            // Example row
            fputcsv($file, array('MV Example', 'customer@example.com', 'engineer@example.com', 'IMO1234567', 'Supply Vessel', 'CALL123', 'active'));
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $this->authorize('create', Vessel::class);
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
                if (count($row) < 2) continue;
                
                $name = $row[0] ?? null;
                $customerEmail = $row[1] ?? null;
                
                if (!$name || !$customerEmail) continue;
                
                // Find Customer
                $customer = \App\Models\Customer::where('email', $customerEmail)->first();
                if (!$customer) continue; // Skip if customer not found
                
                // Find Contact (Optional)
                $contactId = null;
                $contactEmail = $row[2] ?? null;
                if ($contactEmail) {
                    $contact = \App\Models\CustomerContact::where('email', $contactEmail)->where('customer_id', $customer->id)->first();
                    $contactId = $contact?->id;
                }

                Vessel::updateOrCreate(
                    ['imo_number' => $row[3] ?? 'TEMP-' . uniqid()], // Match by IMO if present, else create new
                    [
                        'name' => $name,
                        'customer_id' => $customer->id,
                        'company_contact_id' => $contactId,
                        'imo_number' => $row[3] ?? null,
                        'type' => $row[4] ?? 'Unknown',
                        'call_sign' => $row[5] ?? null,
                        'status' => $row[6] ?? 'active'
                    ]
                );
                $count++;
            }
            
            fclose($handle);
            \Illuminate\Support\Facades\DB::commit();
            
            return redirect()->route('admin.vessels.index')->with('success', "Successfully imported $count vessels.");
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Error importing file: ' . $e->getMessage());
        }
    }
}
