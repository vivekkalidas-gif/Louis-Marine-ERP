<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Models\Stat;
use Illuminate\Http\Request;

class StatController extends Controller
{
    public function index()
    {
        $stats = Stat::ordered()->get();
        return view('admin.cms.landing-page.stats.index', compact('stats'));
    }

    public function create()
    {
        return view('admin.cms.landing-page.stats.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'value' => 'required|string|max:50',
            'icon' => 'nullable|string',
            'sort_order' => 'integer',
        ]);

        Stat::create($validated);

        return redirect()->route('admin.cms.landing-page.stats.index')
            ->with('success', 'Statistic created successfully.');
    }

    public function edit(Stat $stat)
    {
        return view('admin.cms.landing-page.stats.edit', compact('stat'));
    }

    public function update(Request $request, Stat $stat)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'value' => 'required|string|max:50',
            'icon' => 'nullable|string',
            'sort_order' => 'integer',
        ]);

        $stat->update($validated);

        return redirect()->route('admin.cms.landing-page.stats.index')
            ->with('success', 'Statistic updated successfully.');
    }

    public function destroy(Stat $stat)
    {
        $stat->delete();

        return redirect()->route('admin.cms.landing-page.stats.index')
            ->with('success', 'Statistic deleted successfully.');
    }

    public function reorder(Request $request)
    {
        $order = $request->input('order', []);
        
        foreach ($order as $index => $id) {
            Stat::where('id', $id)->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true]);
    }
}
