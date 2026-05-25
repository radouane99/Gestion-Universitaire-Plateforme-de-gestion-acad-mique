<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tight italic">
            Années Universitaires & Affectations
        </h2>
    </x-slot>

    <div class="py-10 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <x-alert-messages />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Années Universitaires -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                    <h3 class="text-lg font-black text-upf-blue mb-6">Gestion des Années</h3>
                    
                    <form action="{{ route('admin.academic.year.store') }}" method="POST" class="mb-8 flex gap-4">
                        @csrf
                        <div class="flex-1">
                            <input type="text" name="name" placeholder="Ex: 2026/2027" required class="w-full rounded-xl border-gray-200 focus:ring-upf-magenta focus:border-upf-magenta text-sm font-bold shadow-sm">
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="set_current" id="set_current" class="rounded text-upf-magenta focus:ring-upf-magenta shadow-sm">
                            <label for="set_current" class="text-xs font-bold text-gray-600">Année courante</label>
                        </div>
                        <button type="submit" class="bg-upf-blue hover:bg-upf-navy text-white px-4 py-2 rounded-xl text-sm font-bold shadow-md transition-colors">
                            Ajouter
                        </button>
                    </form>

                    <div class="space-y-3">
                        @foreach($academicYears as $year)
                            <div class="flex items-center justify-between p-4 rounded-xl border {{ $year->is_current ? 'border-upf-magenta bg-pink-50/30' : 'border-gray-100 bg-gray-50' }}">
                                <div class="flex items-center gap-3">
                                    <span class="font-black text-gray-800">{{ $year->name }}</span>
                                    @if($year->is_current)
                                        <span class="bg-upf-magenta text-white text-[10px] font-black uppercase tracking-widest px-2 py-1 rounded-md shadow-sm">Courante</span>
                                    @endif
                                </div>
                                @if(!$year->is_current)
                                    <form action="{{ route('admin.academic.year.current', $year) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-xs font-bold text-upf-blue hover:text-upf-magenta transition-colors">
                                            Définir comme courante
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Semestres -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                    <h3 class="text-lg font-black text-upf-blue mb-6">Semestres (Lecture seule)</h3>
                    <div class="grid grid-cols-2 gap-4">
                        @foreach($semesters as $semester)
                            <div class="p-4 rounded-xl border border-gray-100 bg-gray-50 flex items-center justify-between">
                                <span class="font-black text-gray-800">{{ $semester->name }}</span>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Niveau {{ $semester->level }}</span>
                            </div>
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-400 mt-4 italic">Les semestres sont générés automatiquement ou via le panneau Filières.</p>
                </div>

                <!-- Période d'Examens -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 md:col-span-2">
                    <h3 class="text-lg font-black text-upf-blue mb-6">Périodes des Sessions d'Examens (Année Courante)</h3>
                    @if($currentYear)
                        <form action="{{ route('admin.academic.year.exam-period', $currentYear) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                @php
                                    $sessionTypes = [
                                        'normal_autumn' => 'Normale Automne',
                                        'normal_spring' => 'Normale Printemps',
                                        'retake_autumn' => 'Rattrapage Automne',
                                        'retake_spring' => 'Rattrapage Printemps'
                                    ];
                                @endphp

                                @foreach($sessionTypes as $key => $label)
                                    <div class="p-4 bg-gray-50 rounded-2xl border border-gray-200">
                                        <h4 class="font-bold text-gray-800 mb-4">{{ $label }}</h4>
                                        <div class="space-y-3">
                                            <div>
                                                <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-1">Début</label>
                                                <input type="date" name="sessions[{{ $key }}][start_date]" value="{{ $examSessions[$key]?->start_date?->format('Y-m-d') ?? '' }}" class="w-full rounded-xl border-gray-200 focus:ring-upf-magenta focus:border-upf-magenta text-sm shadow-sm">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-1">Fin</label>
                                                <input type="date" name="sessions[{{ $key }}][end_date]" value="{{ $examSessions[$key]?->end_date?->format('Y-m-d') ?? '' }}" class="w-full rounded-xl border-gray-200 focus:ring-upf-magenta focus:border-upf-magenta text-sm shadow-sm">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="bg-upf-magenta hover:bg-pink-600 text-white px-8 py-3 rounded-xl text-sm font-black shadow-md transition-colors">
                                    Enregistrer les périodes
                                </button>
                            </div>
                        </form>
                        <p class="text-xs text-gray-400 mt-4 italic">Les examens ne pourront être planifiés que dans les périodes définies pour chaque session.</p>
                    @else
                        <p class="text-red-500 font-bold">Veuillez définir une année courante d'abord.</p>
                    @endif
                </div>
            </div>

            <!-- Affectations -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-black text-gray-900 tracking-tight">Affectations des Professeurs</h3>
                        <p class="text-sm text-gray-500 font-medium mt-1">
                            Affecter un professeur à un module pour un groupe donné (Année {{ $currentYear?->name ?? 'N/A' }}).
                        </p>
                    </div>
                    {{-- Export / Import Buttons --}}
                    <div class="flex items-center gap-3">
                        {{-- Download Template --}}
                        <a href="{{ route('admin.academic.assignments.template') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 text-gray-600 text-sm font-bold hover:bg-gray-50 transition-colors" title="Télécharger le modèle CSV">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Modèle
                        </a>

                        {{-- Import Button --}}
                        <button onclick="document.getElementById('importModal').classList.remove('hidden')"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold shadow-sm transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            Importer
                        </button>

                        {{-- Export Button --}}
                        @if($currentYear)
                        <a href="{{ route('admin.academic.assignments.export', ['year_id' => $currentYear->id]) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-upf-blue hover:bg-upf-navy text-white text-sm font-bold shadow-sm transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Exporter Excel
                        </a>
                        @endif
                    </div>
                </div>

                @if($currentYear)
                    <div class="p-8 bg-gray-50 border-b border-gray-100">
                        <form action="{{ route('admin.academic.assignment.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            @csrf
                            <input type="hidden" name="academic_year_id" value="{{ $currentYear->id }}">
                            
                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Professeur</label>
                                <select name="professor_id" required class="w-full rounded-xl border-gray-200 focus:ring-upf-magenta focus:border-upf-magenta text-sm font-bold shadow-sm">
                                    <option value="">Sélectionner</option>
                                    @foreach($professors as $prof)
                                        <option value="{{ $prof->id }}">{{ $prof->user->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Module</label>
                                <select name="module_id" required class="w-full rounded-xl border-gray-200 focus:ring-upf-magenta focus:border-upf-magenta text-sm font-bold shadow-sm">
                                    <option value="">Sélectionner</option>
                                    @foreach($modules as $mod)
                                        <option value="{{ $mod->id }}">{{ $mod->code }} - {{ $mod->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Groupe</label>
                                <select name="group_id" required class="w-full rounded-xl border-gray-200 focus:ring-upf-magenta focus:border-upf-magenta text-sm font-bold shadow-sm">
                                    <option value="">Sélectionner</option>
                                    @foreach($groups as $grp)
                                        <option value="{{ $grp->id }}">{{ $grp->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex items-end">
                                <button type="submit" class="w-full bg-gray-900 hover:bg-black text-white px-4 py-2.5 rounded-xl text-sm font-black uppercase tracking-widest shadow-md transition-colors flex items-center justify-center gap-2">
                                    + Affecter
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="overflow-x-auto p-4">
                        <table class="w-full text-sm text-left">
                            <thead class="text-[10px] text-gray-400 uppercase tracking-widest font-black bg-gray-50/50">
                                <tr>
                                    <th class="px-6 py-4 rounded-l-2xl">Professeur</th>
                                    <th class="px-6 py-4">Module</th>
                                    <th class="px-6 py-4">Groupe</th>
                                    <th class="px-6 py-4 text-right rounded-r-2xl">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($assignments as $assignment)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-6 py-4 font-bold text-gray-900">
                                            {{ $assignment->professor->user->name }}
                                        </td>
                                        <td class="px-6 py-4 font-bold text-gray-700">
                                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-md mr-2">{{ $assignment->module->code }}</span>
                                            {{ $assignment->module->name }}
                                        </td>
                                        <td class="px-6 py-4 font-bold text-gray-900">
                                            {{ $assignment->group->name }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <form action="{{ route('admin.academic.assignment.destroy', $assignment) }}" method="POST" class="inline-block" onsubmit="return confirm('Êtes-vous sûr ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 p-2 hover:bg-red-50 rounded-xl transition-colors">
                                                    🗑️
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-gray-400 font-bold">
                                            Aucune affectation pour l'année courante.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-8 text-center">
                        <p class="text-red-500 font-bold">Veuillez d'abord définir une année universitaire courante.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- Import Modal --}}
    <div id="importModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-600 to-emerald-500 px-8 py-5 flex items-center justify-between">
                <div>
                    <h4 class="text-white font-black text-lg">Importer des Affectations</h4>
                    <p class="text-emerald-100 text-sm mt-0.5">Fichier Excel (.xlsx) ou CSV</p>
                </div>
                <button onclick="document.getElementById('importModal').classList.add('hidden')" class="text-white/80 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('admin.academic.assignments.import') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-5">
                @csrf

                <div>
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Année Universitaire</label>
                    <select name="academic_year_id" required class="w-full rounded-xl border-gray-200 focus:ring-emerald-500 focus:border-emerald-500 text-sm font-bold shadow-sm">
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ $year->is_current ? 'selected' : '' }}>{{ $year->name }}{{ $year->is_current ? ' (Courante)' : '' }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Fichier Excel / CSV</label>
                    <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-emerald-400 transition-colors cursor-pointer" onclick="document.getElementById('importFile').click()">
                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        <p class="text-sm font-bold text-gray-500" id="importFileName">Cliquer pour choisir un fichier</p>
                        <p class="text-xs text-gray-400 mt-1">.xlsx, .xls, .csv — Max 5MB</p>
                    </div>
                    <input id="importFile" type="file" name="file" accept=".xlsx,.xls,.csv" class="hidden" onchange="document.getElementById('importFileName').textContent = this.files[0]?.name || 'Aucun fichier choisi'" required>
                </div>

                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                    <p class="text-xs font-bold text-amber-700 mb-1">📋 Format requis (colonnes) :</p>
                    <code class="text-xs text-amber-800 font-mono">email_professeur | module_code | groupe</code>
                    <p class="text-xs text-amber-600 mt-2">
                        <a href="{{ route('admin.academic.assignments.template') }}" class="underline font-bold">⬇ Télécharger le modèle</a>
                    </p>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-bold text-sm hover:bg-gray-50 transition-colors">
                        Annuler
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-sm shadow-md transition-colors">
                        Importer
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>
