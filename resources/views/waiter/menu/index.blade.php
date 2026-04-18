<x-waiter-layout>
    <x-slot name="header">
        {{ config('salon.services') }} catalog
    </x-slot>

    <div class="mb-8">
        <div class="flex gap-3 overflow-x-auto pb-3 hide-scrollbar">
            <button class="px-5 py-2.5 bg-gradient-to-r from-violet-600 to-cyan-600 text-white rounded-xl font-semibold shadow-lg shadow-violet-500/20">{{ config('salon.all_service_items') }}</button>
            @foreach($categories as $category)
                <button class="px-5 py-2.5 glass text-white/60 rounded-xl font-semibold hover:text-white hover:bg-white/10 transition-all whitespace-nowrap">{{ $category->name }}</button>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        @foreach($menuItems as $item)
            <div class="glass-card rounded-2xl overflow-hidden card-hover group">
                <div class="relative h-48 bg-white/5 overflow-hidden">
                    @if($item->image)
                        <img src="{{ asset('storage/' . $item->image) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-white/20">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                            </svg>
                        </div>
                    @endif
                    
                    <div class="absolute top-3 right-3 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $item->is_available ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/20 text-rose-400 border border-rose-500/20' }}">
                        {{ $item->is_available ? 'Available' : 'Sold Out' }}
                    </div>

                    <div class="absolute bottom-3 left-3 px-2.5 py-1 bg-white/10 backdrop-blur-md rounded-full text-[10px] font-bold text-white uppercase tracking-wider">
                        {{ $item->category->name }}
                    </div>
                </div>

                <div class="p-5">
                    <div class="flex justify-between items-start mb-2">
                        <h4 class="text-lg font-bold text-white leading-tight">{{ $item->name }}</h4>
                        <span class="font-bold text-violet-400 whitespace-nowrap ml-2">Tsh {{ number_format($item->price) }}</span>
                    </div>
                    
                    <p class="text-sm text-white/40 mb-4 line-clamp-2">{{ $item->description }}</p>
                    
                    <div class="flex items-center justify-between pt-4 border-t border-white/5">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 glass rounded-lg flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white/40">
                                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                                </svg>
                            </div>
                            <span class="text-[11px] font-semibold text-white/40 uppercase tracking-wider">{{ $item->preparation_time ?? '15' }} min</span>
                        </div>
                        
                        @if($item->is_available)
                            <div class="flex items-center gap-1.5 text-emerald-400">
                                <div class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></div>
                                <span class="text-[10px] font-bold uppercase tracking-wider">In Stock</span>
                            </div>
                        @else
                            <div class="flex items-center gap-1.5 text-rose-400">
                                <div class="w-1.5 h-1.5 bg-rose-400 rounded-full"></div>
                                <span class="text-[10px] font-bold uppercase tracking-wider">Out of Stock</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</x-waiter-layout>
