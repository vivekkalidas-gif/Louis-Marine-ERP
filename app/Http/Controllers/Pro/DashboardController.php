<?php

namespace App\Http\Controllers\Pro;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Show pending certificates for PRO review
        $pendingCertificates = Certificate::with(['type', 'customer', 'vessel'])
            ->where('status', 'PENDING')
            ->orderBy('created_at', 'asc')
            ->get();

        $recentHistory = Certificate::where('approved_by', auth()->id())
            ->orWhere('status', 'REJECTED')
            ->latest()
            ->take(5)
            ->get();

        return view('pro.dashboard', compact('pendingCertificates', 'recentHistory'));
    }
}
