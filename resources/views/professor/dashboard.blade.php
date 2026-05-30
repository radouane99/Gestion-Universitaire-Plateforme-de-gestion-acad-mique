<x-app-layout>
    <x-slot name="header">
        @php
            $hour = now()->hour;
            $greeting = ($hour >= 18) ? __('Bonsoir') : __('Bonjour');
            $emoji = ($hour >= 18) ? '🌙' : '☀️';
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
                    <span>{{ __('Tableau de bord Enseignant') }}</span>
                </p>
            </div>
            <!-- Premium Active Role Badge -->
            <div class="flex items-center gap-3 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 shadow-sm px-4 py-2.5 rounded-2xl">
                <span class="w-2.5 h-2.5 rounded-full bg-upf-magenta animate-pulse"></span>
                <div class="text-left">
                    <p class="text-[9px] uppercase font-black text-slate-400 dark:text-slate-500 tracking-wider leading-none">{{ __('Rôle Académique') }}</p>
                    <p class="text-xs font-bold text-slate-700 dark:text-slate-300 mt-1 leading-none">{{ __('Professeur') }}</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-10 bg-[#F8FAFC] dark:bg-[#020617] transition-colors duration-300">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">
            
            <!-- Hero / Welcome Banner -->
            <div class="bg-gradient-to-br from-upf-blue via-upf-navy to-black dark:from-slate-900 dark:via-slate-950 dark:to-black rounded-[3rem] p-10 lg:p-12 text-white shadow-2xl relative overflow-hidden border border-white/5">
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
                    <div class="space-y-2">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-pink-500/20 text-pink-400 text-[10px] font-black uppercase tracking-widest w-fit">
                            {{ __('Espace Pédagogique') }}
                        </span>
                        <h2 class="text-4xl font-black italic tracking-tighter">{{ __('Portail Enseignant') }} 📚</h2>
                        <p class="text-blue-100/70 dark:text-slate-400 text-sm max-w-xl">
                            {{ __('Bon retour parmi nous. Pilotez vos enseignements avec précision et accompagnez vos étudiants vers l\'excellence.') }}
                        </p>
                    </div>
                    <div class="shrink-0 flex gap-4">
                        <div class="text-center bg-white/10 dark:bg-white/5 backdrop-blur-xl px-8 py-5 rounded-[2rem] border border-white/10 dark:border-white/5 shadow-inner hover:scale-105 transition-transform duration-300">
                            <p class="text-4xl font-black text-white tracking-tighter">{{ $schedules->pluck('group_id')->unique()->count() ?? 0 }}</p>
                            <p class="text-[9px] uppercase font-black tracking-widest text-blue-200 dark:text-slate-400 mt-1 italic">{{ __('Groupes Assignés') }}</p>
                        </div>
                    </div>
                </div>
                <div class="absolute -bottom-48 -left-48 w-[500px] h-[500px] bg-upf-magenta/10 dark:bg-pink-500/5 rounded-full blur-[100px] pointer-events-none"></div>
                <div class="absolute -top-48 -right-48 w-[400px] h-[400px] bg-blue-500/10 rounded-full blur-[80px] pointer-events-none"></div>
            </div>

            <!-- Dashboard Stats Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Success Rate (Glassmorphism circle / premium layout) -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 shadow-sm border border-slate-100 dark:border-slate-800 flex flex-col items-center justify-center text-center relative overflow-hidden group hover:border-emerald-500/50 transition-all duration-300">
                    <div class="w-20 h-20 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-500 rounded-full flex items-center justify-center mb-6 group-hover:bg-emerald-500 group-hover:text-white transition-all duration-500 shadow-inner">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                    <h3 class="text-slate-400 dark:text-slate-500 font-bold uppercase tracking-widest text-[10px] mb-2">{{ __('Taux de Réussite Global') }}</h3>
                    <div class="text-5xl font-black {{ ($successRate ?? 0) >= 50 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }} tracking-tighter">
                        {{ $successRate ?? 0 }}%
                    </div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-4 italic font-medium max-w-[240px] leading-snug">
                        {{ __('Pourcentage d\'étudiants ayant obtenu la moyenne dans vos modules.') }}
                    </p>
                </div>

                <!-- Top Students -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 shadow-sm border border-slate-100 dark:border-slate-800 relative overflow-hidden transition-colors">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-black text-slate-900 dark:text-white text-base italic tracking-tight">{{ __('Top Étudiants') }} 🏆</h3>
                        <span class="text-[9px] font-black text-amber-500 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/30 px-2.5 py-1 rounded-lg uppercase tracking-wider">{{ __('Vos Modules') }}</span>
                    </div>
                    <div class="space-y-4">
                        @forelse($topStudents ?? [] as $index => $student)
                        <div class="flex items-center justify-between p-3 rounded-2xl border transition-all {{ $index === 0 ? 'bg-amber-50/50 dark:bg-amber-950/20 border-amber-100 dark:border-amber-900/30' : 'bg-slate-50/50 dark:bg-slate-950/40 border-slate-100/50 dark:border-slate-800' }}">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center font-black text-xs {{ $index === 0 ? 'bg-amber-500 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-650 dark:text-slate-400' }}">
                                    #{{ $index + 1 }}
                                </div>
                                <span class="font-bold text-slate-800 dark:text-slate-205 text-sm">{{ $student->user->name }}</span>
                            </div>
                            <span class="font-black {{ $index === 0 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-900 dark:text-white' }} text-sm">{{ number_format($student->prof_avg, 2) }}</span>
                        </div>
                        @empty
                        <div class="text-center py-10 text-slate-400 dark:text-slate-500 italic text-xs font-semibold">
                            {{ __('Pas encore de notes enregistrées.') }}
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Frequent Absentees -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 shadow-sm border border-slate-100 dark:border-slate-800 relative overflow-hidden transition-colors">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-black text-slate-900 dark:text-white text-base italic tracking-tight">{{ __('Absents Fréquents') }} ⚠️</h3>
                        <span class="text-[9px] font-black text-rose-500 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/30 px-2.5 py-1 rounded-lg uppercase tracking-wider">{{ __('Non Justifiés') }}</span>
                    </div>
                    <div class="space-y-4">
                        @forelse($frequentAbsentees ?? [] as $student)
                        <div class="flex items-center justify-between p-3 rounded-2xl bg-rose-50/50 dark:bg-rose-950/20 border border-rose-100/60 dark:border-rose-900/30 transition-all">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-rose-200 dark:bg-rose-900/40 text-rose-700 dark:text-rose-400 flex items-center justify-center font-black text-xs">
                                    {{ substr($student->user->name, 0, 1) }}
                                </div>
                                <span class="font-bold text-rose-900 dark:text-rose-300 text-sm truncate max-w-[110px]">{{ $student->user->name }}</span>
                            </div>
                            <span class="font-black text-rose-600 dark:text-rose-400 text-xs bg-white dark:bg-slate-900 border border-rose-100 dark:border-slate-800 px-2 py-1 rounded-lg">
                                {{ $student->prof_absences }} {{ __('absences') }}
                            </span>
                        </div>
                        @empty
                        <div class="text-center py-10 text-slate-400 dark:text-slate-500 italic text-xs font-semibold">
                            {{ __('Aucune absence injustifiée. Excellent !') }}
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Quick Actions Section -->
            <div class="space-y-5">
                <h3 class="font-black text-2xl text-slate-850 dark:text-white pl-4 italic tracking-tight">{{ __('Outils Pédagogiques') }}</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
                    
                    <!-- EDT (Planning) -->
                    <a href="{{ route('professor.schedule') }}" class="group bg-white dark:bg-slate-900 p-6 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-800 hover:shadow-lg hover:border-amber-500/50 transition-all duration-300 transform hover:-translate-y-1">
                        <div class="w-12 h-12 bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-amber-500 group-hover:text-white transition-colors shadow-inner">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <h4 class="text-base font-extrabold text-slate-900 dark:text-white mb-1 group-hover:text-amber-500 transition-colors">{{ __('Planning') }}</h4>
                        <p class="text-[11px] text-slate-400 dark:text-slate-550">{{ __('Emploi du temps') }}</p>
                    </a>

                    <!-- Grades -->
                    <a href="{{ route('professor.grades.index') }}" class="group bg-white dark:bg-slate-900 p-6 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-800 hover:shadow-lg hover:border-upf-blue/50 transition-all duration-300 transform hover:-translate-y-1">
                        <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-950/40 text-upf-blue dark:text-blue-400 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-upf-blue group-hover:text-white transition-colors shadow-inner">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h4 class="text-base font-extrabold text-slate-900 dark:text-white mb-1 group-hover:text-upf-blue transition-colors">{{ __('Notes') }}</h4>
                        <p class="text-[11px] text-slate-400 dark:text-slate-550">{{ __('Saisie et validation') }}</p>
                    </a>

                    <!-- Attendance -->
                    <a href="{{ route('professor.absences.index') }}" class="group bg-white dark:bg-slate-900 p-6 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-800 hover:shadow-lg hover:border-emerald-500/50 transition-all duration-300 transform hover:-translate-y-1">
                        <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-emerald-600 group-hover:text-white transition-colors shadow-inner">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h4 class="text-base font-extrabold text-slate-900 dark:text-white mb-1 group-hover:text-emerald-600 transition-colors">{{ __('Absences') }}</h4>
                        <p class="text-[11px] text-slate-400 dark:text-slate-550">{{ __('Appels et feuilles') }}</p>
                    </a>

                    <!-- Classroom -->
                    <a href="{{ route('classroom.index') }}" class="group bg-white dark:bg-slate-900 p-6 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-800 hover:shadow-lg hover:border-orange-500/50 transition-all duration-300 transform hover:-translate-y-1">
                        <div class="w-12 h-12 bg-orange-50 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-orange-600 group-hover:text-white transition-colors shadow-inner">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                        </div>
                        <h4 class="text-base font-extrabold text-slate-900 dark:text-white mb-1 group-hover:text-orange-500 transition-colors">{{ __('Classroom') }}</h4>
                        <p class="text-[11px] text-slate-400 dark:text-slate-550">{{ __('Supports et annonces') }}</p>
                    </a>

                    <!-- Reservations -->
                    <a href="{{ route('professor.reservations.index') }}" class="group bg-white dark:bg-slate-900 p-6 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-800 hover:shadow-lg hover:border-rose-500/50 transition-all duration-300 transform hover:-translate-y-1">
                        <div class="w-12 h-12 bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-rose-600 group-hover:text-white transition-colors shadow-inner">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <h4 class="text-base font-extrabold text-slate-900 dark:text-white mb-1 group-hover:text-rose-600 transition-colors">{{ __('Salles') }}</h4>
                        <p class="text-[11px] text-slate-400 dark:text-slate-550">{{ __('Réservations ponctuelles') }}</p>
                    </a>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
