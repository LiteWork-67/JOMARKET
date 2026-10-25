@extends('layouts.admin')

@section('title', 'إدارة التجار والشركاء — JordanMart')
@section('page_title', 'إدارة التجار والشركاء')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-black text-stone-900">سجل التجار والمتاجر الشريكة</h2>
            <p class="text-xs text-stone-500 mt-0.5">اعتماد طلبات الانضمام، مراجعة السجلات التجارية، وتحديد نسب العمولة وإيقاف أو تفعيل الحسابات.</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl p-4 border border-stone-200/80 shadow-xs">
        <form method="GET" action="{{ route('admin.sellers.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث باسم المتجر، البريد، أو السجل التجاري..." class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-red-500 focus:ring-red-500">
            </div>
            <div>
                <select name="status" class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-red-500 focus:ring-red-500">
                    <option value="">كافة الحالات</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>بانتظار الاعتماد (Pending)</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>معتمد ومفعل (Approved)</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>موقوف (Suspended)</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>مرفوض (Rejected)</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2 bg-stone-900 hover:bg-stone-800 text-white font-bold text-xs rounded-xl transition-colors">
                    تصفية
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('admin.sellers.index') }}" class="px-3 py-2 bg-stone-100 hover:bg-stone-200 text-stone-700 font-bold text-xs rounded-xl transition-colors">
                        إعادة ضبط
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Sellers Table -->
    <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-start text-xs">
                <thead class="bg-stone-50 text-stone-600 border-b border-stone-200">
                    <tr>
                        <th class="py-3 px-4 font-bold text-start">اسم المتجر</th>
                        <th class="py-3 px-4 font-bold text-start">المالك / الاتصال</th>
                        <th class="py-3 px-4 font-bold text-start">السجل التجاري</th>
                        <th class="py-3 px-4 font-bold text-start">نسبة العمولة</th>
                        <th class="py-3 px-4 font-bold text-start">الموقع</th>
                        <th class="py-3 px-4 font-bold text-start">الحالة</th>
                        <th class="py-3 px-4 font-bold text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-stone-700">
                    @forelse($sellers as $seller)
                        <tr class="hover:bg-stone-50/60">
                            <td class="py-3 px-4">
                                <a href="{{ route('admin.sellers.show', $seller->id) }}" class="font-bold text-stone-900 hover:text-red-600">
                                    {{ $seller->store_name }}
                                </a>
                                <span class="block text-[10px] text-stone-400 font-mono">{{ $seller->slug }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-semibold text-stone-800">{{ $seller->user?->name }}</span>
                                <span class="block text-[10px] text-stone-500">{{ $seller->contact_email }}</span>
                            </td>
                            <td class="py-3 px-4 font-mono font-medium text-stone-800">{{ $seller->commercial_registration_no ?: '—' }}</td>
                            <td class="py-3 px-4 font-black text-stone-900">{{ $seller->commission_rate }}%</td>
                            <td class="py-3 px-4 text-stone-600">{{ $seller->governorate ?? 'الأردن' }}</td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold
                                    @if($seller->status === 'approved') bg-emerald-50 text-emerald-700 border border-emerald-200
                                    @elseif($seller->status === 'pending') bg-amber-50 text-amber-700 border border-amber-200
                                    @elseif($seller->status === 'suspended') bg-red-50 text-red-700 border border-red-200
                                    @elseif($seller->status === 'rejected') bg-stone-100 text-stone-600 border border-stone-200
                                    @endif">
                                    {{ $seller->status }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('admin.sellers.show', $seller->id) }}" class="px-2.5 py-1 bg-stone-100 hover:bg-stone-200 text-stone-800 font-bold rounded-lg text-xs transition-colors">
                                    إدارة
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-stone-400">لا يوجد تجار مطابقين لشروط التصفية.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($sellers->hasPages())
            <div class="p-4 border-t border-stone-100">
                {{ $sellers->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
