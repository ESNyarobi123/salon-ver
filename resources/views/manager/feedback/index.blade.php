<x-manager-layout>
    <x-slot name="header">
        Feedback & Ratings
    </x-slot>

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-3xl font-bold text-white tracking-tight">{{ config('salon.customer') }} feedback</h2>
            <p class="text-sm font-medium text-white/40 uppercase tracking-wider">Insights into your service quality</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="glass px-4 py-2 rounded-xl flex items-center gap-2">
                <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                <span class="text-[11px] font-bold text-white/70 uppercase tracking-wider">Live Updates</span>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
        <div class="glass-card p-6 rounded-3xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-500/10 rounded-full blur-2xl group-hover:bg-amber-500/20 transition-all duration-500"></div>
            <p class="text-[10px] font-bold text-white/40 uppercase tracking-wider mb-1">Average Rating</p>
            <div class="flex items-end gap-2">
                <h3 class="text-4xl font-black text-white">{{ number_format($avgRating, 1) }}</h3>
                <div class="flex text-amber-400 mb-1.5">
                    @for($i = 1; $i <= 5; $i++)
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="{{ $i <= round($avgRating) ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" class="{{ $i <= round($avgRating) ? '' : 'text-white/10' }}">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                    @endfor
                </div>
            </div>
        </div>

        <div class="glass-card p-6 rounded-3xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-violet-500/10 rounded-full blur-2xl group-hover:bg-violet-500/20 transition-all duration-500"></div>
            <p class="text-[10px] font-bold text-white/40 uppercase tracking-wider mb-1">Total Reviews</p>
            <h3 class="text-4xl font-black text-white">{{ number_format($totalReviews) }}</h3>
        </div>

        <div class="glass-card p-6 rounded-3xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition-all duration-500"></div>
            <p class="text-[10px] font-bold text-white/40 uppercase tracking-wider mb-1">Positive Feedback</p>
            @php
                $positiveCount = ($ratingBreakdown[5] ?? 0) + ($ratingBreakdown[4] ?? 0);
                $positivePercent = $totalReviews > 0 ? ($positiveCount / $totalReviews) * 100 : 0;
            @endphp
            <h3 class="text-4xl font-black text-white">{{ round($positivePercent) }}%</h3>
        </div>

        <div class="glass-card p-6 rounded-3xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-rose-500/10 rounded-full blur-2xl group-hover:bg-rose-500/20 transition-all duration-500"></div>
            <p class="text-[10px] font-bold text-white/40 uppercase tracking-wider mb-1">Response Rate</p>
            <h3 class="text-4xl font-black text-white">0%</h3>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Breakdown -->
        <div class="lg:col-span-1 space-y-6">
            <div class="glass-card p-8 rounded-3xl">
                <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-6">Rating Breakdown</h4>
                <div class="space-y-5">
                    @foreach([5, 4, 3, 2, 1] as $star)
                        @php
                            $count = $ratingBreakdown[$star] ?? 0;
                            $percentage = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
                        @endphp
                        <div class="space-y-2">
                            <div class="flex justify-between text-[11px] font-bold uppercase tracking-widest">
                                <span class="text-white/60 flex items-center gap-1.5">
                                    {{ $star }} Stars
                                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="currentColor" class="text-amber-400">
                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                    </svg>
                                </span>
                                <span class="text-white">{{ $count }}</span>
                            </div>
                            <div class="h-1.5 w-full bg-white/5 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-amber-500 to-amber-300 rounded-full transition-all duration-1000" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="glass-card p-8 rounded-3xl bg-gradient-to-br from-violet-600/20 to-transparent border-violet-500/20">
                <h4 class="text-sm font-bold text-white mb-2">Improve your score</h4>
                <p class="text-xs text-white/40 leading-relaxed mb-4">Responding to negative feedback within 24 hours can increase customer retention by 30%.</p>
                <button class="w-full py-3 bg-white/10 hover:bg-white/20 text-white rounded-xl text-xs font-bold transition-all uppercase tracking-widest">View Tips</button>
            </div>
        </div>

        <!-- List -->
        <div class="lg:col-span-2 space-y-4">
            @forelse($feedbacks as $feedback)
                <div class="glass-card p-6 rounded-3xl card-hover group">
                    <div class="flex gap-5">
                        <div class="shrink-0">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-white/10 to-white/5 flex items-center justify-center border border-white/10 group-hover:border-white/20 transition-all">
                                <span class="text-lg font-black text-white/80">{{ substr($feedback->order->customer_name ?? $feedback->waiter->name ?? 'C', 0, 1) }}</span>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start mb-1">
                                <div>
                                    <h5 class="font-bold text-white truncate">{{ $feedback->order->customer_name ?? 'Feedback for ' . ($feedback->waiter->name ?? 'Service') }}</h5>
                                    <p class="text-[10px] font-bold text-white/30 uppercase tracking-widest">{{ $feedback->created_at->diffForHumans() }} • {{ config('salon.seat') }} #{{ $feedback->order->table_number ?? 'N/A' }}</p>
                                </div>
                                <div class="flex gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="{{ $i <= $feedback->rating ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" class="{{ $i <= $feedback->rating ? 'text-amber-400' : 'text-white/10' }}">
                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                        </svg>
                                    @endfor
                                </div>
                            </div>
                            <p class="text-sm text-white/70 leading-relaxed italic mb-4">"{{ $feedback->comment }}"</p>
                            <div class="flex items-center gap-4">
                                <button class="text-[10px] font-black uppercase tracking-widest text-violet-400 hover:text-violet-300 transition-colors flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                    Reply
                                </button>
                                <button class="text-[10px] font-black uppercase tracking-widest text-white/20 hover:text-rose-400 transition-colors flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                    Report
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="glass-card p-20 rounded-[40px] text-center">
                    <div class="w-20 h-20 bg-white/5 rounded-3xl flex items-center justify-center mx-auto mb-6 border border-white/5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-white/20">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-2">Silence is golden?</h3>
                    <p class="text-white/40 max-w-xs mx-auto">No feedback yet. Once your customers start sharing their thoughts, they'll appear here in style.</p>
                </div>
            @endforelse

            <div class="mt-8">
                {{ $feedbacks->links() }}
            </div>
        </div>
    </div>
</x-manager-layout>
