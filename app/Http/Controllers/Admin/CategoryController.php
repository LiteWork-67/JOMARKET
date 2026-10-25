<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $query = Category::with('parent')->withCount('products');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name_ar', 'like', "%{$search}%")
                    ->orWhere('name_en', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $categories = $query->orderBy('sort_order')->paginate(20)->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        $parentCategories = Category::whereNull('parent_id')->orderBy('name_ar')->get();

        return view('admin.categories.create', compact('parentCategories'));
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = (int) $request->input('sort_order', 0);

        $category = Category::create($validated);

        AuditService::log('admin.category.create', $category, null, $category->toArray());

        return redirect()->route('admin.categories.index')
            ->with('success', __('Category created successfully.'));
    }

    public function edit(Category $category): View
    {
        $parentCategories = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->orderBy('name_ar')
            ->get();

        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $oldValues = $category->toArray();
        $validated = $request->validated();
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = (int) $request->input('sort_order', 0);

        $category->update($validated);

        AuditService::log('admin.category.update', $category, $oldValues, $category->toArray());

        return redirect()->route('admin.categories.index')
            ->with('success', __('Category updated successfully.'));
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists() || $category->children()->exists()) {
            return back()->with('error', __('Cannot delete category with associated products or child subcategories. Deactivate it instead.'));
        }

        $oldValues = $category->toArray();
        $category->delete();

        AuditService::log('admin.category.delete', null, $oldValues, null);

        return redirect()->route('admin.categories.index')
            ->with('success', __('Category deleted successfully.'));
    }
}
