<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use Illuminate\Http\Request;

class EnquiryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'enquirable_type' => 'required|string',
            'enquirable_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string|max:1000'
        ]);

        $enquiry = Enquiry::create([
            'user_id' => auth()->id(),
            'enquirable_type' => $validated['enquirable_type'],
            'enquirable_id' => $validated['enquirable_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'message' => $validated['message'],
            'status' => 'new'
        ]);

        return back()->with('success', 'Your enquiry has been submitted successfully!');
    }

    public function myEnquiries()
    {
        $enquiries = Enquiry::where('user_id', auth()->id())
            ->with('enquirable')
            ->latest()
            ->paginate(20);

        return view('enquiries.index', compact('enquiries'));
    }
}
