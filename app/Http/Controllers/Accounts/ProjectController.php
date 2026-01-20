<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\Accounting\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        // Eager load for Net Profit calculation if needed optimzed via subquery, 
        // but for now relying on accessor logic in Model (N+1 risk if list is huge, acceptable for MVP).
        $projects = Project::with(['customer', 'manager'])->latest()->get();
        return view('accounts.projects.index', compact('projects'));
    }

    public function create()
    {
        return view('accounts.projects.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:projects,code',
            'name' => 'required',
            'status' => 'required'
        ]);

        $project = Project::create($request->all());

        return redirect()->route('accounts.projects.show', $project);
    }

    public function show(Project $project)
    {
        // Load related data
        // Get Profit & Loss specifics
        // For simple view, just pass project
        return view('accounts.projects.show', compact('project'));
    }
}
