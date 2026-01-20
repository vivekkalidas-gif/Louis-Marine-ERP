<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Models\CmsService;
use App\Models\CmsCertification;
use App\Models\CmsPartner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CmsController extends Controller
{
    // SERVICES
    public function servicesIndex()
    {
        $services = CmsService::orderBy('order')->paginate(10);
        return view('admin.cms.services.index', compact('services'));
    }

    public function servicesStore(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required',
            'description' => 'required',
            'icon_class' => 'nullable',
            'image_path' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image_path')) {
            $validated['image_path'] = $request->file('image_path')->store('cms/services', 'public');
        }

        CmsService::create($validated);
        return back()->with('success', 'Service created successfully.');
    }

    public function servicesDestroy(CmsService $service)
    {
        $service->delete();
        return back()->with('success', 'Service deleted.');
    }

    // CERTIFICATIONS
    public function certificationsIndex()
    {
        $certifications = CmsCertification::orderBy('order')->paginate(10);
        return view('admin.cms.certifications.index', compact('certifications'));
    }

    public function certificationsStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'issuing_body' => 'nullable',
            'image_path' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image_path')) {
            $validated['image_path'] = $request->file('image_path')->store('cms/certifications', 'public');
        }

        CmsCertification::create($validated);
        return back()->with('success', 'Certification created successfully.');
    }
    
    public function certificationsDestroy(CmsCertification $certification)
    {
        $certification->delete();
        return back()->with('success', 'Certification deleted.');
    }

    // PARTNERS
    public function partnersIndex()
    {
        $partners = CmsPartner::orderBy('order')->paginate(10);
        return view('admin.cms.partners.index', compact('partners'));
    }

    public function partnersStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'website_url' => 'nullable|url',
            'logo_path' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo_path')) {
            $validated['logo_path'] = $request->file('logo_path')->store('cms/partners', 'public');
        }

        CmsPartner::create($validated);
        return back()->with('success', 'Partner added successfully.');
    }

    public function partnersDestroy(CmsPartner $partner)
    {
        $partner->delete();
        return back()->with('success', 'Partner removed.');
    }
}
