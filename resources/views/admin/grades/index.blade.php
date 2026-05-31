<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tight italic">
            {{ __('Gestion Globale des Notes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Hero --}}
            <div class="bg-gradient-to-r from-upf-navy to-upf-magenta rounded-3xl p-10 text-white shadow-2xl relative overflow-hidden mb-8">
                <div class="relative z-10">
                    <h2 class="text-3xl font-black mb-2">{{ __('Console d\'Administration des Notes') }} 🎯</h2>
                    <p class="text-blue-100 opacity-80">Sélectionnez une filière, puis un groupe — les modules concernés s'affichent automatiquement.</p>
                </div>
                <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
            </div>

            <div class="bg-white dark:bg-slate-900 overflow-hidden shadow-xl sm:rounded-3xl border border-gray-100 dark:border-slate-800">
                <div class="p-8 lg:p-12">

                    @if(session('success'))
                        <div class="mb-8 p-4 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-200 flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Alpine.js Component with cascade logic --}}
                    <div
                        x-data="{
                            /* ---- State ---- */
                            filiereId: '',
                            groupId: '',
                            moduleId: '',

                            /* ---- Static data from server ---- */
                            allGroups: {{ $groups->map->only('id','name','level','filiere_id')->toJson() }},
                            allModules: {{ $modules->map->only('id','name','code','filiere_id')->toJson() }},

                            /* ---- Dynamic data fetched via API ---- */
                            filteredGroups: [],
                            filteredModules: [],
                            loadingModules: false,

                            /* ---- Computed: filter groups when filiere changes ---- */
                            onFiliereChange() {
                                this.groupId   = '';
                                this.moduleId  = '';
                                this.filteredModules = [];
                                if (this.filiereId) {
                                    this.filteredGroups = this.allGroups.filter(g => g.filiere_id == this.filiereId);
                                } else {
                                    this.filteredGroups = this.allGroups;
                                }
                            },

                            /* ---- Fetch modules for the chosen group via API ---- */
                            async onGroupChange() {
                                this.moduleId = '';
                                this.filteredModules = [];
                                if (!this.groupId) return;

                                this.loadingModules = true;
                                try {
                                    const url = '/admin/api/groups/' + this.groupId + '/modules';
                                    const res = await fetch(url, {
                                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                                    });
                                    this.filteredModules = await res.json();
                                } catch(e) {
                                    console.error('Erreur chargement modules:', e);
                                    /* Fallback: filter by filiere */
                                    this.filteredModules = this.allModules.filter(m => !this.filiereId || m.filiere_id == this.filiereId);
                                } finally {
                                    this.loadingModules = false;
                                }
                            },

                            init() {
                                this.filteredGroups  = this.allGroups;
                                this.filteredModules = [];
                            }
                        }"
                        x-init="init()"
                    >
                        <form method="GET" action="{{ route('admin.grades.edit') }}" class="space-y-8">

                            {{-- ========= ÉTAPE 1 : FILIÈRE ========= --}}
                            <div>
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-8 h-8 rounded-full bg-upf-magenta text-white flex items-center justify-center text-xs font-black">1</div>
                                    <label class="block text-sm font-black text-upf-magenta tracking-widest uppercase">
                                        Choisir la Filière
                                    </label>
                                </div>
                                <div class="relative">
                                    <select
                                        x-model="filiereId"
                                        @change="onFiliereChange()"
                                        class="block w-full rounded-2xl border border-pink-200 bg-pink-50/40 shadow-sm focus:border-upf-magenta focus:ring-upf-magenta text-base py-4 pl-6 pr-10 appearance-none font-bold text-gray-800">
                                        <option value="">— Toutes les filières —</option>
                                        @foreach($filieres as $filiere)
                                            <option value="{{ $filiere->id }}">🏛️ {{ $filiere->name }} ({{ $filiere->code }})</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-upf-magenta">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </div>
                            </div>

                            {{-- Divider with arrow --}}
                            <div class="flex items-center gap-4">
                                <div class="flex-1 h-px bg-gray-100"></div>
                                <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                <div class="flex-1 h-px bg-gray-100"></div>
                            </div>

                            {{-- ========= ÉTAPE 2 + 3 en GRID ========= --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                                {{-- GROUPE --}}
                                <div>
                                    <div class="flex items-center gap-3 mb-4">
                                        <div class="w-8 h-8 rounded-full bg-upf-blue text-white flex items-center justify-center text-xs font-black">2</div>
                                        <label for="group_id" class="block text-sm font-black text-upf-blue tracking-widest uppercase">
                                            Sélectionner un Groupe
                                        </label>
                                    </div>
                                    <div class="relative">
                                        <select
                                            name="group_id"
                                            id="group_id"
                                            required
                                            x-model="groupId"
                                            @change="onGroupChange()"
                                            class="block w-full rounded-2xl border border-gray-200 bg-gray-50 shadow-sm focus:border-upf-blue focus:ring-upf-blue text-base py-4 pl-6 pr-10 appearance-none font-bold text-gray-800">
                                            <option value="" disabled selected>Choisir un groupe...</option>
                                            <template x-for="group in filteredGroups" :key="group.id">
                                                <option :value="group.id" x-text="'👥 ' + group.name + ' (' + group.level + ')'"></option>
                                            </template>
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-upf-blue">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </div>
                                    </div>
                                    {{-- Helper text --}}
                                    <p class="mt-2 text-[10px] text-gray-400 font-bold uppercase tracking-widest"
                                        x-show="filiereId && filteredGroups.length === 0">
                                        ⚠️ Aucun groupe dans cette filière.
                                    </p>
                                </div>

                                {{-- MODULE --}}
                                <div>
                                    <div class="flex items-center gap-3 mb-4">
                                        <div class="w-8 h-8 rounded-full text-white flex items-center justify-center text-xs font-black transition-colors"
                                            :class="groupId ? 'bg-emerald-600' : 'bg-gray-300'">3</div>
                                        <label for="module_id" class="block text-sm font-black tracking-widest uppercase transition-colors"
                                            :class="groupId ? 'text-emerald-700' : 'text-gray-400'">
                                            Module du Groupe
                                        </label>
                                    </div>
                                    <div class="relative">
                                        <select
                                            name="module_id"
                                            id="module_id"
                                            required
                                            x-model="moduleId"
                                            :disabled="!groupId || loadingModules"
                                            class="block w-full rounded-2xl border border-gray-200 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-base py-4 pl-6 pr-10 appearance-none font-bold text-gray-800 transition-all"
                                            :class="groupId && !loadingModules ? 'bg-emerald-50/40 border-emerald-200' : 'bg-gray-100 opacity-60 cursor-not-allowed'">
                                            <option value="" disabled selected x-text="loadingModules ? 'Chargement...' : (groupId ? 'Choisir un module...' : 'Choisissez d\'abord un groupe')"></option>
                                            <template x-for="module in filteredModules" :key="module.id">
                                                <option :value="module.id" x-text="'📘 ' + module.name + ' (' + module.code + ')'"></option>
                                            </template>
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4"
                                            :class="groupId ? 'text-emerald-600' : 'text-gray-300'">
                                            <svg x-show="!loadingModules" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            {{-- Spinner --}}
                                            <svg x-show="loadingModules" class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    {{-- Module count badge --}}
                                    <p class="mt-2 text-[10px] font-bold uppercase tracking-widest transition-colors"
                                        :class="filteredModules.length > 0 ? 'text-emerald-600' : 'text-gray-400'"
                                        x-show="groupId && !loadingModules"
                                        x-text="filteredModules.length + ' module(s) disponible(s) pour ce groupe'">
                                    </p>
                                </div>
                            </div>

                            {{-- Submit with Exports --}}
                            <div class="pt-6 border-t border-gray-100 dark:border-slate-800 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                                <div class="flex flex-wrap gap-2 items-center">
                                    <span class="text-xs font-black uppercase tracking-wider text-slate-400 mr-2">Outils d'export :</span>
                                    
                                    {{-- Global Stats --}}
                                    <a
                                        href="{{ route('admin.export.statistics') }}"
                                        class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-blue-50/50 hover:bg-blue-100 text-upf-blue text-xs font-bold rounded-xl border border-blue-100/50 transition-all duration-200 transform hover:-translate-y-0.5 shadow-sm"
                                    >
                                        📊 Statistiques Globales
                                    </a>

                                    {{-- All grades --}}
                                    <a
                                        href="{{ route('admin.export.grades') }}"
                                        class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-bold rounded-xl border border-slate-200/60 transition-all duration-200 transform hover:-translate-y-0.5 shadow-sm"
                                    >
                                        📁 Toutes les Notes
                                    </a>

                                    {{-- Group Export (contextual) --}}
                                    <a
                                        x-show="groupId"
                                        :href="'/admin/export/grades/group/' + groupId"
                                        class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-pink-50 hover:bg-pink-100 text-upf-magenta text-xs font-bold rounded-xl border border-pink-100 transition-all duration-200 transform hover:-translate-y-0.5 shadow-sm"
                                        style="display:none;"
                                    >
                                        👥 Notes du Groupe
                                    </a>

                                    {{-- Module Export (contextual) --}}
                                    <a
                                        x-show="moduleId"
                                        :href="'/admin/export/grades/module/' + moduleId"
                                        class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold rounded-xl border border-emerald-100 transition-all duration-200 transform hover:-translate-y-0.5 shadow-sm"
                                        style="display:none;"
                                    >
                                        📘 Notes du Module
                                    </a>
                                </div>

                                <button
                                    type="submit"
                                    :disabled="!groupId || !moduleId"
                                    :class="(groupId && moduleId) ? 'bg-upf-blue hover:bg-upf-navy cursor-pointer shadow-xl hover:-translate-y-1' : 'bg-gray-300 cursor-not-allowed'"
                                    class="inline-flex items-center px-10 py-4 border border-transparent rounded-2xl font-black text-white uppercase tracking-widest transition-all duration-200 transform text-sm">
                                    {{ __('Ouvrir le Registre des Notes') }}
                                    <svg class="ml-3 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                </button>
                            </div>

                        </form>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
