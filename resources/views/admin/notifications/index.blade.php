<x-admin-layout>
    <x-slot name="header">Push Notifications</x-slot>

    <div class="space-y-8">
        <p class="text-white/50 text-sm max-w-2xl">Tuma tangazo la papo hapo kwa managers na waiters. Chagua hadhira na restaurant (hiari).</p>

        {{-- Broadcast form --}}
        <div class="glass-card rounded-2xl overflow-hidden border border-white/10">
            <div class="p-6 border-b border-white/5">
                <h2 class="text-xl font-black text-white tracking-tight">Broadcast Message</h2>
                <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest mt-1">Tuma tangazo kwa staff wa restaurants</p>
            </div>
            <div class="p-6 md:p-8">
                <form id="notificationForm" action="{{ route('admin.notifications.send') }}" method="POST" class="space-y-6 max-w-2xl">
                    @csrf
                    @if ($errors->any())
                        <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm">
                            <p class="font-semibold mb-1">Tafadhali sahihisha makosa:</p>
                            <ul class="list-disc list-inside space-y-0.5">
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="space-y-2">
                        <label for="title" class="text-[10px] font-bold uppercase tracking-wider text-white/40 block">Kichwa (Title)</label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" required
                               class="w-full px-4 py-3.5 bg-white/5 border border-white/10 rounded-xl text-sm font-bold text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent"
                               placeholder="k.m. Matengenezo ya Mfumo">
                    </div>
                    <div class="space-y-2">
                        <label for="message" class="text-[10px] font-bold uppercase tracking-wider text-white/40 block">Ujumbe</label>
                        <textarea id="message" name="message" rows="5" required
                                  class="w-full px-4 py-3.5 bg-white/5 border border-white/10 rounded-xl text-sm font-bold text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent"
                                  placeholder="Andika ujumbe hapa...">{{ old('message') }}</textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="targetSelect" class="text-[10px] font-bold uppercase tracking-wider text-white/40 block">Hadhira</label>
                            <select id="targetSelect" name="target" class="w-full px-4 py-3.5 bg-white/5 border border-white/10 rounded-xl text-sm font-bold text-white focus:ring-2 focus:ring-violet-500 [&>option]:bg-gray-900">
                                <option value="all" {{ old('target', 'all') === 'all' ? 'selected' : '' }}>Watumiaji wote</option>
                                <option value="managers" {{ old('target') === 'managers' ? 'selected' : '' }}>Managers wote</option>
                                <option value="waiters" {{ old('target') === 'waiters' ? 'selected' : '' }}>{{ config('salon.staff_plural') }} wote</option>
                                <option value="specific_restaurant" {{ old('target') === 'specific_restaurant' ? 'selected' : '' }}>{{ config('salon.entity') }} maalum</option>
                            </select>
                        </div>
                        <div id="restaurantSelectContainer" class="space-y-2 {{ old('target') === 'specific_restaurant' ? '' : 'hidden' }}">
                            <label for="restaurant_id" class="text-[10px] font-bold uppercase tracking-wider text-white/40 block">Chagua {{ config('salon.entity') }}</label>
                            <select id="restaurant_id" name="restaurant_id" class="w-full px-4 py-3.5 bg-white/5 border border-white/10 rounded-xl text-sm font-bold text-white focus:ring-2 focus:ring-violet-500 [&>option]:bg-gray-900">
                                <option value="">-- Chagua --</option>
                                @foreach ($restaurants as $r)
                                    <option value="{{ $r->id }}" {{ old('restaurant_id') == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="pt-2">
                        <button type="submit" id="sendBtn" class="notification-send-btn px-10 py-4 bg-gradient-to-r from-violet-600 to-cyan-600 text-white rounded-xl font-bold text-sm hover:shadow-lg hover:shadow-violet-500/25 transition-all flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="send-icon"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                            <span class="send-label">Tuma Tangazo</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Recent broadcasts --}}
        <div class="glass-card rounded-2xl overflow-hidden border border-white/10">
            <div class="p-6 border-b border-white/5">
                <h2 class="text-xl font-black text-white tracking-tight">Tangazo Zilizotumwa Hivi Karibuni</h2>
                <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest mt-1">Orodha ya mabroadcast uliyotuma</p>
            </div>
            <div class="overflow-x-auto custom-scrollbar">
                @if ($recent->isEmpty())
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-white/30 mx-auto mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                        </div>
                        <p class="text-white font-bold">Bado hakuna tangazo lililotumwa</p>
                        <p class="text-sm text-white/50 mt-1">Tangazo lako la kwanza litaonekana hapa.</p>
                    </div>
                @else
                    <table class="w-full min-w-[600px]">
                        <thead>
                            <tr class="bg-white/5">
                                <th class="px-6 py-4 text-left text-[10px] font-black text-white/40 uppercase tracking-widest">Tarehe</th>
                                <th class="px-6 py-4 text-left text-[10px] font-black text-white/40 uppercase tracking-widest">Kichwa</th>
                                <th class="px-6 py-4 text-left text-[10px] font-black text-white/40 uppercase tracking-widest">Hadhira</th>
                                <th class="px-6 py-4 text-left text-[10px] font-black text-white/40 uppercase tracking-widest">Ujumbe (kifupi)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @foreach ($recent as $sent)
                                <tr class="hover:bg-white/5 transition-all">
                                    <td class="px-6 py-4 text-sm text-white/60 font-medium">{{ $sent->created_at->format('M d, H:i') }}</td>
                                    <td class="px-6 py-4 font-bold text-white">{{ $sent->title }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-semibold bg-violet-500/20 text-violet-400 border border-violet-500/30">{{ $sent->target_label }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-white/70 max-w-xs truncate">{{ Str::limit($sent->message, 50) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        {{-- Tips --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="glass-card rounded-2xl p-6 border border-white/10">
                <h3 class="text-lg font-black text-white tracking-tight mb-4">Vidokezo</h3>
                <ul class="space-y-3">
                    <li class="flex gap-3">
                        <span class="w-5 h-5 rounded-full bg-emerald-500/20 flex items-center justify-center shrink-0"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-emerald-400"><polyline points="20 6 9 17 4 12"/></svg></span>
                        <p class="text-xs text-white/60 font-medium">Tumia kichwa kifupi na wazi kwa ufanisi.</p>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-5 h-5 rounded-full bg-emerald-500/20 flex items-center justify-center shrink-0"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-emerald-400"><polyline points="20 6 9 17 4 12"/></svg></span>
                        <p class="text-xs text-white/60 font-medium">Chagua hadhira maalum ili kuepuka spam.</p>
                    </li>
                </ul>
            </div>
            <div class="glass-card rounded-2xl p-6 border border-white/10 border-violet-500/20">
                <h3 class="text-lg font-black text-white tracking-tight mb-4">Onyo la Simu</h3>
                <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-violet-600 to-cyan-500 rounded-xl flex items-center justify-center text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase text-white/50">TIPTAP</p>
                            <p class="text-xs text-white/70">Sasa hivi</p>
                        </div>
                    </div>
                    <p class="text-sm font-bold text-white">Kichwa cha tangazo</p>
                    <p class="text-xs text-white/60">Ujumbe utaonekana hivi kwenye simu ya mtumiaji.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('targetSelect').addEventListener('change', function () {
            const container = document.getElementById('restaurantSelectContainer');
            container.classList.toggle('hidden', this.value !== 'specific_restaurant');
        });

        document.getElementById('notificationForm').addEventListener('submit', function () {
            var btn = document.getElementById('sendBtn');
            var label = btn.querySelector('.send-label');
            if (btn && !btn.disabled) {
                btn.disabled = true;
                if (label) label.textContent = 'Inatumwa...';
            }
        });
    </script>
</x-admin-layout>
