<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ManageSellerRequest;
use App\Models\Role;
use App\Models\Seller;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SellerManagementController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Seller::class);

        $query = Seller::with('user');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('store_name_ar', 'like', "%{$search}%")
                    ->orWhere('store_name_en', 'like', "%{$search}%")
                    ->orWhere('contact_email', 'like', "%{$search}%")
                    ->orWhere('commercial_registration_no', 'like', "%{$search}%");
            });
        }

        $sellers = $query->latest()->paginate(15)->withQueryString();

        return view('admin.sellers.index', compact('sellers'));
    }

    public function show(int|string $id): View
    {
        $seller = Seller::with(['user', 'documents', 'offers.product'])->findOrFail($id);

        Gate::authorize('view', $seller);

        $ordersCount = $seller->orders()->count();
        $totalSales = (float) $seller->orders()->where('status', 'delivered')->sum('total_amount');
        $settledBalance = $seller->getSettledBalance();

        return view('admin.sellers.show', compact('seller', 'ordersCount', 'totalSales', 'settledBalance'));
    }

    public function manage(ManageSellerRequest $request, int|string $id): RedirectResponse
    {
        $seller = Seller::with('user')->findOrFail($id);

        Gate::authorize('manage', $seller);

        $action = $request->input('action');
        $oldValues = $seller->toArray();

        switch ($action) {
            case 'approve':
                $seller->status = 'approved';
                $seller->verified_at = now();
                $seller->rejection_reason = null;
                if ($request->filled('commission_rate')) {
                    $seller->commission_rate = (float) $request->input('commission_rate');
                }
                // Ensure the user has the seller role attached
                $sellerRole = Role::where('slug', 'seller')->first();
                if ($sellerRole && ! $seller->user->roles->contains('id', $sellerRole->id)) {
                    $seller->user->roles()->syncWithoutDetaching([$sellerRole->id]);
                }
                break;

            case 'reject':
                $seller->status = 'rejected';
                $seller->rejection_reason = $request->input('rejection_reason');
                break;

            case 'suspend':
                $seller->status = 'suspended';
                break;

            case 'reactivate':
                $seller->status = 'approved';
                $seller->rejection_reason = null;
                break;
        }

        $seller->save();

        AuditService::log("admin.seller.{$action}", $seller, $oldValues, $seller->toArray());

        return back()->with('success', __('Seller status successfully updated to :status.', ['status' => $seller->status]));
    }
}
