<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use Illuminate\Http\Request;

class AboutPageController extends Controller
{
    public function index()
    {
        $page = CmsPage::where('slug', 'about-us')->with(['sections' => function($query) {
            $query->where('is_visible', true)->orderBy('order');
        }])->firstOrFail();

        return view('frontend.about', compact('page'));
    }
}
