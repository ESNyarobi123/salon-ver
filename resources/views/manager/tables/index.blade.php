<x-manager-layout>
    <x-slot name="header">
        {{ config('salon.seat_management_title') }}
    </x-slot>

    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-3xl font-bold text-white tracking-tight">{{ config('salon.seat_management_title') }}</h2>
            <p class="text-sm font-medium text-white/40 uppercase tracking-wider">{{ config('salon.seat_management_subtitle') }}</p>
        </div>
        <button type="button" onclick="openAddTableModal()" class="min-h-[44px] inline-flex items-center justify-center gap-2 bg-gradient-to-r from-violet-600 to-cyan-600 text-white px-6 py-3 rounded-xl font-semibold hover:shadow-lg hover:shadow-violet-500/25 transition-all focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2 focus:ring-offset-[#0f0a1e]">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14"/><path d="M12 5v14"/>
            </svg>
            Add {{ config('salon.seat') }}
        </button>
    </div>

    <!-- Official saloon QR -->
    <div class="glass-card rounded-2xl p-8 mb-12 border-violet-500/30 bg-violet-500/5 relative overflow-hidden group">
        <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:opacity-20 transition-opacity">
            <svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="text-violet-400">
                <path d="M3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
        </div>
        
        <div class="flex flex-col md:flex-row items-center gap-10 relative z-10">
            <div class="bg-white p-4 rounded-[2rem] shadow-2xl shadow-violet-500/20">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode(Auth::user()->restaurant->whatsapp_qr_url) }}" alt="Official QR" class="w-40 h-40">
            </div>
            
            <div class="flex-1 text-center md:text-left">
                <div class="inline-flex items-center px-3 py-1 rounded-full bg-violet-500/20 text-violet-400 text-[10px] font-bold uppercase tracking-widest mb-4">
                    {{ config('salon.official_saloon_qr') }}
                </div>
                <h3 class="text-3xl font-black text-white mb-4 tracking-tight">{{ config('salon.saloon_entrance_qr') }}</h3>
                <p class="text-white/50 max-w-xl mb-8 font-medium">{{ config('salon.saloon_qr_help') }}</p>
                
                <div class="flex flex-wrap justify-center md:justify-start gap-4">
                    <a href="https://api.qrserver.com/v1/create-qr-code/?size=1000x1000&data={{ urlencode(Auth::user()->restaurant->whatsapp_qr_url) }}" download="official-qr.png" target="_blank" class="px-8 py-4 bg-violet-600 text-white rounded-2xl font-bold shadow-xl shadow-violet-600/20 hover:bg-violet-500 transition-all flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/>
                        </svg>
                        Download High-Res
                    </a>
                    <button onclick="copyLink('{{ Auth::user()->restaurant->whatsapp_qr_url }}')" class="px-8 py-4 glass text-white rounded-2xl font-bold hover:bg-white/10 transition-all flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                        </svg>
                        Copy Bot Link
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-8">
        <h3 class="text-xl font-bold text-white">{{ config('salon.seat') }}-specific QR codes</h3>
        <p class="text-sm text-white/40">QR codes for each {{ strtolower(config('salon.seat')) }} to track bookings automatically.</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($tables as $table)
            <div class="glass-card rounded-2xl overflow-hidden card-hover group">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-violet-500/20 to-cyan-500/20 rounded-xl flex items-center justify-center border border-violet-500/20 group-hover:scale-110 transition-transform">
                            <span class="text-2xl font-bold text-violet-400">{{ $loop->iteration }}</span>
                        </div>
                        <div class="flex gap-1">
                            <button onclick="openEditTableModal({{ json_encode($table) }})" class="p-2 text-white/40 hover:text-violet-400 hover:bg-white/5 rounded-lg transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/>
                                </svg>
                            </button>
                            <form action="{{ route('manager.tables.destroy', $table->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-white/40 hover:text-rose-400 hover:bg-rose-500/10 rounded-lg transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    <h4 class="text-lg font-bold text-white mb-1">{{ $table->name }}</h4>
                    <p class="text-sm font-medium text-white/40">{{ $table->capacity }} Seats</p>
                    @if($table->waiter)
                    <p class="text-xs font-medium text-violet-400/80">{{ config('salon.staff') }}: {{ $table->waiter->name }}</p>
                    @endif
                </div>
                
                <div class="bg-white/5 p-6 flex flex-col items-center gap-4 border-t border-white/5">
                    <!-- Table Tag Badge -->
                    @if($table->table_tag)
                    <div class="w-full p-2 bg-white/5 rounded-lg border border-white/10 flex items-center justify-between mb-2">
                        <span class="text-[10px] font-bold text-white/40 uppercase tracking-wider">Tag</span>
                        <div class="flex items-center gap-2">
                            <code class="text-sm font-mono font-bold text-cyan-400">{{ $table->table_tag }}</code>
                            <button onclick="copyLink('{{ $table->table_tag }}')" class="text-white/40 hover:text-white transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                            </button>
                        </div>
                    </div>
                    @endif

                    <div class="bg-white p-2 rounded-xl">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($table->whatsapp_qr_url) }}" alt="QR Code" class="w-28 h-28">
                    </div>
                    <div class="flex gap-2 w-full">
                        <a href="https://api.qrserver.com/v1/create-qr-code/?size=500x500&data={{ urlencode($table->whatsapp_qr_url) }}" download="table-{{ $table->id }}-qr.png" target="_blank" class="flex-1 glass py-2.5 rounded-xl font-semibold text-sm text-white/70 hover:text-white hover:bg-violet-600 transition-all flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/>
                            </svg>
                            Download
                        </a>
                        <button onclick="copyLink('{{ $table->whatsapp_qr_url }}', this)" class="flex-1 glass py-2.5 rounded-xl font-semibold text-sm text-white/70 hover:text-white hover:bg-cyan-600 transition-all flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                            </svg>
                            Copy Link
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full glass-card py-16 text-center rounded-2xl">
                <div class="w-16 h-16 bg-white/5 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-white/5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white/20">
                        <rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">No {{ strtolower(config('salon.seat_plural')) }} found</h3>
                <p class="text-white/40">Start by adding your first {{ strtolower(config('salon.seat')) }}.</p>
            </div>
        @endforelse

        <!-- Add New Card -->
        <button onclick="openAddTableModal()" class="glass rounded-2xl border border-dashed border-white/20 flex flex-col items-center justify-center p-8 hover:border-violet-500 hover:bg-violet-500/10 transition-all group min-h-[320px]">
            <div class="w-14 h-14 bg-white/5 rounded-xl flex items-center justify-center group-hover:bg-violet-500/20 group-hover:scale-110 transition-all mb-4 border border-white/10">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white/40 group-hover:text-violet-400">
                    <path d="M5 12h14"/><path d="M12 5v14"/>
                </svg>
            </div>
            <span class="font-semibold text-white/40 group-hover:text-violet-400 uppercase tracking-wider text-sm">Add new {{ strtolower(config('salon.seat')) }}</span>
        </button>
    </div>

    <!-- Table Modal -->
    <div id="tableModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-6">
        <div class="bg-surface-900 w-full max-w-md rounded-2xl shadow-2xl overflow-hidden border border-white/10">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 id="modalTitle" class="text-xl font-bold text-white">Add new {{ config('salon.seat') }}</h3>
                    <button onclick="closeTableModal()" class="p-2 hover:bg-white/10 rounded-xl transition-all text-white/40 hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                        </svg>
                    </button>
                </div>
                
                <form id="tableForm" action="{{ route('manager.tables.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    
                    <div>
                        <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">{{ config('salon.seat') }} name</label>
                        <input type="text" name="name" id="tableName" required placeholder="e.g. Seat 1" 
                               class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl font-medium text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all">
                    </div>
                    
                    <div>
                        <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">Capacity (Seats)</label>
                        <input type="number" name="capacity" id="tableCapacity" placeholder="e.g. 4" 
                               class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl font-medium text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all">
                    </div>

                    <div id="waiterSelectWrap" class="hidden">
                        <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">Assigned {{ config('salon.staff') }} (link {{ strtolower(config('salon.seat')) }} → {{ strtolower(config('salon.staff')) }})</label>
                        <select name="waiter_id" id="tableWaiterId" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl font-medium text-white focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all">
                            <option value="">— None —</option>
                            @foreach($waiters as $w)
                                <option value="{{ $w->id }}">{{ $w->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="statusToggle" class="hidden flex items-center gap-3 p-3 bg-white/5 rounded-xl">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" id="tableActive" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-white/20 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                        </label>
                        <span class="text-sm font-medium text-white/60">Active</span>
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-violet-600 to-cyan-600 text-white py-3.5 rounded-xl font-semibold hover:shadow-lg hover:shadow-violet-500/25 transition-all mt-2">
                        Save {{ config('salon.seat') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openAddTableModal() {
            document.getElementById('modalTitle').innerText = @json('Add new '.config('salon.seat'));
            document.getElementById('tableForm').action = '{{ route("manager.tables.store") }}';
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('tableName').value = '';
            document.getElementById('tableCapacity').value = '';
            document.getElementById('tableWaiterId').value = '';
            document.getElementById('statusToggle').classList.add('hidden');
            document.getElementById('waiterSelectWrap').classList.add('hidden');
            
            document.getElementById('tableModal').classList.remove('hidden');
            document.getElementById('tableModal').classList.add('flex');
        }

        function openEditTableModal(table) {
            document.getElementById('modalTitle').innerText = @json('Edit '.config('salon.seat'));
            document.getElementById('tableForm').action = `/manager/tables/${table.id}`;
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('tableName').value = table.name;
            document.getElementById('tableCapacity').value = table.capacity;
            document.getElementById('tableWaiterId').value = table.waiter_id || '';
            document.getElementById('tableActive').checked = table.is_active;
            document.getElementById('statusToggle').classList.remove('hidden');
            document.getElementById('waiterSelectWrap').classList.remove('hidden');
            
            document.getElementById('tableModal').classList.remove('hidden');
            document.getElementById('tableModal').classList.add('flex');
        }

        function closeTableModal() {
            document.getElementById('tableModal').classList.add('hidden');
            document.getElementById('tableModal').classList.remove('flex');
        }

        async function copyLink(link, button = null) {
            try {
                await navigator.clipboard.writeText(link);
                
                if (button) {
                    // Visual feedback
                    const originalContent = button.innerHTML;
                    button.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-400">
                            <path d="M20 6 9 17l-5-5"/>
                        </svg>
                        Copied!
                    `;
                    button.classList.add('bg-emerald-500/20', 'border-emerald-500/30', 'text-emerald-400');
                    
                    setTimeout(() => {
                        button.innerHTML = originalContent;
                        button.classList.remove('bg-emerald-500/20', 'border-emerald-500/30', 'text-emerald-400');
                    }, 2000);
                } else {
                    alert('Link copied to clipboard!');
                }
                
            } catch (err) {
                console.error('Failed to copy:', err);
                alert('Failed to copy to clipboard');
            }
        }
    </script>
</x-manager-layout>
