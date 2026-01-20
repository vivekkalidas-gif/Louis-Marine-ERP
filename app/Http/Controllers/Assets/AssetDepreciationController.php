<?php

namespace App\Http\Controllers\Assets;

use App\Http\Controllers\Controller;
use App\Models\Assets\DepreciationSchedule;
use App\Models\Accounting\JournalEntry;
use App\Models\Accounting\AccountingJournal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AssetDepreciationController extends Controller
{
    public function index()
    {
        // Show pending depreciation for this month
        $currentMonthEnd = Carbon::now()->endOfMonth();
        
        $pending = DepreciationSchedule::with('asset.category')
            ->where('is_posted', false)
            ->where('schedule_date', '<=', $currentMonthEnd)
            ->orderBy('schedule_date')
            ->get();

        return view('assets.depreciation.index', compact('pending'));
    }

    public function post(Request $request)
    {
        // Post all selected or all pending up to date
        // For MVP: Post ALL pending showing in index
        
        $currentMonthEnd = Carbon::now()->endOfMonth();
        
        $schedules = DepreciationSchedule::with(['asset.category'])
            ->where('is_posted', false)
            ->where('schedule_date', '<=', $currentMonthEnd)
            ->get();

        if ($schedules->isEmpty()) {
            return back()->with('error', 'No pending depreciation found.');
        }

        DB::transaction(function () use ($schedules) {
            $journal = AccountingJournal::firstOrCreate(['code' => 'DEP'], ['name' => 'Depreciation', 'type' => 'general']);

            foreach ($schedules as $schedule) {
                $asset = $schedule->asset;
                $category = $asset->category;

                // Create Journal Entry
                $entry = JournalEntry::create([
                    'journal_id' => $journal->id,
                    'date' => $schedule->schedule_date,
                    'name' => 'DEP/' . $asset->asset_code . '/' . $schedule->schedule_date->format('Ym'),
                    'reference' => "Depreciation for {$asset->name}",
                    'state' => 'posted', // Auto-post
                    'created_by' => auth()->id(),
                    'posted_by' => auth()->id(),
                ]);

                // Debit Expense
                $entry->items()->create([
                    'account_id' => $category->depreciation_expense_account_id,
                    'debit' => $schedule->amount,
                    'credit' => 0,
                    'date' => $schedule->schedule_date,
                    'label' => "Dep Exp - {$asset->name}"
                ]);

                // Credit Accumulated Depreciation
                $entry->items()->create([
                    'account_id' => $category->accumulated_depreciation_account_id,
                    'debit' => 0,
                    'credit' => $schedule->amount,
                    'date' => $schedule->schedule_date,
                    'label' => "Acc Dep - {$asset->name}"
                ]);

                // Update Asset & Schedule
                $schedule->update([
                    'is_posted' => true,
                    'journal_entry_id' => $entry->id
                ]);

                $asset->increment('accumulated_depreciation', $schedule->amount);
                $asset->decrement('current_value', $schedule->amount);
            }
        });

        return back()->with('success', 'Depreciation Posted Successfully.');
    }
}
