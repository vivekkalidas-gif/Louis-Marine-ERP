<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::ordered()->get();
        return view('admin.cms.landing-page.testimonials.index', compact('testimonials'));
    }

    public function create()
    {
        return view('admin.cms.landing-page.testimonials.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'testimonial_text' => 'required|string',
            'client_photo' => 'nullable|image|max:1024',
            'rating' => 'required|integer|min:1|max:5',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        if ($request->hasFile('client_photo')) {
            $validated['client_photo'] = $request->file('client_photo')->store('testimonials', 'public');
        }

        Testimonial::create($validated);

        return redirect()->route('admin.cms.landing-page.testimonials.index')
            ->with('success', 'Testimonial created successfully.');
    }

    public function edit(Testimonial $testimonial)
    {
        return view('admin.cms.landing-page.testimonials.edit', compact('testimonial'));
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'testimonial_text' => 'required|string',
            'client_photo' => 'nullable|image|max:1024',
            'rating' => 'required|integer|min:1|max:5',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        if ($request->hasFile('client_photo')) {
            if ($testimonial->client_photo) {
                Storage::disk('public')->delete($testimonial->client_photo);
            }
            $validated['client_photo'] = $request->file('client_photo')->store('testimonials', 'public');
        }

        $testimonial->update($validated);

        return redirect()->route('admin.cms.landing-page.testimonials.index')
            ->with('success', 'Testimonial updated successfully.');
    }

    public function destroy(Testimonial $testimonial)
    {
        if ($testimonial->client_photo) {
            Storage::disk('public')->delete($testimonial->client_photo);
        }
        
        $testimonial->delete();

        return redirect()->route('admin.cms.landing-page.testimonials.index')
            ->with('success', 'Testimonial deleted successfully.');
    }

    public function reorder(Request $request)
    {
        $order = $request->input('order', []);
        
        foreach ($order as $index => $id) {
            Testimonial::where('id', $id)->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true]);
    }
}
