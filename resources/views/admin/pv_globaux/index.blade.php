<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tight italic">
            {{ __('Procès-Verbaux Globaux & Synthèses Académiques') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-screen-2xl mx-auto sm:px-6 lg:px-8">

            {{-- Hero Banner --}}
            <div class="bg-gradient-to-r from-upf-navy via-upf-blue to-upf-magenta rounded-3xl p-8 text-white shadow-2xl relative overflow-hidden mb-8">
                <div class="relative z-10">
                    <span class="px-4 py-1.5 bg-white/20 backdrop-blur-md rounded-full text-xs font-bold uppercase tracking-widest mb-3 inline-block">Portail de Pilotage Académique</span>
                    <h2 class="text-3xl font-black mb-2">Synthèses des PV Globaux & Annuels 📊</h2>
                    <p class="text-blue-100 opacity-90 max-w-3xl">Générez instantanément des procès-verbaux de notes semestriels ou annuels avec calculs de moyennes pondérées, compensation de modules et surbrillance automatique de vigilance.</p>
                </div>
                <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
            </div>

            {{-- Filter Panel --}}
            <div class="bg-white dark:bg-slate-900 overflow-hidden shadow-xl sm:rounded-3xl border border-gray-100 dark:border-slate-800 mb-8 p-6 lg:p-8">
                <form method="GET" action="{{ route('admin.pv_globaux.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
                    
                    {{-- Academic Year --}}
                    <div>
                        <label for="academic_year_id" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Année Universitaire</label>
                        <select name="academic_year_id" id="academic_year_id" class="w-full rounded-2xl border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-gray-100 font-bold p-3">
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ $currentYearId == $year->id ? 'selected' : '' }}>
                                    🎓 {{ $year->name }} {{ $year->is_current ? '(Actuelle)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filiere --}}
                    <div>
                        <label for="filiere_id" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Filière</label>
                        <select name="filiere_id" id="filiere_id" required class="w-full rounded-2xl border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-gray-100 font-bold p-3">
                            <option value="" disabled selected>Choisir une filière...</option>
                            @foreach($filieres as $fil)
                                <option value="{{ $fil->id }}" {{ $selectedFiliereId == $fil->id ? 'selected' : '' }}>
                                    🏛️ {{ $fil->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Level / Year --}}
                    <div>
                        <label for="level" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Niveau d'Étude</label>
                        <select name="level" id="level" required class="w-full rounded-2xl border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-gray-100 font-bold p-3">
                            <option value="" disabled selected>Choisir un niveau...</option>
                            <option value="1" {{ $selectedLevel == '1' ? 'selected' : '' }}>1ère Année (S1 + S2)</option>
                            <option value="2" {{ $selectedLevel == '2' ? 'selected' : '' }}>2ème Année (S3 + S4)</option>
                            <option value="3" {{ $selectedLevel == '3' ? 'selected' : '' }}>3ème Année (S5 + S6)</option>
                        </select>
                    </div>

                    {{-- Semester filter or Annual --}}
                    <div>
                        <label for="semester_id" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Période d'Évaluation</label>
                        <select name="semester_id" id="semester_id" class="w-full rounded-2xl border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-gray-100 font-bold p-3">
                            <option value="">📁 Toute l'Année (Global)</option>
                            @if($selectedLevel && $semesters->isNotEmpty())
                                @foreach($semesters as $sem)
                                    <option value="{{ $sem->id }}" {{ $selectedSemesterId == $sem->id ? 'selected' : '' }}>
                                        📅 Semestre {{ $sem->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    {{-- Buttons --}}
                    <div class="md:col-span-4 flex justify-end gap-4 mt-2">
                        <button type="submit" class="inline-flex items-center px-6 py-3 bg-upf-blue hover:bg-upf-navy text-white rounded-2xl font-black uppercase tracking-wider text-xs transition-all duration-200 shadow-lg">
                            🔍 Générer la Synthèse
                        </button>
                        @if($selectedFiliereId && $selectedLevel)
                            <a href="{{ route('admin.pv_globaux.export_excel', request()->all()) }}" class="inline-flex items-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-black uppercase tracking-wider text-xs transition-all duration-200 shadow-lg">
                                📥 Exporter Excel
                            </a>
                            <a href="{{ route('admin.pv_globaux.export_pdf', request()->all()) }}" class="inline-flex items-center px-6 py-3 bg-upf-magenta hover:bg-pink-700 text-white rounded-2xl font-black uppercase tracking-wider text-xs transition-all duration-200 shadow-lg">
                                📄 Télécharger PDF
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Content Area --}}
            @if(!$selectedFiliereId || !$selectedLevel)
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-16 text-center border border-gray-100 dark:border-slate-800 shadow-xl">
                    <div class="w-24 h-24 bg-blue-50 dark:bg-slate-800 text-upf-blue rounded-full flex items-center justify-center mx-auto mb-6 text-4xl animate-bounce">
                        💡
                    </div>
                    <h3 class="text-xl font-black text-gray-800 dark:text-gray-200 mb-2">Veuillez sélectionner les critères</h3>
                    <p class="text-gray-400 dark:text-slate-500 max-w-md mx-auto">Choisissez une filière, un niveau d'étude et une période dans le filtre ci-dessus pour afficher la synthèse complète.</p>
                </div>
            @elseif($students->isEmpty())
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-16 text-center border border-gray-100 dark:border-slate-800 shadow-xl">
                    <div class="w-24 h-24 bg-rose-50 dark:bg-slate-800 text-rose-500 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl">
                        ⚠️
                    </div>
                    <h3 class="text-xl font-black text-gray-800 dark:text-gray-200 mb-2">Aucun étudiant trouvé</h3>
                    <p class="text-gray-400 dark:text-slate-500 max-w-md mx-auto">Il n'y a aucun étudiant inscrit dans cette filière au niveau sélectionné pour l'année universitaire active.</p>
                </div>
            @else
                {{-- Deliberation Pipeline Widget --}}
                @php
                    $pvApproval = \App\Models\PVGlobalApproval::where('filiere_id', $selectedFiliereId)
                        ->where('academic_year_id', $currentYearId)
                        ->where('level', $selectedLevel)
                        ->first();
                    $isPvApproved = $pvApproval && $pvApproval->is_validated;
                @endphp

                <div class="mb-8 p-6 rounded-3xl bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 shadow-xl flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center shadow-inner shrink-0 {{ $isPvApproved ? 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400' : 'bg-amber-50 dark:bg-amber-950/30 text-amber-600 dark:text-amber-400' }}">
                            @if($isPvApproved)
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            @else
                                <svg class="w-8 h-8 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            @endif
                        </div>
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Statut de Délibération Pipeline</span>
                            @if($isPvApproved)
                                <h4 class="text-xl font-black text-emerald-600 dark:text-emerald-400 mt-0.5">Procès-Verbal Global Officiellement Validé ✅</h4>
                                <p class="text-xs font-bold text-slate-400 dark:text-slate-500 mt-0.5">
                                    Approuvé par <strong class="text-slate-600 dark:text-slate-350">{{ $pvApproval->validator->name ?? 'Administration' }}</strong> le {{ $pvApproval->validated_at->format('d/m/Y à H:i') }}.
                                </p>
                            @else
                                <h4 class="text-xl font-black text-amber-600 dark:text-amber-500 mt-0.5">En Attente de Validation Administrative ⏳</h4>
                                <p class="text-xs font-bold text-slate-400 dark:text-slate-505 mt-0.5">Le PV annuel doit être validé pour débloquer les attestations de réussite (tous niveaux) et les diplômes (3ème année) des étudiants.</p>
                            @endif
                        </div>
                    </div>
                    
                    <div>
                        @if(!$isPvApproved)
                            <form method="POST" action="{{ route('admin.pv_globaux.validate') }}">
                                @csrf
                                <input type="hidden" name="filiere_id" value="{{ $selectedFiliereId }}">
                                <input type="hidden" name="academic_year_id" value="{{ $currentYearId }}">
                                <input type="hidden" name="level" value="{{ $selectedLevel }}">
                                <button type="submit" class="px-6 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-black rounded-2xl text-xs uppercase tracking-widest transition-all shadow-md flex items-center gap-2">
                                    ⚖️ Valider & Débloquer les Diplômes
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.pv_globaux.reject') }}">
                                @csrf
                                <input type="hidden" name="filiere_id" value="{{ $selectedFiliereId }}">
                                <input type="hidden" name="academic_year_id" value="{{ $currentYearId }}">
                                <input type="hidden" name="level" value="{{ $selectedLevel }}">
                                <button type="submit" class="px-6 py-3.5 bg-rose-600 hover:bg-rose-700 text-white font-black rounded-2xl text-xs uppercase tracking-widest transition-all shadow-md flex items-center gap-2">
                                    ❌ Annuler la Validation
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- Global PV Grid (Horizontal Scrollable Table) --}}
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-xl overflow-hidden mb-8">
                    <div class="p-6 border-b border-gray-100 dark:border-slate-800 flex flex-wrap justify-between items-center gap-4">
                        <div>
                            <h3 class="text-lg font-black text-upf-navy dark:text-white uppercase tracking-tight">
                                {{ $isAnnual ? "PV Global de la " . $selectedLevel . "ère/ème Année" : "PV du Semestre " . $semester->name }}
                            </h3>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mt-1">Filière: {{ $filieres->firstWhere('id', $selectedFiliereId)->name }} | Effectif: {{ $students->count() }} Étudiants</p>
                        </div>
                        <div class="flex gap-4 items-center">
                            <span class="flex items-center gap-1.5 text-xs font-bold text-gray-500">
                                <span class="w-3.5 h-3.5 rounded-full bg-rose-100 border border-rose-300 inline-block"></span> Discipline
                            </span>
                            <span class="flex items-center gap-1.5 text-xs font-bold text-gray-500">
                                <span class="w-3.5 h-3.5 rounded-full bg-emerald-100 border border-emerald-300 inline-block"></span> Validé
                            </span>
                            <span class="flex items-center gap-1.5 text-xs font-bold text-gray-500">
                                <span class="w-3.5 h-3.5 rounded-full bg-violet-100 border border-violet-300 inline-block"></span> VAR
                            </span>
                            <span class="flex items-center gap-1.5 text-xs font-bold text-gray-500">
                                <span class="w-3.5 h-3.5 rounded-full bg-amber-100 border border-amber-300 inline-block"></span> Rattrapage
                            </span>
                        </div>
                    </div>

                    {{-- Responsive Table --}}
                    <div class="overflow-x-auto max-w-full">
                        <table class="min-w-full border-collapse text-left text-xs font-medium text-gray-900 border-spacing-0">
                            
                            {{-- Table Headers --}}
                            <thead class="bg-gray-50 dark:bg-slate-800 text-gray-500 uppercase tracking-widest text-[10px] font-black border-b border-gray-200 dark:border-slate-700">
                                <tr>
                                    <th rowspan="2" class="p-4 border-r border-gray-200 dark:border-slate-700 text-center sticky left-0 bg-gray-50 dark:bg-slate-800 z-10 w-12">N°</th>
                                    <th rowspan="2" class="p-4 border-r border-gray-200 dark:border-slate-700 sticky left-12 bg-gray-50 dark:bg-slate-800 z-10 w-24">CIN</th>
                                    <th rowspan="2" class="p-4 border-r border-gray-200 dark:border-slate-700 sticky left-36 bg-gray-50 dark:bg-slate-800 z-10 w-28">CNE / Massar</th>
                                    <th rowspan="2" class="p-4 border-r border-gray-200 dark:border-slate-700 sticky left-[256px] bg-gray-50 dark:bg-slate-800 z-10 w-44">Nom & Prénom</th>
                                    
                                    {{-- Modules columns --}}
                                    @foreach($modules as $module)
                                        <th colspan="3" class="p-3 border-r border-gray-200 dark:border-slate-700 text-center bg-blue-50/40 dark:bg-slate-800/40 font-bold border-b border-gray-200 dark:border-slate-700 min-w-[210px]">
                                            <span class="block truncate max-w-[200px]" title="{{ $module->name }}">
                                                {{ $module->code }} : {{ $module->name }}
                                            </span>
                                            <span class="text-[9px] text-gray-400 normal-case block">Coef: {{ $module->coefficient ?? 1 }}</span>
                                        </th>
                                    @endforeach

                                    {{-- Semesters summary headers --}}
                                    @if($isAnnual)
                                        @foreach($semesters as $sem)
                                            <th colspan="2" class="p-3 border-r border-gray-200 dark:border-slate-700 text-center bg-violet-50/40 dark:bg-violet-950/20 font-bold border-b border-gray-200 dark:border-slate-700 min-w-[130px]">
                                                Semestre {{ $sem->name }}
                                            </th>
                                        @endforeach
                                        <th colspan="2" class="p-3 border-r border-gray-200 dark:border-slate-700 text-center bg-emerald-50 dark:bg-emerald-950/20 font-black text-upf-blue border-b border-gray-200 dark:border-slate-700 min-w-[150px]">
                                            Résultat Annuel
                                        </th>
                                    @else
                                        <th colspan="2" class="p-3 border-r border-gray-200 dark:border-slate-700 text-center bg-emerald-50 dark:bg-emerald-950/20 font-black text-upf-blue border-b border-gray-200 dark:border-slate-700 min-w-[150px]">
                                            Résultat Semestre
                                        </th>
                                    @endif
                                    <th rowspan="2" class="p-4 text-center min-w-[150px]">Observations</th>
                                </tr>
                                <tr>
                                    {{-- Sub headers for modules (Moyenne, Décision, Année) --}}
                                    @foreach($modules as $module)
                                        <th class="p-2 border-r border-gray-200 dark:border-slate-700 text-center text-[9px] min-w-[70px]">Moy.</th>
                                        <th class="p-2 border-r border-gray-200 dark:border-slate-700 text-center text-[9px] min-w-[70px]">Déc.</th>
                                        <th class="p-2 border-r border-gray-200 dark:border-slate-700 text-center text-[9px] min-w-[70px]">Date Val./Ex.</th>
                                    @endforeach

                                    {{-- Sub headers for semesters summaries --}}
                                    @if($isAnnual)
                                        @foreach($semesters as $sem)
                                            <th class="p-2 border-r border-gray-200 dark:border-slate-700 text-center text-[9px] min-w-[65px]">Moy.</th>
                                            <th class="p-2 border-r border-gray-200 dark:border-slate-700 text-center text-[9px] min-w-[65px]">Déc.</th>
                                        @endforeach
                                        <th class="p-2 border-r border-gray-200 dark:border-slate-700 text-center text-[9px] min-w-[75px] font-black">Moy. Ann.</th>
                                        <th class="p-2 border-r border-gray-200 dark:border-slate-700 text-center text-[9px] min-w-[75px] font-black">Décision</th>
                                    @else
                                        <th class="p-2 border-r border-gray-200 dark:border-slate-700 text-center text-[9px] min-w-[75px] font-black">Moyenne</th>
                                        <th class="p-2 border-r border-gray-200 dark:border-slate-700 text-center text-[9px] min-w-[75px] font-black">Décision</th>
                                    @endif
                                </tr>
                            </thead>

                            {{-- Table Rows --}}
                            <tbody class="divide-y divide-gray-200 dark:divide-slate-800 text-[11px] font-bold text-gray-700 dark:text-gray-300">
                                @foreach($students as $idx => $student)
                                    @php
                                        $data = $pvData[$student->id];
                                    @endphp
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 {{ $data['is_disciplinary'] ? 'bg-rose-50/70 hover:bg-rose-100/70 text-rose-950 dark:bg-rose-950/20 dark:hover:bg-rose-950/30' : '' }}">
                                        
                                        {{-- N° --}}
                                        <td class="p-3 text-center border-r border-gray-200 dark:border-slate-700 sticky left-0 {{ $data['is_disciplinary'] ? 'bg-rose-100/90 dark:bg-rose-950/40' : 'bg-white dark:bg-slate-900' }} z-10">{{ $idx + 1 }}</td>
                                        
                                        {{-- CIN --}}
                                        <td class="p-3 border-r border-gray-200 dark:border-slate-700 sticky left-12 {{ $data['is_disciplinary'] ? 'bg-rose-100/90 dark:bg-rose-950/40' : 'bg-white dark:bg-slate-900' }} z-10 font-mono">{{ $student->cin ?? 'N/A' }}</td>
                                        
                                        {{-- CNE --}}
                                        <td class="p-3 border-r border-gray-200 dark:border-slate-700 sticky left-36 {{ $data['is_disciplinary'] ? 'bg-rose-100/90 dark:bg-rose-950/40' : 'bg-white dark:bg-slate-900' }} z-10 font-mono">{{ $student->student_number ?? 'N/A' }}</td>
                                        
                                        {{-- Student Name --}}
                                        <td class="p-3 border-r border-gray-200 dark:border-slate-700 sticky left-[256px] {{ $data['is_disciplinary'] ? 'bg-rose-100/90 dark:bg-rose-950/40' : 'bg-white dark:bg-slate-900' }} z-10 uppercase tracking-tight truncate max-w-[180px]">
                                            {{ $student->user->name ?? 'N/A' }}
                                        </td>

                                        {{-- Grades for modules --}}
                                        @foreach($modules as $module)
                                            @php
                                                $modData = $data['modules'][$module->id] ?? null;
                                                $decision = $modData['decision'] ?? '';
                                                $bgClass = '';
                                                
                                                if ($decision === 'V') {
                                                    $bgClass = 'bg-emerald-50 text-emerald-800 dark:bg-emerald-950/20 dark:text-emerald-300';
                                                } elseif ($decision === 'VAR') {
                                                    $bgClass = 'bg-violet-50 text-violet-800 dark:bg-violet-950/20 dark:text-violet-300';
                                                } elseif ($decision === 'R') {
                                                    $bgClass = 'bg-amber-50 text-amber-800 dark:bg-amber-950/20 dark:text-amber-300';
                                                } elseif ($decision === 'NV') {
                                                    $bgClass = 'bg-rose-50 text-rose-800 dark:bg-rose-950/20 dark:text-rose-300';
                                                } elseif ($decision === 'ABS') {
                                                    $bgClass = 'bg-gray-100 text-gray-500 dark:bg-slate-800 dark:text-slate-400';
                                                }
                                            @endphp

                                            {{-- Moy. --}}
                                            <td class="p-2 border-r border-gray-200 dark:border-slate-700 text-center font-mono {{ $bgClass }}">
                                                {{ $modData['final_grade'] !== null ? number_format($modData['final_grade'], 2, ',', ' ') : '-' }}
                                            </td>

                                            {{-- Décision --}}
                                            <td class="p-2 border-r border-gray-200 dark:border-slate-700 text-center {{ $bgClass }}">
                                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider block text-center">
                                                    {{ $decision ?? '-' }}
                                                </span>
                                            </td>

                                            {{-- Date Validation / Examen --}}
                                            <td class="p-2 border-r border-gray-200 dark:border-slate-700 text-center font-mono {{ $bgClass }}">
                                                {{ $modData['val_date'] ?? '-' }}
                                            </td>
                                        @endforeach

                                        {{-- Semesters summary data --}}
                                        @if($isAnnual)
                                            @foreach($semesters as $sem)
                                                @php
                                                    $semData = $data['semesters'][$sem->id] ?? null;
                                                    $semDecision = $semData['decision'] ?? '';
                                                    $semBg = ($semDecision === 'V') ? 'bg-emerald-50/50 text-emerald-800 dark:bg-emerald-950/10' : (($semDecision === 'NV') ? 'bg-rose-50/50 text-rose-800 dark:bg-rose-950/10' : '');
                                                @endphp
                                                <td class="p-2 border-r border-gray-200 dark:border-slate-700 text-center font-mono {{ $semBg }}">
                                                    {{ $semData['average'] !== null ? number_format($semData['average'], 2, ',', ' ') : '-' }}
                                                </td>
                                                <td class="p-2 border-r border-gray-200 dark:border-slate-700 text-center {{ $semBg }}">
                                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-black">
                                                        {{ $semDecision ?? '-' }}
                                                    </span>
                                                </td>
                                            @endforeach

                                            {{-- Annual summary average --}}
                                            @php
                                                $annualAvg = $data['annual_average'];
                                                $annualDec = $data['annual_decision'];
                                                $annualBg = ($annualDec === 'Admis' || $annualDec === 'Diplômé') 
                                                    ? 'bg-emerald-100/70 text-emerald-900 dark:bg-emerald-950/30 dark:text-emerald-300' 
                                                    : (($annualDec === 'Admis avec Crédit') 
                                                        ? 'bg-blue-100/70 text-blue-900 dark:bg-blue-950/30 dark:text-blue-300' 
                                                        : (($annualDec === 'Ajourné') 
                                                            ? 'bg-rose-100/70 text-rose-900 dark:bg-rose-950/30 dark:text-rose-300' 
                                                            : ''));
                                            @endphp
                                            <td class="p-3 border-r border-gray-200 dark:border-slate-700 text-center font-black font-mono {{ $annualBg }} text-[12px]">
                                                {{ $annualAvg !== null ? number_format($annualAvg, 2, ',', ' ') : '-' }}
                                            </td>
                                            <td class="p-3 border-r border-gray-200 dark:border-slate-700 text-center {{ $annualBg }}">
                                                <span class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-wider block text-center">
                                                    {{ $annualDec ?? '-' }}
                                                </span>
                                            </td>
                                        @else
                                            {{-- Single Semester average --}}
                                            @php
                                                $semData = $data['semesters'][$semester->id] ?? null;
                                                $semAvg = $semData['average'] ?? null;
                                                $semDec = $semData['decision'] ?? '';
                                                $semBg = ($semDec === 'V') ? 'bg-emerald-100/70 text-emerald-900 dark:bg-emerald-950/30 dark:text-emerald-300' : (($semDec === 'NV') ? 'bg-rose-100/70 text-rose-900 dark:bg-rose-950/30 dark:text-rose-300' : '');
                                            @endphp
                                            <td class="p-3 border-r border-gray-200 dark:border-slate-700 text-center font-black font-mono {{ $semBg }} text-[12px]">
                                                {{ $semAvg !== null ? number_format($semAvg, 2, ',', ' ') : '-' }}
                                            </td>
                                            <td class="p-3 border-r border-gray-200 dark:border-slate-700 text-center {{ $semBg }}">
                                                <span class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-wider block text-center">
                                                    {{ $semDec === 'V' ? 'VALIDÉ' : ($semDec === 'NV' ? 'NON VALIDÉ' : '-') }}
                                                </span>
                                            </td>
                                        @endif

                                         {{-- Observations --}}
                                         <td class="p-3 text-center text-[10px] min-w-[160px]">
                                             @if($data['is_disciplinary'])
                                                 @if($student->hasActiveDisciplineCase())
                                                     <span class="px-2.5 py-1 rounded-md bg-rose-200 text-rose-900 dark:bg-rose-950/50 dark:text-rose-300 border border-rose-300 font-black block text-center">
                                                         ⚖️ Conseil de Discipline
                                                     </span>
                                                 @else
                                                     <span class="px-2.5 py-1 rounded-md bg-rose-100 text-rose-800 border border-rose-200 font-black block text-center animate-pulse" title="Absences non justifiées: {{ $student->absence_score }}h">
                                                         ⚠️ {{ $student->absence_score }}h Absences
                                                     </span>
                                                 @endif
                                             @else
                                                 @if(strpos($data['observations'], 'NV') !== false)
                                                     <span class="px-2.5 py-1 rounded-md bg-rose-50 text-rose-800 border border-rose-200 dark:bg-rose-950/20 dark:text-rose-300 dark:border-rose-900 font-bold block text-center truncate" title="{{ $data['observations'] }}">
                                                         {{ $data['observations'] }}
                                                     </span>
                                                 @elseif($data['observations'] === 'V')
                                                     <span class="px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200 dark:bg-emerald-950/20 dark:text-emerald-300 dark:border-emerald-900 font-bold block text-center">
                                                         ✅ Validé
                                                     </span>
                                                 @else
                                                     <span class="text-gray-400 italic font-medium">R.A.S</span>
                                                 @endif
                                             @endif
                                         </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
