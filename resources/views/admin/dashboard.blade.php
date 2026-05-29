<x-app-layout>
    <x-slot name="header">
        @php
            $hour = now()->hour;
            $greeting = ($hour >= 18) ? __('Bonsoir') : __('Bonjour');
            $emoji = ($hour >= 18) ? '🌙' : '☀️';
            $academicYear = \App\Models\Setting::first()->academic_year ?? '2025-2026';
            $todayDate = \Carbon\Carbon::now()->translatedFormat('l j F Y');
        @endphp
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="text-2xl">{{ $emoji }}</span>
                    <h2 class="font-black text-2xl text-slate-900 dark:text-white leading-tight tracking-tight italic">
                        {{ $greeting }}, {{ Auth::user()->name }}
                    </h2>
                </div>
                <p class="text-xs text-slate-400 dark:text-slate-500 font-semibold uppercase tracking-wider flex items-center gap-1.5">
                    <span>📅 {{ $todayDate }}</span>
                    <span class="text-slate-300 dark:text-slate-700">•</span>
                    <span>{{ __('Centre de Contrôle Administrateur') }}</span>
                </p>
            </div>
            <!-- Premium Active Year Badge -->
            <div class="flex items-center gap-3 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 shadow-sm px-4 py-2.5 rounded-2xl">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <div class="text-left">
                    <p class="text-[9px] uppercase font-black text-slate-400 dark:text-slate-500 tracking-wider leading-none">{{ __('Année Académique') }}</p>
                    <p class="text-xs font-bold text-slate-700 dark:text-slate-350 mt-1 leading-none">{{ $academicYear }}</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-10 bg-[#F8FAFC] dark:bg-[#020617] transition-colors duration-300">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">
            
            <!-- Hero Stats Grid -->
            <div class="bg-gradient-to-br from-upf-blue via-upf-navy to-black dark:from-slate-900 dark:via-slate-950 dark:to-black rounded-[3rem] p-10 lg:p-12 text-white shadow-2xl relative overflow-hidden border border-white/5">
                <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- Status Widget -->
                    <div class="space-y-2 flex flex-col justify-center">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-400 text-[10px] font-black uppercase tracking-widest w-fit">
                            <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-ping"></span>
                            {{ __('Opérationnel') }}
                        </span>
                        <h3 class="text-4xl font-black italic tracking-tighter">{{ __('UPF Portail') }}</h3>
                        <p class="text-blue-200/60 dark:text-slate-400 text-[10px] uppercase font-bold tracking-widest mt-1">{{ __('Dernière synchro : À l\'instant') }}</p>
                    </div>

                    <!-- Stat Card: Students -->
                    <div class="bg-white/10 dark:bg-white/5 backdrop-blur-xl p-8 rounded-[2rem] border border-white/10 dark:border-white/5 flex flex-col justify-between group hover:scale-[1.03] hover:bg-white/15 dark:hover:bg-white/10 transition-all duration-300 cursor-pointer shadow-md">
                        <div class="flex justify-between items-start">
                            <p class="text-5xl font-black tracking-tighter">{{ $stats['students_count'] ?? 0 }}</p>
                            <span class="text-2xl bg-white/10 w-10 h-10 rounded-xl flex items-center justify-center group-hover:rotate-6 transition-transform">🎒</span>
                        </div>
                        <div class="mt-6">
                            <p class="text-[10px] uppercase font-black tracking-widest text-blue-200 dark:text-slate-350 italic">{{ __('Total Étudiants') }}</p>
                            <p class="text-[9px] text-emerald-400 font-bold mt-1">📈 +4 {{ __('ce trimestre') }}</p>
                        </div>
                    </div>

                    <!-- Stat Card: Professors -->
                    <div class="bg-white/10 dark:bg-white/5 backdrop-blur-xl p-8 rounded-[2rem] border border-white/10 dark:border-white/5 flex flex-col justify-between group hover:scale-[1.03] hover:bg-white/15 dark:hover:bg-white/10 transition-all duration-300 cursor-pointer shadow-md">
                        <div class="flex justify-between items-start">
                            <p class="text-5xl font-black tracking-tighter">{{ $stats['professors_count'] ?? 0 }}</p>
                            <span class="text-2xl bg-white/10 w-10 h-10 rounded-xl flex items-center justify-center group-hover:rotate-6 transition-transform">👤</span>
                        </div>
                        <div class="mt-6">
                            <p class="text-[10px] uppercase font-black tracking-widest text-blue-200 dark:text-slate-350 italic">{{ __('Corps Enseignant') }}</p>
                            <p class="text-[9px] text-emerald-400 font-bold mt-1">✔ 100% {{ __('membres actifs') }}</p>
                        </div>
                    </div>

                    <!-- Stat Card: Absences -->
                    <div class="bg-white/10 dark:bg-white/5 backdrop-blur-xl p-8 rounded-[2rem] border border-white/10 dark:border-white/5 flex flex-col justify-between group hover:scale-[1.03] hover:bg-white/15 dark:hover:bg-white/10 transition-all duration-300 cursor-pointer shadow-md">
                        <div class="flex justify-between items-start">
                            <p class="text-5xl font-black tracking-tighter">{{ $stats['absences_total'] ?? 0 }}</p>
                            <span class="text-2xl bg-white/10 w-10 h-10 rounded-xl flex items-center justify-center group-hover:rotate-6 transition-transform">🚨</span>
                        </div>
                        <div class="mt-6">
                            <p class="text-[10px] uppercase font-black tracking-widest text-upf-magenta dark:text-pink-400 mt-0.5 italic">{{ __('Absences Cumulées') }}</p>
                            <p class="text-[9px] text-rose-400 font-bold mt-1">💡 {{ __('Heures d\'absence') }}</p>
                        </div>
                    </div>
                </div>
                <div class="absolute -bottom-48 -left-48 w-[500px] h-[500px] bg-upf-magenta/10 dark:bg-pink-500/5 rounded-full blur-[100px]"></div>
            </div>

            <!-- Analytics Double Grid (Dashboard Main View) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Grade Distribution Chart -->
                <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-3xl p-8 lg:p-10 shadow-sm border border-slate-100 dark:border-slate-800 transition-colors">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-10">
                        <div>
                            <h4 class="text-2xl font-black text-slate-850 dark:text-white tracking-tighter italic">{{ __('Performance Académique') }}</h4>
                            <p class="text-xs text-slate-400 dark:text-slate-500 font-bold uppercase tracking-widest mt-1">{{ __('Répartition des notes par module') }}</p>
                        </div>
                        <div class="bg-indigo-50/50 dark:bg-indigo-950/30 px-4 py-2 rounded-xl text-xs font-black text-upf-blue dark:text-blue-400 shadow-inner">
                             {{ __('Moyenne Générale') }}: <span class="text-sm ml-1">{{ number_format($stats['grades_avg'] ?? 0, 2) }}/20</span>
                        </div>
                    </div>
                    <div class="relative w-full overflow-hidden">
                        <canvas id="gradeChart" height="140"></canvas>
                    </div>
                </div>

                <!-- Action Hub (Refactored visual grid of feature tiles) -->
                <div class="space-y-6">
                    <!-- Global Schedule (Pink Card) -->
                    <div class="bg-gradient-to-br from-upf-magenta to-pink-600 dark:from-pink-800 dark:to-rose-950 rounded-3xl p-8 text-white shadow-lg relative overflow-hidden group">
                        <div class="relative z-10 flex flex-col justify-between h-full">
                            <div>
                                <h4 class="text-xl font-black italic tracking-tighter leading-tight">{!! __('Emploi du Temps Global') !!}</h4>
                                <p class="text-xs text-pink-100/70 mt-1">{{ __('Planifiez et organisez les séances académiques de l\'UPF.') }}</p>
                            </div>
                            <a href="{{ route('admin.schedules.index') }}" class="mt-6 inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-widest bg-white/20 hover:bg-white/30 px-5 py-3 rounded-xl transition-all w-fit shadow-md">
                                📅 {{ __('Gérer le calendrier') }} →
                            </a>
                        </div>
                        <div class="absolute -right-5 -bottom-5 opacity-10 group-hover:scale-110 transition-transform">
                            <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path></svg>
                        </div>
                    </div>

                    <!-- Pilotage & Modules Avancés (Premium Action Tiles Grid) -->
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 lg:p-8 shadow-sm border border-slate-100 dark:border-slate-800 space-y-5 transition-colors">
                        <div class="flex items-center justify-between">
                            <h4 class="text-lg font-black text-slate-850 dark:text-white italic tracking-tighter">{{ __('Pilotage & Modules Avancés') }}</h4>
                            <span class="text-xs font-black text-upf-blue dark:text-blue-400 bg-indigo-50 dark:bg-indigo-950/40 px-2 py-0.5 rounded-lg">7 Modules</span>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <!-- 0. Statistiques Avancées (Cyan Tile) -->
                            <a href="{{ route('admin.analytics.index') }}" class="group flex flex-col justify-between p-4 bg-slate-50/50 dark:bg-slate-950/40 hover:bg-cyan-50/20 dark:hover:bg-slate-800 border border-slate-100/60 dark:border-slate-850 hover:border-cyan-100 dark:hover:border-slate-750 rounded-2xl transition-all duration-300 shadow-sm hover:shadow-md hover:-translate-y-0.5">
                                <div class="w-10 h-10 bg-cyan-100/70 dark:bg-cyan-950/30 text-cyan-600 dark:text-cyan-400 rounded-xl flex items-center justify-center text-lg shadow-inner group-hover:scale-105 transition-transform">
                                    📈
                                </div>
                                <div class="mt-4">
                                    <span class="font-bold text-xs text-slate-800 dark:text-slate-200 block">{{ __('Analytics') }}</span>
                                    <span class="text-[10px] text-slate-400 font-semibold block mt-0.5">{{ __('Statistiques Avancées') }}</span>
                                </div>
                            </a>

                            <!-- 1. Rapports PDF (Violet Tile) -->
                            <a href="{{ route('admin.reports.index') }}" class="group flex flex-col justify-between p-4 bg-slate-50/50 dark:bg-slate-950/40 hover:bg-indigo-50/20 dark:hover:bg-slate-800 border border-slate-100/60 dark:border-slate-850 hover:border-indigo-100 dark:hover:border-slate-750 rounded-2xl transition-all duration-300 shadow-sm hover:shadow-md hover:-translate-y-0.5">
                                <div class="w-10 h-10 bg-violet-100/70 dark:bg-violet-950/30 text-violet-600 dark:text-violet-400 rounded-xl flex items-center justify-center text-lg shadow-inner group-hover:scale-105 transition-transform">
                                    📊
                                </div>
                                <div class="mt-4">
                                    <span class="font-bold text-xs text-slate-800 dark:text-slate-200 block">{{ __('Rapports PDF') }}</span>
                                    <span class="text-[10px] text-slate-400 font-semibold block mt-0.5">{{ __('Générer rapports A4') }}</span>
                                </div>
                            </a>

                            <!-- 2. Étudiants à Risque (Orange Tile) -->
                            <a href="{{ route('admin.students_risk.index') }}" class="group flex flex-col justify-between p-4 bg-slate-50/50 dark:bg-slate-950/40 hover:bg-orange-50/20 dark:hover:bg-slate-800 border border-slate-100/60 dark:border-slate-850 hover:border-orange-100 dark:hover:border-slate-750 rounded-2xl transition-all duration-300 shadow-sm hover:shadow-md hover:-translate-y-0.5">
                                <div class="w-10 h-10 bg-orange-100/70 dark:bg-orange-950/30 text-orange-600 dark:text-orange-400 rounded-xl flex items-center justify-center text-lg shadow-inner group-hover:scale-105 transition-transform">
                                    🚨
                                </div>
                                <div class="mt-4">
                                    <span class="font-bold text-xs text-slate-800 dark:text-slate-200 block">{{ __('Suivi des Risques') }}</span>
                                    <span class="text-[10px] text-slate-400 font-semibold block mt-0.5">{{ __('Conseil & Alertes') }}</span>
                                </div>
                            </a>

                            <!-- 3. Réclamations (Indigo Tile with Pulse Badge) -->
                            <a href="{{ route('admin.reclamations.index') }}" class="group flex flex-col justify-between p-4 bg-slate-50/50 dark:bg-slate-950/40 hover:bg-indigo-50/20 dark:hover:bg-slate-800 border border-slate-100/60 dark:border-slate-850 hover:border-indigo-100 dark:hover:border-slate-750 rounded-2xl transition-all duration-300 shadow-sm hover:shadow-md hover:-translate-y-0.5 relative">
                                @if($stats['pending_reclamations'] > 0)
                                    <span class="absolute top-3 right-3 bg-rose-500 text-white text-[9px] font-black w-4.5 h-4.5 rounded-full flex items-center justify-center animate-pulse">
                                        {{ $stats['pending_reclamations'] }}
                                    </span>
                                @endif
                                <div class="w-10 h-10 bg-indigo-100/70 dark:bg-indigo-950/30 text-indigo-600 dark:text-indigo-400 rounded-xl flex items-center justify-center text-lg shadow-inner group-hover:scale-105 transition-transform">
                                    💬
                                </div>
                                <div class="mt-4">
                                    <span class="font-bold text-xs text-slate-800 dark:text-slate-200 block">{{ __('Réclamations') }}</span>
                                    <span class="text-[10px] text-slate-400 font-semibold block mt-0.5">{{ __('Contestations notes') }}</span>
                                </div>
                            </a>

                            <!-- 4. Justifications (Amber Tile with Badge) -->
                            <a href="{{ route('admin.exam_justifications.index') }}" class="group flex flex-col justify-between p-4 bg-slate-50/50 dark:bg-slate-950/40 hover:bg-amber-50/20 dark:hover:bg-slate-800 border border-slate-100/60 dark:border-slate-850 hover:border-amber-100 dark:hover:border-slate-750 rounded-2xl transition-all duration-300 shadow-sm hover:shadow-md hover:-translate-y-0.5 relative">
                                @if($stats['pending_justifications'] > 0)
                                    <span class="absolute top-3 right-3 bg-amber-500 text-white text-[9px] font-black w-4.5 h-4.5 rounded-full flex items-center justify-center shadow-sm">
                                        {{ $stats['pending_justifications'] }}
                                    </span>
                                @endif
                                <div class="w-10 h-10 bg-amber-100/70 dark:bg-amber-950/30 text-amber-600 dark:text-amber-400 rounded-xl flex items-center justify-center text-lg shadow-inner group-hover:scale-105 transition-transform">
                                    📋
                                </div>
                                <div class="mt-4">
                                    <span class="font-bold text-xs text-slate-800 dark:text-slate-200 block">{{ __('Justifications') }}</span>
                                    <span class="text-[10px] text-slate-400 font-semibold block mt-0.5">{{ __('Absences aux examens') }}</span>
                                </div>
                            </a>

                            <!-- 5. CSV Import (Emerald Tile) -->
                            <a href="{{ route('admin.students.import.show') }}" class="group flex flex-col justify-between p-4 bg-slate-50/50 dark:bg-slate-950/40 hover:bg-emerald-50/20 dark:hover:bg-slate-800 border border-slate-100/60 dark:border-slate-850 hover:border-emerald-100 dark:hover:border-slate-750 rounded-2xl transition-all duration-300 shadow-sm hover:shadow-md hover:-translate-y-0.5">
                                <div class="w-10 h-10 bg-emerald-100/70 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center text-lg shadow-inner group-hover:scale-105 transition-transform">
                                    📥
                                </div>
                                <div class="mt-4">
                                    <span class="font-bold text-xs text-slate-800 dark:text-slate-200 block">{{ __('Import CSV') }}</span>
                                    <span class="text-[10px] text-slate-400 font-semibold block mt-0.5">{{ __('Validation cliente JS') }}</span>
                                </div>
                            </a>

                            <!-- 6. Archiving Rollover (Rose/Danger Tile) -->
                            <a href="{{ route('admin.archiving.index') }}" class="group flex flex-col justify-between p-4 bg-slate-50/50 dark:bg-slate-950/40 hover:bg-rose-50/20 dark:hover:bg-slate-800 border border-slate-100/60 dark:border-slate-850 hover:border-rose-100 dark:hover:border-slate-750 rounded-2xl transition-all duration-300 shadow-sm hover:shadow-md hover:-translate-y-0.5">
                                <div class="w-10 h-10 bg-rose-100/70 dark:bg-rose-950/30 text-rose-650 dark:text-rose-400 rounded-xl flex items-center justify-center text-lg shadow-inner group-hover:scale-105 transition-transform">
                                    🗄️
                                </div>
                                <div class="mt-4">
                                    <span class="font-bold text-xs text-slate-800 dark:text-slate-200 block">{{ __('Archivage') }}</span>
                                    <span class="text-[10px] text-slate-400 font-semibold block mt-0.5">{{ __('Rollover & Clôture') }}</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Absence Trends -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 lg:p-10 shadow-sm border border-slate-100 dark:border-slate-800 transition-colors">
                <div class="flex justify-between items-center mb-10">
                    <div>
                        <h4 class="text-2xl font-black text-slate-850 dark:text-white tracking-tighter italic">{{ __('Prévision des Absences') }}</h4>
                        <p class="text-xs text-slate-400 font-semibold uppercase tracking-widest mt-1">{{ __('Fréquence et évolution mensuelle globale') }}</p>
                    </div>
                    <div class="flex gap-4">
                        <span class="flex items-center gap-2 text-[10px] font-black uppercase text-slate-400 dark:text-slate-500">
                             <span class="w-2.5 h-2.5 rounded-full bg-upf-magenta"></span> {{ __('Absences mensuelles') }}
                        </span>
                    </div>
                </div>
                <div class="relative w-full overflow-hidden">
                    <canvas id="absenceChart" height="100"></canvas>
                </div>
            </div>

            <!-- Filiere Success Rates + Recent Requests -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Filiere Success Rates -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 shadow-sm border border-slate-100 dark:border-slate-800 transition-colors">
                    <h4 class="text-xl font-black text-slate-850 dark:text-white tracking-tighter italic mb-6">📊 {{ __('Taux de Réussite par Filière') }}</h4>
                    <div class="space-y-5">
                        @forelse($filiereStats ?? [] as $fs)
                        <div>
                            <div class="flex justify-between items-center mb-1.5">
                                <span class="text-xs font-black text-slate-700 dark:text-slate-300 uppercase tracking-wider">{{ $fs['name'] }}</span>
                                <span class="text-xs font-black {{ $fs['rate'] >= 70 ? 'text-emerald-600 dark:text-emerald-400' : ($fs['rate'] >= 50 ? 'text-amber-600 dark:text-amber-400' : 'text-rose-600 dark:text-rose-400') }}">{{ $fs['rate'] }}%</span>
                            </div>
                            <div class="w-full h-3 bg-slate-50 dark:bg-slate-950/70 border border-slate-100 dark:border-slate-800 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-700 shadow-inner {{ $fs['rate'] >= 70 ? 'bg-gradient-to-r from-emerald-400 to-emerald-500' : ($fs['rate'] >= 50 ? 'bg-gradient-to-r from-amber-400 to-amber-500' : 'bg-gradient-to-r from-rose-500 to-red-650') }}"
                                     style="width: {{ $fs['rate'] }}%"></div>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1 font-semibold">{{ $fs['pass'] }} / {{ $fs['total'] }} {{ __('étudiants admis avec moyenne >= 10/20') }}</p>
                        </div>
                        @empty
                        <p class="text-slate-400 text-sm italic text-center py-6">{{ __('Aucune filière évaluée') }}</p>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Requests -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 shadow-sm border border-slate-100 dark:border-slate-800 transition-colors">
                    <div class="flex justify-between items-center mb-6">
                        <h4 class="text-xl font-black text-slate-850 dark:text-white tracking-tighter italic">📬 {{ __('Demandes Récentes') }}</h4>
                        <a href="{{ route('admin.requests.index') }}" class="text-xs font-black text-upf-magenta dark:text-pink-400 hover:underline">{{ __('Voir tout') }} →</a>
                    </div>
                    <div class="space-y-4">
                        @forelse($recentRequests ?? [] as $req)
                        <div class="flex items-center gap-3.5 p-3.5 bg-slate-50/50 dark:bg-slate-950/40 hover:bg-indigo-50/20 dark:hover:bg-slate-800/40 border border-slate-100/50 dark:border-slate-850 rounded-2xl transition-colors">
                            <div class="w-10 h-10 bg-upf-blue/10 dark:bg-indigo-950/30 rounded-xl flex items-center justify-center font-black text-upf-blue dark:text-indigo-400 text-sm flex-shrink-0 shadow-inner">
                                {{ strtoupper(substr($req->user->name ?? '?', 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-black text-slate-850 dark:text-white truncate">{{ $req->user->name ?? __('Inconnu') }}</p>
                                <p class="text-[10px] text-slate-400 font-semibold truncate mt-0.5">{{ $req->type ?? $req->request_type ?? __('Demande Administrative') }}</p>
                            </div>
                            <span class="text-[9px] font-black px-2.5 py-1 rounded-lg flex-shrink-0 shadow-sm
                                {{ ($req->status ?? '') === 'approved' ? 'bg-emerald-100 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200/50' : 
                                   (($req->status ?? '') === 'rejected' ? 'bg-rose-100 dark:bg-rose-950/30 text-rose-650 dark:text-rose-400 border border-rose-200/50' : 'bg-amber-100 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 border border-amber-200/50') }}">
                                {{ __(ucfirst($req->status ?? 'pending')) }}
                            </span>
                        </div>
                        @empty
                        <p class="text-slate-400 text-sm italic text-center py-6">{{ __('Aucune demande récente') }}</p>
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
            const ctxGrade = document.getElementById('gradeChart');
            if (ctxGrade) {
                const gradeData = @json($gradeDistribution ?? []);
                if (gradeData.length > 0) {
                    new Chart(ctxGrade.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: gradeData.map(d => d.grade + '/20'),
                            datasets: [{
                                label: '{{ __('Étudiants') }}',
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
                }
            }

            // Absence Trends Chart
            const ctxAbsence = document.getElementById('absenceChart');
            if(ctxAbsence) {
                const absenceData = @json($absencesByMonth ?? []);
                if(absenceData.length > 0) {
                    const monthLabels = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
                    new Chart(ctxAbsence.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: absenceData.map(d => monthLabels[d.month - 1]),
                            datasets: [{
                                label: '{{ __('Absences') }}',
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
                }
            }
        });
    </script>
</x-app-layout>
