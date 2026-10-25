@extends('layouts.admin')

@section('title', 'مراجعة واعتماد المنتجات — JordanMart')
@section('page_title', 'مراجعة واعتماد المنتجات')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-black text-stone-900">كتالوج المنتجات والاعتماد المركزي</h2>
            <p class="text-xs text-stone-500 mt-0.5">مراجعة المنتجات المقترحة من التجار والموافقة على إدراجها بالكتالوج العام للمنصة.</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl p-4 border border-stone-200/80 shadow-xs">
        <form method="GET" action="{{ route('admin.products.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث باسم المنتج أو الباركود..." class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-red-500 focus:ring-red-500">
            </div>
            <div>
                <select name="status" class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-red-500 focus:ring-red-500">
                    <option value="">كافة الحالات</option>
                    <option value="pending_approval" {{ request('status') === 'pending_approval' ? 'selected' : '' }}>بانتظار المراجعة (Pending Approval)</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>معتمد ونشط (Approved)</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>مرفوض (Rejected)</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2 bg-stone-900 hover:bg-stone-800 text-white font-bold text-xs rounded-xl transition-colors">
                    تصفية
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('admin.products.index') }}" class="px-3 py-2 bg-stone-100 hover:bg-stone-200 text-stone-700 font-bold text-xs rounded-xl transition-colors">
                        إعادة ضبط
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-start text-xs">
                <thead class="bg-stone-50 text-stone-600 border-b border-stone-200">
                    <tr>
                        <th class="py-3 px-4 font-bold text-start">المنتج</th>
                        <th class="py-3 px-4 font-bold text-start">التصنيف</th>
                        <th class="py-3 px-4 font-bold text-start">العلامة التجارية</th>
                        <th class="py-3 px-4 font-bold text-start">الباركود</th>
                        <th class="py-3 px-4 font-bold text-start">عروض التجار</th>
                        <th class="py-3 px-4 font-bold text-start">الحالة</th>
                        <th class="py-3 px-4 font-bold text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-stone-700">
                    @forelse($products as $prod)
                        <tr class="hover:bg-stone-50/60">
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-3">
                                    @if($prod->main_image_url)
                                        <img src="{{ $prod->main_image_url }}" alt="" class="w-10 h-10 rounded-lg object-cover border border-stone-200 shrink-0">
                                    @endif
                                    <div class="min-w-0">
                                        <a href="{{ route('admin.products.show', $prod->id) }}" class="font-bold text-stone-900 hover:text-red-600 line-clamp-1">
                                            {{ $prod->name }}
                                        </a>
                                        <span class="block text-[10px] text-stone-400 font-mono">{{ $prod->slug }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4 font-medium">{{ $prod->category?->name }}</td>
                            <td class="py-3 px-4 font-medium">{{ $prod->brand?->name }}</td>
                            <td class="py-3 px-4 font-mono text-stone-600">{{ $prod->barcode ?: '—' }}</td>
                            <td class="py-3 px-4 font-bold text-stone-900">{{ $prod->offers->count() }} عرض</td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold
                                    @if($prod->status === 'approved') bg-emerald-50 text-emerald-700 border border-emerald-200
                                    @elseif($prod->status === 'pending') bg-amber-50 text-amber-700 border border-amber-200
                                    @elseif($prod->status === 'rejected') bg-red-50 text-red-700 border border-red-200
                                    @endif">
                                    {{ $prod->status }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('admin.products.show', $prod->id) }}" class="px-2.5 py-1 bg-stone-100 hover:bg-stone-200 text-stone-800 font-bold rounded-lg text-xs transition-colors">
                                    تدقيق ومراجعة
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-stone-400">لا توجد منتجات مسجلة تطابق التصفية.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($products->hasPages())
            <div class="p-4 border-t border-stone-100">
                {{ $products->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
