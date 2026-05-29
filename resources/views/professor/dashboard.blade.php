<x-app-layout>
    <x-slot name="header">
        <x-page-header 
            title="{{ __('Tableau de bord Professeur') }}" 
            subtitle="{{ __('Gérez vos sessions, vos notes et l\'assiduité de vos étudiants.') }}"
            icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>'
        >
        </x-page-header>
    </x-slot>

    <div class="py-12 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Hero / Welcome -->
            <div class="bg-gradient-to-r from-upf-blue to-upf-navy rounded-[3rem] p-10 text-white shadow-2xl relative overflow-hidden">
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-center">
                    <div>
                        <h2 class="text-4xl font-black mb-2 italic">{{ __('Portail Enseignant') }} 📚</h2>
                        <p class="text-blue-100 text-lg opacity-80">{{ __('Bon retour parmi nous. Pilotez vos enseignements avec précision.') }}</p>
                    </div>
                    <div class="mt-6 md:mt-0 flex gap-4">
                        <div class="text-center bg-white/10 backdrop-blur px-8 py-4 rounded-3xl border border-white/20 shadow-inner">
                            <p class="text-4xl font-black">{{ $schedules->pluck('group_id')->unique()->count() ?? 0 }}</p>
                            <p class="text-[10px] uppercase font-bold tracking-widest text-blue-200 mt-1">{{ __('Groupes Assignés') }}</p>
                        </div>
                    </div>
                </div>
                <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-upf-magenta/10 rounded-full blur-3xl"></div>
                <div class="absolute -top-24 -right-24 w-64 h-64 bg-indigo-500/20 rounded-full blur-3xl"></div>
            </div>

            <!-- Dashboard Stats Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Success Rate -->
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center relative overflow-hidden group hover:border-emerald-500 transition-colors">
                    <div class="w-20 h-20 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center mb-6 group-hover:bg-emerald-500 group-hover:text-white transition-all duration-500">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                    <h3 class="text-gray-400 font-bold uppercase tracking-widest text-xs mb-2">{{ __('Taux de Réussite Global') }}</h3>
                    <div class="text-5xl font-black {{ ($successRate ?? 0) >= 50 ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ $successRate ?? 0 }}%
                    </div>
                    <p class="text-xs text-gray-500 mt-4 italic font-medium">{{ __('Pourcentage d\'étudiants ayant la moyenne dans vos modules.') }}</p>
                </div>

                <!-- Top Students -->
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 relative overflow-hidden">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-black text-gray-900 text-lg">{{ __('Top Étudiants') }} 🏆</h3>
                        <span class="text-[10px] font-bold text-amber-500 bg-amber-50 px-2 py-1 rounded-md uppercase tracking-wider">{{ __('Vos Modules') }}</span>
                    </div>
                    <div class="space-y-4">
                        @forelse($topStudents ?? [] as $index => $student)
                        <div class="flex items-center justify-between p-3 rounded-2xl {{ $index === 0 ? 'bg-amber-50 border border-amber-100' : 'bg-gray-50' }}">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center font-black text-xs {{ $index === 0 ? 'bg-amber-500 text-white' : 'bg-gray-200 text-gray-600' }}">
                                    #{{ $index + 1 }}
                                </div>
                                <span class="font-bold text-gray-800 text-sm">{{ $student->user->name }}</span>
                            </div>
                            <span class="font-black {{ $index === 0 ? 'text-amber-600' : 'text-gray-900' }}">{{ number_format($student->prof_avg, 2) }}</span>
                        </div>
                        @empty
                        <div class="text-center py-6 text-gray-400 italic text-sm">{{ __('Pas encore de notes enregistrées.') }}</div>
                        @endforelse
                    </div>
                </div>

                <!-- Frequent Absentees -->
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 relative overflow-hidden">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-black text-gray-900 text-lg">{{ __('Absents Fréquents') }} ⚠️</h3>
                        <span class="text-[10px] font-bold text-rose-500 bg-rose-50 px-2 py-1 rounded-md uppercase tracking-wider">{{ __('Non Justifiés') }}</span>
                    </div>
                    <div class="space-y-4">
                        @forelse($frequentAbsentees ?? [] as $student)
                        <div class="flex items-center justify-between p-3 rounded-2xl bg-rose-50 border border-rose-100">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-rose-200 text-rose-700 flex items-center justify-center font-black text-xs">
                                    {{ substr($student->user->name, 0, 1) }}
                                </div>
                                <span class="font-bold text-rose-900 text-sm">{{ $student->user->name }}</span>
                            </div>
                            <span class="font-black text-rose-600 text-xs bg-white px-2 py-1 rounded-lg">{{ $student->prof_absences }} {{ __('absences') }}</span>
                        </div>
                        @empty
                        <div class="text-center py-6 text-gray-400 italic text-sm">{{ __('Aucun étudiant n\'a d\'absences injustifiées. Parfait !') }}</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Quick Actions Grid -->
            <h3 class="font-black text-2xl text-upf-navy mb-4 mt-12 pl-4 italic">{{ __('Outils Pédagogiques') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                <!-- EDT -->
                <a href="{{ route('professor.schedule') }}" class="group bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-lg hover:border-amber-500 transition-all duration-300 transform hover:-translate-y-1">
                    <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-amber-500 group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h4 class="text-lg font-extrabold text-gray-900 mb-1">{{ __('Planning') }}</h4>
                    <p class="text-xs text-gray-500">{{ __('Emploi du temps') }}</p>
                </a>

                <!-- Grades -->
                <a href="{{ route('professor.grades.index') }}" class="group bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-lg hover:border-upf-blue transition-all duration-300 transform hover:-translate-y-1">
                    <div class="w-12 h-12 bg-indigo-50 text-upf-blue rounded-2xl flex items-center justify-center mb-4 group-hover:bg-upf-blue group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h4 class="text-lg font-extrabold text-gray-900 mb-1">{{ __('Notes') }}</h4>
                    <p class="text-xs text-gray-500">{{ __('Saisie et validation') }}</p>
                </a>

                <!-- Attendance -->
                <a href="{{ route('professor.absences.index') }}" class="group bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-lg hover:border-emerald-500 transition-all duration-300 transform hover:-translate-y-1">
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h4 class="text-lg font-extrabold text-gray-900 mb-1">{{ __('Absences') }}</h4>
                    <p class="text-xs text-gray-500">{{ __('Appels et feuilles') }}</p>
                </a>

                <!-- Classroom -->
                <a href="{{ route('classroom.index') }}" class="group bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-lg hover:border-orange-500 transition-all duration-300 transform hover:-translate-y-1">
                    <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-orange-600 group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                    </div>
                    <h4 class="text-lg font-extrabold text-gray-900 mb-1">{{ __('Classroom') }}</h4>
                    <p class="text-xs text-gray-500">{{ __('Supports et annonces') }}</p>
                </a>

                <!-- Reservations -->
                <a href="{{ route('professor.reservations.index') }}" class="group bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-lg hover:border-rose-500 transition-all duration-300 transform hover:-translate-y-1">
                    <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-rose-600 group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <h4 class="text-lg font-extrabold text-gray-900 mb-1">{{ __('Salles') }}</h4>
                    <p class="text-xs text-gray-500">{{ __('Réservations ponctuelles') }}</p>
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
