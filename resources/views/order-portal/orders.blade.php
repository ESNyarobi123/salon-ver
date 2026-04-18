@extends('layouts.order-portal')

@section('content')
@php
    $totalActive = $pendingOrders->count() + $preparingOrders->count() + $servedOrders->count();
@endphp

{{-- Header: title + actions --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-5 sm:mb-6">
    <div>
        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-white tracking-tight">{{ config('salon.live_bookings') }}</h1>
        <p class="text-xs sm:text-sm font-medium text-white/40 uppercase tracking-wider mt-0.5">Pending → {{ config('salon.order_column_in_progress') }} → Served → Paid</p>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
        <span class="px-3 py-2 rounded-xl bg-white/5 border border-white/10 text-xs font-semibold text-white/80">
            <span class="text-white font-bold">{{ $totalActive }}</span> active
        </span>
        <button type="button" onclick="window.location.reload()" class="p-2.5 rounded-xl glass hover:bg-white/10 text-white/70 hover:text-white transition-colors touch-action-manipulation" title="Refresh" aria-label="Refresh">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
        </button>
        <button type="button" onclick="openCreateOrderModal()" class="flex items-center gap-2 bg-gradient-to-r from-violet-600 to-cyan-600 hover:from-violet-500 hover:to-cyan-500 text-white px-4 py-2.5 rounded-xl font-semibold text-sm shadow-lg shadow-violet-500/25 transition-all touch-action-manipulation">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 4v16m8-8H4"/></svg>
            New booking
        </button>
    </div>
</div>

{{-- Grid: 1 col mobile, 2 cols tablet, 4 cols desktop --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-5 lg:gap-6">
    {{-- Pending --}}
    <div class="min-w-0 flex flex-col">
        <div class="glass-card rounded-2xl p-4 md:p-5 flex flex-col min-h-[260px] sm:min-h-[320px] md:min-h-[420px] lg:min-h-[500px]">
            <div class="flex items-center justify-between mb-4 shrink-0">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 bg-rose-500 rounded-full animate-pulse" aria-hidden="true"></span>
                    <h2 class="font-bold text-white uppercase tracking-wider text-xs">Pending</h2>
                </div>
                <span class="bg-rose-500/20 text-rose-400 text-xs font-bold px-2.5 py-1 rounded-full border border-rose-500/20">{{ $pendingOrders->count() }}</span>
            </div>
            <div class="space-y-3 flex-1 overflow-y-auto custom-scrollbar min-h-0">
                @forelse($pendingOrders as $order)
                    @include('order-portal.partials.order-card', ['order' => $order, 'status' => 'pending'])
                @empty
                    <p class="text-sm text-white/30 text-center py-8 md:py-10">No pending orders</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Preparing --}}
    <div class="min-w-0 flex flex-col">
        <div class="glass-card rounded-2xl p-4 md:p-5 flex flex-col min-h-[260px] sm:min-h-[320px] md:min-h-[420px] lg:min-h-[500px]">
            <div class="flex items-center justify-between mb-4 shrink-0">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 bg-amber-500 rounded-full animate-pulse" aria-hidden="true"></span>
                    <h2 class="font-bold text-white uppercase tracking-wider text-xs">{{ config('salon.order_column_in_progress') }}</h2>
                </div>
                <span class="bg-amber-500/20 text-amber-400 text-xs font-bold px-2.5 py-1 rounded-full border border-amber-500/20">{{ $preparingOrders->count() }}</span>
            </div>
            <div class="space-y-3 flex-1 overflow-y-auto custom-scrollbar min-h-0">
                @forelse($preparingOrders as $order)
                    @include('order-portal.partials.order-card', ['order' => $order, 'status' => 'preparing'])
                @empty
                    <p class="text-sm text-white/30 text-center py-8 md:py-10">Nothing on {{ strtolower(config('salon.floor_display_short')) }} yet</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Served --}}
    <div class="min-w-0 flex flex-col">
        <div class="glass-card rounded-2xl p-4 md:p-5 flex flex-col min-h-[260px] sm:min-h-[320px] md:min-h-[420px] lg:min-h-[500px]">
            <div class="flex items-center justify-between mb-4 shrink-0">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-pulse" aria-hidden="true"></span>
                    <h2 class="font-bold text-white uppercase tracking-wider text-xs">Served</h2>
                </div>
                <span class="bg-emerald-500/20 text-emerald-400 text-xs font-bold px-2.5 py-1 rounded-full border border-emerald-500/20">{{ $servedOrders->count() }}</span>
            </div>
            <div class="space-y-3 flex-1 overflow-y-auto custom-scrollbar min-h-0">
                @forelse($servedOrders as $order)
                    @include('order-portal.partials.order-card', ['order' => $order, 'status' => 'served'])
                @empty
                    <p class="text-sm text-white/30 text-center py-8 md:py-10">No served orders</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Completed --}}
    <div class="min-w-0 flex flex-col">
        <div class="glass-card rounded-2xl p-4 md:p-5 flex flex-col min-h-[260px] sm:min-h-[320px] md:min-h-[420px] lg:min-h-[500px] opacity-95">
            <div class="flex items-center justify-between mb-4 shrink-0">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 bg-cyan-500 rounded-full" aria-hidden="true"></span>
                    <h2 class="font-bold text-white uppercase tracking-wider text-xs">Completed</h2>
                </div>
                <span class="bg-cyan-500/20 text-cyan-400 text-xs font-bold px-2.5 py-1 rounded-full border border-cyan-500/20">{{ $paidOrders->count() }}</span>
            </div>
            <div class="space-y-3 flex-1 overflow-y-auto custom-scrollbar min-h-0">
                @forelse($paidOrders as $order)
                    <div class="glass p-4 rounded-xl">
                        <div class="flex justify-between items-center gap-2">
                            <span class="text-sm font-bold text-white">{{ config('salon.seat') }} #{{ $order->table_number }}</span>
                            <span class="text-xs font-medium text-white/50 shrink-0">Tsh {{ number_format($order->total_amount) }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-white/30 text-center py-8 md:py-10">No completed orders today</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@include('order-portal.partials.create-order-modal')
@include('order-portal.partials.edit-order-modal')
@include('order-portal.partials.edit-items-modal')
@include('order-portal.partials.payment-modal')
@endsection
