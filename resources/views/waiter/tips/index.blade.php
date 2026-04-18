<x-waiter-layout>
    <x-slot name="header">
        My Tips History
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
        <div class="lg:col-span-1">
            <div class="glass-card p-8 rounded-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-amber-500/20 to-amber-500/5 rounded-full blur-3xl"></div>
                <div class="relative z-10">
                    <p class="text-[11px] font-bold text-white/40 uppercase tracking-wider mb-2">Total Tips Earned</p>
                    <h3 class="text-4xl font-bold text-white mb-5">Tsh {{ number_format($totalTips) }}</h3>
                    <div class="glass p-4 rounded-xl">
                        <p class="text-[11px] font-medium text-white/50 uppercase tracking-wider">Keep up the great service! 🌟</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-white/[0.02]">
                        <th class="px-6 py-4 text-[10px] font-bold text-white/40 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-white/40 uppercase tracking-wider">{{ config('salon.booking') }} ID</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-white/40 uppercase tracking-wider">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($tips as $tip)
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <td class="px-6 py-5">
                                <p class="font-semibold text-white text-sm">{{ $tip->created_at->format('M d, Y') }}</p>
                                <p class="text-[11px] text-white/40 font-medium uppercase">{{ $tip->created_at->format('H:i A') }}</p>
                            </td>
                            <td class="px-6 py-5">
                                <span class="text-sm font-medium text-white/60">#{{ $tip->order_id }}</span>
                            </td>
                            <td class="px-6 py-5">
                                <p class="font-bold text-amber-400">Tsh {{ number_format($tip->amount) }}</p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-16 text-center">
                                <p class="text-sm text-white/40 font-medium">No tips recorded yet</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-white/5">
            {{ $tips->links() }}
        </div>
    </div>
</x-waiter-layout>
