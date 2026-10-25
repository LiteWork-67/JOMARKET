@extends('layouts.storefront')

@section('title', 'حالة طلب الانضمام كبائع — JordanMart')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 py-16 text-center">

    <div class="bg-white rounded-3xl border border-stone-200/80 shadow-xs p-8 sm:p-10 space-y-6">
        
        @if($seller->status === 'pending')
            <div class="w-16 h-16 rounded-full bg-amber-500/10 text-amber-600 flex items-center justify-center mx-auto text-2xl">
                ⏳
            </div>
            <h1 class="text-xl font-black text-stone-900">طلبك قيد المراجعة والتدقيق</h1>
            <p class="text-xs text-stone-600 max-w-md mx-auto leading-relaxed">
                شكراً لاهتمامك بالانضمام إلى شبكة تجار <span class="font-bold text-stone-900">جوردن مارت</span>. طلبك للمتجر <span class="font-bold text-amber-700">"{{ $seller->store_name }}"</span> قيد التدقيق القانوني والتجاري من قبل فريق إدارة العمليات.
            </p>
            <div class="p-4 bg-stone-50 rounded-2xl border border-stone-200 text-xs text-start space-y-1.5">
                <p><span class="font-bold text-stone-700">رقم السجل التجاري:</span> <span class="font-mono text-stone-900">{{ $seller->commercial_registration_no }}</span></p>
                <p><span class="font-bold text-stone-700">البريد الإلكتروني للعمليات:</span> {{ $seller->contact_email }}</p>
                <p><span class="font-bold text-stone-700">الموقع:</span> {{ $seller->governorate }}، {{ $seller->city }}</p>
                <p><span class="font-bold text-stone-700">تاريخ التقديم:</span> {{ $seller->created_at->format('Y-m-d H:i') }}</p>
            </div>
        @elseif($seller->status === 'rejected')
            <div class="w-16 h-16 rounded-full bg-red-500/10 text-red-600 flex items-center justify-center mx-auto text-2xl">
                ✕
            </div>
            <h1 class="text-xl font-black text-stone-900">نعتذر، لم يتم قبول الطلب</h1>
            <p class="text-xs text-stone-600 max-w-md mx-auto leading-relaxed">
                تمت مراجعة بيانات المتجر ولم تستوفِ معايير التسجيل الحالية على المنصة.
            </p>
            @if($seller->rejection_reason)
                <div class="p-4 bg-red-50 rounded-2xl border border-red-200 text-xs text-red-800 font-bold text-start">
                    سبب الرفض: {{ $seller->rejection_reason }}
                </div>
            @endif
        @else
            <div class="w-16 h-16 rounded-full bg-emerald-500/10 text-emerald-600 flex items-center justify-center mx-auto text-2xl">
                ✓
            </div>
            <h1 class="text-xl font-black text-stone-900">حساب البائع مفعل ومعتمد</h1>
            <p class="text-xs text-stone-600">يمكنك الدخول إلى لوحة تحكم متجرك والبدء بإضافة عروضك ومتابعة الطلبات.</p>
            <div class="pt-2">
                <a href="{{ route('seller.dashboard') }}" class="inline-block px-6 py-2.5 bg-amber-500 hover:bg-amber-400 text-stone-950 font-bold text-xs rounded-xl shadow-xs transition-colors">
                    الانتقال للوحة تحكم التاجر ←
                </a>
            </div>
        @endif

        <div class="pt-4 border-t border-stone-100">
            <a href="{{ route('storefront.home') }}" class="text-xs font-bold text-stone-500 hover:text-stone-800">
                العودة للصفحة الرئيسية
            </a>
        </div>
    </div>

</div>
@endsection
