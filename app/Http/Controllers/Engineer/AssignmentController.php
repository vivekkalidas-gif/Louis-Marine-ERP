<?php

namespace App\Http\Controllers\Engineer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceJob;
use App\Models\User;
use App\Models\JobAssignment;

class AssignmentController extends Controller
{
    public function create(ServiceJob $job)
    {
        // Get available technicians ONLY (not engineers)
        $technicians = User::whereHas('roles', function($q) {
            $q->where('slug', 'technician');
        })->get();

        // Get current assignments (only those with existing users)
        $currentAssignments = $job->assignments()
            ->with('user')
            ->whereHas('user')
            ->get();

        return view('engineer.assignments.create', compact('job', 'technicians', 'currentAssignments'));
    }

    public function store(Request $request, ServiceJob $job)
    {
        $validated = $request->validate([
            'technicians' => 'required|array|min:1',
            'technicians.*' => 'exists:users,id'
        ]);

        // Remove existing assignments
        $job->assignments()->delete();

        // Create new assignments
        foreach ($validated['technicians'] as $techId) {
            JobAssignment::create([
                'service_job_id' => $job->id,
                'user_id' => $techId,
                'assigned_by' => auth()->id(),
                'status' => 'assigned',
                'assigned_at' => now()
            ]);
        }

        return redirect()->route('engineer.jobs.show', $job->id)
            ->with('success', 'Technicians assigned successfully.');
    }
}
