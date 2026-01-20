<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Customer;
use App\Models\Vessel;
use App\Models\ServiceJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ServiceReport;

class InvoiceController extends Controller
{
    public function createFromServiceReport($reportId)
    {
        $report = ServiceReport::with(['job', 'items.serviceType', 'charges'])->findOrFail($reportId);
        
        DB::beginTransaction();
        try {

            // Check if an invoice already exists for this report
            // If report is 'Invoiced', check if there is a DRAFT invoice for this job.
            if ($report->status === 'Invoiced') {
                $existingInvoice = Invoice::withTrashed()
                                          ->where('service_job_id', $report->job->id)
                                          ->where('status', 'draft')
                                          ->latest()
                                          ->first();

                if (!$existingInvoice) {
                    throw new \Exception('Report already processed and invoice is finalized.');
                }
                
                // If invoice was deleted, restore it
                if ($existingInvoice->trashed()) {
                    $existingInvoice->restore();
                }
                
                // If draft exists, we will UPDATE it instead of creating new.
                $invoice = $existingInvoice;
                // Clear existing items to regenerate
                $invoice->items()->delete();
            } else {
                // New Invoice Logic
                // Generate Invoice Number
                $count = Invoice::withTrashed()->count() + 1;
                $prefix = config('settings.prefix_invoice', 'INV');
                do {
                        $invoiceNumber = $prefix . '-' . date('Y') . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
                        $count++;
                } while(Invoice::withTrashed()->where('invoice_number', $invoiceNumber)->exists());

                $invoice = Invoice::create([
                    'invoice_number' => $invoiceNumber,
                    'customer_id' => $report->job->customer_id,
                    'vessel_id' => $report->job->vessel_id,
                    'service_job_id' => $report->job->id,
                    'date' => now(),
                    'due_date' => now()->addDays(30),
                    'status' => 'draft',
                    'subtotal' => 0,
                    'tax_amount' => 0,
                    'total_amount' => 0,
                    'notes' => 'Generated from Service Report (' . $report->created_at->format('Y-m-d') . ')',
                    'created_by' => auth()->id(),
                ]);
            }
            
            $subtotal = 0;

            // Map Service Items (Checklist)
            foreach ($report->items as $item) {
                // Use Default Price from ServiceType if available
                $unitPrice = $item->serviceType->default_price ?? $item->price;
                $quantity = $item->quantity; // Use actual quantity

                $serviceName = optional($item->serviceType)->name ?? ('Service Item #' . $item->id);

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $serviceName . ' (Service)',
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'amount' => $quantity * $unitPrice,
                ]);

                $subtotal += ($quantity * $unitPrice);
            }

