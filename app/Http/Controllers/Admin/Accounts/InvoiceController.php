<?php

namespace App\Http\Controllers\Admin\Accounts;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Customer;
use App\Models\ServiceJob;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    use \Illuminate\Foundation\Auth\Access\AuthorizesRequests;

    public function __construct()
    {
        $this->authorizeResource(Invoice::class, 'invoice');
    }

    public function index()
    {
        $invoices = Invoice::with(['customer', 'payments'])->latest()->paginate(20);
        return view('admin.invoices.index', compact('invoices'));
    }

    public function create()
    {
        $customers = Customer::all();
        $jobs = ServiceJob::with('customer')->where('status', '!=', 'Closed')->get();
        // Fetch active inventory items for selection
        $inventoryItems = \App\Models\InventoryItem::where('status', 'active')
            ->select('id', 'item_name', 'selling_price')
            ->orderBy('item_name')
            ->get();
            
        return view('admin.invoices.create', compact('customers', 'jobs', 'inventoryItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:date',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $subtotal = 0;
        foreach ($request->items as $item) {
            $subtotal += $item['quantity'] * $item['unit_price'];
        }

        $tax = $subtotal * 0.05; // 5% VAT Fixed
        if($request->has('tax_exempt')) {
            $tax = 0;
        }

        $year = date('Y');
        $seq = str_pad(Invoice::whereYear('date', $year)->count() + 1, 4, '0', STR_PAD_LEFT);
        $invoiceNumber = "INV-{$year}-{$seq}";

        $invoice = Invoice::create([
            'customer_id' => $request->customer_id,
            'service_job_id' => $request->service_job_id,
            'invoice_number' => $invoiceNumber,
            'date' => $request->date,
            'due_date' => $request->due_date,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $subtotal + $tax,
            'status' => 'SENT',
            'notes' => $request->notes
        ]);

        foreach ($request->items as $item) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $item['quantity'] * $item['unit_price'],
            ]);
        }

        return redirect()->route('admin.invoices.show', $invoice->id)->with('success', 'Invoice Created Successfully');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['customer', 'items', 'payments', 'job']);
        return view('admin.invoices.show', compact('invoice'));
    }

    public function addPayment(Request $request, Invoice $invoice)
    {
        $this->authorize('update', $invoice);
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'method' => 'required|string',
        ]);

        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'method' => $request->method,
            'reference' => $request->reference,
            'notes' => $request->notes,
        ]);

        // Auto-update status
        $totalPaid = $invoice->payments()->sum('amount');
        if ($totalPaid >= $invoice->total) {
            $invoice->update(['status' => 'PAID']);
        } elseif ($totalPaid > 0) {
            $invoice->update(['status' => 'PARTIAL']);
        }

        return back()->with('success', 'Payment Recorded.');
    }

    public function print(Invoice $invoice)
    {
        $this->authorize('view', $invoice);
        $invoice->load(['customer', 'items', 'job']);
        return view('admin.invoices.print', compact('invoice'));
    }
}
