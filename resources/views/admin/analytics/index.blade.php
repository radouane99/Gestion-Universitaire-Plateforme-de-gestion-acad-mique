<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-slate-800 leading-tight tracking-tighter italic">📊 Statistiques Avancées & Pilotage</h2>
    </x-slot>

    <div class="py-10 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- KPI Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-gradient-to-br from-indigo-600 to-blue-700 p-6 rounded-3xl text-white shadow-lg">
                    <p class="text-xs font-black uppercase tracking-widest text-indigo-200">Taux de Réussite Estimé</p>
                    <p class="text-4xl font-black mt-2">{{ $successRate }}%</p>
                    <p class="text-[10px] mt-1 opacity-70">Basé sur les moyennes >= 10</p>
                </div>
                
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-black uppercase tracking-widest text-slate-400">Admis vs Ajournés</p>
                        <span class="text-emerald-500 bg-emerald-50 p-2 rounded-lg text-lg">🎓</span>
                    </div>
                    <p class="text-2xl font-black mt-2 text-slate-700">{{ $admis }} / <span class="text-slate-400 text-lg">{{ $ajournes }}</span></p>
                    <p class="text-[10px] mt-1 text-slate-400">Total étudiants notés : {{ $admis + $ajournes }}</p>
                </div>

                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-black uppercase tracking-widest text-slate-400">Absences Totales</p>
                        <span class="text-rose-500 bg-rose-50 p-2 rounded-lg text-lg">🚨</span>
                    </div>
                    <p class="text-2xl font-black mt-2 text-slate-700">{{ $totalAbsences }}</p>
                    <p class="text-[10px] mt-1 text-rose-400 font-bold">{{ $unjustifiedAbsences }} Non justifiées</p>
                </div>

                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-black uppercase tracking-widest text-slate-400">Rattrapages (Top Module)</p>
                        <span class="text-amber-500 bg-amber-50 p-2 rounded-lg text-lg">⚠️</span>
                    </div>
                    <p class="text-xl font-black mt-2 text-slate-700 truncate" title="{{ $retakeStats->first()?->module?->name ?? 'N/A' }}">
                        {{ $retakeStats->first()?->module?->code ?? 'N/A' }}
                    </p>
                    <p class="text-[10px] mt-1 text-amber-500 font-bold">{{ $retakeStats->first()?->count ?? 0 }} étudiants convoqués</p>
                </div>
            </div>

            {{-- Charts Row 1 --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                {{-- Flop 5 Modules (Difficult) --}}
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100">
                    <h4 class="text-lg font-black text-slate-800 italic tracking-tighter mb-6">📉 Top 5 Modules Difficiles (Flop)</h4>
                    <canvas id="flopModulesChart" height="200"></canvas>
                </div>

                {{-- Top 5 Modules (Easy) --}}
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100">
                    <h4 class="text-lg font-black text-slate-800 italic tracking-tighter mb-6">🏆 Top 5 Modules Réussis</h4>
                    <canvas id="topModulesChart" height="200"></canvas>
                </div>
            </div>

            {{-- Charts Row 2 --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Absences Distribution --}}
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 lg:col-span-1">
                    <h4 class="text-lg font-black text-slate-800 italic tracking-tighter mb-6">Pie Chart: Absences</h4>
                    <div class="relative w-full aspect-square max-h-[250px] mx-auto">
                        <canvas id="absencesChart"></canvas>
                    </div>
                </div>

                {{-- Retakes per module --}}
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 lg:col-span-2">
                    <h4 class="text-lg font-black text-slate-800 italic tracking-tighter mb-6">📊 Volumes de Rattrapages par Module (Top 5)</h4>
                    <canvas id="retakesChart" height="120"></canvas>
                </div>
            </div>

        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chartOptions = {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [5, 5] } },
                    x: { grid: { display: false } }
                }
            };

            // 1. Flop Modules (Red)
            const flopCtx = document.getElementById('flopModulesChart').getContext('2d');
            const flopData = @json($flopModules->values());
            new Chart(flopCtx, {
                type: 'bar',
                data: {
                    labels: flopData.map(d => d.module.code),
                    datasets: [{
                        label: 'Moyenne / 20',
                        data: flopData.map(d => parseFloat(d.avg_grade).toFixed(2)),
                        backgroundColor: '#f43f5e',
                        borderRadius: 6
                    }]
                },
                options: chartOptions
            });

            // 2. Top Modules (Green)
            const topCtx = document.getElementById('topModulesChart').getContext('2d');
            const topData = @json($topModules->values());
            new Chart(topCtx, {
                type: 'bar',
                data: {
                    labels: topData.map(d => d.module.code),
                    datasets: [{
                        label: 'Moyenne / 20',
                        data: topData.map(d => parseFloat(d.avg_grade).toFixed(2)),
                        backgroundColor: '#10b981',
                        borderRadius: 6
                    }]
                },
                options: chartOptions
            });

            // 3. Absences (Pie)
            const absCtx = document.getElementById('absencesChart').getContext('2d');
            new Chart(absCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Justifiées', 'Non Justifiées'],
                    datasets: [{
                        data: [{{ $justifiedAbsences }}, {{ $unjustifiedAbsences }}],
                        backgroundColor: ['#10b981', '#f43f5e'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });

            // 4. Retakes (Bar)
            const retakeCtx = document.getElementById('retakesChart').getContext('2d');
            const retakeData = @json($retakeStats);
            new Chart(retakeCtx, {
                type: 'bar',
                data: {
                    labels: retakeData.map(d => d.module.code),
                    datasets: [{
                        label: 'Étudiants convoqués',
                        data: retakeData.map(d => d.count),
                        backgroundColor: '#f59e0b',
                        borderRadius: 6
                    }]
                },
                options: chartOptions
            });
        });
    </script>
</x-app-layout>
