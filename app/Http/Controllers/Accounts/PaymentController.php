<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payments = Payment::with(['invoice.customer', 'invoice.vessel'])
            ->latest('payment_date')
            ->paginate(15);
            
        return view('accounts.payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $invoice = null;
        if ($request->has('invoice_id')) {
            $invoice = Invoice::findOrFail($request->invoice_id);
        }
        
        // If invoice is provided, we can prepopulate
        $invoices = Invoice::with('customer')
            //->where('status', '!=', 'draft')
            ->where('status', '!=', 'cancelled')
            ->whereRaw('total_amount > COALESCE(paid_amount, 0)') // Only unpaid/partial
            ->get();

        return view('accounts.payments.create', compact('invoice', 'invoices'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, \App\Services\Accounting\AccountsReceivableService $arService)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
        ]);

        $invoice = Invoice::findOrFail($request->invoice_id);

        if ($request->amount > $invoice->balance) {
            return back()->with('error', 'Payment amount cannot exceed invoice balance (' . number_format($invoice->balance, 2) . ').');
        }

        DB::beginTransaction();
        try {
            $payment = Payment::create([
                'payment_number' => 'PAY-' . date('Ymd') . '-' . rand(1000, 9999), // Simple generator
                'invoice_id' => $invoice->id,
                'customer_id' => $invoice->customer_id, // Ensure customer match
                'payment_date' => $request->payment_date,
                'amount' => $request->amount,
                'status' => 'completed',
                'payment_method' => $request->payment_method,
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
                'created_by' => auth()->id(),
            ]);

            // 1. Record in General Ledger (Accounts Receivable Integration)
            $arService->recordPayment($payment);

            // 2. Update Customer Sub-Ledger (Operational Statement)
            $ledgerService = new \App\Services\LedgerService();
            $ledgerService->recordEntry(
                $invoice->customer_id,
                'payment',
                $request->amount,
                $payment,
                'Payment ' . ($payment->reference_number ? "({$payment->reference_number})" : ""),
                $request->payment_date
            );

            DB::commit();

            if ($request->has('print_receipt')) {
                return redirect()->route('accounts.payments.show', $payment);
            }

            return redirect()->route('accounts.invoices.show', $invoice)->with('success', 'Payment recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error recording payment: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        return view('accounts.payments.show', compact('payment'));
    }

    /**
     * Generate Receipt PDF
     */
    public function receipt(Payment $payment)
    {
        $payment->load(['invoice.customer', 'invoice.vessel', 'invoice.serviceJob']);
        $settings = \App\Models\Setting::getAllCached();
        $pdf = Pdf::loadView('accounts.payments.receipt', compact('payment', 'settings'));
        return $pdf->stream('Receipt-' . $payment->payment_number . '.pdf');
    }
}
