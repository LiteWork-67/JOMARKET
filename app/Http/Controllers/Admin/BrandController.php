<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBrandRequest;
use App\Http\Requests\Admin\UpdateBrandRequest;
use App\Models\Brand;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BrandController extends Controller
{
    public function index(Request $request): View
    {
        $query = Brand::withCount('products');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name_ar', 'like', "%{$search}%")
                    ->orWhere('name_en', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $brands = $query->orderBy('name_ar')->paginate(20)->withQueryString();

        return view('admin.brands.index', compact('brands'));
    }

    public function create(): View
    {
        return view('admin.brands.create');
    }

    public function store(StoreBrandRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['is_featured'] = $request->boolean('is_featured', false);
        $validated['is_active'] = $request->boolean('is_active', true);

        $brand = Brand::create($validated);

        AuditService::log('admin.brand.create', $brand, null, $brand->toArray());

        return redirect()->route('admin.brands.index')
            ->with('success', __('Brand created successfully.'));
    }

    public function edit(Brand $brand): View
    {
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(UpdateBrandRequest $request, Brand $brand): RedirectResponse
    {
        $oldValues = $brand->toArray();
        $validated = $request->validated();
        $validated['is_featured'] = $request->boolean('is_featured', false);
        $validated['is_active'] = $request->boolean('is_active', true);

        $brand->update($validated);

        AuditService::log('admin.brand.update', $brand, $oldValues, $brand->toArray());

        return redirect()->route('admin.brands.index')
            ->with('success', __('Brand updated successfully.'));
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        if ($brand->products()->exists()) {
            return back()->with('error', __('Cannot delete brand with associated products. Deactivate it instead.'));
        }

        $oldValues = $brand->toArray();
        $brand->delete();

        AuditService::log('admin.brand.delete', null, $oldValues, null);

        return redirect()->route('admin.brands.index')
            ->with('success', __('Brand deleted successfully.'));
    }
}
