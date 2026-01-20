<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use Illuminate\Http\Request;

class ContactSubmissionController extends Controller
{
    public function index()
    {
        $submissions = ContactSubmission::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.cms.contact.index', compact('submissions'));
    }

    public function show(ContactSubmission $contactSubmission)
    {
        if (!$contactSubmission->read_at) {
            $contactSubmission->update(['read_at' => now(), 'status' => 'In Progress']);
        }
        return view('admin.cms.contact.show', ['submission' => $contactSubmission]);
    }

    public function update(Request $request, ContactSubmission $contactSubmission)
    {
        $request->validate(['status' => 'required|string']);
        $contactSubmission->update(['status' => $request->status]);
        return back()->with('success', 'Status updated successfully.');
    }

    public function destroy(ContactSubmission $contactSubmission)
    {
        $contactSubmission->delete();
        return redirect()->route('admin.cms.contact-submissions.index')->with('success', 'Submission deleted.');
    }
}
