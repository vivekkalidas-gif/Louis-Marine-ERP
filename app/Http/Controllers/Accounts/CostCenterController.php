<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\Accounting\CostCenter;
use Illuminate\Http\Request;

class CostCenterController extends Controller
{
    public function index()
    {
        // Get root nodes with recursion
        $costCenters = CostCenter::whereNull('parent_id')
            ->with(['children.children', 'manager'])
            ->get();
            
        return view('accounts.cost_centers.index', compact('costCenters'));
    }

    public function create()
    {
        $parents = CostCenter::orderBy('name')->get();
        return view('accounts.cost_centers.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:cost_centers,code',
            'name' => 'required',
            'type' => 'required|in:operational,support',
        ]);

        CostCenter::create($request->all());

        return redirect()->route('accounts.cost-centers.index')->with('success', 'Cost Center created.');
    }
}
