@extends('layouts.admin')

@section('title', 'إدارة التصنيفات والأقسام — JordanMart')
@section('page_title', 'إدارة التصنيفات والأقسام')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-black text-stone-900">هيكلية أقسام وتصنيفات المتجر</h2>
            <p class="text-xs text-stone-500 mt-0.5">تنظيم الأقسام الرئيسية والفرعية، تحديد نسب العمولات الافتراضية لكل تصنيف، وأيقونات العرض.</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="px-4 py-2 bg-red-600 hover:bg-red-500 text-white font-bold text-xs rounded-xl shadow-xs transition-colors flex items-center justify-center gap-1.5 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>إضافة تصنيف جديد</span>
        </a>
    </div>

    <!-- Categories Table -->
    <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-start text-xs">
                <thead class="bg-stone-50 text-stone-600 border-b border-stone-200">
                    <tr>
                        <th class="py-3 px-4 font-bold text-start">الاسم بالعربية</th>
                        <th class="py-3 px-4 font-bold text-start">الاسم بالإنجليزية</th>
                        <th class="py-3 px-4 font-bold text-start">الرابط اللطيف (Slug)</th>
                        <th class="py-3 px-4 font-bold text-start">القسم الرئيسي</th>
                        <th class="py-3 px-4 font-bold text-start">نسبة العمولة</th>
                        <th class="py-3 px-4 font-bold text-start">المنتجات</th>
                        <th class="py-3 px-4 font-bold text-start">الحالة</th>
                        <th class="py-3 px-4 font-bold text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-stone-700">
                    @forelse($categories as $cat)
                        <tr class="hover:bg-stone-50/60">
                            <td class="py-3 px-4 font-bold text-stone-900">{{ $cat->name_ar }}</td>
                            <td class="py-3 px-4 font-medium text-stone-600" dir="ltr">{{ $cat->name_en }}</td>
                            <td class="py-3 px-4 font-mono text-[11px] text-stone-500">{{ $cat->slug }}</td>
                            <td class="py-3 px-4 text-stone-500">{{ $cat->parent?->name_ar ?? 'قسم رئيسي' }}</td>
                            <td class="py-3 px-4 font-bold text-stone-900">{{ $cat->commission_rate ? $cat->commission_rate . '%' : 'افتراضي' }}</td>
                            <td class="py-3 px-4 font-bold">{{ $cat->products_count }} منتج</td>
                            <td class="py-3 px-4">
                                <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold {{ $cat->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-stone-100 text-stone-600' }}">
                                    {{ $cat->is_active ? 'نشط' : 'معطل' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.categories.edit', $cat->id) }}" class="px-2.5 py-1 bg-stone-100 hover:bg-stone-200 text-stone-800 font-bold rounded-lg text-xs transition-colors">
                                        تعديل
                                    </a>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $cat->id) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذا التصنيف؟');" class="inline">
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
                            <td colspan="8" class="py-8 text-center text-stone-400">لا توجد تصنيفات مضافة حالياً.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($categories->hasPages())
            <div class="p-4 border-t border-stone-100">
                {{ $categories->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
