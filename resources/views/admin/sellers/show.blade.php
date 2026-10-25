@extends('layouts.admin')

@section('title', 'ملف التاجر: ' . $seller->store_name . ' — JordanMart')
@section('page_title', 'ملف التاجر وإدارة الاعتماد')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-xl font-black text-stone-900">{{ $seller->store_name }}</h2>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold
                    @if($seller->status === 'approved') bg-emerald-50 text-emerald-700 border border-emerald-200
                    @elseif($seller->status === 'pending') bg-amber-50 text-amber-700 border border-amber-200
                    @elseif($seller->status === 'suspended') bg-red-50 text-red-700 border border-red-200
                    @elseif($seller->status === 'rejected') bg-stone-100 text-stone-600 border border-stone-200
                    @endif">
                    {{ $seller->status }}
                </span>
            </div>
            <p class="text-xs text-stone-500 mt-1">المعرف: #{{ $seller->id }} • تاريخ التسجيل: {{ $seller->created_at->format('Y-m-d H:i') }}</p>
        </div>
        <a href="{{ route('admin.sellers.index') }}" class="text-xs font-bold text-stone-600 hover:text-stone-900">
            ← العودة لقائمة التجار
        </a>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-stone-200/80 shadow-xs">
            <span class="text-xs font-semibold text-stone-500">إجمالي الطلبات المستلمة</span>
            <div class="text-2xl font-black text-stone-900 mt-1">{{ $ordersCount }}</div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-stone-200/80 shadow-xs">
            <span class="text-xs font-semibold text-stone-500">المبيعات المكتملة</span>
            <div class="text-2xl font-black text-stone-900 mt-1">{{ number_format($totalSales, 3) }} <span class="text-xs font-bold text-stone-600">د.أ</span></div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-stone-200/80 shadow-xs">
            <span class="text-xs font-semibold text-stone-500">الرصيد الدفتري المسوى</span>
            <div class="text-2xl font-black text-emerald-600 mt-1">{{ number_format($settledBalance, 3) }} <span class="text-xs font-bold text-stone-600">د.أ</span></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2 Cols: Details -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Store & Legal Information -->
            <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs p-6 space-y-4">
                <h3 class="text-xs font-black text-stone-900 uppercase tracking-wider border-b border-stone-100 pb-2">البيانات التجارية والقانونية</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-stone-400">الاسم بالعربية:</span>
                        <p class="font-bold text-stone-900 mt-0.5">{{ $seller->store_name_ar }}</p>
                    </div>
                    <div>
                        <span class="text-stone-400">الاسم بالإنجليزية:</span>
                        <p class="font-bold text-stone-900 mt-0.5">{{ $seller->store_name_en }}</p>
                    </div>
                    <div>
                        <span class="text-stone-400">رقم السجل التجاري:</span>
                        <p class="font-bold font-mono text-stone-900 mt-0.5">{{ $seller->commercial_registration_no ?: '—' }}</p>
                    </div>
                    <div>
                        <span class="text-stone-400">الرقم الضريبي:</span>
                        <p class="font-bold font-mono text-stone-900 mt-0.5">{{ $seller->tax_number ?: '—' }}</p>
                    </div>
                    <div>
                        <span class="text-stone-400">المستخدم المسؤول:</span>
                        <p class="font-bold text-stone-900 mt-0.5">{{ $seller->user?->name }} ({{ $seller->user?->email }})</p>
                    </div>
                    <div>
                        <span class="text-stone-400">هاتف الاتصال:</span>
                        <p class="font-bold text-stone-900 mt-0.5" dir="ltr">{{ $seller->contact_phone }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <span class="text-stone-400">العنوان في الأردن:</span>
                        <p class="font-bold text-stone-900 mt-0.5">{{ $seller->governorate }}، {{ $seller->city }} — {{ $seller->address_details }}</p>
                    </div>
                    @if($seller->description_ar)
                        <div class="sm:col-span-2">
                            <span class="text-stone-400">الوصف:</span>
                            <p class="text-stone-700 mt-0.5 leading-relaxed">{{ $seller->description_ar }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Bank Information -->
            <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs p-6 space-y-3">
                <h3 class="text-xs font-black text-stone-900 uppercase tracking-wider border-b border-stone-100 pb-2">بيانات التحويل البنكي الأردني</h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                    <div>
                        <span class="text-stone-400">اسم البنك:</span>
                        <p class="font-bold text-stone-900 mt-0.5">{{ $seller->bank_name ?: '—' }}</p>
                    </div>
                    <div>
                        <span class="text-stone-400">الآيبان IBAN:</span>
                        <p class="font-mono font-bold text-stone-900 mt-0.5" dir="ltr">{{ $seller->iban ?: '—' }}</p>
                    </div>
                    <div>
                        <span class="text-stone-400">صاحب الحساب:</span>
                        <p class="font-bold text-stone-900 mt-0.5">{{ $seller->account_holder_name ?: '—' }}</p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Col: Administrative Management Controls -->
        <div class="space-y-6">

            <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs p-6 space-y-5">
                <h3 class="text-xs font-black text-stone-900 uppercase tracking-wider border-b border-stone-100 pb-2">إجراءات الاعتماد والرقابة</h3>

                <!-- Approve / Commission form -->
                <form method="POST" action="{{ route('admin.sellers.manage', $seller->id) }}" class="space-y-3">
                    @csrf
                    <input type="hidden" name="action" value="approve">

                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-1">نسبة العمولة المقتطعة (%)</label>
                        <input type="number" step="0.1" name="commission_rate" value="{{ old('commission_rate', $seller->commission_rate) }}" min="0" max="100" class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-red-500 focus:ring-red-500">
                    </div>

                    <button type="submit" class="w-full py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-xs transition-colors">
                        ✓ اعتماد وتفعيل حساب التاجر
                    </button>
                </form>

                <!-- Reject form -->
                <form method="POST" action="{{ route('admin.sellers.manage', $seller->id) }}" class="space-y-3 pt-3 border-t border-stone-100">
                    @csrf
                    <input type="hidden" name="action" value="reject">

                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-1">سبب الرفض (في حال الرفض)</label>
                        <textarea name="rejection_reason" rows="2" placeholder="أدخل سبب عدم مطابقة الشروط..." class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-red-500 focus:ring-red-500"></textarea>
                    </div>

                    <button type="submit" onclick="return confirm('هل أنت متأكد من رفض طلب التاجر؟');" class="w-full py-2 bg-stone-100 hover:bg-stone-200 text-stone-800 font-bold text-xs rounded-xl transition-colors">
                        ✕ رفض طلب الانضمام
                    </button>
                </form>

                <!-- Suspend button if approved -->
                @if($seller->status === 'approved')
                    <form method="POST" action="{{ route('admin.sellers.manage', $seller->id) }}" class="pt-3 border-t border-stone-100">
                        @csrf
                        <input type="hidden" name="action" value="suspend">
                        <button type="submit" onclick="return confirm('تحذير: إيقاف المتجر سيخفي جميع عروضه من السوق. هل تريد الاستمرار؟');" class="w-full py-2 bg-red-50 hover:bg-red-100 text-red-700 font-bold text-xs rounded-xl border border-red-200 transition-colors">
                            ⚠️ تجميد حساب المتجر مؤقتاً
                        </button>
                    </form>
                @endif
            </div>

        </div>

    </div>

</div>
@endsection
