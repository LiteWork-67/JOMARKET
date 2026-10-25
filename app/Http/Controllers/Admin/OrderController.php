<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ParentOrder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = ParentOrder::with(['user', 'sellerOrders.seller']);

        if ($search = $request->input('search')) {
            $query->where('order_number', 'like', "%{$search}%");
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(int|string $id): View
    {
        $order = ParentOrder::with([
            'user',
            'sellerOrders.seller',
            'sellerOrders.items.product',
            'sellerOrders.items.variant',
            'sellerOrders.statusHistories.changedByUser',
            'paymentTransactions',
            'refunds',
        ])->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }
}
