<nav x-data="{ open: false }" class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-100 dark:border-slate-800 sticky top-0 z-50 shadow-sm shadow-slate-100/40 dark:shadow-none transition-colors duration-300">
    <!-- Spotlight Modal Overlay -->
    <div x-data="{ isOpen: false, query: '', results: { students:[], professors:[], modules:[], rooms:[], exams:[], requests:[] } }"
         @keydown.window.prevent.ctrl.k="isOpen = true"
         @keydown.window.prevent.cmd.k="isOpen = true"
         @keydown.escape.window="isOpen = false"
         @open-spotlight.window="isOpen = true"
         class="relative z-[100]"
         x-init="$watch('query', q => {
             if(q.length >= 2) {
                 fetch('{{ route('global_search') }}?q=' + q)
                     .then(r => r.json())
                     .then(data => { results = data; });
             } else {
                 results = { students:[], professors:[], modules:[], rooms:[], exams:[], requests:[] };
             }
         })">
        
        <!-- Backdrop -->
        <div x-show="isOpen" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-950/60 backdrop-blur-md" 
             @click="isOpen = false"
             style="display: none;"></div>

        <!-- Spotlight Dialog -->
        <div x-show="isOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="fixed inset-0 overflow-y-auto p-4 sm:p-6 md:p-20 flex items-start justify-center"
             style="display: none;">
            
            <div class="mx-auto max-w-2xl w-full transform divide-y divide-slate-100 dark:divide-slate-800 rounded-3xl bg-white/95 dark:bg-slate-900/95 backdrop-blur-lg shadow-2xl ring-1 ring-black/5 dark:ring-white/5 transition-all mt-10">
                <!-- Search bar -->
                <div class="relative flex items-center p-5">
                    <svg class="h-6 w-6 text-slate-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input type="text" x-model="query" @keydown.escape="isOpen = false"
                        class="h-12 w-full border-0 bg-transparent text-slate-900 dark:text-white placeholder-slate-400 focus:ring-0 text-lg font-bold outline-none" 
                        placeholder="Recherche instantanée (Étudiants, Salles, Examens...)" autofocus>
                    <button @click="isOpen = false" type="button" class="text-xs font-black text-slate-400 hover:text-red-500 uppercase tracking-widest ml-3">Fermer</button>
                </div>

                <!-- Results container -->
                <div class="max-h-96 overflow-y-auto p-4 space-y-4" x-show="query.length >= 2">
                    <!-- Group: Students -->
                    <template x-if="results.students && results.students.length > 0">
                        <div>
                            <h4 class="text-[10px] font-black uppercase text-upf-blue dark:text-blue-400 tracking-widest mb-2">🎒 Étudiants</h4>
                            <div class="grid grid-cols-1 gap-2">
                                <template x-for="item in results.students" :key="item.title">
                                    <a :href="item.url" class="flex items-center justify-between p-3 rounded-2xl bg-slate-50/50 dark:bg-slate-800/50 hover:bg-indigo-50 dark:hover:bg-indigo-950/30 border border-slate-100/50 dark:border-slate-800 transition-all">
                                        <div>
                                            <div class="font-black text-slate-900 dark:text-white text-xs" x-text="item.title"></div>
                                            <div class="text-[10px] text-slate-400 font-bold mt-0.5" x-text="item.subtitle"></div>
                                        </div>
                                        <span class="text-xs text-upf-blue dark:text-blue-400 font-black">Voir →</span>
                                    </a>
                                </template>
                            </div>
                        </div>
                    </template>

                    <!-- Group: Professors -->
                    <template x-if="results.professors && results.professors.length > 0">
                        <div>
                            <h4 class="text-[10px] font-black uppercase text-upf-magenta dark:text-pink-400 tracking-widest mb-2">👤 Professeurs</h4>
                            <div class="grid grid-cols-1 gap-2">
                                <template x-for="item in results.professors" :key="item.title">
                                    <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50/50 dark:bg-slate-800/50 border border-slate-100/50 dark:border-slate-800 transition-all">
                                        <div>
                                            <div class="font-black text-slate-900 dark:text-white text-xs" x-text="item.title"></div>
                                            <div class="text-[10px] text-slate-400 font-bold mt-0.5" x-text="item.subtitle"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    <!-- Group: Exams -->
                    <template x-if="results.exams && results.exams.length > 0">
                        <div>
                            <h4 class="text-[10px] font-black uppercase text-indigo-600 dark:text-indigo-400 tracking-widest mb-2">📋 Examens</h4>
                            <div class="grid grid-cols-1 gap-2">
                                <template x-for="item in results.exams" :key="item.title">
                                    <a :href="item.url" class="flex items-center justify-between p-3 rounded-2xl bg-slate-50/50 dark:bg-slate-800/50 hover:bg-indigo-50 dark:hover:bg-indigo-950/30 border border-slate-100/50 dark:border-slate-800 transition-all">
                                        <div>
                                            <div class="font-black text-slate-900 dark:text-white text-xs" x-text="item.title"></div>
                                            <div class="text-[10px] text-slate-400 font-bold mt-0.5" x-text="item.subtitle"></div>
                                        </div>
                                        <span class="text-xs text-indigo-600 dark:text-indigo-400 font-black">Voir →</span>
                                    </a>
                                </template>
                            </div>
                        </div>
                    </template>

                    <!-- Group: Modules -->
                    <template x-if="results.modules && results.modules.length > 0">
                        <div>
                            <h4 class="text-[10px] font-black uppercase text-amber-600 dark:text-amber-400 tracking-widest mb-2">📚 Modules</h4>
                            <div class="grid grid-cols-1 gap-2">
                                <template x-for="item in results.modules" :key="item.title">
                                    <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50/50 dark:bg-slate-800/50 border border-slate-100/50 dark:border-slate-800 transition-all">
                                        <div>
                                            <div class="font-black text-slate-900 dark:text-white text-xs" x-text="item.title"></div>
                                            <div class="text-[10px] text-slate-400 font-bold mt-0.5" x-text="item.subtitle"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    <!-- Group: Rooms -->
                    <template x-if="results.rooms && results.rooms.length > 0">
                        <div>
                            <h4 class="text-[10px] font-black uppercase text-emerald-600 dark:text-emerald-400 tracking-widest mb-2">🏛️ Salles</h4>
                            <div class="grid grid-cols-1 gap-2">
                                <template x-for="item in results.rooms" :key="item.title">
                                    <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50/50 dark:bg-slate-800/50 border border-slate-100/50 dark:border-slate-800 transition-all">
                                        <div>
                                            <div class="font-black text-slate-900 dark:text-white text-xs" x-text="item.title"></div>
                                            <div class="text-[10px] text-slate-400 font-bold mt-0.5" x-text="item.subtitle"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    <!-- Fallback when no results are found -->
                    <div x-show="!results.students.length && !results.professors.length && !results.exams.length && !results.modules.length && !results.rooms.length && !results.requests.length" 
                         class="p-8 text-center text-slate-400 italic">
                        Aucun résultat correspondant à votre recherche. 🔍
                    </div>
                </div>

                <!-- Empty State -->
                <div class="p-8 text-center text-slate-400 italic text-xs font-bold" x-show="query.length < 2">
                    Saisissez au moins 2 caractères pour lancer la recherche...
                </div>
                
                <div class="p-3 bg-slate-50 dark:bg-slate-800/50 flex justify-between items-center text-[9px] font-black text-slate-400 uppercase tracking-widest rounded-b-3xl">
                    <span>⌨️ Entrée pour ouvrir • Échap pour fermer</span>
                    <span>Raccourci : Ctrl + K</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Primary Navigation Menu -->
    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <div class="flex items-center shrink-0">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="group transition-transform hover:scale-[1.03]">
                        <x-application-logo class="block h-10 w-auto fill-current text-slate-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-1 lg:space-x-1.5 xl:space-x-2 lg:-my-px lg:ms-4 xl:ms-6 lg:flex items-center shrink-0 h-full">

                    {{-- ════════════════════════════════════════
                         ADMIN NAV
                    ════════════════════════════════════════ --}}
                    @if(Auth::user()->isAdmin())
                        @php
                            $isAdminDash = request()->routeIs('admin.dashboard');
                        @endphp
                        <a href="{{ route('admin.dashboard') }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-[13px] font-semibold transition-all duration-200 whitespace-nowrap {{ $isAdminDash ? 'bg-indigo-50/70 dark:bg-indigo-950/30 text-upf-blue dark:text-blue-400 font-bold' : 'text-slate-600 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60' }}">
                            <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            <span>{{ __('Tableau de bord') }}</span>
                        </a>

                        {{-- ── Dropdown Scolarité ── --}}
                        @php
                            $isScolariteActive = request()->routeIs('admin.academic.*')
                                || request()->routeIs('admin.users.*')
                                || request()->routeIs('admin.students.*')
                                || request()->routeIs('admin.filieres.*')
                                || request()->routeIs('admin.groups.*')
                                || request()->routeIs('admin.schedules.*')
                                || request()->routeIs('admin.reservations.*');
                        @endphp
                        <x-dropdown align="left" width="60" contentClasses="py-1.5 bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl border border-slate-100 dark:border-slate-800 rounded-2xl shadow-2xl p-1">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center gap-1 px-3 py-2 rounded-xl text-[13px] font-semibold transition-all duration-200 whitespace-nowrap {{ $isScolariteActive ? 'bg-indigo-50/70 dark:bg-indigo-950/30 text-upf-blue dark:text-blue-400 font-bold' : 'text-slate-600 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60' }}">
                                    <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                                    <span>{{ __('Scolarité') }}</span>
                                    <svg class="fill-current h-3.5 w-3.5 opacity-60 transition-transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <div class="px-3 py-1.5 border-b border-slate-50 dark:border-slate-800 mb-1">
                                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-400">Administration</p>
                                </div>
                                <a href="{{ route('admin.academic.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-semibold text-slate-700 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all duration-150">
                                    <span>🎓</span> <span>{{ __('Année & Affectations') }}</span>
                                </a>
                                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-semibold text-slate-700 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all duration-150">
                                    <span>👤</span> <span>{{ __('Staff & Professeurs') }}</span>
                                </a>
                                <a href="{{ route('admin.students.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-semibold text-slate-700 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all duration-150">
                                    <span>🎒</span> <span>{{ __('Étudiants') }}</span>
                                </a>
                                <a href="{{ route('admin.filieres.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-semibold text-slate-700 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all duration-150">
                                    <span>🏫</span> <span>{{ __('Filières') }}</span>
                                </a>
                                <a href="{{ route('admin.groups.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-semibold text-slate-700 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all duration-150">
                                    <span>👥</span> <span>{{ __('Groupes') }}</span>
                                </a>
                                <a href="{{ route('admin.schedules.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-semibold text-slate-700 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all duration-150">
                                    <span>📅</span> <span>{{ __('Emploi du temps') }}</span>
                                </a>
                                <a href="{{ route('admin.reservations.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-semibold text-slate-700 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all duration-150">
                                    <span>🔑</span> <span>{{ __('Réservations') }}</span>
                                </a>
                            </x-slot>
                        </x-dropdown>

                        {{-- ── Dropdown Examens ── --}}
                        @php
                            $isExamsActive = request()->routeIs('admin.exams.*')
                                || request()->routeIs('admin.convocations.*')
                                || request()->routeIs('admin.exam_justifications.*')
                                || request()->routeIs('admin.reports.*')
                                || request()->routeIs('admin.analytics.*')
                                || request()->routeIs('admin.pv_globaux.*');
                        @endphp
                        <x-dropdown align="left" width="64" contentClasses="py-1.5 bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl border border-slate-100 dark:border-slate-800 rounded-2xl shadow-2xl p-1">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center gap-1 px-3 py-2 rounded-xl text-[13px] font-semibold transition-all duration-200 whitespace-nowrap {{ $isExamsActive ? 'bg-indigo-50/70 dark:bg-indigo-950/30 text-upf-blue dark:text-blue-400 font-bold' : 'text-slate-600 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60' }}">
                                    <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                    <span>{{ __('Examens') }}</span>
                                    <svg class="fill-current h-3.5 w-3.5 opacity-60" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <div class="px-3 py-1.5 border-b border-slate-50 dark:border-slate-800 mb-1">
                                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-400">Planification & Notes</p>
                                </div>
                                <a href="{{ route('admin.exams.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-semibold text-slate-700 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all duration-150">
                                    <span>📋</span> <span>{{ __('Gestion des Examens') }}</span>
                                </a>
                                <a href="{{ route('admin.retake.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-semibold text-slate-700 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all duration-150">
                                    <span>🎓</span> <span>{{ __('Rattrapages') }}</span>
                                </a>
                                <a href="{{ route('admin.exams.calendar') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-semibold text-slate-700 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all duration-150">
                                    <span>📅</span> <span>{{ __('Calendrier des Examens') }}</span>
                                </a>
                                <div class="px-3 py-1.5 border-t border-b border-slate-50 dark:border-slate-800 my-1">
                                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-400">Convocations & Justifs</p>
                                </div>
                                <a href="{{ route('admin.convocations.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-semibold text-slate-700 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all duration-150">
                                    <span>📄</span> <span>{{ __('Convocations Étudiants') }}</span>
                                </a>
                                <a href="{{ route('admin.convocations.professor_availabilities') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-semibold text-slate-700 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all duration-150">
                                    <span>📅</span> <span>{{ __('Disponibilités Enseignants') }}</span>
                                </a>
                                <a href="{{ route('admin.exam_justifications.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-semibold text-slate-700 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all duration-150">
                                    <span>📑</span> <span>{{ __('Justifications Absences') }}</span>
                                </a>
                                <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-semibold text-slate-700 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all duration-150 border-t border-slate-50 dark:border-slate-800/50 mt-1 pt-2">
                                    <span>📊</span> <span>{{ __('Rapports & Pilotage') }}</span>
                                </a>
                                <a href="{{ route('admin.analytics.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-semibold text-slate-700 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all duration-150">
                                    <span>📈</span> <span>{{ __('Statistiques Avancées') }}</span>
                                </a>
                                <a href="{{ route('admin.pv_globaux.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-semibold text-slate-700 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all duration-150">
                                    <span>📋</span> <span>{{ __('Synthèses & PV Globaux') }}</span>
                                </a>
                            </x-slot>
                        </x-dropdown>

                        {{-- ── Dropdown Gestion ── --}}
                        @php
                            $isGestionActive = request()->routeIs('admin.modules.*')
                                || request()->routeIs('admin.rooms.*')
                                || request()->routeIs('admin.messages.*')
                                || request()->routeIs('admin.activity-logs.*')
                                || request()->routeIs('admin.grades.*')
                                || request()->routeIs('admin.textbooks.*')
                                || request()->routeIs('admin.absences.*')
                                || request()->routeIs('admin.requests.*')
                                || request()->routeIs('admin.settings.*')
                                || request()->routeIs('admin.students_risk.*')
                                || request()->routeIs('admin.reclamations.*');
                        @endphp
                        <x-dropdown align="left" width="60" contentClasses="py-1.5 bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl border border-slate-100 dark:border-slate-800 rounded-2xl shadow-2xl p-1">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center gap-1 px-3 py-2 rounded-xl text-[13px] font-semibold transition-all duration-200 whitespace-nowrap {{ $isGestionActive ? 'bg-indigo-50/70 dark:bg-indigo-950/30 text-upf-blue dark:text-blue-400 font-bold' : 'text-slate-600 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60' }}">
                                    <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span>{{ __('Gestion') }}</span>
                                    <svg class="fill-current h-3.5 w-3.5 opacity-60" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <div class="px-3 py-1.5 border-b border-slate-50 dark:border-slate-800 mb-1">
                                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-400">Ressources & Notes</p>
                                </div>
                                <a href="{{ route('admin.modules.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-semibold text-slate-700 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all duration-150">
                                    <span>📚</span> <span>{{ __('Modules') }}</span>
                                </a>
                                <a href="{{ route('admin.rooms.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-semibold text-slate-700 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all duration-150">
                                    <span>🏛️</span> <span>{{ __('Salles') }}</span>
                                </a>
                                <a href="{{ route('admin.grades.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-semibold text-slate-700 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all duration-150">
                                    <span>📊</span> <span>{{ __('Gestion des Notes') }}</span>
                                </a>
                                <a href="{{ route('admin.reclamations.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-semibold text-slate-700 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all duration-150">
                                    <span>💬</span> <span>{{ __('Réclamations Notes') }}</span>
                                </a>
                                <div class="px-3 py-1.5 border-t border-b border-slate-50 dark:border-slate-800 my-1">
                                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-400">Suivi Académique</p>
                                </div>
                                <a href="{{ route('admin.absences.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-semibold text-slate-700 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all duration-150">
                                    <span>🚨</span> <span>{{ __('Absences & Justificatifs') }}</span>
                                </a>
                                <a href="{{ route('admin.students_risk.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-semibold text-slate-700 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all duration-150">
                                    <span>🚨</span> <span>{{ __('Étudiants à Risque') }}</span>
                                </a>
                                <a href="{{ route('admin.textbooks.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-semibold text-slate-700 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all duration-150">
                                    <span>📖</span> <span>{{ __('Cahiers de Textes') }}</span>
                                </a>
                                <a href="{{ route('admin.requests.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-semibold text-slate-700 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all duration-150">
                                    <span>📬</span> <span>{{ __('Demandes Administratives') }}</span>
                                </a>
                                <div class="px-3 py-1.5 border-t border-b border-slate-50 dark:border-slate-800 my-1">
                                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-400">Système</p>
                                </div>
                                <a href="{{ route('admin.messages.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-semibold text-slate-700 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all duration-150">
                                    <span>💬</span> <span>{{ __('Messages Directs') }}</span>
                                </a>
                                <a href="{{ route('admin.activity-logs.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-semibold text-slate-700 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all duration-150">
                                    <span>📋</span> <span>{{ __("Journal d'Activité") }}</span>
                                </a>
                                <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-semibold text-slate-700 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all duration-150">
                                    <span>⚙️</span> <span>{{ __("Paramètres Globaux") }}</span>
                                </a>
                            </x-slot>
                        </x-dropdown>
                    @endif

                    {{-- ════════════════════════════════════════
                         PROFESSOR NAV
                    ════════════════════════════════════════ --}}
                    @if(Auth::user()->isProfessor())
                        @php
                            $isProfDash = request()->routeIs('professor.dashboard');
                        @endphp
                        <a href="{{ route('professor.dashboard') }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-[13px] font-semibold transition-all duration-200 whitespace-nowrap {{ $isProfDash ? 'bg-indigo-50/70 dark:bg-indigo-950/30 text-upf-blue dark:text-blue-400 font-bold' : 'text-slate-600 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60' }}">
                            <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            <span>{{ __('Tableau de bord') }}</span>
                        </a>

                        {{-- ── Dropdown Pédagogie ── --}}
                        @php
                            $isPedagogieActive = request()->routeIs('professor.grades.*')
                                || request()->routeIs('professor.absences.*')
                                || request()->routeIs('professor.textbook.*');
                        @endphp
                        <x-dropdown align="left" width="56" contentClasses="py-1.5 bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl border border-slate-100 dark:border-slate-800 rounded-2xl shadow-2xl p-1">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center gap-1 px-3 py-2 rounded-xl text-[13px] font-semibold transition-all duration-200 whitespace-nowrap {{ $isPedagogieActive ? 'bg-indigo-50/70 dark:bg-indigo-950/30 text-upf-blue dark:text-blue-400 font-bold' : 'text-slate-600 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60' }}">
                                    <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                    <span>{{ __('Pédagogie') }}</span>
                                    <svg class="fill-current h-3.5 w-3.5 opacity-60" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <a href="{{ route('professor.grades.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-semibold text-slate-700 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all duration-150">
                                    <span>📊</span> <span>{{ __('Gestion des Notes') }}</span>
                                </a>
                                <a href="{{ route('professor.absences.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-semibold text-slate-700 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all duration-150">
                                    <span>🚨</span> <span>{{ __('Absences & Présence') }}</span>
                                </a>
                                <a href="{{ route('professor.textbook.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-semibold text-slate-700 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all duration-150">
                                    <span>📖</span> <span>{{ __('Cahier de Textes') }}</span>
                                </a>
                            </x-slot>
                        </x-dropdown>

                        {{-- ── Dropdown Examens ── --}}
                        @php
                            $isSurveillanceActive = request()->routeIs('professor.proctor_convocations.*')
                                || request()->routeIs('professor.availability.*');
                            $pendingConvocations = 0;
                            try {
                                $pendingConvocations = Auth::user()->professor
                                    ? \App\Models\ProfessorConvocation::where('professor_id', Auth::user()->professor->id)
                                        ->whereIn('status', ['generated', 'sent'])
                                        ->count()
                                    : 0;
                            } catch (\Exception $e) {}
                        @endphp
                        <x-dropdown align="left" width="60" contentClasses="py-1.5 bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl border border-slate-100 dark:border-slate-800 rounded-2xl shadow-2xl p-1">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center gap-1 px-3 py-2 rounded-xl text-[13px] font-semibold transition-all duration-200 whitespace-nowrap {{ $isSurveillanceActive ? 'bg-indigo-50/70 dark:bg-indigo-950/30 text-upf-blue dark:text-blue-400 font-bold' : 'text-slate-600 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60' }}">
                                    <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                    <span>{{ __('Examens') }}</span>
                                    @if($pendingConvocations > 0)
                                        <span class="bg-upf-magenta text-white text-[9px] font-black w-4 h-4 rounded-full flex items-center justify-center animate-pulse">
                                            {{ $pendingConvocations }}
                                        </span>
                                    @endif
                                    <svg class="fill-current h-3.5 w-3.5 opacity-60" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <a href="{{ route('professor.proctor_convocations.index') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-[13px] font-semibold text-slate-700 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all duration-150">
                                    <span class="flex items-center gap-2.5">
                                        <span>🎓</span> <span>{{ __('Mes Surveillances') }}</span>
                                    </span>
                                    @if($pendingConvocations > 0)
                                        <span class="bg-upf-magenta text-white text-[9px] font-black px-1.5 py-0.5 rounded-full">{{ $pendingConvocations }}</span>
                                    @endif
                                </a>
                                <a href="{{ route('professor.availability.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-semibold text-slate-700 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all duration-150">
                                    <span>📅</span> <span>{{ __('Déclarer Disponibilités') }}</span>
                                </a>
                            </x-slot>
                        </x-dropdown>

                        {{-- ── Dropdown Services ── --}}
                        @php
                            $isServicesActive = request()->routeIs('professor.requests.*')
                                || request()->routeIs('professor.reservations.*');
                        @endphp
                        <x-dropdown align="left" width="56" contentClasses="py-1.5 bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl border border-slate-100 dark:border-slate-800 rounded-2xl shadow-2xl p-1">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center gap-1 px-3 py-2 rounded-xl text-[13px] font-semibold transition-all duration-200 whitespace-nowrap {{ $isServicesActive ? 'bg-indigo-50/70 dark:bg-indigo-950/30 text-upf-blue dark:text-blue-400 font-bold' : 'text-slate-600 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60' }}">
                                    <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    <span>{{ __('Services') }}</span>
                                    <svg class="fill-current h-3.5 w-3.5 opacity-60" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <a href="{{ route('professor.requests.create') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-semibold text-slate-700 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all duration-150">
                                    <span>📬</span> <span>{{ __('Demandes Administratives') }}</span>
                                </a>
                                <a href="{{ route('professor.reservations.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-semibold text-slate-700 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all duration-150">
                                    <span>🔑</span> <span>{{ __('Réservations Salles') }}</span>
                                </a>
                            </x-slot>
                        </x-dropdown>
                    @endif

                    {{-- ════════════════════════════════════════
                         STUDENT NAV
                    ════════════════════════════════════════ --}}
                    @if(Auth::user()->isStudent())
                        @php
                            $isStudentDash = request()->routeIs('student.dashboard');
                            $isStudentGrades = request()->routeIs('student.grades');
                            $isStudentAbs = request()->routeIs('student.absences');
                            $isStudentConv = request()->routeIs('student.convocations.*');
                            
                            $newConvocations = 0;
                            try {
                                $newConvocations = Auth::user()->student
                                    ? \App\Models\Convocation::where('student_id', Auth::user()->student->id)
                                        ->whereIn('status', ['generated', 'sent'])
                                        ->count()
                                    : 0;
                            } catch (\Exception $e) {}
                        @endphp
                        <a href="{{ route('student.dashboard') }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-[13px] font-semibold transition-all duration-200 whitespace-nowrap {{ $isStudentDash ? 'bg-indigo-50/70 dark:bg-indigo-950/30 text-upf-blue dark:text-blue-400 font-bold' : 'text-slate-600 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60' }}">
                            <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            <span>{{ __('Mon Espace') }}</span>
                        </a>
                        <a href="{{ route('student.grades') }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-[13px] font-semibold transition-all duration-200 whitespace-nowrap {{ $isStudentGrades ? 'bg-indigo-50/70 dark:bg-indigo-950/30 text-upf-blue dark:text-blue-400 font-bold' : 'text-slate-600 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60' }}">
                            <span>📊</span> <span>{{ __('Mes Notes') }}</span>
                        </a>
                        <a href="{{ route('student.reclamations.index') }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-[13px] font-semibold transition-all duration-200 whitespace-nowrap {{ request()->routeIs('student.reclamations.*') ? 'bg-indigo-50/70 dark:bg-indigo-950/30 text-upf-blue dark:text-blue-400 font-bold' : 'text-slate-600 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60' }}">
                            <span>💬</span> <span>{{ __('Réclamations') }}</span>
                        </a>
                        <a href="{{ route('student.retake.index') }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-[13px] font-semibold transition-all duration-200 whitespace-nowrap {{ request()->routeIs('student.retake.*') ? 'bg-indigo-50/70 dark:bg-indigo-950/30 text-upf-blue dark:text-blue-400 font-bold' : 'text-slate-600 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60' }}">
                            <span>🎓</span> <span>{{ __('Rattrapages') }}</span>
                        </a>
                        <a href="{{ route('student.absences') }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-[13px] font-semibold transition-all duration-200 whitespace-nowrap {{ $isStudentAbs ? 'bg-indigo-50/70 dark:bg-indigo-950/30 text-upf-blue dark:text-blue-400 font-bold' : 'text-slate-600 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60' }}">
                            <span>🚨</span> <span>{{ __('Mes Absences') }}</span>
                        </a>
                        <a href="{{ route('student.convocations.index') }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-[13px] font-semibold transition-all duration-200 whitespace-nowrap {{ $isStudentConv ? 'bg-indigo-50/70 dark:bg-indigo-950/30 text-upf-blue dark:text-blue-400 font-bold' : 'text-slate-600 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60' }}">
                            <span>📄</span> <span>{{ __('Convocations') }}</span>
                            @if($newConvocations > 0)
                                <span class="bg-upf-magenta text-white text-[9px] font-black w-4 h-4 rounded-full flex items-center justify-center animate-pulse">
                                    {{ $newConvocations }}
                                </span>
                            @endif
                        </a>
                    @endif

                    {{-- ════════════════════════════════════════
                         LIENS COMMUNS (Outils)
                    ════════════════════════════════════════ --}}
                    @php
                        $isCommunsActive = request()->routeIs('classroom.*')
                            || request()->routeIs('chat.*')
                            || request()->routeIs('calendar')
                            || request()->routeIs('faq');
                    @endphp
                    <x-dropdown align="left" width="52" contentClasses="py-1.5 bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl border border-slate-100 dark:border-slate-800 rounded-2xl shadow-2xl p-1">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-1 px-3 py-2 rounded-xl text-[13px] font-semibold transition-all duration-200 whitespace-nowrap {{ $isCommunsActive ? 'bg-indigo-50/70 dark:bg-indigo-950/30 text-upf-blue dark:text-blue-400 font-bold' : 'text-slate-600 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60' }}">
                                <svg class="w-4 h-4 opacity-85" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                <span>{{ __('Outils') }}</span>
                                <svg class="fill-current h-3.5 w-3.5 opacity-60" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <a href="{{ route('classroom.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-semibold text-slate-700 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all duration-150">
                                <span>🖥️</span> <span>{{ __('Classroom') }}</span>
                            </a>
                            <a href="{{ route('chat.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-semibold text-slate-700 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all duration-150">
                                <span>💬</span> <span>{{ __('Messagerie') }}</span>
                            </a>
                            <a href="{{ route('calendar') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-semibold text-slate-700 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all duration-150">
                                <span>📆</span> <span>{{ __('Calendrier') }}</span>
                            </a>
                            <a href="{{ route('faq') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-semibold text-slate-700 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all duration-150">
                                <span>❓</span> <span>{{ __('FAQ') }}</span>
                            </a>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- RIGHT SIDE: Dark Mode + Notifications + Lang + User -->
            <div class="hidden lg:flex lg:items-center lg:gap-2 shrink-0">
                
                <!-- Premium Global Search Button (Ctrl+K) -->
                <!-- Collapsible Search Input (Icon on lg, pill on xl) to resolve all overlaps -->
                <button @click="$dispatch('open-spotlight')"
                    class="flex items-center justify-center 2xl:justify-between gap-2 h-10 w-10 2xl:w-44 bg-slate-50/70 dark:bg-slate-800/70 border border-slate-200/60 dark:border-slate-700/60 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl px-2 2xl:px-3.5 transition-all duration-200 focus:outline-none text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 shadow-sm"
                    title="{{ __('Rechercher (Ctrl+K)') }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <span class="hidden 2xl:inline text-xs font-semibold">{{ __('Rechercher...') }}</span>
                    <kbd class="hidden 2xl:inline-flex bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-[9px] text-slate-400 dark:text-slate-500 px-1.5 py-0.5 rounded font-black shadow-sm">Ctrl K</kbd>
                </button>

                <!-- Dark Mode Toggle -->
                <div x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val)); if(darkMode) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark');">
                    <button @click="darkMode = !darkMode; if(darkMode) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark');"
                            class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-upf-blue dark:hover:text-amber-400 transition-colors focus:outline-none rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 border border-transparent hover:border-slate-100 dark:hover:border-slate-700/50">
                        <template x-if="!darkMode">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                        </template>
                        <template x-if="darkMode">
                            <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </template>
                    </button>
                </div>

                <!-- Notifications Bell -->
                <x-dropdown align="right" width="80" contentClasses="py-1 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-2xl overflow-hidden">
                    <x-slot name="trigger">
                        <button class="relative w-10 h-10 flex items-center justify-center text-slate-400 hover:text-upf-blue transition-colors focus:outline-none rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 border border-transparent hover:border-slate-100 dark:hover:border-slate-700/50">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            @php $unreadCount = Auth::user()->unreadNotifications->count(); @endphp
                            @if($unreadCount > 0)
                                <span class="absolute top-1.5 right-1.5 w-4 h-4 bg-upf-magenta text-white text-[9px] font-black rounded-full flex items-center justify-center border-2 border-white dark:border-slate-900 animate-pulse">
                                    {{ $unreadCount }}
                                </span>
                            @endif
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <div class="w-80">
                            <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                <h4 class="text-xs font-black uppercase tracking-widest text-slate-700 dark:text-slate-300">🔔 Notifications</h4>
                                @if($unreadCount > 0)
                                    <form action="{{ route('notifications.markAllRead') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-[10px] font-black text-upf-blue hover:text-upf-magenta transition-colors uppercase tracking-wide">Tout lire</button>
                                    </form>
                                @endif
                            </div>
                            <div class="max-h-80 overflow-y-auto divide-y divide-slate-50 dark:divide-slate-800/50">
                                @forelse(Auth::user()->notifications->take(8) as $notification)
                                    @php
                                        $data = $notification->data;
                                        $isUnread = is_null($notification->read_at);
                                    @endphp
                                    <a href="{{ $data['url'] ?? '#' }}"
                                       onclick="fetch('/notifications/{{ $notification->id }}/read', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}})"
                                       class="flex items-start gap-3 p-4 hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-all {{ $isUnread ? 'bg-indigo-50/30 dark:bg-indigo-950/10' : '' }}">
                                        <div class="text-xl flex-shrink-0 mt-0.5">{{ $data['icon'] ?? '🔔' }}</div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-black text-slate-900 dark:text-white leading-tight {{ $isUnread ? 'text-upf-blue dark:text-blue-400' : '' }} truncate">{{ $data['title'] ?? 'Notification' }}</p>
                                            <p class="text-[10px] text-slate-500 mt-0.5 leading-snug line-clamp-2">{{ $data['message'] ?? ($data['body'] ?? '') }}</p>
                                            <p class="text-[9px] text-slate-400 mt-1 font-bold">{{ $notification->created_at->diffForHumans() }}</p>
                                        </div>
                                        @if($isUnread)
                                            <div class="w-2 h-2 rounded-full bg-upf-blue dark:bg-blue-400 flex-shrink-0 mt-2"></div>
                                        @endif
                                    </a>
                                @empty
                                    <div class="text-center py-10 px-4">
                                        <div class="text-4xl mb-3">🔕</div>
                                        <p class="text-sm font-black text-slate-400">Tout est calme ici !</p>
                                        <p class="text-xs text-slate-300 mt-1">Vous n'avez aucune notification.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </x-slot>
                </x-dropdown>

                <!-- Language Switcher -->
                <x-dropdown align="right" width="48" contentClasses="py-1 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-2xl p-1">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-1.5 px-3 py-2 text-xs leading-4 font-bold rounded-xl text-upf-magenta dark:text-pink-400 bg-pink-50/50 dark:bg-pink-950/20 hover:bg-pink-100/50 dark:hover:bg-pink-900/30 focus:outline-none transition ease-in-out duration-150 uppercase tracking-wider border border-transparent hover:border-pink-200/40">
                            🌐 {{ strtoupper(App::getLocale()) }}
                            <svg class="fill-current h-3 w-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <a href="{{ route('lang.switch', 'en') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-[13px] font-semibold text-slate-700 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all duration-150">
                            <span>🇺🇸 English</span><span class="text-[9px] font-black text-slate-300">EN</span>
                        </a>
                        <a href="{{ route('lang.switch', 'fr') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-[13px] font-semibold text-slate-700 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all duration-150">
                            <span>🇫🇷 Français</span><span class="text-[9px] font-black text-slate-300">FR</span>
                        </a>
                        <a href="{{ route('lang.switch', 'ar') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-[13px] font-semibold text-slate-700 dark:text-slate-300 hover:text-upf-blue dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all duration-150">
                            <span dir="rtl">🇲🇦 العربية</span><span class="text-[9px] font-black text-slate-300">AR</span>
                        </a>
                    </x-slot>
                </x-dropdown>

                <!-- User Dropdown -->
                <x-dropdown align="right" width="56" contentClasses="py-1 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-2xl p-1 overflow-hidden">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-3 py-1.5 text-sm leading-4 font-bold rounded-xl text-slate-700 dark:text-slate-300 bg-slate-50/50 dark:bg-slate-800/50 hover:bg-slate-100/50 dark:hover:bg-slate-800 border border-slate-100 dark:border-slate-700/50 transition-all duration-200">
                            <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-upf-blue to-upf-magenta flex items-center justify-center text-white text-xs font-black uppercase shadow-sm">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <span class="max-w-[80px] truncate text-xs font-semibold hidden xl:inline">{{ Auth::user()->name }}</span>
                            <svg class="fill-current h-3.5 w-3.5 opacity-60" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40">
                            <p class="text-xs font-black text-slate-800 dark:text-white truncate">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500 truncate mt-0.5">{{ Auth::user()->email }}</p>
                            <span class="inline-block mt-1 px-2 py-0.5 bg-indigo-50 dark:bg-indigo-950/40 text-upf-blue dark:text-blue-400 text-[8px] font-black rounded-full uppercase tracking-widest">
                                {{ Auth::user()->role->name ?? 'user' }}
                            </span>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-850 transition-colors">
                            👤 {{ __('Mon Profil') }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}" 
                               onclick="event.preventDefault(); this.closest('form').submit();"
                               class="flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-red-600 hover:bg-red-50 dark:hover:bg-red-950/20 transition-colors border-t border-slate-50 dark:border-slate-800/60 mt-1">
                                🚪 {{ __('Déconnexion') }}
                            </a>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center lg:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus:outline-none focus:bg-slate-100 focus:text-slate-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden lg:hidden border-t border-slate-200/50 dark:border-slate-800">
        <div class="pt-2 pb-3 space-y-1 px-4 max-h-[80vh] overflow-y-auto">

            @if(Auth::user()->isAdmin())
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">🏠 {{ __('Tableau de bord') }}</x-responsive-nav-link>
                <div class="py-1"><p class="px-3 py-1 text-[9px] font-black text-slate-400 uppercase tracking-widest border-t border-slate-100 dark:border-slate-800/80 pt-2">Scolarité</p></div>
                <x-responsive-nav-link :href="route('admin.academic.index')">🎓 {{ __('Année & Affectations') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.users.index')">👤 {{ __('Staff & Professeurs') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.students.index')">🎒 {{ __('Étudiants') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.groups.index')">👥 {{ __('Groupes') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.schedules.index')">📅 {{ __('Emploi du temps') }}</x-responsive-nav-link>
                <div class="py-1"><p class="px-3 py-1 text-[9px] font-black text-slate-400 uppercase tracking-widest border-t border-slate-100 dark:border-slate-800/80 pt-2">Examens & Convocations</p></div>
                <x-responsive-nav-link :href="route('admin.exams.index')">📋 {{ __('Gestion des Examens') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.exams.calendar')">📅 {{ __('Calendrier des Examens') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.convocations.index')">📄 {{ __('Tableau de bord Convocations') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.convocations.professor_availabilities')">📅 {{ __('Disponibilités Professeurs') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.exam_justifications.index')">📑 {{ __('Justifications Absences') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.reports.index')">📊 {{ __('Rapports & Pilotage') }}</x-responsive-nav-link>
                <div class="py-1"><p class="px-3 py-1 text-[9px] font-black text-slate-400 uppercase tracking-widest border-t border-slate-100 dark:border-slate-800/80 pt-2">Gestion</p></div>
                <x-responsive-nav-link :href="route('admin.modules.index')">📚 {{ __('Modules') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.rooms.index')">🏛️ {{ __('Salles') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.grades.index')">📊 {{ __('Notes') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.reclamations.index')">💬 {{ __('Réclamations Notes') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.absences.index')">🚨 {{ __('Absences') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.students_risk.index')">🚨 {{ __('Étudiants à Risque') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.requests.index')">📬 {{ __('Demandes') }}</x-responsive-nav-link>
            @endif

            @if(Auth::user()->isProfessor())
                <x-responsive-nav-link :href="route('professor.dashboard')">🏠 {{ __('Tableau de bord') }}</x-responsive-nav-link>
                <div class="py-1"><p class="px-3 py-1 text-[9px] font-black text-slate-400 uppercase tracking-widest border-t border-slate-100 dark:border-slate-800/80 pt-2">Pédagogie</p></div>
                <x-responsive-nav-link :href="route('professor.grades.index')">📊 {{ __('Gestion des Notes') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('professor.absences.index')">🚨 {{ __('Absences') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('professor.textbook.index')">📖 {{ __('Cahier de Textes') }}</x-responsive-nav-link>
                <div class="py-1"><p class="px-3 py-1 text-[9px] font-black text-slate-400 uppercase tracking-widest border-t border-slate-100 dark:border-slate-800/80 pt-2">Examens & Surveillance</p></div>
                <x-responsive-nav-link :href="route('professor.proctor_convocations.index')">🎓 {{ __('Mes Convocations de Surveillance') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('professor.availability.index')">📅 {{ __('Mes Disponibilités') }}</x-responsive-nav-link>
                <div class="py-1"><p class="px-3 py-1 text-[9px] font-black text-slate-400 uppercase tracking-widest border-t border-slate-100 dark:border-slate-800/80 pt-2">Services</p></div>
                <x-responsive-nav-link :href="route('professor.requests.create')">📬 {{ __('Demandes Administratives') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('professor.reservations.index')">🔑 {{ __('Réservations Salles') }}</x-responsive-nav-link>
            @endif

            @if(Auth::user()->isStudent())
                <x-responsive-nav-link :href="route('student.dashboard')">🏠 {{ __('Mon Espace') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('student.grades')">📊 {{ __('Mes Notes') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('student.absences')">🚨 {{ __('Mes Absences') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('student.convocations.index')">📄 {{ __('Mes Convocations') }}</x-responsive-nav-link>
            @endif

            <div class="py-1"><p class="px-3 py-1 text-[9px] font-black text-slate-400 uppercase tracking-widest border-t border-slate-100 dark:border-slate-800/80 pt-2">Commun</p></div>
            <x-responsive-nav-link :href="route('classroom.index')" :active="request()->routeIs('classroom.*')">🖥️ {{ __('Classroom') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('chat.index')" :active="request()->routeIs('chat.*')">💬 {{ __('Messagerie') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('calendar')" :active="request()->routeIs('calendar')">📆 {{ __('Calendrier') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('faq')" :active="request()->routeIs('faq')">❓ {{ __('FAQ') }}</x-responsive-nav-link>
        </div>

        <!-- Responsive Settings -->
        <div class="pt-4 pb-1 border-t border-slate-200 dark:border-slate-800">
            <div class="px-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-upf-blue to-upf-magenta flex items-center justify-center text-white font-black uppercase shadow">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div>
                    <div class="font-black text-sm text-slate-850 dark:text-white">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-xs text-slate-400 dark:text-slate-500">{{ Auth::user()->email }}</div>
                </div>
            </div>
            <div class="mt-3 space-y-1 px-1">
                <x-responsive-nav-link :href="route('profile.edit')">👤 {{ __('Mon Profil') }}</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-600 hover:bg-red-50 dark:hover:bg-red-950/20">
                        🚪 {{ __('Déconnexion') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
