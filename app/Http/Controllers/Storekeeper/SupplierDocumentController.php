<?php

namespace App\Http\Controllers\Storekeeper;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\SupplierDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SupplierDocumentController extends Controller
{
    public function store(Request $request, Supplier $supplier)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240', // 10MB Max
            'expiry_date' => 'nullable|date',
            'remarks' => 'nullable|string'
        ]);

        if ($request->hasFile('document')) {
            $path = $request->file('document')->store('supplier-documents', 'public');

            $supplier->documents()->create([
                'title' => $request->title,
                'file_path' => $path,
                'expiry_date' => $request->expiry_date,
                'remarks' => $request->remarks,
                'is_compliant' => true
            ]);

            return back()->with('success', 'Document uploaded successfully.');
        }

        return back()->with('error', 'File upload failed.');
    }

    public function destroy(SupplierDocument $document)
    {
        // Delete file from storage
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return back()->with('success', 'Document deleted successfully.');
    }

    public function download(SupplierDocument $document)
    {
        if (Storage::disk('public')->exists($document->file_path)) {
            return Storage::disk('public')->download($document->file_path, $document->title . '.' . pathinfo($document->file_path, PATHINFO_EXTENSION));
        }

        return back()->with('error', 'File not found.');
    }
}
