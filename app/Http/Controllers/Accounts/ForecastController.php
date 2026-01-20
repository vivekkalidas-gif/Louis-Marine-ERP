<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\Accounting\Forecast;
use App\Models\Accounting\Budget;
use Illuminate\Http\Request;

class ForecastController extends Controller
{
    public function index()
    {
        $forecasts = Forecast::latest()->get();
        return view('accounts.forecasts.index', compact('forecasts'));
    }
    
    // Future Implementation: Generate forecasts
}
