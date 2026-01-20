<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Expense;
use Carbon\Carbon;

class TaxReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Output Tax (collected from Sales Invoices)
        // Only consider PAID or SENT invoices? Usually Tax logic applies on Accrual basis (Invoice Date) regardless of payment status,
        // unless it's Cash Basis. Let's assume Accrual (Issue Date).
        // Exclude Cancelled / Draft ideally.
        $outputTaxInvoices = Invoice::whereBetween('date', [$startDate, $endDate])
            ->where('status', '!=', 'CANCELLED')
            ->where('status', '!=', 'DRAFT')
            ->select('id', 'invoice_number', 'date', 'total_amount', 'tax')
            ->orderBy('date', 'desc')
            ->get();

        $totalOutputTax = $outputTaxInvoices->sum('tax');
        $totalSales = $outputTaxInvoices->sum('total_amount') - $totalOutputTax; // Net Sales

        // Input Tax (paid on Expenses)
        $inputTaxExpenses = Expense::whereBetween('date', [$startDate, $endDate])
            ->where('tax_amount', '>', 0)
            ->select('id', 'reference_number', 'date', 'amount', 'tax_amount', 'vendor_name', 'description')
            ->orderBy('date', 'desc')
            ->get();

        $totalInputTax = $inputTaxExpenses->sum('tax_amount');
        $totalPurchases = $inputTaxExpenses->sum('amount') - $totalInputTax; // Net Purchases

        $netTaxPayable = $totalOutputTax - $totalInputTax;

        return view('admin.reports.tax', compact(
            'startDate', 'endDate',
            'outputTaxInvoices', 'totalOutputTax', 'totalSales',
            'inputTaxExpenses', 'totalInputTax', 'totalPurchases',
            'netTaxPayable'
        ));
    }
}
