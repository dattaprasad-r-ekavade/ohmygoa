<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessListing;
use App\Models\Classified;
use App\Models\Product;
use App\Models\ServiceExpert;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContentApprovalController extends Controller
{
    /**
     * Display pending content for approval.
     */
    public function index(Request $request): View
    {
        $type = $request->get('type', 'all');

        $pendingListings = $type === 'all' || $type === 'listings'
            ? BusinessListing::with(['user', 'category', 'location'])
                ->where('status', 'pending')
                ->latest()
                ->get()
            : collect();

        $pendingProducts = $type === 'all' || $type === 'products'
            ? Product::with(['user', 'category'])
                ->where('status', 'pending')
                ->latest()
                ->get()
            : collect();

        $pendingClassifieds = $type === 'all' || $type === 'classifieds'
            ? Classified::with(['user', 'category', 'location'])
                ->where('status', 'pending')
                ->latest()
                ->get()
            : collect();

        $pendingServiceExperts = $type === 'all' || $type === 'service_experts'
            ? ServiceExpert::with(['user', 'category', 'location'])
                ->where('status', 'pending')
                ->latest()
                ->get()
            : collect();

        return view('admin.approvals.index', compact(
            'pendingListings',
            'pendingProducts',
            'pendingClassifieds',
            'pendingServiceExperts',
            'type'
        ));
    }

    /**
     * Approve a business listing.
     */
    public function approveListing(BusinessListing $listing)
    {
        $listing->update(['status' => 'active']);

        // TODO: Send notification to user

        return redirect()->back()
            ->with('success', 'Business listing approved successfully.');
    }

    /**
     * Reject a business listing.
     */
    public function rejectListing(Request $request, BusinessListing $listing)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $listing->update([
            'status' => 'rejected',
            'rejection_reason' => $request->reason,
        ]);

        // TODO: Send notification to user with reason

        return redirect()->back()
            ->with('success', 'Business listing rejected.');
    }

    /**
     * Approve a product.
     */
    public function approveProduct(Product $product)
    {
        $product->update(['status' => 'active']);

        // TODO: Send notification to user

        return redirect()->back()
            ->with('success', 'Product approved successfully.');
    }

    /**
     * Reject a product.
     */
    public function rejectProduct(Request $request, Product $product)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $product->update([
            'status' => 'rejected',
            'rejection_reason' => $request->reason,
        ]);

        // TODO: Send notification to user with reason

        return redirect()->back()
            ->with('success', 'Product rejected.');
    }

    /**
     * Approve a classified ad.
     */
    public function approveClassified(Classified $classified)
    {
        $classified->update(['status' => 'active']);

        // TODO: Send notification to user

        return redirect()->back()
            ->with('success', 'Classified ad approved successfully.');
    }

    /**
     * Reject a classified ad.
     */
    public function rejectClassified(Request $request, Classified $classified)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $classified->update([
            'status' => 'rejected',
            'rejection_reason' => $request->reason,
        ]);

        // TODO: Send notification to user with reason

        return redirect()->back()
            ->with('success', 'Classified ad rejected.');
    }

    /**
     * Approve a service expert profile.
     */
    public function approveServiceExpert(ServiceExpert $serviceExpert)
    {
        $serviceExpert->update([
            'status' => 'active',
            'is_verified' => true,
        ]);

        // TODO: Send notification to user

        return redirect()->back()
            ->with('success', 'Service expert profile approved successfully.');
    }

    /**
     * Reject a service expert profile.
     */
    public function rejectServiceExpert(Request $request, ServiceExpert $serviceExpert)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $serviceExpert->update([
            'status' => 'rejected',
            'is_verified' => false,
            'rejection_reason' => $request->reason,
        ]);

        // TODO: Send notification to user with reason

        return redirect()->back()
            ->with('success', 'Service expert profile rejected.');
    }

    /**
     * Bulk approve content.
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'type' => 'required|in:listings,products,classifieds,service_experts',
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $count = 0;

        switch ($request->type) {
            case 'listings':
                $count = BusinessListing::whereIn('id', $request->ids)
                    ->update(['status' => 'active']);
                break;
            case 'products':
                $count = Product::whereIn('id', $request->ids)
                    ->update(['status' => 'active']);
                break;
            case 'classifieds':
                $count = Classified::whereIn('id', $request->ids)
                    ->update(['status' => 'active']);
                break;
            case 'service_experts':
                $count = ServiceExpert::whereIn('id', $request->ids)
                    ->update(['status' => 'active', 'is_verified' => true]);
                break;
        }

        return redirect()->back()
            ->with('success', "{$count} items approved successfully.");
    }

    /**
     * Bulk reject content.
     */
    public function bulkReject(Request $request)
    {
        $request->validate([
            'type' => 'required|in:listings,products,classifieds,service_experts',
            'ids' => 'required|array',
            'ids.*' => 'integer',
            'reason' => 'nullable|string|max:500',
        ]);

        $count = 0;
        $updateData = [
            'status' => 'rejected',
            'rejection_reason' => $request->reason,
        ];

        switch ($request->type) {
            case 'listings':
                $count = BusinessListing::whereIn('id', $request->ids)
                    ->update($updateData);
                break;
            case 'products':
                $count = Product::whereIn('id', $request->ids)
                    ->update($updateData);
                break;
            case 'classifieds':
                $count = Classified::whereIn('id', $request->ids)
                    ->update($updateData);
                break;
            case 'service_experts':
                $updateData['is_verified'] = false;
                $count = ServiceExpert::whereIn('id', $request->ids)
                    ->update($updateData);
                break;
        }

        return redirect()->back()
            ->with('success', "{$count} items rejected.");
    }
}
