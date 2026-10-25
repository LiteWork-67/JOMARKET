<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ModerateProductRequest;
use App\Models\Product;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ProductModerationController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Product::class);

        $query = Product::with(['category', 'brand', 'offers']);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name_ar', 'like', "%{$search}%")
                    ->orWhere('name_en', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        $products = $query->latest()->paginate(15)->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    public function show(int|string $id): View
    {
        $product = Product::with(['category', 'brand', 'images', 'offers.seller'])->findOrFail($id);

        Gate::authorize('view', $product);

        return view('admin.products.show', compact('product'));
    }

    public function moderate(ModerateProductRequest $request, int|string $id): RedirectResponse
    {
        $product = Product::findOrFail($id);

        Gate::authorize('moderate', $product);

        $action = $request->input('action');
        $oldValues = $product->toArray();

        if ($action === 'approve') {
            $product->update([
                'status' => 'approved',
                'is_active' => true,
                'rejection_reason' => null,
            ]);
        } else {
            $product->update([
                'status' => 'rejected',
                'is_active' => false,
                'rejection_reason' => $request->input('rejection_reason'),
            ]);
        }

        AuditService::log("admin.product.{$action}", $product, $oldValues, $product->toArray());

        return back()->with('success', __('Product has been :actiond.', ['action' => $action]));
    }
}
