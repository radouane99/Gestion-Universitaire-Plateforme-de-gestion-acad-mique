<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tight italic">
                {{ __('Admin Control Center') }}
            </h2>
            <div class="relative group" x-data="{ query: '', results: [], show: false }" @click.away="show = false">
                <input type="text" id="global-search" placeholder="{{ __('Quick search (Ctrl+K)...') }}" 
                    x-model="query"
                    @input.debounce.300ms="if (query.length > 2) { fetch('{{ route('admin.search') }}?q=' + query).then(r => r.json()).then(data => { results = data; show = true; }) } else { results = []; show = false; }"
                    @keydown.escape="show = false"
                    @keydown.window.ctrl.k.prevent="$el.focus()"
                    class="bg-gray-100 dark:bg-slate-800 border-none rounded-2xl px-6 py-2.5 text-sm w-64 dark:text-white focus:ring-2 focus:ring-upf-magenta transition-all group-hover:w-80 shadow-inner">
                
                <!-- Live Results Dropdown -->
                <div x-show="show && results.length > 0" x-transition 
                    class="absolute top-full mt-4 right-0 w-96 bg-white dark:bg-slate-900 rounded-[2rem] shadow-2xl border border-gray-100 dark:border-slate-800 overflow-hidden z-[60]">
                    <div class="p-4 border-b border-gray-50 dark:border-slate-800 flex justify-between items-center bg-gray-50/50 dark:bg-slate-800/50">
                        <span class="text-[10px] font-black uppercase text-gray-400 tracking-widest">Search results</span>
                        <span class="text-[10px] font-black uppercase text-upf-blue" x-text="results.length + ' found'"></span>
                    </div>
                    <div class="max-h-96 overflow-y-auto">
                        <template x-for="res in results" :key="res.title">
                            <a :href="res.url" class="flex items-center gap-4 p-4 hover:bg-indigo-50 dark:hover:bg-slate-800 transition-all border-b border-gray-50 dark:border-slate-800 last:border-none">
                                <div class="w-10 h-10 rounded-xl bg-white dark:bg-slate-700 shadow-sm flex items-center justify-center text-upf-blue dark:text-blue-400 font-black text-xs" x-text="res.type[0]"></div>
                                <div>
                                    <p class="font-bold text-sm text-gray-900 dark:text-white" x-text="res.title"></p>
                                    <p class="text-[10px] font-black uppercase text-upf-magenta tracking-widest" x-text="res.type"></p>
                                </div>
                            </a>
                        </template>
                    </div>
                </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">
            
            <!-- Hero Stats -->
            <div class="bg-gradient-to-br from-upf-blue via-upf-navy to-black rounded-[3rem] p-12 text-white shadow-2xl relative overflow-hidden">
                <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div class="space-y-2">
                        <h2 class="text-xs uppercase font-black tracking-[0.2em] text-upf-magenta">{{ __('System Status') }}</h2>
                        <h3 class="text-4xl font-black italic tracking-tighter">{{ __('Operational') }}</h3>
                        <p class="text-blue-200 text-[10px] uppercase font-bold tracking-widest opacity-60">{{ __('Last synced: Just now') }}</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-xl p-8 rounded-[2rem] border border-white/10 flex flex-col justify-between">
                        <p class="text-5xl font-black tracking-tighter">{{ $stats['students_count'] }}</p>
                        <p class="text-[10px] uppercase font-black tracking-widest text-blue-200 mt-4 italic">{{ __('Total Students') }}</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-xl p-8 rounded-[2rem] border border-white/10 flex flex-col justify-between">
                        <p class="text-5xl font-black tracking-tighter">{{ $stats['professors_count'] }}</p>
                        <p class="text-[10px] uppercase font-black tracking-widest text-blue-200 mt-4 italic">{{ __('Active Faculty') }}</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-xl p-8 rounded-[2rem] border border-white/10 flex flex-col justify-between">
                        <p class="text-5xl font-black tracking-tighter">{{ $stats['absences_total'] }}</p>
                        <p class="text-[10px] uppercase font-black tracking-widest text-upf-magenta mt-4 italic">{{ __('Monthly Absences') }}</p>
                    </div>
                </div>
                <div class="absolute -bottom-48 -left-48 w-[500px] h-[500px] bg-upf-magenta/10 rounded-full blur-[100px]"></div>
            </div>

            <!-- Analytics Triple Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                <!-- Grade Distribution Chart -->
                <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-[2.5rem] p-10 shadow-xl border border-gray-100 dark:border-slate-800">
                    <div class="flex justify-between items-center mb-10">
                        <div>
                            <h4 class="text-2xl font-black text-upf-blue tracking-tighter italic">{{ __('Academic Performance') }}</h4>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-1">{{ __('Grade Distribution across modules') }}</p>
                        </div>
                        <div class="bg-gray-50 px-4 py-2 rounded-xl text-xs font-black text-upf-blue">
                             {{ __('Avg') }}: {{ number_format($stats['grades_avg'], 2) }}
                        </div>
                    </div>
                    <canvas id="gradeChart" height="140"></canvas>
                </div>

                <!-- Action Hub -->
                <div class="space-y-6">
                    <div class="bg-upf-magenta rounded-[2.5rem] p-10 text-white shadow-2xl relative overflow-hidden group">
                        <div class="relative z-10">
                            <h4 class="text-2xl font-black italic tracking-tighter leading-tight">{!! __('Master <br> Schedule') !!}</h4>
                            <a href="{{ route('admin.schedules.index') }}" class="mt-8 inline-flex items-center gap-2 text-xs font-black uppercase tracking-widest bg-white/20 px-6 py-3 rounded-xl hover:bg-white/40 transition-all">
                                {{ __('Update Now') }} →
                            </a>
                        </div>
                        <div class="absolute -right-10 -bottom-10 opacity-10 group-hover:scale-110 transition-transform">
                            <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path></svg>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-10 shadow-xl border border-gray-100 dark:border-slate-800">
                        <h4 class="text-xl font-black text-upf-blue dark:text-blue-400 italic tracking-tighter mb-6">{{ __('Quick Tasks') }}</h4>
                        <div class="space-y-4">
                            <a href="{{ route('admin.users.index') }}" class="flex items-center justify-between p-4 bg-gray-50 dark:bg-slate-800 rounded-2xl hover:bg-upf-blue hover:text-white transition-all group">
                                <span class="font-bold text-sm dark:text-white">{{ __('Add New Student') }}</span>
                                <span class="text-lg opacity-40">+</span>
                            </a>
                            <a href="{{ route('admin.rooms.index') }}" class="flex items-center justify-between p-4 bg-gray-50 dark:bg-slate-800 rounded-2xl hover:bg-upf-blue hover:text-white transition-all group">
                                <span class="font-bold text-sm dark:text-white">{{ __('Allocate Rooms') }}</span>
                                <span class="text-lg opacity-40">→</span>
                            </a>
                        </div>
                    </div>

                    <!-- Premium Export Hub & Logs -->
                    <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-10 shadow-xl border border-gray-100 dark:border-slate-800">
                        <h4 class="text-xl font-black text-upf-blue dark:text-blue-400 italic tracking-tighter mb-6">{{ __('Export & Sécurité') }}</h4>
                        <div class="space-y-4">
                            <a href="{{ route('admin.activity-logs.index') }}" class="flex items-center justify-between p-4 bg-indigo-50 dark:bg-slate-800 rounded-2xl border-l-4 border-indigo-500 hover:bg-indigo-500 hover:text-white transition-all group">
                                <span class="font-bold text-sm dark:text-white">{{ __('Journal d\'Activité') }}</span>
                                <span class="text-xs font-black uppercase bg-indigo-200 text-indigo-800 px-2 py-0.5 rounded">{{ __('Audit') }}</span>
                            </a>
                            <div class="pt-2">
                                <p class="text-[10px] font-black uppercase text-gray-400 tracking-widest mb-3">{{ __('Téléchargements CSV') }}</p>
                                <div class="grid grid-cols-3 gap-2">
                                    <a href="{{ route('admin.export.students') }}" class="text-center p-2 bg-gray-50 dark:bg-slate-800 hover:bg-emerald-500 hover:text-white rounded-xl text-xs font-bold transition-all">{{ __('Élèves') }}</a>
                                    <a href="{{ route('admin.export.grades') }}" class="text-center p-2 bg-gray-50 dark:bg-slate-800 hover:bg-emerald-500 hover:text-white rounded-xl text-xs font-bold transition-all">{{ __('Notes') }}</a>
                                    <a href="{{ route('admin.export.absences') }}" class="text-center p-2 bg-gray-50 dark:bg-slate-800 hover:bg-emerald-500 hover:text-white rounded-xl text-xs font-bold transition-all">{{ __('Absences') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Absence Trends -->
            <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-10 shadow-xl border border-gray-100 dark:border-slate-800">
                <div class="flex justify-between items-center mb-10">
                    <h4 class="text-2xl font-black text-upf-blue dark:text-blue-400 tracking-tighter italic">{{ __('Attendance Forecaster') }}</h4>
                    <div class="flex gap-4">
                        <span class="flex items-center gap-2 text-[10px] font-black uppercase text-gray-400">
                             <span class="w-2 h-2 rounded-full bg-upf-blue"></span> {{ __('Absences') }}
                        </span>
                    </div>
                </div>
                <canvas id="absenceChart" height="100"></canvas>
            </div>

            <!-- Filiere Success Rates + Recent Requests -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Filiere Success Rates -->
                <div class="bg-white rounded-[2.5rem] p-8 shadow-xl border border-gray-100">
                    <h4 class="text-xl font-black text-upf-blue tracking-tighter italic mb-6">📊 Taux de Réussite par Filière</h4>
                    <div class="space-y-4">
                        @foreach($filiereStats as $fs)
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-sm font-black text-gray-700">{{ $fs['name'] }}</span>
                                <span class="text-sm font-black {{ $fs['rate'] >= 70 ? 'text-emerald-600' : ($fs['rate'] >= 50 ? 'text-amber-600' : 'text-red-500') }}">{{ $fs['rate'] }}%</span>
                            </div>
                            <div class="w-full h-3 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-700 {{ $fs['rate'] >= 70 ? 'bg-emerald-500' : ($fs['rate'] >= 50 ? 'bg-amber-500' : 'bg-red-500') }}"
                                     style="width: {{ $fs['rate'] }}%"></div>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-0.5 font-semibold">{{ $fs['pass'] }} / {{ $fs['total'] }} étudiants admis</p>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Recent Requests -->
                <div class="bg-white rounded-[2.5rem] p-8 shadow-xl border border-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h4 class="text-xl font-black text-upf-blue tracking-tighter italic">📋 Demandes Récentes</h4>
                        <a href="{{ route('admin.requests.index') }}" class="text-xs font-black text-upf-magenta hover:underline">Voir tout →</a>
                    </div>
                    <div class="space-y-3">
                        @forelse($recentRequests as $req)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-2xl hover:bg-blue-50 transition-colors">
                            <div class="w-10 h-10 bg-upf-blue/10 rounded-xl flex items-center justify-center font-black text-upf-blue text-sm flex-shrink-0">
                                {{ strtoupper(substr($req->user->name ?? '?', 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-black text-gray-900 truncate">{{ $req->user->name ?? 'Inconnu' }}</p>
                                <p class="text-xs text-gray-400 font-semibold truncate">{{ $req->type ?? $req->request_type ?? 'Demande' }}</p>
                            </div>
                            <span class="text-[10px] font-black px-2 py-1 rounded-lg flex-shrink-0 
                                {{ ($req->status ?? '') === 'approved' ? 'bg-emerald-100 text-emerald-700' : 
                                   (($req->status ?? '') === 'rejected' ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-700') }}">
                                {{ ucfirst($req->status ?? 'pending') }}
                            </span>
                        </div>
                        @empty
                        <p class="text-gray-400 text-sm italic text-center py-6">Aucune demande récente</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>


    <!-- Chart.js Integration -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Grade Distribution Chart
            const ctxGrade = document.getElementById('gradeChart').getContext('2d');
            const gradeData = @json($gradeDistribution);
            
            new Chart(ctxGrade, {
                type: 'bar',
                data: {
                    labels: gradeData.map(d => d.grade + '/20'),
                    datasets: [{
                        label: 'Students',
                        data: gradeData.map(d => d.count),
                        backgroundColor: '#003399',
                        borderRadius: 12,
                        barThickness: 32
                    }]
                },
                options: {
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { display: false } },
                        x: { grid: { display: false } }
                    }
                }
            });

            // Absence Trends Chart
            const ctxAbsence = document.getElementById('absenceChart').getContext('2d');
            const absenceData = @json($absencesByMonth);
            const monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            
            new Chart(ctxAbsence, {
                type: 'line',
                data: {
                    labels: absenceData.map(d => monthLabels[d.month - 1]),
                    datasets: [{
                        label: 'Absences',
                        data: absenceData.map(d => d.count),
                        borderColor: '#B00D5D',
                        backgroundColor: 'rgba(176, 13, 93, 0.1)',
                        borderWidth: 4,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 6,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#B00D5D',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { borderDash: [5, 5] } },
                        x: { grid: { display: false } }
                    }
                }
            });
        });
    </script>
</x-app-layout>
