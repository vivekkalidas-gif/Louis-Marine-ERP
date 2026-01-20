<?php

namespace App\Http\Controllers\Admin\Accounts;

use App\Http\Controllers\Controller;
use App\Models\FinanceCategory;
use Illuminate\Http\Request;

class FinanceCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = FinanceCategory::latest()->get();
        return view('admin.accounts.categories.index', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
            'color' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        FinanceCategory::create($request->all());

        return back()->with('success', 'Category Created Successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FinanceCategory $category)
    {
        // System categories restricted edit? Let's allow name edit but maybe warn user.
        
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
            'color' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $category->update($request->all());

        return back()->with('success', 'Category Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FinanceCategory $category)
    {
        if ($category->is_system) {
            return back()->with('error', 'Cannot delete system default categories.');
        }

        if ($category->incomes()->exists() || $category->expenses()->exists()) {
            return back()->with('error', 'Cannot delete category with existing transactions.');
        }

        $category->delete();

        return back()->with('success', 'Category Deleted Successfully');
    }
}
