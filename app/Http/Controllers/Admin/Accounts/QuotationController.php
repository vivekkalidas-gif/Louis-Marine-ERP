<?php

namespace App\Http\Controllers\Admin\Accounts;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Customer;
use App\Models\ServiceJob;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;

class QuotationController extends Controller
{
    public function index()
    {
        $quotations = Quotation::with('customer')->latest()->paginate(20);
        return view('admin.quotations.index', compact('quotations'));
    }

    public function create()
    {
        $customers = Customer::all();
        $jobs = ServiceJob::with('customer')->where('status', '!=', 'Closed')->get();
        return view('admin.quotations.create', compact('customers', 'jobs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'date' => 'required|date',
            'valid_until' => 'required|date|after_or_equal:date',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $subtotal = 0;
        foreach ($request->items as $item) {
            $subtotal += $item['quantity'] * $item['unit_price'];
        }

        $tax = $request->has('tax_exempt') ? 0 : ($subtotal * 0.05);

        $year = date('Y');
        $seq = str_pad(Quotation::whereYear('date', $year)->count() + 1, 4, '0', STR_PAD_LEFT);
        $qtNumber = "QTE-{$year}-{$seq}";

        $quotation = Quotation::create([
            'customer_id' => $request->customer_id,
            'service_job_id' => $request->service_job_id,
            'quotation_number' => $qtNumber,
            'date' => $request->date,
            'valid_until' => $request->valid_until,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $subtotal + $tax,
            'status' => 'DRAFT',
            'notes' => $request->notes,
            'terms' => $request->terms ?? "Validity: 30 Days.\nPayment Terms: 50% Advance, Balance on Completion.",
            'created_by' => auth()->id()
        ]);

        foreach ($request->items as $item) {
            QuotationItem::create([
                'quotation_id' => $quotation->id,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $item['quantity'] * $item['unit_price'],
            ]);
        }

        return redirect()->route('admin.quotations.show', $quotation->id)->with('success', 'Quotation Created');
    }

    public function show(Quotation $quotation)
    {
        $quotation->load(['customer', 'items', 'job']);
        return view('admin.quotations.show', compact('quotation'));
    }

    public function print(Quotation $quotation)
    {
        $quotation->load(['customer', 'items']);
        return view('admin.quotations.print', compact('quotation'));
    }

    public function convertToInvoice(Quotation $quotation)
    {
        if($quotation->status == 'CONVERTED') {
            return back()->with('error', 'Already converted.');
        }

        // Logic to create invoice from quotation
        $year = date('Y');
        $seq = str_pad(Invoice::whereYear('date', $year)->count() + 1, 4, '0', STR_PAD_LEFT);
        $invoiceNumber = "INV-{$year}-{$seq}";

        $invoice = Invoice::create([
            'customer_id' => $quotation->customer_id,
            'service_job_id' => $quotation->service_job_id,
            'invoice_number' => $invoiceNumber,
            'date' => now(),
            'due_date' => now()->addDays(30),
            'subtotal' => $quotation->subtotal,
            'tax' => $quotation->tax,
            'total' => $quotation->total,
            'status' => 'SENT',
            'notes' => "Converted from Quotation " . $quotation->quotation_number . "\n" . $quotation->notes
        ]);

        foreach ($quotation->items as $item) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => $item->description,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total' => $item->total
            ]);
        }

        $quotation->update(['status' => 'CONVERTED']);

        return redirect()->route('admin.invoices.show', $invoice->id)->with('success', 'Converted to Invoice Successfully');
    }
}
