<?php

namespace App\Http\Controllers\Storekeeper;

use App\Http\Controllers\Controller;
use App\Models\Rfq;
use App\Models\SupplierQuotation;
use Illuminate\Http\Request;

class SupplierQuotationController extends Controller
{
    public function store(Request $request, Rfq $rfq)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'subtotal_amount' => 'required|numeric|min:0',
        ]);

        // Logic to calculate landed cost if overseas
        $exchangeRate = $request->exchange_rate ?? 1.0;
        $totalForeign = $request->subtotal_amount + ($request->tax_amount ?? 0) + ($request->shipping_cost ?? 0) + ($request->insurance_cost ?? 0) + ($request->customs_duty ?? 0) + ($request->other_costs ?? 0);
        
        // If local, assumed foreign is same as local (base currency)
        if ($rfq->type === 'local') {
             $totalLocal = $totalForeign;
        } else {
             $totalLocal = $totalForeign * $exchangeRate;
        }

        SupplierQuotation::create([
            'rfq_id' => $rfq->id,
            'supplier_id' => $request->supplier_id,
            'subtotal_amount' => $request->subtotal_amount,
            'tax_amount' => $request->tax_amount ?? 0,
            'shipping_cost' => $request->shipping_cost ?? 0,
            'insurance_cost' => $request->insurance_cost ?? 0,
            'customs_duty' => $request->customs_duty ?? 0,
            'other_costs' => $request->other_costs ?? 0,
            'total_amount_foreign' => $totalForeign,
            'exchange_rate' => $exchangeRate,
            'total_amount_local' => $totalLocal,
            'lead_time' => $request->lead_time,
            'warranty' => $request->warranty,
            'payment_terms' => $request->payment_terms,
            'is_selected' => false,
        ]);

        return back()->with('success', 'Quotation added successfully');
    }

    public function select(Rfq $rfq, SupplierQuotation $quotation)
    {
        // Unselect others
        $rfq->quotations()->update(['is_selected' => false]);
        
        // Select this one
        $quotation->update(['is_selected' => true]);
        
        return back()->with('success', 'Supplier selected for this RFQ');
    }

    public function show(Rfq $rfq, SupplierQuotation $quotation)
    {
        return view('storekeeper.rfq.quotation.show', compact('rfq', 'quotation'));
    }

    public function edit(Rfq $rfq, SupplierQuotation $quotation)
    {
        return view('storekeeper.rfq.quotation.edit', compact('rfq', 'quotation'));
    }

    public function update(Request $request, Rfq $rfq, SupplierQuotation $quotation)
    {
        $request->validate([
            'subtotal_amount' => 'required|numeric|min:0',
        ]);

        // Logic to calculate landed cost if overseas
        $exchangeRate = $request->exchange_rate ?? 1.0;
        $totalForeign = $request->subtotal_amount + ($request->tax_amount ?? 0) + ($request->shipping_cost ?? 0) + ($request->insurance_cost ?? 0) + ($request->customs_duty ?? 0) + ($request->other_costs ?? 0);
        
        // If local, assumed foreign is same as local (base currency)
        if ($rfq->type === 'local') {
             $totalLocal = $totalForeign;
        } else {
             $totalLocal = $totalForeign * $exchangeRate;
        }

        $quotation->update([
            'subtotal_amount' => $request->subtotal_amount,
            'tax_amount' => $request->tax_amount ?? 0,
            'shipping_cost' => $request->shipping_cost ?? 0,
            'insurance_cost' => $request->insurance_cost ?? 0,
            'customs_duty' => $request->customs_duty ?? 0,
            'other_costs' => $request->other_costs ?? 0,
            'total_amount_foreign' => $totalForeign,
            'exchange_rate' => $exchangeRate,
            'total_amount_local' => $totalLocal,
            'lead_time' => $request->lead_time,
            'warranty' => $request->warranty,
            'payment_terms' => $request->payment_terms,
        ]);

        return redirect()->route('storekeeper.rfq.show', $rfq)->with('success', 'Quotation updated successfully');
    }

    public function destroy(Rfq $rfq, SupplierQuotation $quotation)
    {
        $quotation->delete();
        return back()->with('success', 'Quotation deleted successfully');
    }
}
