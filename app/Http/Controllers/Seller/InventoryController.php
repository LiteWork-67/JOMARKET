<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\UpdateStockRequest;
use App\Models\InventoryMovement;
use App\Models\SellerOffer;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', SellerOffer::class);

        $seller = $request->get('current_seller') ?? $request->user()->seller;

        $query = SellerOffer::where('seller_id', $seller->id)
            ->with(['product', 'variant']);

        if ($request->boolean('low_stock')) {
            $query->where('stock_quantity', '<=', 5);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('seller_sku', 'like', "%{$search}%")
                    ->orWhereHas('product', function ($pq) use ($search) {
                        $pq->where('name_ar', 'like', "%{$search}%")
                            ->orWhere('name_en', 'like', "%{$search}%");
                    });
            });
        }

        $offers = $query->orderBy('stock_quantity')->paginate(15)->withQueryString();

        $movements = InventoryMovement::whereHas('offer', function ($q) use ($seller) {
            $q->where('seller_id', $seller->id);
        })->with(['offer.product', 'createdByUser'])->latest()->take(10)->get();

        return view('seller.inventory.index', compact('offers', 'movements', 'seller'));
    }

    public function updateStock(UpdateStockRequest $request, int|string $id): RedirectResponse
    {
        $seller = $request->get('current_seller') ?? $request->user()->seller;
        $offer = SellerOffer::where('seller_id', $seller->id)->findOrFail($id);

        Gate::authorize('update', $offer);

        $adjustment = (int) $request->input('adjustment');
        $reason = $request->input('reason');
        $oldStock = $offer->stock_quantity;
        $newStock = $oldStock + $adjustment;

        if ($newStock < 0) {
            return back()->withErrors(['adjustment' => __('Stock quantity cannot be reduced below 0.')]);
        }

        DB::transaction(function () use ($offer, $adjustment, $reason, $oldStock, $newStock) {
            $offer->update(['stock_quantity' => $newStock]);

            InventoryMovement::create([
                'seller_offer_id' => $offer->id,
                'type' => $adjustment > 0 ? 'restock' : 'adjustment',
                'quantity' => $adjustment,
                'balance_after' => $newStock,
                'reference_type' => 'manual_adjustment',
                'reference_id' => null,
                'notes' => $reason,
                'created_by' => auth()->id(),
            ]);

            AuditService::log('seller.inventory.adjust', $offer, ['stock_quantity' => $oldStock], ['stock_quantity' => $newStock, 'reason' => $reason]);
        });

        return back()->with('success', __('Stock updated successfully.'));
    }
}
