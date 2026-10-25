<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\StoreSellerOfferRequest;
use App\Http\Requests\Seller\UpdateSellerOfferRequest;
use App\Models\Product;
use App\Models\SellerOffer;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class OfferController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', SellerOffer::class);

        $seller = $request->get('current_seller') ?? $request->user()->seller;

        $query = SellerOffer::where('seller_id', $seller->id)
            ->with(['product.category', 'product.brand', 'variant']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('seller_sku', 'like', "%{$search}%")
                    ->orWhereHas('product', function ($pq) use ($search) {
                        $pq->where('name_ar', 'like', "%{$search}%")
                            ->orWhere('name_en', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        $offers = $query->latest()->paginate(15)->withQueryString();

        return view('seller.offers.index', compact('offers', 'seller'));
    }

    public function create(Request $request): View
    {
        Gate::authorize('create', SellerOffer::class);

        $seller = $request->get('current_seller') ?? $request->user()->seller;
        $products = Product::where('status', 'approved')->where('is_active', true)->orderBy('name_ar')->get();

        return view('seller.offers.create', compact('products', 'seller'));
    }

    public function store(StoreSellerOfferRequest $request): RedirectResponse
    {
        Gate::authorize('create', SellerOffer::class);

        $seller = $request->get('current_seller') ?? $request->user()->seller;

        $validated = $request->validated();
        $validated['seller_id'] = $seller->id;
        $validated['currency'] = 'JOD';
        $validated['is_active'] = $request->boolean('is_active', true);

        $offer = SellerOffer::create($validated);

        AuditService::log('seller.offer.create', $offer, null, $offer->toArray());

        return redirect()->route('seller.offers.index')
            ->with('success', __('Offer published successfully.'));
    }

    public function edit(Request $request, int|string $id): View
    {
        $seller = $request->get('current_seller') ?? $request->user()->seller;
        $offer = SellerOffer::where('seller_id', $seller->id)->findOrFail($id);

        Gate::authorize('update', $offer);

        return view('seller.offers.edit', compact('offer', 'seller'));
    }

    public function update(UpdateSellerOfferRequest $request, int|string $id): RedirectResponse
    {
        $seller = $request->get('current_seller') ?? $request->user()->seller;
        $offer = SellerOffer::where('seller_id', $seller->id)->findOrFail($id);

        Gate::authorize('update', $offer);

        $oldValues = $offer->toArray();
        $validated = $request->validated();
        $validated['is_active'] = $request->boolean('is_active', true);

        $offer->update($validated);

        AuditService::log('seller.offer.update', $offer, $oldValues, $offer->toArray());

        return redirect()->route('seller.offers.index')
            ->with('success', __('Offer updated successfully.'));
    }

    public function destroy(Request $request, int|string $id): RedirectResponse
    {
        $seller = $request->get('current_seller') ?? $request->user()->seller;
        $offer = SellerOffer::where('seller_id', $seller->id)->findOrFail($id);

        Gate::authorize('delete', $offer);

        $oldValues = $offer->toArray();
        $offer->update(['is_active' => false]);

        AuditService::log('seller.offer.deactivate', $offer, $oldValues, ['is_active' => false]);

        return redirect()->route('seller.offers.index')
            ->with('success', __('Offer deactivated successfully.'));
    }
}
