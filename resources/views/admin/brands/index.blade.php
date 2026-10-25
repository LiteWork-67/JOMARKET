@extends('layouts.admin')

@section('title', 'إدارة العلامات التجارية — JordanMart')
@section('page_title', 'إدارة العلامات التجارية والوكالات')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-black text-stone-900">العلامات التجارية المعتمدة بالسوق</h2>
            <p class="text-xs text-stone-500 mt-0.5">إدارة الماركات، تحديد العلامات المميزة، ومتابعة المنتجات التابعة لكل براند.</p>
        </div>
        <a href="{{ route('admin.brands.create') }}" class="px-4 py-2 bg-red-600 hover:bg-red-500 text-white font-bold text-xs rounded-xl shadow-xs transition-colors flex items-center justify-center gap-1.5 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>إضافة علامة تجارية</span>
        </a>
    </div>

    <!-- Brands Table -->
    <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-start text-xs">
                <thead class="bg-stone-50 text-stone-600 border-b border-stone-200">
                    <tr>
                        <th class="py-3 px-4 font-bold text-start">الاسم بالعربية</th>
                        <th class="py-3 px-4 font-bold text-start">الاسم بالإنجليزية</th>
                        <th class="py-3 px-4 font-bold text-start">الرابط اللطيف (Slug)</th>
                        <th class="py-3 px-4 font-bold text-start">المنتجات التابعة</th>
                        <th class="py-3 px-4 font-bold text-start">مميز بالرئيسية</th>
                        <th class="py-3 px-4 font-bold text-start">الحالة</th>
                        <th class="py-3 px-4 font-bold text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-stone-700">
                    @forelse($brands as $brand)
                        <tr class="hover:bg-stone-50/60">
                            <td class="py-3 px-4 font-bold text-stone-900">{{ $brand->name_ar }}</td>
                            <td class="py-3 px-4 font-medium text-stone-600" dir="ltr">{{ $brand->name_en }}</td>
                            <td class="py-3 px-4 font-mono text-[11px] text-stone-500">{{ $brand->slug }}</td>
                            <td class="py-3 px-4 font-bold">{{ $brand->products_count }} منتج</td>
                            <td class="py-3 px-4">
                                <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold {{ $brand->is_featured ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'text-stone-400' }}">
                                    {{ $brand->is_featured ? '★ مميز' : 'عادي' }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold {{ $brand->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-stone-100 text-stone-600' }}">
                                    {{ $brand->is_active ? 'نشط' : 'معطل' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.brands.edit', $brand->id) }}" class="px-2.5 py-1 bg-stone-100 hover:bg-stone-200 text-stone-800 font-bold rounded-lg text-xs transition-colors">
                                        تعديل
                                    </a>
                                    <form method="POST" action="{{ route('admin.brands.destroy', $brand->id) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذه العلامة التجارية؟');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 bg-red-50 hover:bg-red-100 text-red-700 font-bold rounded-lg text-xs transition-colors">
                                            حذف
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-stone-400">لا توجد علامات تجارية مسجلة.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($brands->hasPages())
            <div class="p-4 border-t border-stone-100">
                {{ $brands->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
