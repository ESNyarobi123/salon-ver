<x-manager-layout>
    <x-slot name="header">
        {{ config('salon.manager_stock_page_title') }}
    </x-slot>

    <div class="mb-8">
        <h2 class="text-3xl font-bold text-white tracking-tight">{{ config('salon.manager_stock_page_title') }}</h2>
        <p class="text-sm font-medium text-white/40 uppercase tracking-wider">{{ config('salon.manager_stock_page_subtitle') }}</p>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl">
            <p class="text-sm font-medium text-emerald-400">{{ session('success') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 bg-rose-500/10 border border-rose-500/20 rounded-xl">
            <p class="text-sm font-medium text-rose-400">{{ $errors->first() }}</p>
        </div>
    @endif

    @if($lowStockItems->isNotEmpty())
        <div class="glass-card p-6 rounded-2xl border border-amber-500/30 mb-8">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-amber-500/20 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-amber-400">
                        <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" x2="12" y1="9" y2="13"/><line x1="12" x2="12.01" y1="17" y2="17"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white">Low stock</h3>
                    <p class="text-xs text-white/50">These items are at or below your alert level. Restock or adjust quantities.</p>
                </div>
            </div>
            <ul class="space-y-2">
                @foreach($lowStockItems as $item)
                    <li class="flex items-center justify-between py-2 px-3 rounded-lg bg-white/5 text-sm">
                        <span class="text-white font-medium">{{ $item->name }}</span>
                        <span class="text-amber-400 font-mono">{{ $item->stock_quantity }} ≤ {{ $item->low_stock_threshold }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="glass-card p-6 rounded-2xl mb-6">
        <p class="text-sm text-white/60">
            <span class="text-white font-semibold">{{ $trackedCount }}</span> service / product line(s) have stock tracking on.
            Turn tracking on only for items you sell from physical stock (retail products, kits, etc.). Bookings that use stock will reduce counts automatically.
        </p>
    </div>

    <div class="space-y-4">
        @forelse($menuItems as $item)
            <div class="glass-card p-5 rounded-2xl {{ $item->stock_tracked && $item->isLowStock() ? 'ring-1 ring-amber-500/40' : '' }}">
                <div class="flex flex-col lg:flex-row lg:items-end gap-4">
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-white/35">{{ $item->category?->name ?? '—' }}</p>
                        <h3 class="text-lg font-bold text-white truncate">{{ $item->name }}</h3>
                        <p class="text-xs text-white/40 mt-1">Tsh {{ number_format($item->price) }}</p>
                    </div>
                    <form action="{{ route('manager.stock.update', $item) }}" method="POST" class="flex flex-wrap items-end gap-4 flex-1 lg:justify-end">
                        @csrf
                        @method('PUT')
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="stock_tracked" value="1" class="rounded border-white/20 bg-white/5 text-violet-500 focus:ring-violet-500" {{ old('stock_tracked', $item->stock_tracked) ? 'checked' : '' }}>
                            <span class="text-xs font-medium text-white/70">Track stock</span>
                        </label>
                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 block mb-1">On hand</label>
                            <input type="number" name="stock_quantity" min="0" value="{{ old('stock_quantity', $item->stock_quantity) }}" class="w-24 px-3 py-2 bg-white/5 border border-white/10 rounded-xl text-white text-sm font-mono focus:ring-2 focus:ring-violet-500">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 block mb-1">Alert at or below</label>
                            <input type="number" name="low_stock_threshold" min="0" value="{{ old('low_stock_threshold', $item->low_stock_threshold) }}" class="w-24 px-3 py-2 bg-white/5 border border-white/10 rounded-xl text-white text-sm font-mono focus:ring-2 focus:ring-violet-500">
                        </div>
                        <button type="submit" class="bg-gradient-to-r from-violet-600 to-cyan-600 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:shadow-lg hover:shadow-violet-500/25 transition-all">
                            Save
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-white/50 text-sm">No menu items yet. Add services under Service catalog first.</p>
        @endforelse
    </div>
</x-manager-layout>
