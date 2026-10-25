<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ParentOrder;
use App\Models\Product;
use App\Models\Seller;
use App\Models\SellerOrder;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalGMV = (float) SellerOrder::where('status', 'delivered')->sum('total_amount');
        $totalOrders = ParentOrder::count();
        $pendingOrders = ParentOrder::where('status', 'pending')->count();
        $totalCustomers = User::where('role', 'customer')->count();
        $totalSellers = Seller::count();
        $activeSellers = Seller::where('status', 'approved')->count();
        $pendingSellerApprovals = Seller::where('status', 'pending')->count();
        $pendingProducts = Product::where('status', 'pending_approval')->count();
        $totalCommissions = (float) SellerOrder::where('status', 'delivered')->sum('commission_amount');
        $totalProducts = Product::where('is_active', true)->count();

        $recentOrders = ParentOrder::with(['user', 'sellerOrders.seller'])
            ->latest()
            ->take(8)
            ->get();

        $recentDelivered = SellerOrder::where('status', 'delivered')
            ->where('created_at', '>=', now()->subMonths(6))
            ->get(['id', 'total_amount', 'created_at']);

        $monthlyGMV = $recentDelivered->groupBy(function ($order) {
            return $order->created_at->format('Y-m');
        })->map(function ($group, $period) {
            return (object) [
                'period' => $period,
                'total' => (float) $group->sum('total_amount'),
                'count' => $group->count(),
            ];
        })->values();

        $pendingApplications = Seller::where('status', 'pending')
            ->with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalGMV',
            'totalOrders',
            'pendingOrders',
            'totalCustomers',
            'totalSellers',
            'activeSellers',
            'pendingSellerApprovals',
            'pendingProducts',
            'totalCommissions',
            'totalProducts',
            'recentOrders',
            'monthlyGMV',
            'pendingApplications'
        ));
    }
}
