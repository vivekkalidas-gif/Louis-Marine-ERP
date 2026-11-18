<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\CmsPage;
use App\Models\CmsBanner;
use App\Models\ContactSubmission;
use App\Http\Controllers\Controller;

class FrontController extends Controller
{
    public function home()
    {
        try {
            // Landing Page Editor Data
            $heroSlides = \App\Models\HeroSlide::active()->ordered()->get();
            $features = \App\Models\Feature::active()->ordered()->get();
            $testimonials = \App\Models\Testimonial::active()->ordered()->get();
            $stats = \App\Models\Stat::ordered()->get();
            $ctaSections = \App\Models\CtaSection::active()->get();
            
            // Existing CMS Data
            $services = \App\Models\CmsService::where('is_active', true)->orderBy('order')->get();
            $certifications = \App\Models\CmsCertification::where('is_active', true)->orderBy('order')->get();
            $partners = \App\Models\CmsPartner::where('is_active', true)->orderBy('order')->get();
            
            // Legacy hero support (for backward compatibility)
            $hero = $heroSlides->first(); // Use first hero slide as main hero
            
            return view('frontend.home', compact(
                'heroSlides', 'features', 'testimonials', 'stats', 'ctaSections',
                'services', 'certifications', 'partners', 'hero'
            ));
        } catch (\Throwable $e) {
            // If DB is down or tables don't exist, redirect to installer
            return redirect()->route('system.install');
        }
    }

    public function page($slug)
    {
        $page = CmsPage::where('slug', $slug)->where('is_published', true)->firstOrFail();
        
        if (view()->exists("frontend.{$slug}")) {
            return view("frontend.{$slug}", compact('page'));
        }

        return view('frontend.page', compact('page'));
    }

    public function contact()
    {
        return view('frontend.contact');
    }

    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required',
            'subject' => 'nullable'
        ]);

        $validated['ip_address'] = $request->ip();

        ContactSubmission::create($validated);

        return back()->with('success', 'Thank you! We will contact you shortly.');
    }
}