            // Map Charges (Parts/Additional)
            foreach ($report->charges as $charge) {
                // Try to find Product by name to get current stock price
                $searchName = $charge->item_description;
                if (\Illuminate\Support\Str::endsWith($searchName, ' (Stock)')) {
                    $searchName = \Illuminate\Support\Str::beforeLast($searchName, ' (Stock)');
                }
                
                \Illuminate\Support\Facades\Log::info('Invoice Item Lookup', ['original' => $charge->item_description, 'search' => $searchName]);

                $product = \App\Models\Product::where('name', $searchName)->first();
                $inventoryItem = \App\Models\InventoryItem::where('item_name', $searchName)->first();
                
                if ($product && $product->price > 0) {
                    $unitPrice = $product->price;
                    \Illuminate\Support\Facades\Log::info('Found Product', ['price' => $unitPrice]);
                } elseif ($inventoryItem && $inventoryItem->selling_price > 0) {
                    $unitPrice = $inventoryItem->selling_price;
                    \Illuminate\Support\Facades\Log::info('Found InventoryItem', ['price' => $unitPrice]);
                } else {
                    $unitPrice = $charge->unit_price;
                    \Illuminate\Support\Facades\Log::info('Fallback to Charge Price (0)', ['price' => $unitPrice]);
                }
                $quantity = $charge->quantity;

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $charge->item_description,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'amount' => $quantity * $unitPrice,
                ]);

                $subtotal += ($quantity * $unitPrice);
            }

            // Update Invoice Totals based on calculated items
            $invoice->update([
                'subtotal' => $subtotal,
                'total_amount' => $subtotal, // Tax will be added in edit if needed
            ]);

            $report->update(['status' => 'Invoiced']);

            DB::commit();

            return redirect()->route('accounts.invoices.edit', $invoice->id)
                ->with('success', 'Invoice created from Service Report. Please review and finalize.');

        } catch (\Exception $e) {
            DB::rollBack();
            // dd('ERROR GENERATING INVOICE: ' . $e->getMessage()); // Force error display
            return back()->with('error', 'Error generating invoice: ' . $e->getMessage());
        }
    }

    public function index()
    {
        $invoices = Invoice::with(['customer', 'vessel'])->latest()->paginate(10);
        return view('accounts.invoices.index', compact('invoices'));
    }

    public function create(Request $request)
    {
        $customers = Customer::orderBy('company_name')->get();
        // Assuming 'service_jobs' table usage in model
        $jobs = ServiceJob::with(['customer', 'vessel'])->where('status', '!=', 'cancelled')->latest()->get();
        $prefix = config('settings.prefix_invoice', 'INV');
        $invoiceNumber = $prefix . '-' . date('Y') . '-' . str_pad(Invoice::count() + 1, 3, '0', STR_PAD_LEFT);
        
        $quotation = null;
        if ($request->has('quotation_id')) {
            $quotation = \App\Models\Quotation::with('items')->find($request->quotation_id);
        }

        $selectedJob = null;
        if ($request->has('job_id')) {
            $selectedJob = ServiceJob::with(['customer', 'vessel', 'activeReport.items.serviceType', 'activeReport.charges'])->find($request->job_id);
        }

        $initialItems = [['description' => '', 'quantity' => 1, 'unit_price' => 0]];

        if ($quotation) {
            $initialItems = $quotation->items->map(fn($i) => [
                'description' => $i->description, 
                'quantity' => $i->quantity, 
                'unit_price' => $i->unit_price
            ])->toArray();
        } elseif ($selectedJob && $selectedJob->activeReport) {
            $initialItems = [];
            
            // 1. Service Items
            foreach($selectedJob->activeReport->items as $item) {
                $initialItems[] = [
                    'description' => $item->serviceType->name ?? 'Service Item',
                    'quantity' => $item->quantity,
                    'unit_price' => $item->serviceType->default_price ?? 0
                ];
            }

            // 2. Charges (Stock/Materials)
            foreach($selectedJob->activeReport->charges as $charge) {
                // Lookup Logic similar to createFromServiceReport
                $searchName = $charge->item_description;
                if (\Illuminate\Support\Str::endsWith($searchName, ' (Stock)')) {
                    $searchName = \Illuminate\Support\Str::beforeLast($searchName, ' (Stock)');
                }
                
                $unitPrice = 0;
                
                // Try Product First
                $product = \App\Models\Product::where('name', $searchName)->first();
                if ($product && $product->price > 0) {
                    $unitPrice = $product->price;
                } else {
                    // Try Inventory Item
                    $inventoryItem = \App\Models\InventoryItem::where('item_name', $searchName)->first();
                    if ($inventoryItem && $inventoryItem->selling_price > 0) {
                        $unitPrice = $inventoryItem->selling_price;
                    } else {
                        // Fallback to report price (often 0, but maybe manually entered)
                        $unitPrice = $charge->unit_price;
                    }
                }

                $initialItems[] = [
                    'description' => $charge->item_description,
                    'quantity' => $charge->quantity,
                    'unit_price' => $unitPrice
                ];
            }

            if(empty($initialItems)) {
                $initialItems = [['description' => $selectedJob->title, 'quantity' => 1, 'unit_price' => 0]];
            }
        } elseif($selectedJob) {
             $initialItems = [['description' => $selectedJob->title, 'quantity' => 1, 'unit_price' => 0]];
        }

        return view('accounts.invoices.create', compact('customers', 'jobs', 'invoiceNumber', 'quotation', 'selectedJob', 'initialItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required',
            'date' => 'required|date',
            'invoice_number' => 'required|unique:invoices,invoice_number',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required',
            'items.*.unit_price' => 'required|numeric',
            'items.*.quantity' => 'required|numeric|min:0.1',
        ]);

        DB::beginTransaction();
        try {
            $invoice = Invoice::create([
                'invoice_number' => $request->invoice_number,
                'customer_id' => $request->customer_id,
                'vessel_id' => $request->vessel_id,
                'service_job_id' => $request->service_job_id,
                'quotation_id' => $request->quotation_id,
                'date' => $request->date,
                'due_date' => $request->due_date,
                'status' => 'draft',
                'subtotal' => $request->subtotal,
                'tax_amount' => $request->tax_amount,
                'discount' => $request->discount ?? 0,
                'total_amount' => $request->total_amount,
                'notes' => $request->notes,
                'created_by' => auth()->id(),
            ]);

            foreach ($request->items as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'amount' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            DB::commit();
            return redirect()->route('accounts.invoices.index')->with('success', 'Invoice created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating invoice: ' . $e->getMessage());
        }
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['items', 'customer', 'vessel', 'serviceJob', 'payments']);
        return view('accounts.invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        if ($invoice->status !== 'draft') {
            return back()->with('error', 'Only draft invoices can be edited.');
        }
        $customers = Customer::orderBy('company_name')->get();
        $jobs = ServiceJob::with(['customer', 'vessel'])->where('status', '!=', 'cancelled')->latest()->get();
        return view('accounts.invoices.edit', compact('invoice', 'customers', 'jobs'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        if ($invoice->status !== 'draft') {
            return back()->with('error', 'Cannot update non-draft invoice.');
        }

        $request->validate([
            'date' => 'required|date',
            'items' => 'required|array|min:1',
        ]);

        DB::beginTransaction();
        try {
            $invoice->update([
                'date' => $request->date,
                'due_date' => $request->due_date,
                'subtotal' => $request->subtotal,
                'tax_amount' => $request->tax_amount,
                'discount' => $request->discount ?? 0,
                'total_amount' => $request->total_amount,
                'notes' => $request->notes,
                'vessel_id' => $request->vessel_id // Update vessel if changed
            ]);

            // Sync items (Delete old, create new)
            $invoice->items()->delete();
             foreach ($request->items as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'amount' => $item['quantity'] * $item['unit_price'],
                ]);
            }
            
            DB::commit();
            return redirect()->route('accounts.invoices.show', $invoice)->with('success', 'Invoice updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating invoice: ' . $e->getMessage());
        }
    }

    public function destroy(Invoice $invoice)
    {
        if ($invoice->status !== 'draft') {
            return back()->with('error', 'Cannot delete finalized invoice.');
        }

        DB::beginTransaction();
        try {
            // Revert Service Report status if this invoice was generated from one
            if ($invoice->service_job_id) {
                // Find the report associated with this job that is currently marked as 'Invoiced'
                $report = ServiceReport::where('service_job_id', $invoice->service_job_id)
                                       ->where('status', 'Invoiced')
                                       ->latest()
                                       ->first();
                
                if ($report) {
                    $report->update(['status' => 'Submitted']);
                }
            }

            $invoice->delete();
            
            DB::commit();
            return redirect()->route('accounts.invoices.index')->with('success', 'Invoice deleted and report status reverted.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting invoice: ' . $e->getMessage());
        }
    }

    public function pdf(Invoice $invoice)
    {
        $invoice->load(['items', 'customer', 'vessel', 'createdBy']);
        $settings = \App\Models\Setting::getAllCached();
        $pdf = Pdf::loadView('accounts.invoices.pdf', compact('invoice', 'settings'));
        return $pdf->stream('Invoice-' . $invoice->invoice_number . '.pdf');
    }
    public function markSent(Invoice $invoice, \App\Services\Accounting\AccountsReceivableService $arService)
    {
        if ($invoice->status !== 'draft') {
            return back()->with('error', 'Invoice is already finalized.');
        }

        DB::beginTransaction();
        try {
            // 1. Post to General Ledger (Accounts Receivable Integration)
            $arService->postInvoice($invoice);

            // 2. Update Customer Sub-Ledger (Operational Statement)
            // Even though GL has this data, we keep this for the specific "Customer Ledger" view until that view is refactored to read from GL.
            $ledgerService = new \App\Services\LedgerService();
            $ledgerService->recordEntry(
                $invoice->customer_id,
                'invoice',
                $invoice->total_amount,
                $invoice,
                'Invoice ' . $invoice->invoice_number,
                $invoice->date
            );

            // Note: Service handles 'status' update to 'sent'.
            
            DB::commit();
            return back()->with('success', 'Invoice finalized and posted to General Ledger.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error finalizing invoice: ' . $e->getMessage());
        }
    }
}
