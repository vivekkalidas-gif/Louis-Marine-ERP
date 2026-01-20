<?php

namespace App\Http\Controllers\Storekeeper;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::latest()->paginate(10);
        return view('storekeeper.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        $currencies = \App\Models\Currency::all();
        return view('storekeeper.suppliers.create', compact('currencies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:local,overseas',
            'category' => 'nullable|string',
            'status' => 'required|in:active,inactive,blacklisted',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string',
            'tax_id' => 'nullable|string|max:50',
            'payment_terms' => 'nullable|string|max:255',
            'currency_id' => 'nullable|exists:currencies,id',
            'incoterm' => 'nullable|string|max:10',
            'rating' => 'nullable|integer|min:0|max:5',
        ]);

        $validated['created_by'] = auth()->id();

        Supplier::create($validated);

        return redirect()->route('storekeeper.suppliers.index')
            ->with('success', 'Supplier created successfully.');
    }

    public function edit(Supplier $supplier)
    {
        $currencies = \App\Models\Currency::all();
        return view('storekeeper.suppliers.edit', compact('supplier', 'currencies'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:local,overseas',
            'category' => 'nullable|string',
            'status' => 'required|in:active,inactive,blacklisted',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string',
            'tax_id' => 'nullable|string|max:50',
            'payment_terms' => 'nullable|string|max:255',
            'currency_id' => 'nullable|exists:currencies,id',
            'incoterm' => 'nullable|string|max:10',
            'rating' => 'nullable|integer|min:0|max:5',
        ]);

        $supplier->update($validated);

        return redirect()->route('storekeeper.suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('storekeeper.suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }

    public function export()
    {
        $filename = 'suppliers_' . date('Y-m-d') . '.csv';
        $suppliers = Supplier::all();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'Name', 'Type', 'Category', 'Status', 'Contact Person', 'Email', 'Phone', 'Website', 'Address', 'Tax ID', 'Payment Terms', 'Incoterm'
        ];

        $callback = function() use($suppliers, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($suppliers as $supplier) {
                fputcsv($file, [
                    $supplier->name,
                    $supplier->type,
                    $supplier->category,
                    $supplier->status,
                    $supplier->contact_person,
                    $supplier->email,
                    $supplier->phone,
                    $supplier->website,
                    $supplier->address,
                    $supplier->tax_id,
                    $supplier->payment_terms,
                    $supplier->incoterm
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importForm()
    {
        return view('storekeeper.suppliers.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');
        
        // Simple CSV Parser
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle); // Assuming first row is header
        
        // Basic mapping (Order matters matching csv above ideally, or use keys)
        // For simplicity, let's assume valid column order or just specific known columns
        // Let's expect headers: Name, Type, Contact, Email, Phone...
        
        $count = 0;
        while (($row = fgetcsv($handle)) !== false) {
             // Map based on index (0=>Name, 1=>Type...) - User must use Template ideally.
             // Let's strictly require the Template format from Export.
             
             if(count($row) < 5) continue; // Skip bad rows

             Supplier::updateOrCreate(
                 ['name' => $row[0]], // Match by Name
                 [
                     'type' => strtolower($row[1]) ?: 'local',
                     'category' => $row[2] ?? null,
                     'status' => strtolower($row[3]) ?: 'active',
                     'contact_person' => $row[4] ?? null,
                     'email' => $row[5] ?? null,
                     'phone' => $row[6] ?? null,
                     'website' => $row[7] ?? null,
                     'address' => $row[8] ?? null,
                     'tax_id' => $row[9] ?? null,
                     'payment_terms' => $row[10] ?? null,
                     'incoterm' => $row[11] ?? null,
                     'created_by' => auth()->id(),
                 ]
             );
             $count++;
        }
        
        fclose($handle);

        return redirect()->route('storekeeper.suppliers.index')->with('success', "Imported $count suppliers.");
    }

    public function pdf(Supplier $supplier)
    {
         $company = \App\Models\Setting::getAllCached();
         
         // Fix Logo Array access if needed (assuming Setting::getAllCached returns array/collection)
         // Controller usually returns Collection of Key=>Value or just Key=>Value array?
         // In previous tasks we saw it returns Key=>Value array.
         
         // Ensure logo key exists
         $companyData = [
            'name' => $company['company_name'] ?? 'Louis Marine Equipment',
            'address' => $company['company_address'] ?? '',
            'phone' => $company['company_phone'] ?? '',
            'email' => $company['company_email'] ?? '',
            'logo' => $company['company_logo'] ?? null,
         ];

         $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('storekeeper.suppliers.pdf', compact('supplier', 'companyData'));
         $filename = 'supplier_' . str_replace(['/', '\\'], '_', $supplier->name) . '.pdf';
         return $pdf->download($filename);
    }
}
