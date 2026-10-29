<div class="py-8 bg-stone-50 min-h-[80vh]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumb & Title -->
        <div class="mb-6">
            <nav class="flex items-center gap-2 text-xs text-stone-500 mb-2">
                <a href="{{ route('storefront.home') }}" class="hover:text-stone-900 transition-colors">{{ __('messages.app_name') }}</a>
                <span>/</span>
                <a href="{{ route('cart.index') }}" class="hover:text-stone-900 transition-colors">{{ __('messages.shopping_cart') }}</a>
                <span>/</span>
                <span class="text-stone-900 font-semibold">{{ __('messages.proceed_to_checkout') }}</span>
            </nav>
            <h1 class="text-2xl font-black text-stone-900 tracking-tight">
                {{ __('messages.proceed_to_checkout') }}
            </h1>
        </div>

        @if($errorMessage)
            <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs font-semibold flex items-center gap-3 shadow-xs">
                <svg class="w-5 h-5 text-red-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <span>{{ $errorMessage }}</span>
            </div>
        @endif

        <form wire:submit.prevent="placeOrder">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Main Checkout Form (2/3) -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Section 1: Contact & Delivery Address -->
                    <div class="bg-white rounded-2xl border border-stone-200 p-6 shadow-xs space-y-5">
                        <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-stone-900 text-white flex items-center justify-center text-xs font-bold">1</span>
                                <h2 class="text-sm font-black text-stone-900">{{ __('messages.delivery_address') }}</h2>
                            </div>

                            @auth
                                @if($savedAddresses->isNotEmpty())
                                    <button type="button" wire:click="useNewAddress" class="text-xs text-red-600 hover:text-red-700 font-bold">
                                        + {{ __('messages.add_new_address') }}
                                    </button>
                                @endif
                            @endauth
                        </div>

                        <!-- Saved Addresses Picker (for logged-in customers) -->
                        @auth
                            @if($savedAddresses->isNotEmpty())
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                                    @foreach($savedAddresses as $addr)
                                        <div wire:click="selectSavedAddress({{ $addr->id }})"
                                            class="cursor-pointer p-3.5 rounded-xl border text-xs transition-all {{ $selectedAddressId === $addr->id ? 'border-stone-900 bg-stone-50 ring-2 ring-stone-900/10' : 'border-stone-200 hover:border-stone-300' }}">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="font-bold text-stone-900">{{ $addr->contact_name }}</span>
                                                @if($addr->is_default)
                                                    <span class="text-[9px] bg-stone-200 text-stone-700 px-1.5 py-0.5 rounded font-bold">افتراضي</span>
                                                @endif
                                            </div>
                                            <p class="text-stone-600 line-clamp-2">{{ $addr->formatted_address }}</p>
                                            <p class="text-stone-400 font-mono mt-1 text-[11px]">{{ $addr->phone }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @endauth

                        <!-- Recipient & Address Inputs -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div>
                                <label class="block font-bold text-stone-700 mb-1">{{ __('messages.full_name') }} *</label>
                                <input type="text" wire:model="contactName" class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 focus:border-stone-900 focus:ring-1 focus:ring-stone-900 @error('contactName') border-red-500 @enderror">
                                @error('contactName') <span class="text-[10px] text-red-600 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block font-bold text-stone-700 mb-1">{{ __('messages.phone_number') }} (أردني) *</label>
                                <input type="text" wire:model="phone" placeholder="0791234567" class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 focus:border-stone-900 focus:ring-1 focus:ring-stone-900 @error('phone') border-red-500 @enderror">
                                @error('phone') <span class="text-[10px] text-red-600 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            @guest
                                <div class="sm:col-span-2">
                                    <label class="block font-bold text-stone-700 mb-1">البريد الإلكتروني (لتتبع الطلب والفاتورة) *</label>
                                    <input type="email" wire:model="email" placeholder="name@example.com" class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 focus:border-stone-900 focus:ring-1 focus:ring-stone-900 @error('email') border-red-500 @enderror">
                                    @error('email') <span class="text-[10px] text-red-600 mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            @endguest

                            <div>
                                <label class="block font-bold text-stone-700 mb-1">{{ __('messages.governorate') }} *</label>
                                <select wire:model.live="governorate" class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 focus:border-stone-900 focus:ring-1 focus:ring-stone-900">
                                    @foreach($governorates as $key => $name)
                                        <option value="{{ $key }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block font-bold text-stone-700 mb-1">{{ __('messages.city') }} / المنطقة *</label>
                                <input type="text" wire:model="city" placeholder="مثال: الجبيهة، عبدون، صويلح..." class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 focus:border-stone-900 focus:ring-1 focus:ring-stone-900 @error('city') border-red-500 @enderror">
                                @error('city') <span class="text-[10px] text-red-600 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block font-bold text-stone-700 mb-1">{{ __('messages.street_address') }} *</label>
                                <input type="text" wire:model="streetAddress" placeholder="اسم الشارع أو المعلم الرئيسي" class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 focus:border-stone-900 focus:ring-1 focus:ring-stone-900 @error('streetAddress') border-red-500 @enderror">
                                @error('streetAddress') <span class="text-[10px] text-red-600 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block font-bold text-stone-700 mb-1">{{ __('messages.building_number') }}</label>
                                <input type="text" wire:model="buildingNumber" placeholder="رقم العمارة أو الفيلا" class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5">
                            </div>

                            <div>
                                <label class="block font-bold text-stone-700 mb-1">{{ __('messages.apartment') }} / الطابق</label>
                                <input type="text" wire:model="apartment" placeholder="مثال: شقة 4، طابق 2" class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5">
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block font-bold text-stone-700 mb-1">{{ __('messages.delivery_instructions') }}</label>
                                <input type="text" wire:model="deliveryInstructions" placeholder="مثال: الاتصال قبل الوصول بنصف ساعة، بجانب صيدلية..." class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5">
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Payment Method Selection -->
                    <div class="bg-white rounded-2xl border border-stone-200 p-6 shadow-xs space-y-5">
                        <div class="flex items-center gap-2 border-b border-stone-100 pb-3">
                            <span class="w-6 h-6 rounded-full bg-stone-900 text-white flex items-center justify-center text-xs font-bold">2</span>
                            <h2 class="text-sm font-black text-stone-900">{{ __('messages.payment_method') }}</h2>
                        </div>

                        <div class="space-y-3">
                            <!-- Option 1: Cash on Delivery (COD) -->
                            <label class="block p-4 rounded-2xl border cursor-pointer transition-all {{ $paymentMethod === 'cash_on_delivery' ? 'border-amber-500 bg-amber-500/5 ring-2 ring-amber-500/20' : 'border-stone-200 hover:border-stone-300' }}">
                                <div class="flex items-start gap-3">
                                    <input type="radio" wire:model.live="paymentMethod" value="cash_on_delivery" class="mt-1 text-amber-500 focus:ring-amber-400">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-base">💵</span>
                                            <span class="text-xs font-black text-stone-900">{{ __('messages.payment_cod') }}</span>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">الأكثر طلباً في الأردن</span>
                                        </div>
                                        <p class="text-[11px] text-stone-500 mt-1 leading-relaxed">{{ __('messages.payment_cod_desc') }}</p>
                                    </div>
                                </div>
                            </label>

                            <!-- Option 2: CliQ Instant Payment -->
                            <label class="block p-4 rounded-2xl border cursor-pointer transition-all {{ $paymentMethod === 'cliq' ? 'border-emerald-600 bg-emerald-50/50 ring-2 ring-emerald-600/20' : 'border-stone-200 hover:border-stone-300' }}">
                                <div class="flex items-start gap-3">
                                    <input type="radio" wire:model.live="paymentMethod" value="cliq" class="mt-1 text-emerald-600 focus:ring-emerald-500">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-base">⚡</span>
                                            <span class="text-xs font-black text-stone-900">{{ __('messages.payment_cliq') }}</span>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">فوري ومجاني</span>
                                        </div>
                                        <p class="text-[11px] text-stone-500 mt-1 leading-relaxed">{{ __('messages.payment_cliq_desc') }}</p>

                                        @if($paymentMethod === 'cliq')
                                            <div class="mt-3 p-3 rounded-xl bg-white border border-emerald-200 space-y-2">
                                                <p class="text-[11px] text-emerald-800 font-bold flex items-center gap-1.5">
                                                    <span>{{ __('messages.cliq_alias_label') }}:</span>
                                                    <span class="font-mono bg-emerald-100 px-2 py-0.5 rounded text-emerald-900 font-black">JORDANMART</span>
                                                </p>
                                                <p class="text-[10px] text-stone-500 leading-normal">{{ __('messages.cliq_instructions') }}</p>
                                                <div class="pt-2">
                                                    <label class="block text-[11px] font-semibold text-stone-700 mb-1">اسمك المستعار في كليك (أو رقم هاتفك للتحقق):</label>
                                                    <input type="text" wire:model="cliqAlias" placeholder="مثال: AhmadCliQ أو 079XXXXXXX" class="w-full text-xs rounded-lg border border-stone-300 px-3 py-1.5 focus:border-stone-900">
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Section 3: Notes -->
                    <div class="bg-white rounded-2xl border border-stone-200 p-6 shadow-xs space-y-3">
                        <label class="block text-xs font-bold text-stone-700">{{ __('messages.order_notes') }}</label>
                        <textarea wire:model="customerNotes" rows="2" placeholder="أي تفاصيل خاصة بتوقيت التوصيل أو تغليف الشحنة..." class="w-full text-xs rounded-xl border border-stone-300 p-3 focus:border-stone-900"></textarea>
                    </div>

                </div>

                <!-- Order Review Sidebar (1/3) -->
                <div class="space-y-6">
                    <div class="bg-white rounded-2xl border border-stone-200 p-6 shadow-xs space-y-5 sticky top-20">
                        <h2 class="text-sm font-black text-stone-900 border-b border-stone-100 pb-3 flex items-center justify-between">
                            <span>{{ __('messages.order_summary') }}</span>
                            <span class="text-xs font-semibold text-stone-500">{{ $cart->items->sum('quantity') }} قطع</span>
                        </h2>

                        <!-- Items Thumbnails Summary -->
                        <div class="space-y-3 max-h-48 overflow-y-auto pr-1">
                            @foreach($cart->items as $item)
                                <div class="flex items-center justify-between gap-3 text-xs">
                                    <div class="flex items-center gap-2.5 flex-1 min-w-0">
                                        <div class="w-10 h-10 rounded-lg bg-stone-100 border border-stone-200 shrink-0 overflow-hidden flex items-center justify-center">
                                            @if($item->offer->product->primary_image_url)
                                                <img src="{{ $item->offer->product->primary_image_url }}" alt="" class="w-full h-full object-cover">
                                            @else
                                                <span class="text-[9px] font-black text-stone-400">JM</span>
                                            @endif
                                        </div>
                                        <div class="truncate">
                                            <p class="font-bold text-stone-900 truncate">
                                                {{ app()->getLocale() === 'ar' ? $item->offer->product->title_ar : $item->offer->product->title_en }}
                                            </p>
                                            <span class="text-[10px] text-stone-500">الكمية: {{ $item->quantity }}</span>
                                        </div>
                                    </div>
                                    <span class="font-bold text-stone-900 shrink-0">
                                        {{ number_format((float) $item->unit_price * $item->quantity, 3) }} {{ __('messages.currency') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        <!-- Price Breakdown -->
                        <div class="pt-4 border-t border-stone-100 space-y-2.5 text-xs">
                            <div class="flex justify-between text-stone-600">
                                <span>{{ __('messages.subtotal') }}</span>
                                <span class="font-bold text-stone-900">{{ number_format($totals['subtotal'], 3) }} {{ __('messages.currency') }}</span>
                            </div>

                            <div class="flex justify-between text-stone-600">
                                <span>{{ __('messages.shipping_fee') }} ({{ $governorates[$governorate] ?? $governorate }})</span>
                                @if($totals['shipping'] == 0.000)
                                    <span class="font-bold text-emerald-600">مجاناً</span>
                                @else
                                    <span class="font-bold text-stone-900">{{ number_format($totals['shipping'], 3) }} {{ __('messages.currency') }}</span>
                                @endif
                            </div>

                            @if($totals['discount'] > 0)
                                <div class="flex justify-between text-emerald-600 font-semibold">
                                    <span>{{ __('messages.discount') }} ({{ $appliedCoupon->code }})</span>
                                    <span>-{{ number_format($totals['discount'], 3) }} {{ __('messages.currency') }}</span>
                                </div>
                            @endif

                            <div class="pt-3 border-t border-stone-200 flex justify-between items-baseline">
                                <span class="text-sm font-black text-stone-900">{{ __('messages.total') }}</span>
                                <div class="text-end">
                                    <span class="text-xl font-black text-stone-950">{{ number_format($totals['total'], 3) }}</span>
                                    <span class="text-xs font-bold text-stone-600">{{ __('messages.currency') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Order Button -->
                        <button type="submit"
                            wire:loading.attr="disabled"
                            @if($isSubmitting) disabled @endif
                            class="w-full py-4 px-4 rounded-xl bg-amber-500 hover:bg-amber-600 disabled:opacity-50 text-stone-950 font-black text-sm shadow-sm hover:shadow transition-all flex items-center justify-center gap-2">
                            <span wire:loading.remove>{{ __('messages.place_order') }}</span>
                            <span wire:loading class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-stone-950" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                <span>جاري تأكيد الطلب...</span>
                            </span>
                        </button>

                        <p class="text-[10px] text-stone-400 text-center leading-normal">
                            بالضغط على "تأكيد وإتمام الطلب"، فإنك توافق على شروط وأحكام منصة جوردن مارت وسياسة الخصوصية.
                        </p>
                    </div>
                </div>

            </div>
        </form>

    </div>
</div>
