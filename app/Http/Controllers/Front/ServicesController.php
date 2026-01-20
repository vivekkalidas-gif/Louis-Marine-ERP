<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\CmsService;
use Illuminate\Http\Request;

class ServicesController extends Controller
{
    public function index()
    {
        $services = CmsService::where('is_active', true)->orderBy('order')->get();
        return view('frontend.services', compact('services'));
    }

    public function show(CmsService $service)
    {
        if (!$service->is_active) {
            abort(404);
        }
        return view('frontend.services.show', compact('service'));
    }
}
