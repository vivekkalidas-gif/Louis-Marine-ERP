<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use App\Models\WebsiteProduct;
use App\Models\CmsService;

class SuperAdminCMSController extends Controller
{
    public function index()
    {
        // Calculate CMS Stats (Strictly website related)
        $stats = [
            'total_products' => WebsiteProduct::count(),
            'active_products' => WebsiteProduct::where('is_active', true)->count(),
            'services' => CmsService::count(),
            'enquiries' => ContactSubmission::where('status', 'new')->count(),
            'page_views' => 1250, // Placeholder for analytics integration
        ];

        return view('admin.cms.dashboard', compact('stats'));
    }
}
