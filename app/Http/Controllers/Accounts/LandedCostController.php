<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\Accounting\LandedCost;
use App\Models\Accounting\LandedCostLine;
use App\Models\Accounting\ChartOfAccount;
use App\Models\PurchaseOrder;
use App\Services\LandedCostService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LandedCostController extends Controller
{
    protected $landedCostService;

    public function __construct(LandedCostService $landedCostService)
    {
        $this->landedCostService = $landedCostService;
    }

    public function index()
    {
        $landedCosts = LandedCost::with('purchaseOrder')->latest()->paginate(20);
        return view('accounts.landed_cost.index', compact('landedCosts'));
    }

    public function create()
    {
        // Only show POs that are NOT draft? Or Received?
        // For simplicity, showing all POs. Ideal filter: whereIn('status', ['received', 'completed'])
        $purchaseOrders = PurchaseOrder::latest()->take(50)->get();
        return view('accounts.landed_cost.create', compact('purchaseOrders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'date' => 'required|date',
            'reference' => 'nullable|string',
        ]);

        $lc = LandedCost::create([
            'purchase_order_id' => $request->purchase_order_id,
            'date' => $request->date,
            'reference' => $request->reference,
            'status' => 'draft',
            'created_by' => auth()->id()
        ]);

        return redirect()->route('accounts.landed-costs.show', $lc->id);
    }

    public function show(LandedCost $landedCost)
    {
        $landedCost->load(['lines.account', 'purchaseOrder.items.inventoryItem', 'allocations.item']);
        $expenseAccounts = ChartOfAccount::where('type', 'expense')->orderBy('name')->get();
        
        return view('accounts.landed_cost.show', compact('landedCost', 'expenseAccounts'));
    }

    public function addLine(Request $request, LandedCost $landedCost)
    {
        if ($landedCost->status !== 'draft') abort(403);
        
        $request->validate([
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'allocation_method' => 'required|in:value,quantity',
            'account_id' => 'required|exists:chart_of_accounts,id'
        ]);

        $landedCost->lines()->create($request->all());
        
        // Update Total
        $landedCost->total_amount = $landedCost->lines()->sum('amount');
        $landedCost->save();
        
        // Auto Calculate Allocation
        $this->landedCostService->allocateCosts($landedCost);

        return back()->with('success', 'Cost line added.');
    }
    
    public function removeLine(LandedCost $landedCost, LandedCostLine $line)
    {
        if ($landedCost->status !== 'draft') abort(403);
        $line->delete();
        
         // Update Total
        $landedCost->total_amount = $landedCost->lines()->sum('amount');
        $landedCost->save();
        
        $this->landedCostService->allocateCosts($landedCost);
        
        return back()->with('success', 'Cost line removed.');
    }

    public function post(LandedCost $landedCost)
    {
        if ($landedCost->status !== 'draft') abort(403);
        
        try {
            $this->landedCostService->post($landedCost);
            return back()->with('success', 'Landed Cost posted successfully. Stock values updated.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error posting: ' . $e->getMessage());
        }
    }
}
