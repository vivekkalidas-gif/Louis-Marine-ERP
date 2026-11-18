<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DispatchNoteController extends Controller
{
    public function index()
    {
        $notes = \App\Models\DispatchNote::with('creator', 'items')->orderBy('date', 'desc')->get();
        return view('admin.dispatch.index', compact('notes'));
    }

    public function create()
    {
        // Generate a new Dispatch Number
        $lastNote = \App\Models\DispatchNote::latest()->first();
        $nextNum = $lastNote ? intval(substr($lastNote->dispatch_number, 3)) + 1 : 1;
        $prefix = config('settings.prefix_dispatch', 'DN');
        $dispatchNumber = $prefix . '-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

        $preFilledItems = [];
        $dispatchType = request('type', 'Calibration'); // Default or from request

        if (request()->has('equipment_ids')) {
            $ids = explode(',', request('equipment_ids'));
            $equipmentList = \App\Models\EquipmentItem::whereIn('id', $ids)->get();
            
            foreach ($equipmentList as $equipment) {
                $preFilledItems[] = [
                    'description' => $equipment->name,
                    'serial_number' => $equipment->code,
                    'quantity' => 1,
                    'remarks' => $dispatchType === 'Calibration' ? 'Sent for Calibration' : ''
                ];
            }
        } elseif (request()->has('equipment_id')) {
            $equipment = \App\Models\EquipmentItem::find(request('equipment_id'));
            if ($equipment) {
                $preFilledItems[] = [
                    'description' => $equipment->name,
                    'serial_number' => $equipment->code,
                    'quantity' => 1,
                    'remarks' => $dispatchType === 'Calibration' ? 'Sent for Calibration' : ''
                ];
            }
        }

        return view('admin.dispatch.create', compact('dispatchNumber', 'preFilledItems', 'dispatchType'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'dispatch_number' => 'required|unique:dispatch_notes',
            'date' => 'required|date',
            'type' => 'required',
            'receiver_name' => 'required',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
            $dispatchType = $request->type === 'Other' ? ($request->custom_type ?? 'Other') : $request->type;

            $note = \App\Models\DispatchNote::create([
                'dispatch_number' => $request->dispatch_number,
                'date' => $request->date,
                'type' => $dispatchType,
                'receiver_name' => $request->receiver_name,
                'receiver_address' => $request->receiver_address,
                'service_job_id' => $request->service_job_id, // Optional
                'status' => 'Dispatched', // Default for now
                'created_by' => auth()->id(),
            ]);

            foreach ($request->items as $item) {
                $note->items()->create([
                    'description' => $item['description'],
                    'serial_number' => $item['serial_number'] ?? null,
                    'quantity' => $item['quantity'] ?? 1,
                    'remarks' => $item['remarks'] ?? null,
                ]);
            }
        });

        return redirect()->route('admin.dispatch.index')->with('success', 'Dispatch Note created successfully.');
    }

    public function show(\App\Models\DispatchNote $dispatchNote)
    {
        $dispatchNote->load('items', 'creator');
        return view('admin.dispatch.show', compact('dispatchNote'));
    }

    public function edit(\App\Models\DispatchNote $dispatchNote)
    {
        // dd($dispatchNote);
        $dispatchNote->load('items');
        return view('admin.dispatch.edit', compact('dispatchNote'));
    }

    public function update(\Illuminate\Http\Request $request, \App\Models\DispatchNote $dispatchNote)
    {
        $request->validate([
            'date' => 'required|date',
            'type' => 'required',
            'receiver_name' => 'required',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $dispatchNote) {
            $dispatchType = $request->type === 'Other' ? ($request->custom_type ?? 'Other') : $request->type;

            $dispatchNote->update([
                'date' => $request->date,
                'type' => $dispatchType,
                'receiver_name' => $request->receiver_name,
                'receiver_address' => $request->receiver_address,
                'service_job_id' => $request->service_job_id,
            ]);

            // Replace items
            $dispatchNote->items()->delete();
            
            foreach ($request->items as $item) {
                $dispatchNote->items()->create([
                    'description' => $item['description'],
                    'serial_number' => $item['serial_number'] ?? null,
                    'quantity' => $item['quantity'] ?? 1,
                    'remarks' => $item['remarks'] ?? null,
                ]);
            }
        });

        return redirect()->route('admin.dispatch.index')->with('success', 'Dispatch Note updated successfully.');
    }

    public function pdf(\App\Models\DispatchNote $dispatchNote)
    {
        $dispatchNote->load(['items', 'creator', 'serviceJob.vessel', 'serviceJob.customer']);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.dispatch.pdf', compact('dispatchNote'));
        return $pdf->download('Dispatch-Note-' . $dispatchNote->dispatch_number . '.pdf');
    }

    public function destroy(\App\Models\DispatchNote $dispatchNote)
    {
        $dispatchNote->delete();
        return redirect()->route('admin.dispatch.index')->with('success', 'Dispatch Note deleted successfully.');
    }
}
