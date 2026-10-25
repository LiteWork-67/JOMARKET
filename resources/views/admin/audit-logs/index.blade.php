@extends('layouts.admin')

@section('title', 'سجل الرقابة والتدقيق المركزي — JordanMart')
@section('page_title', 'سجل الرقابة والتدقيق المركزي')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-black text-stone-900">سجل الأحداث والعمليات الإدارية (Audit Trail)</h2>
            <p class="text-xs text-stone-500 mt-0.5">تسجيل فوري وغير قابل للتعديل لكافة الإجراءات الحساسة والتغييرات التي يجريها المسؤولون والتجار بالنظام.</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl p-4 border border-stone-200/80 shadow-xs">
        <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="flex items-center gap-3">
            <input type="text" name="action" value="{{ request('action') }}" placeholder="ابحث بنوع الإجراء (مثال: admin.seller.approve)..." class="text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-red-500 focus:ring-red-500 w-72">

            <button type="submit" class="px-4 py-2 bg-stone-900 hover:bg-stone-800 text-white font-bold text-xs rounded-xl transition-colors">
                تصفية
            </button>
            @if(request()->filled('action'))
                <a href="{{ route('admin.audit-logs.index') }}" class="px-3 py-2 bg-stone-100 hover:bg-stone-200 text-stone-700 font-bold text-xs rounded-xl transition-colors">
                    إعادة ضبط
                </a>
            @endif
        </form>
    </div>

    <!-- Audit Logs Table -->
    <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-start text-xs">
                <thead class="bg-stone-50 text-stone-600 border-b border-stone-200">
                    <tr>
                        <th class="py-3 px-4 font-bold text-start">رقم السجل</th>
                        <th class="py-3 px-4 font-bold text-start">المستخدم المسؤول</th>
                        <th class="py-3 px-4 font-bold text-start">نوع الإجراء (Action)</th>
                        <th class="py-3 px-4 font-bold text-start">الكيان المتأثر</th>
                        <th class="py-3 px-4 font-bold text-start">عنوان IP</th>
                        <th class="py-3 px-4 font-bold text-start">التاريخ والوقت</th>
                        <th class="py-3 px-4 font-bold text-start">التغييرات المسجلة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-stone-700">
                    @forelse($logs as $log)
                        <tr class="hover:bg-stone-50/60 font-mono text-[11px]">
                            <td class="py-3 px-4 font-bold text-stone-900">#{{ $log->id }}</td>
                            <td class="py-3 px-4 font-sans font-bold text-stone-800">{{ $log->user?->name ?? 'زائر / ضيف' }}</td>
                            <td class="py-3 px-4 font-semibold text-amber-700">{{ $log->action }}</td>
                            <td class="py-3 px-4 text-stone-500">{{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}</td>
                            <td class="py-3 px-4 text-stone-500">{{ $log->ip_address }}</td>
                            <td class="py-3 px-4 text-stone-500 font-sans text-[10px]">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                            <td class="py-3 px-4 max-w-xs truncate text-[10px] text-stone-600 font-sans" title="{{ json_encode(['old' => $log->old_values, 'new' => $log->new_values], JSON_UNESCAPED_UNICODE) }}">
                                @if($log->new_values)
                                    <span class="text-stone-800">{{ json_encode($log->new_values, JSON_UNESCAPED_UNICODE) }}</span>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-stone-400 font-sans">لا توجد سجلات رقابية مطابقة.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="p-4 border-t border-stone-100">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
