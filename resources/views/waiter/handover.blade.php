<x-waiter-layout>
    <x-slot name="header">
        Hand over {{ strtolower(config('salon.seat_plural')) }}
    </x-slot>

    <div class="mb-8">
        <h2 class="text-3xl font-bold text-white tracking-tight">Hand over {{ strtolower(config('salon.seat_plural')) }} before you leave</h2>
        <p class="text-white/50 font-medium mt-1">Chagua {{ strtolower(config('salon.seat_plural')) }} za kuwapa mwenzako au acha bila mtu kabla huondoka.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl">
            <p class="text-sm font-medium text-emerald-400">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 bg-rose-500/10 border border-rose-500/20 rounded-xl">
            <p class="text-sm font-medium text-rose-400">{{ session('error') }}</p>
        </div>
    @endif

    @if($myTables->isEmpty())
        <div class="glass-card p-8 rounded-2xl text-center">
            <p class="text-white/60 font-medium">You have no {{ strtolower(config('salon.seat_plural')) }} assigned. Nothing to hand over.</p>
            <a href="{{ route('waiter.dashboard') }}" class="inline-block mt-4 px-6 py-3 bg-violet-600 text-white rounded-xl font-semibold hover:bg-violet-500 transition-all">Back to Dashboard</a>
        </div>
    @else
        <form action="{{ route('waiter.handover.submit') }}" method="POST" class="glass-card p-8 rounded-2xl">
            @csrf

            <div class="mb-8">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-white/40 mb-4">Select {{ strtolower(config('salon.seat_plural')) }} to hand over</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($myTables as $table)
                        <label class="flex items-center gap-3 p-4 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 transition-colors cursor-pointer">
                            <input type="checkbox" name="table_ids[]" value="{{ $table->id }}" class="rounded border-white/30 bg-white/5 text-violet-500 focus:ring-violet-500">
                            <span class="font-semibold text-white">{{ config('salon.seat') }} {{ $table->name }}</span>
                            @if($table->table_tag)
                                <span class="text-[10px] text-white/40 font-mono">{{ $table->table_tag }}</span>
                            @endif
                        </label>
                    @endforeach
                </div>
                @error('table_ids')
                    <p class="text-rose-400 text-xs font-medium mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-8">
                <label for="hand_over_to_waiter_id" class="block text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2">Hand over to</label>
                <select name="hand_over_to_waiter_id" id="hand_over_to_waiter_id" class="w-full max-w-md px-4 py-3.5 bg-white/5 border border-white/10 rounded-xl font-medium text-white focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all">
                    <option value="">— Unassigned (no {{ strtolower(config('salon.staff')) }}) —</option>
                    @foreach($colleagues as $colleague)
                        <option value="{{ $colleague->id }}" {{ old('hand_over_to_waiter_id') == $colleague->id ? 'selected' : '' }}>{{ $colleague->name }}</option>
                    @endforeach
                </select>
                <p class="text-white/40 text-xs mt-1">Choose a colleague to take over these {{ strtolower(config('salon.seat_plural')) }}, or leave unassigned for the manager to assign later.</p>
                @error('hand_over_to_waiter_id')
                    <p class="text-rose-400 text-xs font-medium mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-wrap gap-4">
                <button type="submit" class="px-6 py-3.5 bg-gradient-to-r from-violet-600 to-cyan-600 text-white rounded-xl font-semibold hover:shadow-lg hover:shadow-violet-500/25 transition-all">
                    Hand over selected {{ strtolower(config('salon.seat_plural')) }}
                </button>
                <a href="{{ route('waiter.dashboard') }}" class="px-6 py-3.5 glass border border-white/10 text-white rounded-xl font-semibold hover:bg-white/10 transition-all">
                    Cancel
                </a>
            </div>
        </form>
    @endif
</x-waiter-layout>
