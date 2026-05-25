<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tighter italic">
            {{ __('Espace Classroom UPF') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Hero Header -->
            <div class="bg-gradient-to-br from-indigo-900 via-upf-blue to-upf-magenta rounded-[2.5rem] p-10 text-white shadow-2xl relative overflow-hidden">
                <div class="relative z-10">
                    <h2 class="text-4xl font-black mb-2 italic text-blue-50">Espaces d'Échange & Cours</h2>
                    <p class="text-blue-100 opacity-90 text-sm max-w-2xl">Bienvenue dans votre espace d'échange académique. Retrouvez vos modules, partagez des supports de cours et échangez avec vos professeurs et collègues.</p>
                </div>
                <div class="absolute -top-24 -right-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            </div>

            <!-- Search & Filter Bar -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <div class="flex flex-col md:flex-row gap-3">
                    <!-- Search Input -->
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input 
                            id="search-input"
                            type="text" 
                            placeholder="Rechercher un module, un groupe..." 
                            class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-upf-blue/30 focus:border-upf-blue transition-all"
                        >
                    </div>

                    <!-- Filter: Groupe -->
                    <div class="relative">
                        <select id="filter-group" class="appearance-none pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 focus:outline-none focus:ring-2 focus:ring-upf-blue/30 focus:border-upf-blue transition-all cursor-pointer">
                            <option value="">📁 Tous les groupes</option>
                            @foreach(collect($classes)->pluck('group')->unique('id') as $group)
                                <option value="{{ $group->name }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>

                    <!-- Filter: Activité -->
                    <div class="relative">
                        <select id="filter-activity" class="appearance-none pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 focus:outline-none focus:ring-2 focus:ring-upf-blue/30 focus:border-upf-blue transition-all cursor-pointer">
                            <option value="">⚡ Toutes les classes</option>
                            <option value="active">✅ Avec activité</option>
                            <option value="inactive">💤 Sans activité</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>

                    <!-- Reset Button -->
                    <button id="reset-filters" class="px-5 py-3 bg-gray-100 hover:bg-red-50 hover:text-red-500 hover:border-red-200 border border-gray-200 rounded-xl text-sm font-black text-gray-500 transition-all duration-200 whitespace-nowrap">
                        ✕ Réinitialiser
                    </button>
                </div>

                <!-- Active Filter Tags -->
                <div id="filter-tags" class="hidden flex-wrap gap-2 mt-3 pt-3 border-t border-gray-100">
                    <!-- Tags will be injected here by JS -->
                </div>
            </div>

            <!-- Classrooms Grid -->
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-black text-gray-900 italic tracking-tight">Mes Classes Actives 📚</h3>
                    <span id="results-count" class="text-xs font-bold text-gray-400 uppercase tracking-widest bg-white border border-gray-100 px-3 py-1.5 rounded-xl shadow-sm">{{ count($classes) }} Classes</span>
                </div>

                @if(empty($classes))
                    <div class="bg-white p-24 rounded-[2.5rem] text-center border border-gray-100 shadow-sm">
                        <div class="text-5xl mb-4">🏫</div>
                        <p class="text-gray-400 italic font-bold">Vous n'avez aucun espace Classroom actif dans votre emploi du temps actuel.</p>
                    </div>
                @else
                    <!-- No Results State (hidden by default) -->
                    <div id="no-results" class="hidden bg-white p-24 rounded-[2.5rem] text-center border border-gray-100 shadow-sm">
                        <div class="text-5xl mb-4">🔍</div>
                        <p class="text-gray-800 font-black text-lg mb-2">Aucun résultat trouvé</p>
                        <p class="text-gray-400 italic font-bold">Essayez de modifier vos filtres ou votre recherche.</p>
                    </div>

                    <div id="classes-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach($classes as $class)
                            @php
                                $gradients = [
                                    'from-blue-600 to-indigo-800',
                                    'from-emerald-500 to-teal-700',
                                    'from-rose-500 to-pink-700',
                                    'from-amber-500 to-orange-600',
                                    'from-indigo-600 to-purple-800',
                                    'from-violet-500 to-fuchsia-700',
                                ];
                                $grad = $gradients[crc32($class['module']->id) % count($gradients)];
                            @endphp
                            <div 
                                class="classroom-card bg-white rounded-[2.5rem] border border-gray-100 shadow-sm hover:shadow-2xl hover:scale-[1.02] transform transition-all duration-300 overflow-hidden flex flex-col justify-between group"
                                data-module="{{ strtolower($class['module']->name) }}"
                                data-code="{{ strtolower($class['module']->code ?? '') }}"
                                data-group="{{ strtolower($class['group']->name) }}"
                                data-professor="{{ strtolower($class['professor']?->user?->name ?? '') }}"
                                data-has-activity="{{ $class['last_post'] ? 'active' : 'inactive' }}"
                            >
                                <div>
                                    <!-- Card Header -->
                                    <div class="bg-gradient-to-br {{ $grad }} p-6 text-white relative">
                                        <div class="flex justify-between items-start mb-4">
                                            <span class="text-[9px] font-black uppercase tracking-widest bg-white/20 backdrop-blur-md px-2.5 py-1 rounded-md border border-white/10">
                                                {{ $class['group']->name }}
                                            </span>
                                            <svg class="w-6 h-6 opacity-40 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                        </div>
                                        <h4 class="text-lg font-black leading-tight tracking-tight mb-1 truncate">{{ $class['module']->name }}</h4>
                                        <p class="text-[10px] text-white/70 font-bold uppercase tracking-wide truncate">Module ID: {{ $class['module']->code ?? 'MOD-' . $class['module']->id }}</p>
                                    </div>

                                    <!-- Card Content -->
                                    <div class="p-5 space-y-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-center font-black text-sm text-upf-blue">👨‍🏫</div>
                                            <div>
                                                <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Enseignant</p>
                                                <p class="text-xs font-black text-gray-800">{{ $class['professor']?->user?->name ?? 'Non assigné' }}</p>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-2">
                                            <div class="bg-gray-50 rounded-xl p-2.5 text-center border border-gray-100">
                                                <p class="text-lg font-black text-upf-blue">{{ $class['post_count'] }}</p>
                                                <p class="text-[8px] font-black uppercase tracking-widest text-gray-400">Publications</p>
                                            </div>
                                            <div class="bg-pink-50 rounded-xl p-2.5 text-center border border-pink-100">
                                                <p class="text-lg font-black text-upf-magenta">{{ $class['file_count'] }}</p>
                                                <p class="text-[8px] font-black uppercase tracking-widest text-gray-400">Supports</p>
                                            </div>
                                        </div>

                                        @if($class['last_post'])
                                        <p class="text-[9px] text-gray-400 font-bold flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block"></span>
                                            Dernière activité : {{ $class['last_post']->created_at->diffForHumans() }}
                                        </p>
                                        @else
                                        <p class="text-[9px] text-gray-300 font-bold flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-200 inline-block"></span>
                                            Aucune activité pour l'instant
                                        </p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Card Footer Action -->
                                <div class="p-6 pt-0">
                                    <a href="{{ route('classroom.show', [$class['group']->id, $class['module']->id]) }}" class="block w-full text-center py-3.5 bg-gray-50 hover:bg-upf-blue hover:text-white rounded-2xl text-xs font-black uppercase tracking-wider text-upf-blue border border-gray-100 hover:border-upf-blue transition-all duration-300 shadow-sm">
                                        Accéder à la classe
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search-input');
        const filterGroup = document.getElementById('filter-group');
        const filterActivity = document.getElementById('filter-activity');
        const resetBtn = document.getElementById('reset-filters');
        const cards = document.querySelectorAll('.classroom-card');
        const noResults = document.getElementById('no-results');
        const resultsCount = document.getElementById('results-count');
        const filterTagsContainer = document.getElementById('filter-tags');

        function applyFilters() {
            const searchVal = searchInput.value.toLowerCase().trim();
            const groupVal = filterGroup.value.toLowerCase();
            const activityVal = filterActivity.value;
            let visible = 0;

            cards.forEach(card => {
                const module = card.dataset.module;
                const code = card.dataset.code;
                const group = card.dataset.group;
                const professor = card.dataset.professor;
                const hasActivity = card.dataset.hasActivity;

                const matchSearch = !searchVal || 
                    module.includes(searchVal) || 
                    code.includes(searchVal) || 
                    professor.includes(searchVal) ||
                    group.includes(searchVal);
                    
                const matchGroup = !groupVal || group.includes(groupVal);
                const matchActivity = !activityVal || hasActivity === activityVal;

                if (matchSearch && matchGroup && matchActivity) {
                    card.style.display = '';
                    visible++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Update count
            resultsCount.textContent = visible + ' Classes';

            // Show/hide no results
            if (noResults) {
                noResults.classList.toggle('hidden', visible > 0);
            }

            // Update filter tags
            updateTags(searchVal, groupVal, activityVal);
        }

        function updateTags(search, group, activity) {
            filterTagsContainer.innerHTML = '';
            const tags = [];

            if (search) tags.push({ label: '🔍 ' + search, clear: () => { searchInput.value = ''; applyFilters(); } });
            if (group) tags.push({ label: '📁 ' + group, clear: () => { filterGroup.value = ''; applyFilters(); } });
            if (activity) tags.push({ label: activity === 'active' ? '✅ Avec activité' : '💤 Sans activité', clear: () => { filterActivity.value = ''; applyFilters(); } });

            if (tags.length > 0) {
                filterTagsContainer.classList.remove('hidden');
                filterTagsContainer.classList.add('flex');
                tags.forEach(tag => {
                    const el = document.createElement('button');
                    el.className = 'flex items-center gap-1.5 px-3 py-1 bg-upf-blue/10 text-upf-blue rounded-lg text-xs font-black hover:bg-upf-blue/20 transition-colors';
                    el.innerHTML = tag.label + ' <span class="text-upf-blue/60">✕</span>';
                    el.addEventListener('click', tag.clear);
                    filterTagsContainer.appendChild(el);
                });
            } else {
                filterTagsContainer.classList.add('hidden');
                filterTagsContainer.classList.remove('flex');
            }
        }

        searchInput.addEventListener('input', applyFilters);
        filterGroup.addEventListener('change', applyFilters);
        filterActivity.addEventListener('change', applyFilters);

        resetBtn.addEventListener('click', function() {
            searchInput.value = '';
            filterGroup.value = '';
            filterActivity.value = '';
            applyFilters();
        });
    });
    </script>
</x-app-layout>
