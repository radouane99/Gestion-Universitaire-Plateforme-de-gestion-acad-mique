<x-app-layout>
    <x-slot name="header">
        <x-page-header 
            title="{{ __('Mon Espace Étudiant') }}" 
            subtitle="{{ now()->format('l d F Y') }}"
            icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v7l-9-5V9l9 5z"></path></svg>'
        >
            <x-slot name="actions">
                <div class="flex gap-2">
                    @if(isset($pendingRetakes) && $pendingRetakes > 0)
                        <a href="{{ route('student.retake.index') }}" class="flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-600 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-emerald-100 transition-all">
                            🎓 {{ $pendingRetakes }} {{ __('rattrapage(s) éligible(s)') }}
                        </a>
                    @endif
                    @if(isset($pendingReclamations) && $pendingReclamations > 0)
                        <a href="{{ route('student.reclamations.index') }}" class="flex items-center gap-2 bg-blue-50 border border-blue-200 text-blue-600 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-blue-100 transition-all">
                            💬 {{ $pendingReclamations }} {{ __('réclamation(s) en cours') }}
                        </a>
                    @endif
                    @if($unjustified > 0)
                        <a href="{{ route('student.absences') }}" class="flex items-center gap-2 bg-rose-50 border border-rose-200 text-rose-600 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest animate-pulse hover:bg-rose-100 transition-all">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                            {{ $unjustified }} {{ __('absence(s) non justifiée(s)') }}
                        </a>
                    @endif
                </div>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-6 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- ===================== HERO BANNER ===================== --}}
            <div class="bg-gradient-to-br from-upf-blue via-upf-navy to-black rounded-[2.5rem] p-10 text-white shadow-sm relative overflow-hidden">
                <div class="relative z-10 grid grid-cols-1 md:grid-cols-3 gap-8 items-center">

                    {{-- Welcome Text --}}
                    <div class="md:col-span-2 space-y-3">
                        <p class="text-[11px] font-black uppercase tracking-[0.3em] text-upf-magenta">{{ __('Bienvenue sur votre espace') }}</p>
                        <h2 class="text-4xl font-black tracking-tighter leading-none">{{ Auth::user()->name }} 🎓</h2>
                        <div class="flex flex-wrap gap-3 pt-2">
                            @if(Auth::user()->student && Auth::user()->student->group)
                                <span class="bg-white/15 backdrop-blur border border-white/20 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest">
                                    📚 {{ Auth::user()->student->group->name }}
                                </span>
                                @if(Auth::user()->student->group->filiere)
                                <span class="bg-upf-magenta/30 backdrop-blur border border-upf-magenta/50 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest">
                                    🏛️ {{ Auth::user()->student->group->filiere->name }}
                                </span>
                                @endif
                            @endif
                            <span class="bg-white/15 backdrop-blur border border-white/20 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest">
                                📅 {{ __('Année') }} 2024/2025
                            </span>
                        </div>
                    </div>

                    {{-- GPA Circle --}}
                    <div class="flex justify-center md:justify-end">
                        <div class="relative w-40 h-40">
                            <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36">
                                <circle cx="18" cy="18" r="15.9" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="2.5"/>
                                <circle cx="18" cy="18" r="15.9" fill="none"
                                    stroke="{{ $gpa >= 14 ? '#10b981' : ($gpa >= 10 ? '#f59e0b' : '#f43f5e') }}"
                                    stroke-width="2.5"
                                    stroke-dasharray="{{ $gpaPercent }}, 100"
                                    stroke-linecap="round"
                                    id="gpaArc"
                                    style="transition: stroke-dasharray 1.5s ease-in-out;"
                                />
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                                <p class="text-3xl font-black leading-none">{{ number_format($gpa, 1) }}</p>
                                <p class="text-[9px] font-black uppercase tracking-widest text-blue-200 mt-1">/20 — {{ __('GPA') }}</p>
                                @if($gpa >= 14)
                                    <span class="text-emerald-400 text-lg mt-1">★</span>
                                @elseif($gpa >= 10)
                                    <span class="text-amber-400 text-lg mt-1">◎</span>
                                @else
                                    <span class="text-rose-400 text-lg mt-1">⚠</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Decorative blobs --}}
                <div class="absolute -top-20 -right-20 w-72 h-72 bg-upf-magenta/20 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-16 left-16 w-48 h-48 bg-blue-400/10 rounded-full blur-2xl pointer-events-none"></div>
            </div>

            {{-- ===================== NEXT CLASS WIDGET ===================== --}}
            @if($nextClass)
            <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-[2rem] p-6 text-white shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-5">
                    <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center shadow-inner">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-emerald-100">🔔 {{ __('Prochain cours') }}</p>
                        <p class="text-xl font-black mt-0.5">{{ $nextClass->module->name }}</p>
                        <p class="text-emerald-100 text-sm font-bold">
                            {{ date('H:i', strtotime($nextClass->start_time)) }} — {{ $nextClass->room->name ?? __('Salle à définir') }}
                            @if($nextClass->professor && $nextClass->professor->user)
                                · {{ $nextClass->professor->user->name }}
                            @endif
                        </p>
                    </div>
                </div>
                <a href="{{ route('calendar') }}" class="bg-white/20 hover:bg-white/40 transition-all px-5 py-3 rounded-xl text-sm font-black uppercase tracking-widest shadow-sm">
                    {{ __('Voir Planning') }} →
                </a>
            </div>
            @endif

            {{-- ===================== MAIN GRID ===================== --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- LEFT COLUMN : Grades + Radar --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Grades Card --}}
                    <x-card class="p-0">
                        <div class="flex items-center justify-between px-8 py-6 border-b border-gray-100 bg-gray-50/60">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center shadow-inner">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <h4 class="text-lg font-black text-gray-900 tracking-tight">{{ __('Résultats Académiques') }}</h4>
                            </div>
                            <a href="{{ route('student.grades') }}" class="text-xs font-black text-upf-blue hover:text-upf-navy uppercase tracking-widest flex items-center gap-1">
                                {{ __('Rapport Complet') }} →
                            </a>
                        </div>

                        @if($grades->isEmpty())
                            <div class="flex flex-col items-center justify-center py-16 text-center px-8">
                                <div class="text-5xl mb-4">📊</div>
                                <p class="text-gray-400 font-bold text-sm italic">{{ __('Vos notes s\'afficheront ici au fur et à mesure de leur publication.') }}</p>
                            </div>
                        @else
                            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($grades->take(6) as $grade)
                                <div class="group p-5 bg-gray-50 rounded-2xl border border-transparent hover:border-upf-blue hover:bg-white hover:shadow-md transition-all duration-300 cursor-default">
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="flex-1 min-w-0 pr-3">
                                            <p class="font-extrabold text-gray-900 text-sm truncate group-hover:text-upf-blue transition-colors">{{ $grade->module->name }}</p>
                                            <div class="flex gap-3 mt-1 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                                <span>{{ __('CC1') }}: {{ $grade->cc1 ?? '—' }}</span>
                                                <span>{{ __('CC2') }}: {{ $grade->cc2 ?? '—' }}</span>
                                                <span>{{ __('Examen') }}: {{ $grade->exam ?? '—' }}</span>
                                            </div>
                                        </div>
                                        <div class="text-right flex-shrink-0">
                                            <p class="text-2xl font-black leading-none {{ ($grade->final_grade ?? 0) >= 10 ? 'text-emerald-600' : 'text-rose-600' }}">
                                                {{ $grade->final_grade ?? '—' }}
                                            </p>
                                            <p class="text-[8px] uppercase font-black text-gray-400">/ 20</p>
                                        </div>
                                    </div>
                                    @if($grade->final_grade)
                                    <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                        <div class="h-1.5 rounded-full transition-all duration-700 {{ $grade->final_grade >= 10 ? 'bg-emerald-500' : 'bg-rose-500' }}"
                                            style="width: {{ ($grade->final_grade / 20) * 100 }}%">
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        @endif
                    </x-card>

                    {{-- Radar Chart --}}
                    @if(!$grades->isEmpty())
                    <x-card class="p-8">
                        <div class="flex items-center justify-between mb-6">
                            <h4 class="text-lg font-black text-upf-blue italic tracking-tighter">{{ __('Radar de Performance') }}</h4>
                            <span class="text-xs text-gray-400 font-bold uppercase tracking-widest">{{ __('Comparatif par module') }}</span>
                        </div>
                        <div class="relative w-full" style="height: 280px;">
                            <canvas id="performanceRadar"></canvas>
                        </div>
                    </x-card>
                    @endif

                    {{-- Documents Banner --}}
                    <div class="bg-upf-navy rounded-[2rem] p-8 text-white shadow-sm relative overflow-hidden group">
                        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                            <div>
                                <h3 class="text-2xl font-black mb-1">📄 {{ __('Documents Officiels') }}</h3>
                                <p class="text-blue-200 text-sm opacity-80">{{ __('Téléchargez vos attestations et relevés de notes validés par l\'administration.') }}</p>
                            </div>
                            <a href="{{ route('student.requests.create') }}" class="px-6 py-3 bg-white text-upf-navy font-black rounded-2xl hover:bg-upf-magenta hover:text-white transition-all duration-300 shadow-sm group-hover:scale-105 transform whitespace-nowrap text-sm uppercase tracking-widest">
                                {{ __('Gérer mes demandes') }}
                            </a>
                        </div>
                        <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-upf-magenta rounded-full opacity-20 blur-2xl group-hover:opacity-40 transition-opacity pointer-events-none"></div>
                    </div>
                </div>

                {{-- RIGHT COLUMN --}}
                <div class="space-y-6">

                    {{-- Today Schedule --}}
                    <x-card class="p-0">
                        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                            <h3 class="font-black text-gray-900 flex items-center gap-2 text-sm">
                                <svg class="w-5 h-5 text-upf-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                {{ __('Emploi du temps — Aujourd\'hui') }}
                            </h3>
                            <span class="text-[10px] font-black text-gray-400 uppercase">{{ now()->format('D') }}</span>
                        </div>
                        <div class="p-5 space-y-3">
                            @php
                                $currentDay = (int) date('N');
                                $todaySlots = $schedule->where('day_of_week', $currentDay)->sortBy('start_time');
                            @endphp
                            @forelse($todaySlots as $session)
                            <div class="flex items-center p-4 bg-gray-50 rounded-2xl border-l-4 border-upf-blue hover:bg-white hover:shadow-sm transition-all duration-300 group">
                                <div class="mr-4 min-w-max">
                                    <p class="text-[11px] font-black text-upf-blue uppercase tracking-tighter">{{ date('H:i', strtotime($session->start_time)) }}</p>
                                    <p class="text-[9px] text-gray-400 font-bold">{{ date('H:i', strtotime($session->end_time)) }}</p>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-extrabold text-gray-900 text-sm truncate group-hover:text-upf-blue transition-colors">{{ $session->module->name }}</p>
                                    <p class="text-[10px] text-gray-400 uppercase font-bold truncate">{{ $session->room->name ?? '—' }}</p>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-10">
                                <div class="text-4xl mb-2">🎈</div>
                                <p class="text-xs font-bold text-gray-400 uppercase italic">{{ __('Pas de cours aujourd\'hui') }}</p>
                                <p class="text-[10px] text-gray-300 mt-1">{{ __('Profitez de votre journée !') }}</p>
                            </div>
                            @endforelse
                        </div>
                    </x-card>

                    {{-- Absence Widget --}}
                    <x-card class="p-7 border-2 {{ $unjustified > 0 ? 'border-rose-400' : 'border-gray-100' }} transition-all">
                        <h4 class="text-[10px] uppercase font-black {{ $unjustified > 0 ? 'text-rose-500' : 'text-gray-400' }} tracking-widest mb-5">{{ __('Suivi des Absences') }}</h4>
                        <div class="flex items-center justify-between mb-6">
                            <div class="text-center">
                                <p class="text-4xl font-black text-gray-900">{{ $absences->count() }}</p>
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1">{{ __('Total') }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-4xl font-black text-emerald-600">{{ $absences->where('is_justified', true)->count() }}</p>
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1">{{ __('Justifiées') }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-4xl font-black text-rose-600">{{ $unjustified }}</p>
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1">{{ __('Injustifiées') }}</p>
                            </div>
                        </div>
                        @if($unjustified > 0)
                            <div class="mb-4 p-3 bg-rose-50 border border-rose-100 rounded-xl text-xs font-bold text-rose-600 flex items-center gap-2">
                                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                {{ __('Action requise — Veuillez déposer un justificatif') }}
                            </div>
                        @endif
                        <a href="{{ route('student.absences') }}" class="block w-full py-3 {{ $unjustified > 0 ? 'bg-rose-600 hover:bg-rose-700 text-white' : 'bg-gray-100 text-gray-900 hover:bg-upf-blue hover:text-white' }} rounded-xl font-bold text-center text-xs uppercase tracking-widest transition-all duration-300">
                            {{ __('Gérer mes justificatifs') }} →
                        </a>
                    </x-card>

                    {{-- Approved Documents --}}
                    @php $approvedReqs = $requests->where('status', 'approved'); @endphp
                    @if($approvedReqs->count() > 0)
                    <x-card class="p-7">
                        <h4 class="text-[10px] uppercase font-black text-gray-400 tracking-widest mb-5">{{ __('Mes Documents Validés') }}</h4>
                        <div class="space-y-3">
                            @foreach($approvedReqs->take(3) as $req)
                            <div class="flex items-center justify-between p-3 bg-emerald-50 rounded-xl border border-emerald-100 hover:shadow-sm transition-all group">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0 shadow-inner">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-black text-emerald-700 text-xs truncate">{{ __($req->type) }}</p>
                                        <p class="text-[9px] text-gray-400 uppercase font-bold">{{ $req->updated_at->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                                <a href="{{ route('documents.download', $req) }}" target="_blank" class="px-3 py-1.5 bg-emerald-100 text-emerald-700 hover:bg-emerald-600 hover:text-white font-black rounded-lg text-[9px] uppercase transition-all flex-shrink-0 ml-2 shadow-sm">{{ __('PDF') }}</a>
                            </div>
                            @endforeach
                        </div>
                    </x-card>
                    @endif

                </div>
            </div>

        </div>
    </div>

    {{-- GPA Arc Animation --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Animate GPA arc
            const arc = document.getElementById('gpaArc');
            if (arc) {
                const target = arc.getAttribute('stroke-dasharray').split(',')[0];
                arc.setAttribute('stroke-dasharray', '0, 100');
                setTimeout(() => {
                    arc.setAttribute('stroke-dasharray', target + ', 100');
                }, 300);
            }
        });
    </script>

    @if(!$grades->isEmpty())
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('performanceRadar').getContext('2d');
            const data = @json($grades);
            const labels = data.map(g => g.module.name);
            const marks  = data.map(g => g.final_grade || 0);

            new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: '{{ __('Ma Note') }}',
                        data: marks,
                        backgroundColor: 'rgba(0, 51, 153, 0.15)',
                        borderColor: '#003399',
                        pointBackgroundColor: '#003399',
                        pointBorderColor: '#fff',
                        borderWidth: 2.5
                    }, {
                        label: '{{ __('Seuil de Validation (10/20)') }}',
                        data: Array(labels.length).fill(10),
                        backgroundColor: 'rgba(176, 13, 93, 0.05)',
                        borderColor: 'rgba(176, 13, 93, 0.6)',
                        borderDash: [6, 4],
                        pointRadius: 0,
                        borderWidth: 1.5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 1200, easing: 'easeInOutQuart' },
                    scales: {
                        r: {
                            angleLines: { display: true, color: 'rgba(0,0,0,0.06)' },
                            suggestedMin: 0,
                            suggestedMax: 20,
                            ticks: { stepSize: 5, font: { size: 10, weight: 'bold' } },
                            grid: { color: 'rgba(0,0,0,0.06)' }
                        }
                    },
                    plugins: {
                        legend: { position: 'top', labels: { font: { size: 11, weight: 'bold' }, boxWidth: 12 } }
                    }
                }
            });
        });
    </script>
    @endif
</x-app-layout>
