{{-- ═══════════════════════════════════════════════════════════════════
     SIDEBAR NAVIGATION — UPF Portail
     Professional vertical sidebar with slim top bar
     ═══════════════════════════════════════════════════════════════════ --}}
<div x-data="{ sidebarOpen: false }" @keydown.escape.window="sidebarOpen = false">

    {{-- ══════════════════════════════════════
         SPOTLIGHT SEARCH MODAL (Global Overlay)
    ══════════════════════════════════════ --}}
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
             class="fixed inset-0 bg-slate-950/60 backdrop-blur-md z-[100]" 
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
             class="fixed inset-0 overflow-y-auto p-4 sm:p-6 md:p-20 flex items-start justify-center z-[101]"
             style="display: none;">
            
            <div class="mx-auto max-w-2xl w-full transform divide-y divide-slate-100 dark:divide-slate-800 rounded-3xl bg-white/95 dark:bg-slate-900/95 backdrop-blur-lg shadow-2xl ring-1 ring-black/5 dark:ring-white/5 transition-all mt-10">
                <!-- Search bar -->
                <div class="relative flex items-center p-5">
                    <svg class="h-6 w-6 text-slate-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input type="text" x-model="query" @keydown.escape="isOpen = false"
                        @keydown.enter="if(query.startsWith('/')) { const urls = {'/edt':'{{ route('calendar') }}', '/notes':'{{ Auth::user()->isStudent() ? route('student.grades') : (Auth::user()->isProfessor() ? route('professor.grades.index') : route('admin.grades.index')) }}', '/abs':'{{ Auth::user()->isStudent() ? route('student.absences') : (Auth::user()->isProfessor() ? route('professor.absences.index') : route('admin.absences.index')) }}', '/doc':'{{ Auth::user()->isStudent() ? route('student.requests.index') : (Auth::user()->isProfessor() ? route('professor.requests.create') : route('admin.requests.index')) }}', '/chat':'{{ route('chat.index') }}', '/profil':'{{ route('profile.edit') }}', '/classroom':'{{ route('classroom.index') }}'}; const cmd = query.toLowerCase().trim(); if(urls[cmd]) { isOpen = false; window.location.href = urls[cmd]; } }"
                        class="h-12 w-full border-0 bg-transparent text-slate-900 dark:text-white placeholder-slate-400 focus:ring-0 text-lg font-bold outline-none" 
                        placeholder="Recherche ou /commandes (ex: /edt, /notes, /profil)..." autofocus>
                    <button @click="isOpen = false" type="button" class="text-xs font-black text-slate-400 hover:text-red-500 uppercase tracking-widest ml-3">Fermer</button>
                </div>

                <!-- Results container -->
                <div class="max-h-96 overflow-y-auto p-4 space-y-4" x-show="query.length >= 2 || query.startsWith('/')">

                    <!-- Group: Commands -->
                    <template x-if="query.startsWith('/')">
                        <div>
                            <h4 class="text-[10px] font-black uppercase text-upf-magenta dark:text-pink-400 tracking-widest mb-2">🛠️ Raccourcis Command Center</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-4">
                                <a href="{{ route('calendar') }}" class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50/50 dark:bg-slate-800/50 hover:bg-indigo-50 dark:hover:bg-indigo-950/30 border border-slate-100/50 dark:border-slate-800 transition-all">
                                    <span class="text-xl">📅</span>
                                    <div class="min-w-0">
                                        <div class="font-black text-slate-900 dark:text-white text-xs">/edt</div>
                                        <div class="text-[9px] text-slate-400 font-bold uppercase tracking-wider mt-0.5">{{ __('Mon Emploi du Temps') }}</div>
                                    </div>
                                </a>
                                <a href="{{ Auth::user()->isStudent() ? route('student.grades') : (Auth::user()->isProfessor() ? route('professor.grades.index') : route('admin.grades.index')) }}" class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50/50 dark:bg-slate-800/50 hover:bg-indigo-50 dark:hover:bg-indigo-950/30 border border-slate-100/50 dark:border-slate-800 transition-all">
                                    <span class="text-xl">📊</span>
                                    <div class="min-w-0">
                                        <div class="font-black text-slate-900 dark:text-white text-xs">/notes</div>
                                        <div class="text-[9px] text-slate-400 font-bold uppercase tracking-wider mt-0.5">{{ __('Consultation des Notes') }}</div>
                                    </div>
                                </a>
                                <a href="{{ Auth::user()->isStudent() ? route('student.absences') : (Auth::user()->isProfessor() ? route('professor.absences.index') : route('admin.absences.index')) }}" class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50/50 dark:bg-slate-800/50 hover:bg-indigo-50 dark:hover:bg-indigo-950/30 border border-slate-100/50 dark:border-slate-800 transition-all">
                                    <span class="text-xl">🚨</span>
                                    <div class="min-w-0">
                                        <div class="font-black text-slate-900 dark:text-white text-xs">/abs</div>
                                        <div class="text-[9px] text-slate-400 font-bold uppercase tracking-wider mt-0.5">{{ __('Suivi des Absences') }}</div>
                                    </div>
                                </a>
                                <a href="{{ Auth::user()->isStudent() ? route('student.requests.index') : (Auth::user()->isProfessor() ? route('professor.requests.create') : route('admin.requests.index')) }}" class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50/50 dark:bg-slate-800/50 hover:bg-indigo-50 dark:hover:bg-indigo-950/30 border border-slate-100/50 dark:border-slate-800 transition-all">
                                    <span class="text-xl">📬</span>
                                    <div class="min-w-0">
                                        <div class="font-black text-slate-900 dark:text-white text-xs">/doc</div>
                                        <div class="text-[9px] text-slate-400 font-bold uppercase tracking-wider mt-0.5">{{ __('Demandes Administratives') }}</div>
                                    </div>
                                </a>
                                <a href="{{ route('chat.index') }}" class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50/50 dark:bg-slate-800/50 hover:bg-indigo-50 dark:hover:bg-indigo-950/30 border border-slate-100/50 dark:border-slate-800 transition-all">
                                    <span class="text-xl">💬</span>
                                    <div class="min-w-0">
                                        <div class="font-black text-slate-900 dark:text-white text-xs">/chat</div>
                                        <div class="text-[9px] text-slate-400 font-bold uppercase tracking-wider mt-0.5">{{ __('Messagerie Instantanée') }}</div>
                                    </div>
                                </a>
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50/50 dark:bg-slate-800/50 hover:bg-indigo-50 dark:hover:bg-indigo-950/30 border border-slate-100/50 dark:border-slate-800 transition-all">
                                    <span class="text-xl">👤</span>
                                    <div class="min-w-0">
                                        <div class="font-black text-slate-900 dark:text-white text-xs">/profil</div>
                                        <div class="text-[9px] text-slate-400 font-bold uppercase tracking-wider mt-0.5">{{ __('Mon Profil') }}</div>
                                    </div>
                                </a>
                                <a href="{{ route('classroom.index') }}" class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50/50 dark:bg-slate-800/50 hover:bg-indigo-50 dark:hover:bg-indigo-950/30 border border-slate-100/50 dark:border-slate-800 transition-all">
                                    <span class="text-xl">🖥️</span>
                                    <div class="min-w-0">
                                        <div class="font-black text-slate-900 dark:text-white text-xs">/classroom</div>
                                        <div class="text-[9px] text-slate-400 font-bold uppercase tracking-wider mt-0.5">{{ __('Classroom') }}</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </template>
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
                    <!-- Fallback -->
                    <div x-show="!results.students.length && !results.professors.length && !results.exams.length && !results.modules.length && !results.rooms.length && !results.requests.length && !query.startsWith('/')" 
                         class="p-8 text-center text-slate-400 italic">
                        Aucun résultat correspondant à votre recherche. 🔍
                    </div>
                </div>

                <!-- Empty State -->
                <div class="p-8 text-center text-slate-400 italic text-xs font-bold" x-show="query.length < 2 && !query.startsWith('/')">
                    Saisissez au moins 2 caractères pour lancer la recherche...
                </div>
                
                <div class="p-3 bg-slate-50 dark:bg-slate-800/50 flex justify-between items-center text-[9px] font-black text-slate-400 uppercase tracking-widest rounded-b-3xl">
                    <span>⌨️ Entrée pour ouvrir • Échap pour fermer</span>
                    <span>Raccourci : Ctrl + K</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         MOBILE BACKDROP
    ══════════════════════════════════════ --}}
    <div x-show="sidebarOpen" 
         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-40 lg:hidden" style="display:none;"></div>

    {{-- ══════════════════════════════════════
         SIDEBAR
    ══════════════════════════════════════ --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : ({{ app()->getLocale() == 'ar' ? 'true' : 'false' }} ? 'translate-x-full lg:translate-x-0' : '-translate-x-full lg:translate-x-0')"
           class="fixed {{ app()->getLocale() == 'ar' ? 'right-0 border-l' : 'left-0 border-r' }} top-0 bottom-0 w-[280px] bg-[#0a0f1e] z-50 transition-transform duration-300 ease-in-out flex flex-col border-white/[0.06]">

        {{-- ── Logo ── --}}
        <div class="h-16 flex items-center gap-3 px-5 border-b border-white/[0.06] shrink-0">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                <div class="h-9 w-auto flex items-center justify-center bg-white rounded-lg p-1.5 shadow-sm group-hover:scale-105 transition-transform">
                    <img src="https://www.upf.ac.ma/images/logo_upf.png" alt="UPF" class="h-6 object-contain" onerror="this.outerHTML='<div class=\'font-black text-sm text-[#003893]\'>UPF</div>'">
                </div>
                <div>
                    <div class="text-white font-extrabold text-sm tracking-tight leading-none">UNIVERSITÉ PRIVÉE DE FÈS</div>
                    <div class="text-[9px] font-bold text-blue-400 uppercase tracking-[0.2em] mt-0.5">Excellence & Innovation</div>
                </div>
            </a>
        </div>

        {{-- ── Scrollable Navigation ── --}}
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1" style="scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.1) transparent;">

            {{-- ════════════════════════════════════════
                 ADMIN NAV
            ════════════════════════════════════════ --}}
            @if(Auth::user()->isAdmin())
                @php
                    $isAdminDash = request()->routeIs('admin.dashboard');
                    $isScolariteActive = request()->routeIs('admin.academic.*')
                        || request()->routeIs('admin.users.*')
                        || request()->routeIs('admin.students.*')
                        || request()->routeIs('admin.registrations.*')
                        || request()->routeIs('admin.filieres.*')
                        || request()->routeIs('admin.groups.*')
                        || request()->routeIs('admin.schedules.*')
                        || request()->routeIs('admin.reservations.*')
                        || request()->routeIs('admin.student_credits.*');
                    $isExamsActive = request()->routeIs('admin.exams.*')
                        || request()->routeIs('admin.convocations.*')
                        || request()->routeIs('admin.exam_justifications.*')
                        || request()->routeIs('admin.reports.*')
                        || request()->routeIs('admin.analytics.*')
                        || request()->routeIs('admin.retake.*')
                        || request()->routeIs('admin.pv_globaux.*');
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
                        || request()->routeIs('admin.reclamations.*')
                        || request()->routeIs('admin.discipline.*')
                        || request()->routeIs('admin.pilotage.*')
                        || request()->routeIs('admin.archiving.*');
                @endphp

                {{-- Dashboard --}}
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-medium transition-all duration-200 group {{ $isAdminDash ? 'bg-white/10 text-white font-semibold shadow-sm' : 'text-slate-400 hover:text-white hover:bg-white/[0.06]' }}">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $isAdminDash ? 'bg-blue-500/20 text-blue-400' : 'bg-white/[0.04] text-slate-500 group-hover:text-slate-300' }} transition-colors">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                    <span>{{ __('Tableau de bord') }}</span>
                </a>

                {{-- ── Scolarité Section ── --}}
                <div x-data="{ open: {{ $isScolariteActive ? 'true' : 'false' }} }" class="mt-3">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 text-[10px] font-black uppercase tracking-[0.15em] text-slate-500 hover:text-slate-300 transition-colors">
                        <span class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full {{ $isScolariteActive ? 'bg-blue-400' : 'bg-slate-700' }}"></span>
                            {{ __('Scolarité') }}
                        </span>
                        <svg :class="open && 'rotate-180'" class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 -translate-y-1" class="mt-1 space-y-0.5 ml-2" style="display: none;">
                        <a href="{{ route('admin.academic.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('admin.academic.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                            <span class="text-sm">🎓</span> <span>{{ __('Année & Affectations') }}</span>
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('admin.users.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                            <span class="text-sm">👤</span> <span>{{ __('Staff & Professeurs') }}</span>
                        </a>
                        <a href="{{ route('admin.students.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('admin.students.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                            <span class="text-sm">🎒</span> <span>{{ __('Étudiants') }}</span>
                        </a>
                        <a href="{{ route('admin.registrations.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('admin.registrations.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                            <span class="text-sm">📝</span> <span>{{ __('Inscriptions') }}</span>
                        </a>
                        <a href="{{ route('admin.filieres.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('admin.filieres.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                            <span class="text-sm">🏫</span> <span>{{ __('Filières') }}</span>
                        </a>
                        <a href="{{ route('admin.groups.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('admin.groups.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                            <span class="text-sm">👥</span> <span>{{ __('Groupes') }}</span>
                        </a>
                        <a href="{{ route('admin.schedules.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('admin.schedules.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                            <span class="text-sm">📅</span> <span>{{ __('Emploi du temps') }}</span>
                        </a>
                        <a href="{{ route('admin.reservations.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('admin.reservations.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                            <span class="text-sm">🔑</span> <span>{{ __('Réservations') }}</span>
                        </a>
                        <a href="{{ route('admin.student_credits.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('admin.student_credits.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                            <span class="text-sm">🛡️</span> <span>{{ __('Crédits & Dérogations') }}</span>
                        </a>
                    </div>
                </div>

                {{-- ── Examens Section ── --}}
                <div x-data="{ open: {{ $isExamsActive ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 text-[10px] font-black uppercase tracking-[0.15em] text-slate-500 hover:text-slate-300 transition-colors">
                        <span class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full {{ $isExamsActive ? 'bg-pink-400' : 'bg-slate-700' }}"></span>
                            {{ __('Examens & Notes') }}
                        </span>
                        <svg :class="open && 'rotate-180'" class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 -translate-y-1" class="mt-1 space-y-0.5 ml-2" style="display: none;">
                        <a href="{{ route('admin.exams.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('admin.exams.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                            <span class="text-sm">📋</span> <span>{{ __('Gestion des Examens') }}</span>
                        </a>
                        <a href="{{ route('admin.retake.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('admin.retake.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                            <span class="text-sm">🎓</span> <span>{{ __('Rattrapages') }}</span>
                        </a>
                        <a href="{{ route('admin.exams.calendar') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('admin.exams.calendar') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                            <span class="text-sm">📅</span> <span>{{ __('Calendrier Examens') }}</span>
                        </a>
                        <a href="{{ route('admin.convocations.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('admin.convocations.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                            <span class="text-sm">📄</span> <span>{{ __('Convocations') }}</span>
                        </a>
                        <a href="{{ route('admin.convocations.professor_availabilities') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 text-slate-400 hover:text-white hover:bg-white/[0.05]">
                            <span class="text-sm">📅</span> <span>{{ __('Disponibilités Profs') }}</span>
                        </a>
                        <a href="{{ route('admin.exam_justifications.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('admin.exam_justifications.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                            <span class="text-sm">📑</span> <span>{{ __('Justifications') }}</span>
                        </a>
                        <a href="{{ route('admin.pv_globaux.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('admin.pv_globaux.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                            <span class="text-sm">📋</span> <span>{{ __('PV Globaux') }}</span>
                        </a>
                        <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('admin.reports.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                            <span class="text-sm">📊</span> <span>{{ __('Rapports') }}</span>
                        </a>
                        <a href="{{ route('admin.analytics.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('admin.analytics.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                            <span class="text-sm">📈</span> <span>{{ __('Statistiques') }}</span>
                        </a>
                    </div>
                </div>

                {{-- ── Gestion Section ── --}}
                <div x-data="{ open: {{ $isGestionActive ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 text-[10px] font-black uppercase tracking-[0.15em] text-slate-500 hover:text-slate-300 transition-colors">
                        <span class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full {{ $isGestionActive ? 'bg-emerald-400' : 'bg-slate-700' }}"></span>
                            {{ __('Gestion') }}
                        </span>
                        <svg :class="open && 'rotate-180'" class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 -translate-y-1" class="mt-1 space-y-0.5 ml-2" style="display: none;">
                        <a href="{{ route('admin.modules.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('admin.modules.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                            <span class="text-sm">📚</span> <span>{{ __('Modules') }}</span>
                        </a>
                        <a href="{{ route('admin.rooms.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('admin.rooms.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                            <span class="text-sm">🏛️</span> <span>{{ __('Salles') }}</span>
                        </a>
                        <a href="{{ route('admin.grades.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('admin.grades.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                            <span class="text-sm">📊</span> <span>{{ __('Notes') }}</span>
                        </a>
                        <a href="{{ route('admin.reclamations.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('admin.reclamations.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                            <span class="text-sm">💬</span> <span>{{ __('Réclamations') }}</span>
                        </a>
                        <a href="{{ route('admin.absences.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('admin.absences.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                            <span class="text-sm">🚨</span> <span>{{ __('Absences') }}</span>
                        </a>
                        <a href="{{ route('admin.students_risk.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('admin.students_risk.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                            <span class="text-sm">⚠️</span> <span>{{ __('Étudiants à Risque') }}</span>
                        </a>
                        <a href="{{ route('admin.textbooks.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('admin.textbooks.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                            <span class="text-sm">📖</span> <span>{{ __('Cahiers de Textes') }}</span>
                        </a>
                        <a href="{{ route('admin.hours.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('admin.hours.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                            <span class="text-sm">⏱️</span> <span>{{ __('Contrôle des Heures') }}</span>
                        </a>
                        <a href="{{ route('admin.requests.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('admin.requests.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                            <span class="text-sm">📬</span> <span>{{ __('Demandes') }}</span>
                        </a>
                        <a href="{{ route('admin.messages.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('admin.messages.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                            <span class="text-sm">💬</span> <span>{{ __('Messages') }}</span>
                        </a>
                        <a href="{{ route('admin.activity-logs.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('admin.activity-logs.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                            <span class="text-sm">📋</span> <span>{{ __("Journal d'Activité") }}</span>
                        </a>
                        <a href="{{ route('admin.evaluations.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('admin.evaluations.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                            <span class="text-sm">🗳️</span> <span>{{ __('Qualité & Évaluations') }}</span>
                        </a>
                        <a href="{{ route('admin.internships.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('admin.internships.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                            <span class="text-sm">🏛️</span> <span>{{ __('Gestion des Stages') }}</span>
                        </a>
                        <a href="{{ route('admin.appointments.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('admin.appointments.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                            <span class="text-sm">🗓️</span> <span>{{ __('Prise de Rendez-vous') }}</span>
                        </a>
                        <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('admin.settings.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                            <span class="text-sm">⚙️</span> <span>{{ __('Paramètres') }}</span>
                        </a>
                    </div>
                </div>
            @endif

            {{-- ════════════════════════════════════════
                 PROFESSOR NAV
            ════════════════════════════════════════ --}}
            @if(Auth::user()->isProfessor())
                @php
                    $isProfDash = request()->routeIs('professor.dashboard');
                    $isPedagogieActive = request()->routeIs('professor.grades.*')
                        || request()->routeIs('professor.absences.*')
                        || request()->routeIs('professor.textbook.*')
                        || request()->routeIs('professor.reclamations.*');
                    $isSurveillanceActive = request()->routeIs('professor.proctor_convocations.*')
                        || request()->routeIs('professor.availability.*')
                        || request()->routeIs('professor.exam_attendance.*')
                        || request()->routeIs('professor.exams.*');
                    $isServicesActive = request()->routeIs('professor.requests.*')
                        || request()->routeIs('professor.reservations.*');
                    $pendingConvocations = 0;
                    try {
                        $pendingConvocations = Auth::user()->professor
                            ? \App\Models\ProfessorConvocation::where('professor_id', Auth::user()->professor->id)
                                ->whereIn('status', ['generated', 'sent'])
                                ->count()
                            : 0;
                    } catch (\Exception $e) {}
                @endphp

                {{-- Dashboard --}}
                <a href="{{ route('professor.dashboard') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-medium transition-all duration-200 group {{ $isProfDash ? 'bg-white/10 text-white font-semibold shadow-sm' : 'text-slate-400 hover:text-white hover:bg-white/[0.06]' }}">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $isProfDash ? 'bg-blue-500/20 text-blue-400' : 'bg-white/[0.04] text-slate-500 group-hover:text-slate-300' }} transition-colors">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                    <span>{{ __('Tableau de bord') }}</span>
                </a>

                {{-- ── Pédagogie Section ── --}}
                <div x-data="{ open: {{ $isPedagogieActive ? 'true' : 'false' }} }" class="mt-3">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 text-[10px] font-black uppercase tracking-[0.15em] text-slate-500 hover:text-slate-300 transition-colors">
                        <span class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full {{ $isPedagogieActive ? 'bg-blue-400' : 'bg-slate-700' }}"></span>
                            {{ __('Pédagogie') }}
                        </span>
                        <svg :class="open && 'rotate-180'" class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 -translate-y-1" class="mt-1 space-y-0.5 ml-2" style="display: none;">
                        <a href="{{ route('professor.grades.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('professor.grades.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                            <span class="text-sm">📊</span> <span>{{ __('Gestion des Notes') }}</span>
                        </a>
                        <a href="{{ route('professor.absences.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('professor.absences.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                            <span class="text-sm">🚨</span> <span>{{ __('Absences & Présence') }}</span>
                        </a>
                        <a href="{{ route('professor.textbook.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('professor.textbook.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                            <span class="text-sm">📖</span> <span>{{ __('Cahier de Textes') }}</span>
                        </a>
                        <a href="{{ route('professor.hours.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('professor.hours.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                            <span class="text-sm">⏱️</span> <span>{{ __('Suivi des Heures') }}</span>
                        </a>
                        <a href="{{ route('professor.reclamations.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('professor.reclamations.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                            <span class="text-sm">💬</span> <span>{{ __('Réclamations') }}</span>
                        </a>
                        <a href="{{ route('professor.internships.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('professor.internships.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                            <span class="text-sm">🏛️</span> <span>{{ __('Mes Encadrements (Stages)') }}</span>
                        </a>
                    </div>
                </div>

                {{-- ── Examens Section ── --}}
                <div x-data="{ open: {{ $isSurveillanceActive ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 text-[10px] font-black uppercase tracking-[0.15em] text-slate-500 hover:text-slate-300 transition-colors">
                        <span class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full {{ $isSurveillanceActive ? 'bg-pink-400' : 'bg-slate-700' }}"></span>
                            {{ __('Examens & Surveillance') }}
                        </span>
                        <svg :class="open && 'rotate-180'" class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 -translate-y-1" class="mt-1 space-y-0.5 ml-2" style="display: none;">
                        <a href="{{ route('professor.proctor_convocations.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('professor.proctor_convocations.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                            <span class="flex items-center gap-3"><span class="text-sm">🎓</span> <span>{{ __('Mes Surveillances') }}</span></span>
                            @if($pendingConvocations > 0)
                                <span class="bg-pink-500 text-white text-[9px] font-black w-5 h-5 rounded-full flex items-center justify-center animate-pulse">{{ $pendingConvocations }}</span>
                            @endif
                        </a>
                        <a href="{{ route('professor.availability.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('professor.availability.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                            <span class="text-sm">📅</span> <span>{{ __('Disponibilités') }}</span>
                        </a>
                    </div>
                </div>

                {{-- ── Services Section ── --}}
                <div x-data="{ open: {{ $isServicesActive ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 text-[10px] font-black uppercase tracking-[0.15em] text-slate-500 hover:text-slate-300 transition-colors">
                        <span class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full {{ $isServicesActive ? 'bg-emerald-400' : 'bg-slate-700' }}"></span>
                            {{ __('Services') }}
                        </span>
                        <svg :class="open && 'rotate-180'" class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 -translate-y-1" class="mt-1 space-y-0.5 ml-2" style="display: none;">
                        <a href="{{ route('professor.requests.create') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('professor.requests.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                            <span class="text-sm">📬</span> <span>{{ __('Demandes Admin') }}</span>
                        </a>
                        <a href="{{ route('professor.reservations.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('professor.reservations.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                            <span class="text-sm">🔑</span> <span>{{ __('Réservations Salles') }}</span>
                        </a>
                        <a href="{{ route('professor.appointments.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('professor.appointments.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                            <span class="text-sm">🗓️</span> <span>{{ __('Prise de Rendez-vous') }}</span>
                        </a>
                    </div>
                </div>
            @endif

            {{-- ════════════════════════════════════════
                 STUDENT NAV
            ════════════════════════════════════════ --}}
            @if(Auth::user()->isStudent())
                @php
                    $isStudentDash = request()->routeIs('student.dashboard');
                    $newConvocations = 0;
                    try {
                        $newConvocations = Auth::user()->student
                            ? \App\Models\Convocation::where('student_id', Auth::user()->student->id)
                                ->whereIn('status', ['generated', 'sent'])
                                ->count()
                            : 0;
                    } catch (\Exception $e) {}
                @endphp

                {{-- Dashboard --}}
                <a href="{{ route('student.dashboard') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-medium transition-all duration-200 group {{ $isStudentDash ? 'bg-white/10 text-white font-semibold shadow-sm' : 'text-slate-400 hover:text-white hover:bg-white/[0.06]' }}">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $isStudentDash ? 'bg-blue-500/20 text-blue-400' : 'bg-white/[0.04] text-slate-500 group-hover:text-slate-300' }} transition-colors">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                    <span>{{ __('Mon Espace') }}</span>
                </a>

                <div class="mt-3 px-3 py-2">
                    <p class="text-[10px] font-black uppercase tracking-[0.15em] text-slate-500 flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-700"></span>
                        {{ __('Académique') }}
                    </p>
                </div>
                <div class="space-y-0.5 ml-2">
                    <a href="{{ route('student.grades') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('student.grades') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                        <span class="text-sm">📊</span> <span>{{ __('Mes Notes') }}</span>
                    </a>
                    <a href="{{ route('student.reclamations.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('student.reclamations.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                        <span class="text-sm">💬</span> <span>{{ __('Réclamations') }}</span>
                    </a>
                    <a href="{{ route('student.retake.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('student.retake.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                        <span class="text-sm">🎓</span> <span>{{ __('Rattrapages') }}</span>
                    </a>
                    <a href="{{ route('student.absences') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('student.absences') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                        <span class="text-sm">🚨</span> <span>{{ __('Mes Absences') }}</span>
                    </a>
                    <a href="{{ route('student.exams.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('student.exams.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                        <span class="text-sm">📋</span> <span>{{ __('Mes Examens') }}</span>
                    </a>
                    <a href="{{ route('student.convocations.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('student.convocations.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                        <span class="flex items-center gap-3"><span class="text-sm">📄</span> <span>{{ __('Convocations') }}</span></span>
                        @if($newConvocations > 0)
                            <span class="bg-pink-500 text-white text-[9px] font-black w-5 h-5 rounded-full flex items-center justify-center animate-pulse">{{ $newConvocations }}</span>
                        @endif
                    </a>
                    <a href="{{ route('student.requests.create') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('student.requests.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                        <span class="text-sm">📬</span> <span>{{ __('Demandes') }}</span>
                    </a>
                    @if(\App\Models\Setting::get('evaluation_open', false))
                    <a href="{{ route('student.evaluations.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('student.evaluations.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                        <span class="text-sm">🗳️</span> <span>{{ __('Évaluer mes Cours') }}</span>
                    </a>
                    @endif
                    <a href="{{ route('student.internships.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('student.internships.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                        <span class="text-sm">💼</span> <span>{{ __('Mon Stage') }}</span>
                    </a>
                    <a href="{{ route('student.appointments.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('student.appointments.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                        <span class="text-sm">🗓️</span> <span>{{ __('Prise de Rendez-vous') }}</span>
                    </a>
                </div>
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
            <div x-data="{ open: {{ $isCommunsActive ? 'true' : 'false' }} }" class="mt-3">
                <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 text-[10px] font-black uppercase tracking-[0.15em] text-slate-500 hover:text-slate-300 transition-colors">
                    <span class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full {{ $isCommunsActive ? 'bg-amber-400' : 'bg-slate-700' }}"></span>
                        {{ __('Outils') }}
                    </span>
                    <svg :class="open && 'rotate-180'" class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 -translate-y-1" class="mt-1 space-y-0.5 ml-2" style="display: none;">
                    <a href="{{ route('classroom.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('classroom.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                        <span class="text-sm">🖥️</span> <span>{{ __('Classroom') }}</span>
                    </a>
                    <a href="{{ route('chat.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('chat.*') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                        <span class="text-sm">💬</span> <span>{{ __('Messagerie') }}</span>
                    </a>
                    <a href="{{ route('calendar') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('calendar') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                        <span class="text-sm">📆</span> <span>{{ __('Calendrier') }}</span>
                    </a>
                    <a href="{{ route('faq') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150 {{ request()->routeIs('faq') ? 'text-white bg-white/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.05]' }}">
                        <span class="text-sm">❓</span> <span>{{ __('FAQ') }}</span>
                    </a>
                </div>
            </div>

        </nav>

        {{-- ── User Card (bottom of sidebar) ── --}}
        <div class="shrink-0 border-t border-white/[0.06] p-3">
            <div class="flex items-center gap-3 px-2 py-2.5 rounded-xl hover:bg-white/[0.05] transition-colors">
                <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-blue-500 to-pink-500 flex items-center justify-center text-white text-xs font-black uppercase shadow-lg shadow-blue-500/20 shrink-0">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-white text-[13px] font-bold truncate">{{ Auth::user()->name }}</div>
                    <div class="text-slate-500 text-[10px] font-semibold truncate">{{ Auth::user()->email }}</div>
                </div>
                <span class="px-2 py-0.5 bg-blue-500/20 text-blue-400 text-[8px] font-black rounded-full uppercase tracking-widest shrink-0">
                    {{ Auth::user()->role->name ?? 'user' }}
                </span>
            </div>
        </div>

    </aside>

    {{-- ══════════════════════════════════════
         SLIM TOP BAR
    ══════════════════════════════════════ --}}
    <header class="fixed top-0 {{ app()->getLocale() == 'ar' ? 'left-0 right-0 lg:right-[280px]' : 'right-0 left-0 lg:left-[280px]' }} h-16 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-100 dark:border-slate-800 z-30 transition-all duration-300">
        <div class="h-full flex items-center justify-between px-4 sm:px-6">
            
            {{-- Left: Mobile hamburger --}}
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden w-10 h-10 flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-white rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>

            {{-- Right: Actions --}}
            <div class="flex items-center gap-1.5 sm:gap-2 ml-auto">

                {{-- Search Button (Ctrl+K) --}}
                <button @click="$dispatch('open-spotlight')"
                    class="flex items-center justify-center 2xl:justify-between gap-2 h-10 w-10 2xl:w-48 bg-slate-50/70 dark:bg-slate-800/70 border border-slate-200/60 dark:border-slate-700/60 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl px-2 2xl:px-3.5 transition-all duration-200 text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 shadow-sm"
                    title="{{ __('Rechercher (Ctrl+K)') }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <span class="hidden 2xl:inline text-xs font-semibold">{{ __('Rechercher...') }}</span>
                    <kbd class="hidden 2xl:inline-flex bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-[9px] text-slate-400 dark:text-slate-500 px-1.5 py-0.5 rounded font-black shadow-sm">Ctrl K</kbd>
                </button>

                {{-- Dark Mode Toggle --}}
                <div x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val)); if(darkMode) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark');">
                    <button @click="darkMode = !darkMode; if(darkMode) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark');"
                            class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-upf-blue dark:hover:text-amber-400 transition-colors rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 border border-transparent hover:border-slate-100 dark:hover:border-slate-700/50">
                        <template x-if="!darkMode">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                        </template>
                        <template x-if="darkMode">
                            <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </template>
                    </button>
                </div>

                {{-- Accent Color Selector --}}
                <x-dropdown align="{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}" width="48" contentClasses="py-1 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-2xl p-1">
                    <x-slot name="trigger">
                        <button class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-upf-blue dark:hover:text-blue-400 transition-colors rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 border border-transparent hover:border-slate-100 dark:hover:border-slate-700/50"
                                title="{{ __('Personnaliser le thème') }}">
                            🎨
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <div class="px-3 py-2 border-b border-slate-100 dark:border-slate-800/80 mb-1">
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('Couleur d\'accent') }}</span>
                        </div>
                        <button onclick="window.applyThemeAccent('blue')" class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-slate-750 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors">
                            <span class="w-3.5 h-3.5 rounded-full bg-[#003893] border border-white dark:border-slate-900 shadow-sm shrink-0"></span>
                            <span>Classic Blue</span>
                        </button>
                        <button onclick="window.applyThemeAccent('magenta')" class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-slate-750 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors">
                            <span class="w-3.5 h-3.5 rounded-full bg-[#E6007E] border border-white dark:border-slate-900 shadow-sm shrink-0"></span>
                            <span>Creative Magenta</span>
                        </button>
                        <button onclick="window.applyThemeAccent('indigo')" class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-slate-750 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors">
                            <span class="w-3.5 h-3.5 rounded-full bg-[#6366F1] border border-white dark:border-slate-900 shadow-sm shrink-0"></span>
                            <span>Indigo Innovation</span>
                        </button>
                        <button onclick="window.applyThemeAccent('emerald')" class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-slate-750 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors">
                            <span class="w-3.5 h-3.5 rounded-full bg-[#10B981] border border-white dark:border-slate-900 shadow-sm shrink-0"></span>
                            <span>Emerald Success</span>
                        </button>
                    </x-slot>
                </x-dropdown>

                {{-- Notifications Bell --}}
                <div x-data="{
                    unreadCount: 0,
                    notifications: [],
                    async updateNotifications() {
                        try {
                            const countRes = await fetch('{{ route('api.notifications.unread_count') }}');
                            const countData = await countRes.json();
                            this.unreadCount = countData.unread_count;

                            const listRes = await fetch('{{ route('api.notifications.latest') }}');
                            this.notifications = await listRes.json();
                        } catch (e) {
                            console.error('Error fetching notifications:', e);
                        }
                    },
                    async markRead(id, url) {
                        try {
                            await fetch('/notifications/' + id + '/read', {
                                method: 'POST',
                                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
                            });
                        } catch (e) {}
                        window.location.href = url;
                    },
                    init() {
                        this.updateNotifications();
                        // Poll every 5 seconds for a dynamic, real-time feel
                        setInterval(() => { this.updateNotifications() }, 5000);
                    }
                }">
                    <x-dropdown align="{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}" width="80" contentClasses="py-1 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-2xl overflow-hidden">
                        <x-slot name="trigger">
                            <button class="relative w-10 h-10 flex items-center justify-center text-slate-400 hover:text-upf-blue transition-colors rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 border border-transparent hover:border-slate-100 dark:hover:border-slate-700/50">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                <span x-show="unreadCount > 0" x-text="unreadCount"
                                    class="absolute top-1.5 right-1.5 w-4 h-4 bg-upf-magenta text-white text-[9px] font-black rounded-full flex items-center justify-center border-2 border-white dark:border-slate-900 animate-pulse"
                                    style="display: none;">
                                </span>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <div class="w-80">
                                <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                    <h4 class="text-xs font-black uppercase tracking-widest text-slate-700 dark:text-slate-300">🔔 Notifications</h4>
                                    <form x-show="unreadCount > 0" action="{{ route('notifications.markAllRead') }}" method="POST" style="display: none;">
                                        @csrf
                                        <button type="submit" class="text-[10px] font-black text-upf-blue hover:text-upf-magenta transition-colors uppercase tracking-wide">Tout lire</button>
                                    </form>
                                </div>
                                <div class="max-h-80 overflow-y-auto divide-y divide-slate-50 dark:divide-slate-800/50">
                                    <template x-for="n in notifications" :key="n.id">
                                        <a @click.prevent="markRead(n.id, n.url)" href="#"
                                           :class="n.is_unread ? 'bg-indigo-50/30 dark:bg-indigo-950/10' : ''"
                                           class="flex items-start gap-3 p-4 hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-all">
                                            <div class="text-xl flex-shrink-0 mt-0.5" x-text="n.icon"></div>
                                            <div class="flex-1 min-w-0">
                                                <p :class="n.is_unread ? 'text-upf-blue dark:text-blue-400 font-black' : 'text-slate-900 dark:text-white'"
                                                   class="text-xs leading-tight truncate" x-text="n.title"></p>
                                                <p class="text-[10px] text-slate-500 mt-0.5 leading-snug line-clamp-2" x-text="n.message"></p>
                                                <p class="text-[9px] text-slate-400 mt-1 font-bold" x-text="n.time"></p>
                                            </div>
                                            <div x-show="n.is_unread" class="w-2 h-2 rounded-full bg-upf-blue dark:bg-blue-400 flex-shrink-0 mt-2"></div>
                                        </a>
                                    </template>
                                    <div x-show="notifications.length === 0" class="text-center py-10 px-4">
                                        <div class="text-4xl mb-3">🔕</div>
                                        <p class="text-sm font-black text-slate-400">Tout est calme ici !</p>
                                        <p class="text-xs text-slate-300 mt-1">Vous n'avez aucune notification.</p>
                                    </div>
                                </div>
                            </div>
                        </x-slot>
                    </x-dropdown>
                </div>

                {{-- Language Switcher --}}
                <x-dropdown align="{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}" width="48" contentClasses="py-1 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-2xl p-1">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-1.5 px-3 py-2 text-xs leading-4 font-bold rounded-xl text-upf-magenta dark:text-pink-400 bg-pink-50/50 dark:bg-pink-950/20 hover:bg-pink-100/50 dark:hover:bg-pink-900/30 transition ease-in-out duration-150 uppercase tracking-wider border border-transparent hover:border-pink-200/40">
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

                {{-- User Dropdown --}}
                <x-dropdown align="{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}" width="56" contentClasses="py-1 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-2xl p-1 overflow-hidden">
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
        </div>
    </header>

    {{-- ══════════════════════════════════════
         PWA MOBILE BOTTOM NAVIGATION DOCK
    ══════════════════════════════════════ --}}
    <div class="lg:hidden fixed bottom-0 left-0 right-0 h-16 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-t border-slate-100 dark:border-slate-800/80 z-45 flex items-center justify-around px-4 shadow-lg transition-colors duration-300">
        
        <!-- Home -->
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center text-slate-400 hover:text-upf-blue transition-colors {{ request()->routeIs('dashboard') || request()->routeIs('*.dashboard') ? 'text-upf-blue dark:text-blue-400 font-bold' : '' }}">
            <span class="text-xl">🏠</span>
            <span class="text-[9px] uppercase tracking-wider font-extrabold mt-0.5">{{ __('Accueil') }}</span>
        </a>

        <!-- Calendar -->
        <a href="{{ route('calendar') }}" class="flex flex-col items-center justify-center text-slate-400 hover:text-upf-blue transition-colors {{ request()->routeIs('calendar') ? 'text-upf-blue dark:text-blue-400 font-bold' : '' }}">
            <span class="text-xl">📅</span>
            <span class="text-[9px] uppercase tracking-wider font-extrabold mt-0.5">{{ __('Planning') }}</span>
        </a>

        <!-- Messaging -->
        <a href="{{ route('chat.index') }}" class="flex flex-col items-center justify-center text-slate-400 hover:text-upf-blue transition-colors {{ request()->routeIs('chat.*') ? 'text-upf-blue dark:text-blue-400 font-bold' : '' }}">
            <span class="text-xl">💬</span>
            <span class="text-[9px] uppercase tracking-wider font-extrabold mt-0.5">{{ __('Chat') }}</span>
        </a>

        <!-- Services -->
        <a href="{{ Auth::user()->isStudent() ? route('student.requests.index') : (Auth::user()->isProfessor() ? route('professor.requests.create') : route('admin.requests.index')) }}" class="flex flex-col items-center justify-center text-slate-400 hover:text-upf-blue transition-colors {{ request()->routeIs('*requests.*') ? 'text-upf-blue dark:text-blue-400 font-bold' : '' }}">
            <span class="text-xl">📬</span>
            <span class="text-[9px] uppercase tracking-wider font-extrabold mt-0.5">{{ __('Services') }}</span>
        </a>

    </div>

</div>
