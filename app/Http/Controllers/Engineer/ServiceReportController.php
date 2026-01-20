<?php

namespace App\Http\Controllers\Engineer;

use App\Http\Controllers\Controller;
use App\Models\ServiceJob;
use App\Models\ServiceReport;
use App\Models\ServiceType;
use App\Models\ServiceReportItem;
use App\Models\ServiceReportCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ServiceReportController extends Controller
{
    public function create(ServiceJob $job)
    {
        $serviceTypes = ServiceType::where('status', 'active')->get();
        // Check if report already exists? For now assume one report per job or multiple allowed.
        // Usually one final report.

        $job->load(['items', 'equipment.equipmentItem']);
        
        // Eager load approved materials
        $job->load(['materials' => function ($query) {
             $query->whereIn('status', ['approved', 'issued']);
        }]);
        
        // Fetch employees (Technicians & Engineers) for overtime selection
        $employees = \App\Models\User::whereHas('roles', function($q) {
            $q->whereIn('slug', ['technician', 'engineer', 'admin']);
        })->where('is_active', true)->orderBy('name')->get();
        
        // Pre-fill Service Groups from Job Equipment (Scope of Work)
        $prefilledGroups = [];
        if($job->equipment->count() > 0) {
            $items = $job->equipment->map(function($e){
                 return [
                     'description' => $e->equipmentItem->name ?? 'Item',
                     'quantity' => $e->quantity
                 ];
            })->values()->toArray();
            
            // Create a default group with these items. User must select Service Type.
            $prefilledGroups[] = [
                'service_type_id' => '', 
                'items' => $items
            ];
        }

        return view('engineer.reports.create', compact('job', 'serviceTypes', 'employees', 'prefilledGroups'));
    }

    public function store(Request $request, ServiceJob $job)
    {
        $request->validate([
            'report_date' => 'required|date',
            'work_description' => 'required|string',
            'service_groups' => 'array',
            'service_groups.*.service_type_id' => 'required|exists:service_types,id',
            'service_groups.*.items' => 'array',
            'service_groups.*.items.*.description' => 'required|string',
            'service_groups.*.items.*.quantity' => 'required|integer|min:1',
            'charges' => 'array',
            'technicians' => 'array',
            'technicians.*.user_id' => 'required|exists:users,id',
            'technicians.*.hours' => 'required|numeric|min:0.5',
            'hours_worked' => 'nullable|numeric|min:0', // Optional total
        ]);

        DB::beginTransaction();

        try {
            // Calculate total hours from individual technicians if not provided manually
            $totalHours = $request->hours_worked ?? 0;
            if ($request->has('technicians')) {
                $calcHours = collect($request->technicians)->sum('hours');
                // Use calculated if manual is 0 or empty? Or just prefer manual?
                // Let's trust the breakdown if provided.
                if ($totalHours == 0) {
                    $totalHours = $calcHours;
                }
            }

            // 1. Create Report
            $report = ServiceReport::create([
                'service_job_id' => $job->id,
                'engineer_id' => auth()->id(),
                'report_date' => $request->report_date,
                'work_description' => $request->work_description,
                'recommendations' => $request->recommendations,
                'hours_worked' => $totalHours,
                'status' => 'Submitted',
                'total_service_amount' => 0 // Pricing decided by Finance later
            ]);

            // Save Overtime / Time Records
            if ($request->has('technicians')) {
                foreach ($request->technicians as $tech) {
                    \App\Models\OvertimeRecord::create([
                        'user_id' => $tech['user_id'],
                        'job_id' => $job->id,
                        'date' => $request->report_date,
                        'hours' => $tech['hours'],
                        'reason' => 'Service Report #' . $report->id,
                        'status' => 'approved', // Auto-approve as it rests on the Service Report
                        'approved_by' => auth()->id() // Self-approved by the submitting engineer (usually lead)
                    ]);
                }
            }

            // 2. Add Service Items (Grouped by Service Type)
            if ($request->has('service_groups')) {
                foreach ($request->service_groups as $group) {
                    if (!empty($group['items'])) {
                        foreach ($group['items'] as $item) {
                            if (!empty($item['description'])) {
                                ServiceReportItem::create([
                                    'service_report_id' => $report->id,
                                    'service_type_id' => $group['service_type_id'],
                                    'description' => $item['description'],
                                    'quantity' => $item['quantity'] ?? 1,
                                    'price' => 0 
                                ]);
                            }
                        }
                    }
                }
            } elseif ($request->has('items')) {
                 // Legacy support or fallback if needed (though View will be updated)
                 foreach ($request->items as $item) {
                     ServiceReportItem::create([
                        'service_report_id' => $report->id,
                        'service_type_id' => $item['service_type_id'],
                        'description' => $item['description'] ?? null,
                        'quantity' => 1,
                        'price' => 0
                    ]);
                }
            }

            // 3. Add Charges (Materials/Parts) - From Stock and Manual Entry
            // Process stock materials (selected via checkboxes)
            if ($request->has('stock_materials')) {
                $stockMaterialIds = $request->stock_materials;
                $stockMaterials = \App\Models\MaterialRequest::whereIn('id', $stockMaterialIds)->get();
                
                foreach ($stockMaterials as $material) {
                    ServiceReportCharge::create([
                        'service_report_id' => $report->id,
                        'item_description' => $material->item_name . ' (Stock)',
                        'quantity' => $material->quantity,
                        'unit_price' => 0,
                        'total_price' => 0
                    ]);
                }
            }
            
            // Process manual materials (manually entered)
            if ($request->has('manual_materials')) {
                foreach ($request->manual_materials as $material) {
                    if (!empty($material['description'])) {
                        ServiceReportCharge::create([
                            'service_report_id' => $report->id,
                            'item_description' => $material['description'],
                            'quantity' => $material['quantity'] ?? 1,
                            'unit_price' => 0,
                            'total_price' => 0
                        ]);
                    }
                }
            }
            
            // Total amount remains 0 or just charges total. 
            // Since Finance sets prices, the "Total" at this stage is provisional or hidden.
            // We'll leave it as 0 for main services.

            // Update Job Status
            $job->update(['status' => 'Completed', 'completed_at' => now()]);

            DB::commit();

            return redirect()->route('engineer.jobs.show', $job->id)
                ->with('success', 'Service Report submitted. Overtime recorded and sent to Finance.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating report: ' . $e->getMessage())->withInput();
        }
    }

    public function show(ServiceReport $report)
    {
        $report->load(['items.serviceType', 'charges', 'job.customer', 'job.vessel']);
        return view('engineer.reports.show', compact('report'));
    }

    public function download(ServiceReport $report)
    {
        $report->load(['items.serviceType', 'charges', 'job.customer', 'job.vessel']);
        $pdf = Pdf::loadView('engineer.reports.pdf', compact('report'));
        return $pdf->stream('ServiceReport-' . $report->job->job_number . '.pdf');
    }
    public function edit(ServiceReport $report)
    {
        $job = $report->job;
         $job->load(['items', 'equipment.equipmentItem']);
        $serviceTypes = ServiceType::where('status', 'active')->get();
        // Fetch employees (Technicians & Engineers) for overtime selection
        $employees = \App\Models\User::whereHas('roles', function($q) {
            $q->whereIn('slug', ['technician', 'engineer', 'admin']);
        })->where('is_active', true)->orderBy('name')->get();
        
        return view('engineer.reports.edit', compact('report', 'job', 'serviceTypes', 'employees'));
    }

    public function update(Request $request, ServiceReport $report)
    {
        $request->validate([
            'report_date' => 'required|date',
            'work_description' => 'required|string',
            'service_groups' => 'array',
            'service_groups.*.service_type_id' => 'required|exists:service_types,id',
            'service_groups.*.items' => 'array',
            'service_groups.*.items.*.description' => 'required|string',
            'service_groups.*.items.*.quantity' => 'required|integer|min:1',
            'charges' => 'array',
            'technicians' => 'array',
            'technicians.*.user_id' => 'required|exists:users,id',
            'technicians.*.hours' => 'required|numeric|min:0.5',
            'hours_worked' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Calculate total hours
            $totalHours = $request->hours_worked ?? 0;
            if ($request->has('technicians')) {
                $calcHours = collect($request->technicians)->sum('hours');
                if ($totalHours == 0) $totalHours = $calcHours;
            }

            // 1. Update Report Details
            $report->update([
                'report_date' => $request->report_date,
                'work_description' => $request->work_description,
                'recommendations' => $request->recommendations,
                'hours_worked' => $totalHours,
            ]);

            // 2. Sync Overtime Records
            // Delete old records for this report
            \App\Models\OvertimeRecord::where('job_id', $report->service_job_id)
                ->where('reason', 'Service Report #' . $report->id)
                ->delete();

            if ($request->has('technicians')) {
                foreach ($request->technicians as $tech) {
                    \App\Models\OvertimeRecord::create([
                        'user_id' => $tech['user_id'],
                        'job_id' => $report->service_job_id,
                        'date' => $request->report_date,
                        'hours' => $tech['hours'],
                        'reason' => 'Service Report #' . $report->id,
                        'status' => 'approved',
                        'approved_by' => auth()->id()
                    ]);
                }
            }

            // 3. Sync Service Items
            $report->items()->delete(); // Clear old items
            if ($request->has('service_groups')) {
                foreach ($request->service_groups as $group) {
                    if (!empty($group['items'])) {
                        foreach ($group['items'] as $item) {
                            if (!empty($item['description'])) {
                                ServiceReportItem::create([
                                    'service_report_id' => $report->id,
                                    'service_type_id' => $group['service_type_id'],
                                    'description' => $item['description'],
                                    'quantity' => $item['quantity'] ?? 1,
                                    'price' => 0
                                ]);
                            }
                        }
                    }
                }
            }

            // 4. Sync Charges
            $report->charges()->delete(); // Clear old charges
            
            // Stock Materials
            if ($request->has('stock_materials')) {
                $stockMaterialIds = $request->stock_materials;
                $stockMaterials = \App\Models\MaterialRequest::whereIn('id', $stockMaterialIds)->get();
                foreach ($stockMaterials as $material) {
                    ServiceReportCharge::create([
                        'service_report_id' => $report->id,
                        'item_description' => $material->item_name . ' (Stock)',
                        'quantity' => $material->quantity,
                        'unit_price' => 0,
                        'total_price' => 0
                    ]);
                }
            }
            
            // Manual Materials
            if ($request->has('manual_materials')) {
                foreach ($request->manual_materials as $material) {
                    if (!empty($material['description'])) {
                        ServiceReportCharge::create([
                            'service_report_id' => $report->id,
                            'item_description' => $material['description'],
                            'quantity' => $material['quantity'] ?? 1,
                            'unit_price' => 0,
                            'total_price' => 0
                        ]);
                    }
                }
            }

            DB::commit();

            // Detect if Report was already Invoiced and Notify Accounts
            \Illuminate\Support\Facades\Log::info('Checking Notification Trigger', [
                'report_id' => $report->id,
                'original_status' => $report->getOriginal('status'),
                'current_status' => $report->status,
                'status_was_invoiced' => $report->getOriginal('status') === 'Invoiced',
                'status_is_invoiced' => $report->status === 'Invoiced'
            ]);

            if ($report->getOriginal('status') === 'Invoiced' || $report->status === 'Invoiced') {
                $accounts = \App\Models\User::whereHas('roles', function($q) {
                    $q->where('slug', 'accounts');
                })->get();
                
                \Illuminate\Support\Facades\Log::info('Sending Notification to Accounts', ['count' => $accounts->count()]);

                \Illuminate\Support\Facades\Notification::send($accounts, new \App\Notifications\ServiceReportUpdated($report));
            }

            return redirect()->route('engineer.reports.show', $report->id)
                ->with('success', 'Service Report updated successfully.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating report: ' . $e->getMessage())->withInput();
        }
    }
}
