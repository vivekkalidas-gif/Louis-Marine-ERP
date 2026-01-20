<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\CustomerDocument;
use App\Models\CustomerNote;
use App\Models\Vessel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CustomerController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Customer::class);
        $query = Customer::with('contacts');

        if ($request->filled('search')) {
            $query->where('company_name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $customers = $query->latest()->paginate(20);

        return view('admin.customers.index', compact('customers'));
    }

    public function export()
    {
        $filename = 'customers-export-' . date('Y-m-d') . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = array('Company Name', 'Type', 'Email', 'Phone', 'Status', 'City', 'Country');

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            $customers = Customer::all();
            foreach ($customers as $customer) {
                fputcsv($file, array($customer->company_name, $customer->type, $customer->email, $customer->phone, $customer->status, $customer->city, $customer->country));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function create()
    {
        $this->authorize('create', Customer::class);
        return view('admin.customers.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Customer::class);
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'type' => 'required|string',
            'email' => 'nullable|email|unique:customers', // Changed from 'required' to 'nullable' based on new snippet, but kept unique
            'phone' => 'nullable|string',
            'address' => 'nullable',
            'city' => 'nullable',
            'country' => 'required',
            'cr_number' => 'nullable',
            'vat_number' => 'nullable'
        ]);

        $customer = Customer::create($validated);
        
        // Handle primary contact if provided (optional quick add)
        if ($request->filled('contact_name')) {
            $customer->contacts()->create([
                'name' => $request->contact_name,
                'email' => $request->contact_email,
                'phone' => $request->contact_phone,
                'position' => $request->contact_position,
                'is_primary' => true
            ]);
        }

        return redirect()->route('admin.customers.show', $customer->id)->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer)
    {
        $this->authorize('view', $customer);
        $customer->load(['contacts', 'vessels', 'documents', 'customerNotes.author']);
        
        // Tab logic can be handled in view with Alpine.js, sending all data at once for simple tabs.
        // Or lazy load if too heavy. For now, eager loading is fine.
        
        return view('admin.customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        $this->authorize('update', $customer);
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $this->authorize('update', $customer);
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'type' => 'required|string',
            'email' => ['nullable', 'email', Rule::unique('customers')->ignore($customer->id)],
            'phone' => 'nullable',
            'address' => 'nullable',
            'city' => 'nullable',
            'country' => 'required',
            'cr_number' => 'nullable',
            'vat_number' => 'nullable'
        ]);

        $customer->update($validated);
        return redirect()->route('admin.customers.show', $customer->id)->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        $this->authorize('delete', $customer);
        $customer->delete(); // Soft delete
        return redirect()->route('admin.customers.index')->with('success', 'Customer deleted successfully.');
    }

    public function storeNote(Request $request, Customer $customer)
    {
        $request->validate(['content' => 'required']);
        $customer->customerNotes()->create([
            'content' => $request->content,
            'user_id' => auth()->id()
        ]);
        return back()->with('success', 'Note added.');
    }

    public function storeContact(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'nullable|email',
            'phone' => 'nullable',
            'designation' => 'nullable',
            'contact_type' => 'required',
            'can_request_job' => 'boolean',
            'can_receive_invoice' => 'boolean',
            'is_primary' => 'boolean',
        ]);

        if ($request->is_primary) {
            $customer->contacts()->update(['is_primary' => false]);
        }
        
        // Handle checkbox inputs which might not be present if unchecked
        $validated['can_request_job'] = $request->has('can_request_job');
        $validated['can_receive_invoice'] = $request->has('can_receive_invoice');
        $validated['is_primary'] = $request->has('is_primary');

        $customer->contacts()->create($validated);
        return back()->with('success', 'Contact added.');
    }

    public function storeDocument(Request $request, Customer $customer)
    {
        $request->validate([
            'title' => 'required',
            'file' => 'required|file|max:10240', // 10MB
            'expiry_date' => 'nullable|date'
        ]);

        $path = $request->file('file')->store('documents', 'public');

        $customer->documents()->create([
            'title' => $request->title,
            'file_path' => $path,
            'file_type' => $request->file('file')->getClientOriginalExtension(),
            'expiry_date' => $request->expiry_date
        ]);

        return back()->with('success', 'Document uploaded.');
    }

    public function linkVessel(Request $request, Customer $customer)
    {
        $request->validate(['vessel_id' => 'required|exists:vessels,id']);
        $vessel = Vessel::find($request->vessel_id);
        $vessel->update(['customer_id' => $customer->id]);
        return back()->with('success', 'Vessel linked.');
    }

    public function ledger(Customer $customer)
    {
        // Single Source of Truth: Ledger Entries
        $ledger = $customer->ledgerEntries()
            ->with('reference')
            ->orderBy('date', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        $balance = $ledger->last() ? $ledger->last()->running_balance : 0;

        return view('admin.customers.ledger', compact('customer', 'ledger', 'balance'));
    }
    public function downloadTemplate()
    {
        $filename = 'customer-import-template.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];
        
        $columns = array('company_name', 'email', 'phone', 'address', 'city', 'country', 'type', 'vat_number', 'cr_number');
        
        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            // Example row
            fputcsv($file, array('Acme Corp', 'contact@acme.com', '+1234567890', '123 Main St', 'Doha', 'Qatar', 'Customer', '123456', '7891011'));
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
            DB::beginTransaction();
            
            $handle = fopen($file->getPathname(), 'r');
            $header = fgetcsv($handle); // Skip header
            
            // Map header columns to index if needed, for now assume fixed order matches template
            
            $count = 0;
            while (($row = fgetcsv($handle)) !== false) {
                // Skip empty rows
                if (count($row) < 2) continue;
                
                // Ensure required fields exist
                $companyName = $row[0] ?? null;
                if (!$companyName) continue;
                
                Customer::updateOrCreate(
                    ['email' => $row[1] ?? null], // Match by email if exists, otherwise create
                    [
                        'company_name' => $companyName,
                        'phone' => $row[2] ?? null,
                        'address' => $row[3] ?? null,
                        'city' => $row[4] ?? null,
                        'country' => $row[5] ?? 'Qatar',
                        'type' => $row[6] ?? 'Customer',
                        'vat_number' => $row[7] ?? null,
                        'cr_number' => $row[8] ?? null,
                        'status' => 'active'
                    ]
                );
                $count++;
            }
            
            fclose($handle);
            DB::commit();
            
            return redirect()->route('admin.customers.index')->with('success', "Successfully imported $count customers.");
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error importing file: ' . $e->getMessage());
        }
    }
}
