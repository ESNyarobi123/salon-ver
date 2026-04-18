<x-manager-layout>
    <x-slot name="header">
        Performance Report
    </x-slot>

    <div class="mb-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-3xl font-bold text-white tracking-tight">Performance Report</h2>
                <p class="text-sm font-medium text-white/40 uppercase tracking-wider">{{ config('salon.entity') }} &amp; {{ config('salon.staff') }} performance</p>
            </div>
            <a href="{{ route('manager.reports.export-performance', ['period' => $period, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="bg-gradient-to-r from-violet-600 to-cyan-600 text-white px-6 py-3 rounded-xl font-semibold hover:shadow-lg hover:shadow-violet-500/25 transition-all flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/>
                </svg>
                Export CSV
            </a>
        </div>

        <!-- Date Range Filter -->
        <form method="GET" action="{{ route('manager.reports.performance') }}" class="glass p-6 rounded-2xl mb-8">
            <div class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">Period</label>
                    <select name="period" id="periodSelect" onchange="toggleCustomDates()" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl font-medium text-white focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all">
                        <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Today</option>
                        <option value="week" {{ $period === 'week' ? 'selected' : '' }}>This Week</option>
                        <option value="month" {{ $period === 'month' ? 'selected' : '' }}>This Month</option>
                        <option value="custom" {{ $period === 'custom' ? 'selected' : '' }}>Custom Range</option>
                    </select>
                </div>
                <div id="customDates" class="flex gap-4 flex-1 {{ $period === 'custom' ? '' : 'hidden' }}">
                    <div class="flex-1">
                        <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">Start Date</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl font-medium text-white focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all">
                    </div>
                    <div class="flex-1">
                        <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">End Date</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl font-medium text-white focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all">
                    </div>
                </div>
                <button type="submit" class="bg-gradient-to-r from-violet-600 to-cyan-600 text-white px-6 py-3 rounded-xl font-semibold hover:shadow-lg hover:shadow-violet-500/25 transition-all">
                    Apply Filter
                </button>
            </div>
        </form>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="glass-card p-6 rounded-2xl">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-violet-500/20 rounded-xl flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-violet-400">
                            <path d="M3 3h18v18H3zM8 12h8M12 8v8"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-3xl font-bold text-white mb-1">{{ number_format($totalOrders) }}</h3>
                <p class="text-sm text-white/40 font-medium uppercase tracking-wider">Total bookings</p>
            </div>

            <div class="glass-card p-6 rounded-2xl">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-emerald-500/20 rounded-xl flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-400">
                            <line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-3xl font-bold text-white mb-1">Tsh {{ number_format($totalRevenue) }}</h3>
                <p class="text-sm text-white/40 font-medium uppercase tracking-wider">Total Revenue</p>
            </div>

            <div class="glass-card p-6 rounded-2xl">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-amber-500/20 rounded-xl flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-amber-400">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-3xl font-bold text-white mb-1">{{ $avgRating > 0 ? number_format($avgRating, 1) : 'N/A' }}</h3>
                <p class="text-sm text-white/40 font-medium uppercase tracking-wider">Average Rating</p>
            </div>
        </div>

        <!-- Top Performer Badge -->
        @if($topPerformer)
        <div class="glass-card p-6 rounded-2xl mb-8 border-2 border-violet-500/30">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-gradient-to-br from-violet-600 to-cyan-600 rounded-2xl flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                        <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h4 class="text-xl font-bold text-white mb-1">Top Performer</h4>
                    <p class="text-white/60">{{ $topPerformer['name'] }} — {{ $topPerformer['orders_count'] }} bookings handled</p>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-violet-400">{{ number_format($topPerformer['tips_earned']) }} Tsh</div>
                    <div class="text-sm text-white/40">Tips Earned</div>
                </div>
            </div>
        </div>
        @endif

        <!-- Stylist performance -->
        <div class="glass-card rounded-2xl overflow-hidden">
            <div class="p-6 border-b border-white/5">
                <h3 class="text-xl font-bold text-white">{{ config('salon.staff') }} performance</h3>
                <p class="text-sm text-white/40">Detailed metrics for each {{ strtolower(config('salon.staff')) }}</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full" id="performanceTable">
                    <thead>
                        <tr class="border-b border-white/5">
                            <th class="text-left p-4 text-[10px] font-bold uppercase tracking-wider text-white/40 cursor-pointer hover:text-white/60" onclick="sortTable(0)">
                                {{ config('salon.staff') }} name
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline ml-1">
                                    <path d="m7 15 5 5 5-5"/><path d="m7 9 5-5 5 5"/>
                                </svg>
                            </th>
                            <th class="text-left p-4 text-[10px] font-bold uppercase tracking-wider text-white/40 cursor-pointer hover:text-white/60" onclick="sortTable(1)">
                                Bookings handled
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline ml-1">
                                    <path d="m7 15 5 5 5-5"/><path d="m7 9 5-5 5 5"/>
                                </svg>
                            </th>
                            <th class="text-left p-4 text-[10px] font-bold uppercase tracking-wider text-white/40 cursor-pointer hover:text-white/60" onclick="sortTable(2)">
                                Tips Earned
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline ml-1">
                                    <path d="m7 15 5 5 5-5"/><path d="m7 9 5-5 5 5"/>
                                </svg>
                            </th>
                            <th class="text-left p-4 text-[10px] font-bold uppercase tracking-wider text-white/40 cursor-pointer hover:text-white/60" onclick="sortTable(3)">
                                Avg Rating
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline ml-1">
                                    <path d="m7 15 5 5 5-5"/><path d="m7 9 5-5 5 5"/>
                                </svg>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($waiterStats as $stat)
                        <tr class="border-b border-white/5 hover:bg-white/5 transition-all">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-white/5 rounded-full flex items-center justify-center border border-white/10">
                                        <span class="text-sm font-bold text-white/60">{{ substr($stat['name'], 0, 1) }}</span>
                                    </div>
                                    <span class="font-semibold text-white">{{ $stat['name'] }}</span>
                                </div>
                            </td>
                            <td class="p-4">
                                <span class="font-semibold text-white">{{ number_format($stat['orders_count']) }}</span>
                            </td>
                            <td class="p-4">
                                <span class="font-semibold text-emerald-400">Tsh {{ number_format($stat['tips_earned']) }}</span>
                            </td>
                            <td class="p-4">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-amber-400">{{ $stat['avg_rating'] > 0 ? number_format($stat['avg_rating'], 1) : 'N/A' }}</span>
                                    @if($stat['avg_rating'] > 0)
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="none" class="text-amber-400">
                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                    </svg>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-white/40">
                                No {{ strtolower(config('salon.staff')) }} data for this period
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function toggleCustomDates() {
            const period = document.getElementById('periodSelect').value;
            const customDates = document.getElementById('customDates');
            if (period === 'custom') {
                customDates.classList.remove('hidden');
            } else {
                customDates.classList.add('hidden');
            }
        }

        function sortTable(columnIndex) {
            const table = document.getElementById('performanceTable');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            
            if (rows.length === 0 || rows[0].cells.length === 1) return;
            
            const isNumeric = columnIndex > 0;
            
            rows.sort((a, b) => {
                let aVal = a.cells[columnIndex].textContent.trim();
                let bVal = b.cells[columnIndex].textContent.trim();
                
                if (isNumeric) {
                    aVal = parseFloat(aVal.replace(/[^0-9.-]/g, '')) || 0;
                    bVal = parseFloat(bVal.replace(/[^0-9.-]/g, '')) || 0;
                    return bVal - aVal;
                } else {
                    return aVal.localeCompare(bVal);
                }
            });
            
            rows.forEach(row => tbody.appendChild(row));
        }
    </script>
</x-manager-layout>
