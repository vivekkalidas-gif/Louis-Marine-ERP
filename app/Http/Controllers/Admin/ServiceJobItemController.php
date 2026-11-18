<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceJob;
use App\Models\JobItem;
use Illuminate\Http\Request;

class ServiceJobItemController extends Controller
{
    public function store(Request $request, ServiceJob $job)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'description' => 'nullable|string|max:255',
        ]);

        $job->items()->create($validated);

        return redirect()->back()->with('success', 'Item added successfully.');
    }

    public function destroy(ServiceJob $job, JobItem $item)
    {
        // Ensure the item belongs to the job
        if ($item->service_job_id !== $job->id) {
            abort(404, 'Item not found in this job.');
        }

        $item->delete();

        return redirect()->back()->with('success', 'Item removed successfully.');
    }
}
