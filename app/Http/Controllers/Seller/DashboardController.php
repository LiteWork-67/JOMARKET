<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $seller = $request->get('current_seller') ?? $request->user()->seller;

        $totalSales = (float) $seller->orders()->where('status', 'delivered')->sum('total_amount');
        $pendingOrders = $seller->orders()->whereIn('status', ['pending_confirmation', 'confirmed', 'processing'])->count();
        $shippedOrders = $seller->orders()->where('status', 'shipped')->count();
        $deliveredOrders = $seller->orders()->where('status', 'delivered')->count();
        $totalOrders = $seller->orders()->count();
        $commissionDeducted = (float) $seller->orders()->where('status', 'delivered')->sum('commission_amount');
        $settledBalance = $seller->getSettledBalance();
        $activeOffers = $seller->offers()->where('is_active', true)->count();
        $lowStockOffers = $seller->offers()->where('is_active', true)->where('stock_quantity', '<=', 5)->count();

        $recentOrders = $seller->orders()
            ->with(['parentOrder.user', 'items.product'])
            ->latest()
            ->take(8)
            ->get();

        $recentDelivered = $seller->orders()
            ->where('status', 'delivered')
            ->where('created_at', '>=', now()->subMonths(6))
            ->get(['id', 'total_amount', 'created_at']);

        $monthlySales = $recentDelivered->groupBy(function ($order) {
            return $order->created_at->format('Y-m');
        })->map(function ($group, $period) {
            return (object) [
                'period' => $period,
                'total' => (float) $group->sum('total_amount'),
                'count' => $group->count(),
            ];
        })->values();

        return view('seller.dashboard', compact(
            'seller',
            'totalSales',
            'pendingOrders',
            'shippedOrders',
            'deliveredOrders',
            'totalOrders',
            'commissionDeducted',
            'settledBalance',
            'activeOffers',
            'lowStockOffers',
            'recentOrders',
            'monthlySales'
        ));
    }
}
