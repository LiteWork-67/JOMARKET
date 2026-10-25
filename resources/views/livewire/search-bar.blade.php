<div class="relative w-full" x-data @click.outside="$wire.hideDropdown()">
    <form wire:submit.prevent="submitSearch" class="flex items-center w-full rounded-xl border border-stone-300 focus-within:border-amber-500 focus-within:ring-2 focus-within:ring-amber-500/20 bg-stone-50 transition-all overflow-hidden">
        
        <!-- Category Filter inside Search Input -->
        <select wire:model.live="selectedCategory" class="hidden sm:block text-xs font-semibold bg-stone-100 text-stone-700 py-2.5 px-3 border-{{ ($isRtl ?? (app()->getLocale() === 'ar')) ? 'l' : 'r' }} border-stone-200 outline-none focus:bg-white cursor-pointer transition-colors max-w-[140px] truncate">
            <option value="">{{ __('messages.all_categories') }}</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->slug }}">{{ $cat->name }}</option>
            @endforeach
        </select>

        <!-- Live Search Input -->
        <div class="relative flex-1">
            <input
                type="text"
                wire:model.live.debounce.250ms="query"
                placeholder="{{ __('messages.search_placeholder') }}"
                class="w-full py-2.5 px-3.5 text-xs text-stone-900 bg-transparent placeholder:text-stone-400 outline-none focus:outline-none"
                autocomplete="off"
            />
            @if(!empty($query))
                <button type="button" wire:click="$set('query', '')" class="absolute {{ ($isRtl ?? (app()->getLocale() === 'ar')) ? 'left-2.5' : 'right-2.5' }} top-2.5 text-stone-400 hover:text-stone-600 text-xs">
                    ✕
                </button>
            @endif
        </div>

        <!-- Submit Button -->
        <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-stone-950 p-2.5 px-4 font-bold shrink-0 transition-colors flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </button>
    </form>

    <!-- Autocomplete Suggestions Dropdown -->
    @if($showDropdown && !empty($suggestions))
        <div class="absolute top-full left-0 right-0 mt-1.5 bg-white rounded-xl shadow-2xl border border-stone-200 overflow-hidden z-50 divide-y divide-stone-100 text-xs animate-in fade-in duration-100">
            @foreach($suggestions as $item)
                <a href="{{ $item['url'] }}" class="flex items-center gap-3 p-3 hover:bg-amber-50/50 transition-colors group">
                    @if(!empty($item['image']))
                        <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="w-10 h-10 object-cover rounded-lg border border-stone-100 shrink-0">
                    @else
                        <div class="w-10 h-10 bg-stone-100 rounded-lg flex items-center justify-center text-stone-400 shrink-0">📦</div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-stone-900 truncate group-hover:text-amber-600">{{ $item['title'] }}</p>
                        @if(!empty($item['category']))
                            <p class="text-[10px] text-stone-500">{{ $item['category'] }}</p>
                        @endif
                    </div>
                    @if(!empty($item['price']))
                        <div class="font-bold text-stone-900 shrink-0">
                            {{ $item['price'] }} <span class="text-[10px] text-amber-600">{{ __('messages.currency') }}</span>
                        </div>
                    @endif
                </a>
            @endforeach
            <div class="p-2 bg-stone-50 text-center">
                <button type="button" wire:click="submitSearch" class="text-[11px] font-bold text-amber-600 hover:text-amber-700">
                    عرض كافة النتائج لـ "{{ $query }}" →
                </button>
            </div>
        </div>
    @endif
</div>
