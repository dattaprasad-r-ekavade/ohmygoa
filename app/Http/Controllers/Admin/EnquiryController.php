<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use Illuminate\Http\Request;

class EnquiryController extends Controller
{
    public function index(Request $request)
    {
        $enquiries = Enquiry::query()
            ->when($request->status, function($query) use ($request) {
                $query->byStatus($request->status);
            })
            ->when($request->type, function($query) use ($request) {
                $query->where('enquirable_type', $request->type);
            })
            ->when($request->search, function($query) use ($request) {
                $query->where(function($q) use ($request) {
                    $q->where('name', 'LIKE', "%{$request->search}%")
                      ->orWhere('email', 'LIKE', "%{$request->search}%")
                      ->orWhere('message', 'LIKE', "%{$request->search}%");
                });
            })
            ->with(['user', 'enquirable'])
            ->latest()
            ->paginate(20);

        $newCount = Enquiry::new()->count();
        $todayCount = Enquiry::whereDate('created_at', today())->count();

        return view('admin.enquiries.index', compact('enquiries', 'newCount', 'todayCount'));
    }

    public function show($id)
    {
        $enquiry = Enquiry::with(['user', 'enquirable'])->findOrFail($id);
        
        // Mark as read if new
        $enquiry->markAsRead();

        return view('admin.enquiries.show', compact('enquiry'));
    }

    public function update(Request $request, $id)
    {
        $enquiry = Enquiry::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:new,read,replied,closed',
            'admin_notes' => 'nullable|string'
        ]);

        $updateData = [
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'] ?? null
        ];

        if ($validated['status'] === 'replied' && $enquiry->status !== 'replied') {
            $updateData['replied_at'] = now();
        }

        $enquiry->update($updateData);

        return back()->with('success', 'Enquiry updated successfully!');
    }

    public function markAsReplied($id)
    {
        $enquiry = Enquiry::findOrFail($id);
        $enquiry->markAsReplied();

        return back()->with('success', 'Enquiry marked as replied!');
    }

    public function close($id)
    {
        $enquiry = Enquiry::findOrFail($id);
        $enquiry->close();

        return back()->with('success', 'Enquiry closed!');
    }

    public function destroy($id)
    {
        $enquiry = Enquiry::findOrFail($id);
        $enquiry->delete();

        return redirect()->route('admin.enquiries.index')
            ->with('success', 'Enquiry deleted successfully!');
    }

    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'enquiry_ids' => 'required|array',
            'enquiry_ids.*' => 'exists:enquiries,id',
            'action' => 'required|in:mark_read,mark_replied,close,delete'
        ]);

        $enquiries = Enquiry::whereIn('id', $validated['enquiry_ids'])->get();

        foreach ($enquiries as $enquiry) {
            switch ($validated['action']) {
                case 'mark_read':
                    $enquiry->markAsRead();
                    break;
                case 'mark_replied':
                    $enquiry->markAsReplied();
                    break;
                case 'close':
                    $enquiry->close();
                    break;
                case 'delete':
                    $enquiry->delete();
                    break;
            }
        }

        return back()->with('success', 'Bulk action completed successfully!');
    }

    public function statistics()
    {
        $stats = [
            'total' => Enquiry::count(),
            'new' => Enquiry::new()->count(),
            'read' => Enquiry::read()->count(),
            'replied' => Enquiry::replied()->count(),
            'closed' => Enquiry::closed()->count(),
            'today' => Enquiry::whereDate('created_at', today())->count(),
            'this_week' => Enquiry::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => Enquiry::whereMonth('created_at', now()->month)->count(),
        ];

        // Enquiries by type
        $byType = Enquiry::selectRaw('enquirable_type, COUNT(*) as count')
            ->groupBy('enquirable_type')
            ->get();

        // Daily enquiries (last 30 days)
        $dailyEnquiries = Enquiry::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.enquiries.statistics', compact('stats', 'byType', 'dailyEnquiries'));
    }
}
