<x-waiter-layout>
    <x-slot name="header">
        {{ config('salon.customer') }} feedback
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($feedbacks as $feedback)
            <div class="glass-card p-6 rounded-2xl card-hover group">
                <div class="flex justify-between items-start mb-5">
                    <div class="flex text-amber-400">
                        @for($i = 1; $i <= 5; $i++)
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="{{ $i <= $feedback->rating ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ $i <= $feedback->rating ? '' : 'text-white/20' }}">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                            </svg>
                        @endfor
                    </div>
                    <span class="text-[10px] font-medium text-white/30 uppercase tracking-wider">{{ $feedback->created_at->diffForHumans() }}</span>
                </div>
                
                <p class="text-white/60 italic font-medium leading-relaxed mb-6">"{{ $feedback->comment }}"</p>
                
                <div class="flex items-center justify-between pt-4 border-t border-white/5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 glass rounded-xl flex items-center justify-center font-bold text-violet-400 text-sm">
                            #{{ $feedback->order->table_number ?? 'N/A' }}
                        </div>
                        <p class="text-[11px] font-medium text-white/40 uppercase tracking-wider">{{ config('salon.seat') }} number</p>
                    </div>
                    <div class="w-8 h-8 bg-rose-500/20 rounded-lg flex items-center justify-center text-rose-400 opacity-0 group-hover:opacity-100 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>
                        </svg>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full glass-card py-16 text-center rounded-2xl">
                <div class="w-16 h-16 bg-white/5 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-white/5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white/20">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                </div>
                <h4 class="text-xl font-bold text-white mb-2">No feedback yet</h4>
                <p class="text-white/40">Keep providing great service to get ratings!</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $feedbacks->links() }}
    </div>
</x-waiter-layout>
