@extends('layouts.admin')

@section('title', 'سجل الحسابات المالي العام — JordanMart')
@section('page_title', 'سجل الحسابات المالي العام')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-black text-stone-900">دفتر الأستاذ المالي الموحد (Financial Ledger)</h2>
            <p class="text-xs text-stone-500 mt-0.5">سجل قيود غير قابل للتعديل يوثق كافة حركات الدائن والمدين لأرباح المتاجر، السحوبات، والتسويات المالية بالأردن.</p>
        </div>
    </div>

    <!-- Totals Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-stone-200/80 shadow-xs">
            <span class="text-xs font-semibold text-stone-500">إجمالي القيود الدائنة (مستحقات أرباح)</span>
            <div class="text-2xl font-black text-emerald-600 mt-1">
                +{{ number_format($totalCredits, 3) }} <span class="text-xs font-bold text-stone-600">د.أ</span>
            </div>
            <p class="text-[11px] text-stone-400 mt-1">Credits المضافة لأرصدة البائعين</p>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-stone-200/80 shadow-xs">
            <span class="text-xs font-semibold text-stone-500">إجمالي القيود المدينة (سحوبات ومصروفات)</span>
            <div class="text-2xl font-black text-red-600 mt-1">
                -{{ number_format($totalDebits, 3) }} <span class="text-xs font-bold text-stone-600">د.أ</span>
            </div>
            <p class="text-[11px] text-stone-400 mt-1">Debits المحولة أو المستردة</p>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-stone-200/80 shadow-xs">
            <span class="text-xs font-semibold text-stone-500">صافي التزامات المنصة الحالية للتجار</span>
            <div class="text-2xl font-black text-stone-900 mt-1">
                {{ number_format($totalCredits - $totalDebits, 3) }} <span class="text-xs font-bold text-stone-600">د.أ</span>
            </div>
            <p class="text-[11px] text-stone-400 mt-1">إجمالي أرصدة التجار المستحقة غير المسحوبة</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl p-4 border border-stone-200/80 shadow-xs">
        <form method="GET" action="{{ route('admin.finance.ledger') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <select name="seller_id" class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-red-500 focus:ring-red-500">
                    <option value="">كافة المتاجر</option>
                    @foreach($sellers as $sel)
                        <option value="{{ $sel->id }}" {{ request('seller_id') == $sel->id ? 'selected' : '' }}>
                            {{ $sel->store_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="entry_type" class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-red-500 focus:ring-red-500">
                    <option value="">نوع القيد (دائن / مدين)</option>
                    <option value="credit" {{ request('entry_type') === 'credit' ? 'selected' : '' }}>دائن (Credit - إضافة)</option>
                    <option value="debit" {{ request('entry_type') === 'debit' ? 'selected' : '' }}>مدين (Debit - خصم / تحويل)</option>
                </select>
            </div>
            <div>
                <input type="text" name="type" value="{{ request('type') }}" placeholder="نوع الحركة (مثال: order_earning)..." class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-red-500 focus:ring-red-500">
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2 bg-stone-900 hover:bg-stone-800 text-white font-bold text-xs rounded-xl transition-colors">
                    تصفية القيود
                </button>
                @if(request()->hasAny(['seller_id', 'entry_type', 'type']))
                    <a href="{{ route('admin.finance.ledger') }}" class="px-3 py-2 bg-stone-100 hover:bg-stone-200 text-stone-700 font-bold text-xs rounded-xl transition-colors">
                        إعادة ضبط
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Ledger Table -->
    <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-start text-xs">
                <thead class="bg-stone-50 text-stone-600 border-b border-stone-200">
                    <tr>
                        <th class="py-3 px-4 font-bold text-start">رقم القيد</th>
                        <th class="py-3 px-4 font-bold text-start">المتجر / الحساب</th>
                        <th class="py-3 px-4 font-bold text-start">نوع الحركة</th>
                        <th class="py-3 px-4 font-bold text-start">القيد</th>
                        <th class="py-3 px-4 font-bold text-start">المبلغ (JOD)</th>
                        <th class="py-3 px-4 font-bold text-start">الرصيد بعد القيد</th>
                        <th class="py-3 px-4 font-bold text-start">البيان والوصف</th>
                        <th class="py-3 px-4 font-bold text-start">التاريخ والمنشئ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-stone-700">
                    @forelse($entries as $entry)
                        <tr class="hover:bg-stone-50/60 font-mono text-[11px]">
                            <td class="py-3 px-4 font-bold text-stone-900">#{{ $entry->id }}</td>
                            <td class="py-3 px-4 font-sans font-bold text-stone-800">{{ $entry->seller?->store_name }}</td>
                            <td class="py-3 px-4 font-sans text-stone-600">{{ $entry->type }}</td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold font-sans {{ $entry->entry_type === 'credit' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
                                    {{ $entry->entry_type === 'credit' ? 'دائن (Credit)' : 'مدين (Debit)' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-black {{ $entry->entry_type === 'credit' ? 'text-emerald-700' : 'text-red-700' }}">
                                {{ $entry->entry_type === 'credit' ? '+' : '-' }}{{ number_format($entry->amount, 3) }} د.أ
                            </td>
                            <td class="py-3 px-4 font-bold text-stone-900">{{ number_format($entry->balance_after, 3) }} د.أ</td>
                            <td class="py-3 px-4 font-sans text-stone-600 max-w-xs truncate">{{ $entry->description }}</td>
                            <td class="py-3 px-4 text-stone-400 font-sans text-[10px]">{{ $entry->created_at->format('Y-m-d H:i') }} ({{ $entry->createdByUser?->name ?? 'System' }})</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-stone-400 font-sans">لا توجد قيود مالية مسجلة بالدفتر مطابقة للشروط.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($entries->hasPages())
            <div class="p-4 border-t border-stone-100">
                {{ $entries->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
