<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\ReturnItem;
use App\Models\ReturnRequest;
use App\Models\SellerOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReturnRequestController extends Controller
{
    public function index(): View
    {
        $returnRequests = ReturnRequest::where('user_id', Auth::id())
            ->with(['sellerOrder.seller', 'sellerOrder.parentOrder', 'items.orderItem'])
            ->latest()
            ->paginate(10);

        return view('account.returns.index', [
            'returnRequests' => $returnRequests,
        ]);
    }

    public function create(SellerOrder $sellerOrder): View
    {
        // Enforce customer ownership and delivered/completed status
        if ($sellerOrder->parentOrder->user_id !== Auth::id()) {
            abort(403);
        }

        if (! in_array($sellerOrder->status, ['delivered', 'completed'])) {
            abort(400, 'الطلب غير مؤهل لطلب الإرجاع حالياً.');
        }

        $sellerOrder->load(['items.product', 'seller']);

        return view('account.returns.create', [
            'sellerOrder' => $sellerOrder,
        ]);
    }

    public function store(SellerOrder $sellerOrder, Request $request): RedirectResponse
    {
        if ($sellerOrder->parentOrder->user_id !== Auth::id()) {
            abort(403);
        }

        if (! in_array($sellerOrder->status, ['delivered', 'completed'])) {
            return back()->with('error', 'الطلب غير مؤهل لطلب الإرجاع.');
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:1000'],
            'resolution_type' => ['required', 'in:refund,exchange'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.order_item_id' => ['required', 'exists:order_items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.condition_notes' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($sellerOrder, $validated) {
            $returnRequest = ReturnRequest::create([
                'seller_order_id' => $sellerOrder->id,
                'user_id' => Auth::id(),
                'reason' => $validated['reason'],
                'resolution_type' => $validated['resolution_type'],
                'status' => 'pending',
            ]);

            foreach ($validated['items'] as $itemData) {
                ReturnItem::create([
                    'return_request_id' => $returnRequest->id,
                    'order_item_id' => $itemData['order_item_id'],
                    'quantity' => $itemData['quantity'],
                    'condition_notes' => $itemData['condition_notes'] ?? null,
                ]);
            }

            $sellerOrder->update(['status' => 'return_requested']);
        });

        return redirect()->route('account.returns.index')
            ->with('success', 'تم تقديم طلب الإرجاع بنجاح وسيقوم فريق الدعم والتاجر بمراجعته.');
    }

    public function show(ReturnRequest $returnRequest): View
    {
        if ($returnRequest->user_id !== Auth::id()) {
            abort(403);
        }

        $returnRequest->load(['sellerOrder.seller', 'sellerOrder.parentOrder', 'items.orderItem.product']);

        return view('account.returns.show', [
            'returnRequest' => $returnRequest,
        ]);
    }
}
