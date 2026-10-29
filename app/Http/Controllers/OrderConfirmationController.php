<?php

namespace App\Http\Controllers;

use App\Models\ParentOrder;
use Illuminate\View\View;

class OrderConfirmationController extends Controller
{
    public function show(string $orderNumber): View
    {
        $order = ParentOrder::with([
            'sellerOrders.seller',
            'sellerOrders.items.product',
            'paymentTransactions',
        ])->where('order_number', $orderNumber)->firstOrFail();

        return view('orders.confirmation', [
            'order' => $order,
        ]);
    }
}
