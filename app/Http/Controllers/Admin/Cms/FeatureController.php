<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use Illuminate\Http\Request;

class FeatureController extends Controller
{
    public function index()
    {
        $features = Feature::ordered()->get();
        return view('admin.cms.landing-page.features.index', compact('features'));
    }

    public function create()
    {
        return view('admin.cms.landing-page.features.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        Feature::create($validated);

        return redirect()->route('admin.cms.landing-page.features.index')
            ->with('success', 'Feature created successfully.');
    }

    public function edit(Feature $feature)
    {
        return view('admin.cms.landing-page.features.edit', compact('feature'));
    }

    public function update(Request $request, Feature $feature)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $feature->update($validated);

        return redirect()->route('admin.cms.landing-page.features.index')
            ->with('success', 'Feature updated successfully.');
    }

    public function destroy(Feature $feature)
    {
        $feature->delete();

        return redirect()->route('admin.cms.landing-page.features.index')
            ->with('success', 'Feature deleted successfully.');
    }

    public function reorder(Request $request)
    {
        $order = $request->input('order', []);
        
        foreach ($order as $index => $id) {
            Feature::where('id', $id)->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true]);
    }
}
