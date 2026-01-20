<?php

namespace App\Http\Controllers\Assets;

use App\Http\Controllers\Controller;
use App\Models\Assets\FixedAsset;
use App\Models\Assets\AssetCategory;
use App\Models\Assets\DepreciationSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FixedAssetController extends Controller
{
    public function index()
    {
        $assets = FixedAsset::with('category')->get();
        return view('assets.index', compact('assets'));
    }

    public function create()
    {
        $categories = AssetCategory::all();
        return view('assets.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required|exists:asset_categories,id',
            'purchase_date' => 'required|date',
            'purchase_cost' => 'required|numeric|min:0',
            'useful_life_years' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            // Generate Code
            $count = FixedAsset::count() + 1;
            $code = 'FA-' . str_pad($count, 5, '0', STR_PAD_LEFT);

            $asset = FixedAsset::create([
                'asset_code' => $code,
                'category_id' => $request->category_id,
                'name' => $request->name,
                'description' => $request->description,
                'serial_number' => $request->serial_number,
                'location' => $request->location,
                'purchase_date' => $request->purchase_date,
                'capitalization_date' => $request->capitalization_date ?? $request->purchase_date,
                'purchase_cost' => $request->purchase_cost,
                'salvage_value' => $request->salvage_value ?? 0,
                'useful_life_years' => $request->useful_life_years,
                'depreciation_method' => $request->depreciation_method ?? 'SLM',
                'current_value' => $request->purchase_cost,
                'status' => 'active',
                'created_by' => auth()->id(),
            ]);

            // Generate Depreciation Schedule (Preview)
            $this->generateSchedule($asset);
        });

        return redirect()->route('assets.index')->with('success', 'Fixed Asset Created Successfully');
    }

    public function show(FixedAsset $asset)
    {
        $asset->load(['category', 'schedules.journalEntry']);
        return view('assets.show', compact('asset'));
    }

    private function generateSchedule(FixedAsset $asset)
    {
        // Simple Straight Line Method (Monthly)
        if ($asset->depreciation_method !== 'SLM') {
            echo "Method not SLM: " . $asset->depreciation_method . "\n";
            return;
        }

        $cost = $asset->purchase_cost;
        $salvage = $asset->salvage_value;
        $years = $asset->useful_life_years;
        $totalMonths = $years * 12;

        $depreciableAmount = $cost - $salvage;
        $monthlyDepreciation = $depreciableAmount / $totalMonths;

        $currentValue = $cost;
        // Start from capitalization date
        $baseDate = Carbon::parse($asset->capitalization_date)->startOfMonth(); 

        for ($i = 1; $i <= $totalMonths; $i++) {
            if ($currentValue <= $salvage) break;

            $scheduleDate = $baseDate->copy()->addMonths($i - 1)->endOfMonth();

            $amount = $monthlyDepreciation;
            // Adjust last installment for rounding
            if ($i == $totalMonths) {
                // Ensure we don't go below salvage
                if ($currentValue - $amount < $salvage) {
                    $amount = $currentValue - $salvage;
                }
                // OR just take remaining
                 $amount = $currentValue - $salvage;
            }

            $currentValue -= $amount;

            try {
                DepreciationSchedule::create([
                    'fixed_asset_id' => $asset->id,
                    'schedule_date' => $scheduleDate,
                    'amount' => $amount,
                    'net_book_value_before' => $currentValue + $amount,
                    'net_book_value_after' => $currentValue,
                    'is_posted' => false,
                ]);
            } catch (\Exception $e) {
               // Log error
            }
        }
    }
}
