<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceJob;
use App\Models\JobItem;

class JobItemController extends Controller
{
    public function store(Request $request, ServiceJob $job)
    {
        $request->validate([
            'name' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'description' => 'nullable|string'
        ]);

        $job->items()->create($request->all());

        return back()->with('success', 'Item added to job.');
    }

    public function destroy(ServiceJob $job, JobItem $item)
    {
        $item->delete();
        return back()->with('success', 'Item removed.');
    }
}
