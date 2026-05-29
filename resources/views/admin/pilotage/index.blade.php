<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tighter italic">
                🎯 Centre de Pilotage Académique
            </h2>
            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Tableau de bord intelligent</span>
        </div>
    </x-slot>

    <div class="py-10 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Hero Banner --}}
            <div class="bg-gradient-to-br from-upf-blue via-indigo-700 to-purple-800 rounded-3xl p-10 text-white shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
                <div class="relative z-10">
                    <h1 class="text-4xl font-black mb-2 italic">Pilotage Académique</h1>
                    <p class="text-indigo-200 max-w-2xl">Vue d'ensemble de toutes les alertes académiques : absences, discipline, examens, rattrapage et convocations.</p>
                    <div class="mt-4 flex items-center gap-3 text-sm font-bold text-indigo-200">
                        <span>⚠️ Seuil avertissement : <strong class="text-white">{{ $warning }}h</strong></span>
                        <span class="text-indigo-400">|</span>
                        <span>🚨 Seuil discipline : <strong class="text-white">{{ $discipline }}h</strong></span>
                        <a href="{{ route('admin.settings.index') }}" class="ml-4 bg-white/20 hover:bg-white/30 text-white px-3 py-1 rounded-lg text-xs transition-all">⚙️ Configurer</a>
                    </div>
                </div>
            </div>

            {{-- Cards Statistiques --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                {{-- Étudiants à risque --}}
                <a href="{{ route('admin.absences.index') }}" class="group bg-white rounded-2xl p-6 shadow-sm border border-amber-100 hover:border-amber-300 hover:shadow-md transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center text-xl">⚠️</div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-amber-400">Risque</span>
                    </div>
                    <div class="text-3xl font-black text-amber-600">{{ $studentsAtRisk->count() }}</div>
                    <div class="text-xs font-bold text-gray-500 mt-1">Étudiants à risque<br><span class="text-amber-400">(≥ {{ $warning }}h)</span></div>
                </a>

                {{-- Conseil de discipline --}}
                <a href="{{ route('admin.discipline.index') }}" class="group bg-white rounded-2xl p-6 shadow-sm border border-red-100 hover:border-red-300 hover:shadow-md transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center text-xl">🚨</div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-red-400">Discipline</span>
                    </div>
                    <div class="text-3xl font-black text-red-600">{{ $studentsDiscipline->count() }}</div>
                    <div class="text-xs font-bold text-gray-500 mt-1">Conseil de discipline<br><span class="text-red-400">(≥ {{ $discipline }}h)</span></div>
                </a>

                {{-- Heures non justifiées --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-rose-100">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 bg-rose-50 rounded-xl flex items-center justify-center text-xl">📊</div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-rose-400">Total</span>
                    </div>
                    <div class="text-3xl font-black text-rose-600">{{ number_format($totalUnjustifiedHours, 1) }}h</div>
                    <div class="text-xs font-bold text-gray-500 mt-1">Heures non justifiées<br>cumulées</div>
                </div>

                {{-- Justificatifs en attente --}}
                <a href="{{ route('admin.absences.index', ['status' => 'pending']) }}" class="group bg-white rounded-2xl p-6 shadow-sm border border-orange-100 hover:border-orange-300 hover:shadow-md transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center text-xl">📋</div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-orange-400">En attente</span>
                    </div>
                    <div class="text-3xl font-black text-orange-600">{{ $pendingJustifications }}</div>
                    <div class="text-xs font-bold text-gray-500 mt-1">Justificatifs cours<br>en attente</div>
                </a>

                {{-- Absences examens --}}
                <a href="{{ route('admin.exam_justifications.index') }}" class="group bg-white rounded-2xl p-6 shadow-sm border border-purple-100 hover:border-purple-300 hover:shadow-md transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center text-xl">📝</div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-purple-400">Examens</span>
                    </div>
                    <div class="text-3xl font-black text-purple-600">{{ $examAbsences }}</div>
                    <div class="text-xs font-bold text-gray-500 mt-1">Absences enregistrées<br>aux examens</div>
                </a>

                {{-- Fraudes --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-fuchsia-100">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 bg-fuchsia-50 rounded-xl flex items-center justify-center text-xl">🚫</div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-fuchsia-400">Fraude</span>
                    </div>
                    <div class="text-3xl font-black text-fuchsia-600">{{ $fraudCases }}</div>
                    <div class="text-xs font-bold text-gray-500 mt-1">Cas de fraude<br>détectés</div>
                </div>

                {{-- Rattrapages accordés --}}
                <a href="{{ route('admin.retake.index') }}" class="group bg-white rounded-2xl p-6 shadow-sm border border-emerald-100 hover:border-emerald-300 hover:shadow-md transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center text-xl">🎓</div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-emerald-400">Rattrapage</span>
                    </div>
                    <div class="text-3xl font-black text-emerald-600">{{ $retakesApproved }}</div>
                    <div class="text-xs font-bold text-gray-500 mt-1">Rattrapages<br>accordés</div>
                </a>

                {{-- Convocations non téléchargées --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-blue-100">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-xl">📄</div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-blue-400">Convocs</span>
                    </div>
                    <div class="text-3xl font-black text-blue-600">{{ $unconvocated }}</div>
                    <div class="text-xs font-bold text-gray-500 mt-1">Convocations non<br>téléchargées</div>
                </div>
            </div>

            {{-- Alertes actives: Deux colonnes --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Étudiants Conseil de Discipline --}}
                <div class="bg-white rounded-3xl shadow-sm border border-red-100 overflow-hidden">
                    <div class="p-6 border-b border-red-50 bg-red-50/50 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-red-100 rounded-xl flex items-center justify-center">🚨</div>
                            <div>
                                <h3 class="font-black text-red-700">Conseil de Discipline</h3>
                                <p class="text-xs text-red-400 font-bold">{{ $studentsDiscipline->count() }} étudiant(s)</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.discipline.index') }}" class="text-xs font-black text-red-500 hover:text-red-700 uppercase tracking-widest">Voir tout →</a>
                    </div>
                    <div class="divide-y divide-gray-50">
                        @forelse($topRiskStudents->where('discipline_status', 'conseil_discipline')->take(5) as $s)
                        <div class="p-4 flex items-center justify-between hover:bg-red-50/30 transition-colors">
                            <div>
                                <div class="font-black text-gray-900 text-sm">{{ $s->user?->name }}</div>
                                <div class="text-xs text-gray-400 font-bold">{{ $s->group?->name }} • {{ $s->group?->filiere?->name }}</div>
                            </div>
                            <div class="text-right">
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-xl text-xs font-black">{{ $s->absence_score }}h</span>
                            </div>
                        </div>
                        @empty
                        <div class="p-8 text-center text-gray-400 text-sm italic">Aucun étudiant en conseil de discipline ✅</div>
                        @endforelse
                    </div>
                </div>

                {{-- Justifications Examens en Attente --}}
                <div class="bg-white rounded-3xl shadow-sm border border-amber-100 overflow-hidden">
                    <div class="p-6 border-b border-amber-50 bg-amber-50/50 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-amber-100 rounded-xl flex items-center justify-center">📋</div>
                            <div>
                                <h3 class="font-black text-amber-700">Justifications Examen en Attente</h3>
                                <p class="text-xs text-amber-400 font-bold">{{ $pendingExamJustifications->count() }} en attente</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.exam_justifications.index') }}" class="text-xs font-black text-amber-500 hover:text-amber-700 uppercase tracking-widest">Voir tout →</a>
                    </div>
                    <div class="divide-y divide-gray-50">
                        @forelse($pendingExamJustifications->take(5) as $j)
                        <div class="p-4 flex items-center justify-between hover:bg-amber-50/30 transition-colors">
                            <div>
                                <div class="font-black text-gray-900 text-sm">{{ $j->student?->user?->name }}</div>
                                <div class="text-xs text-gray-400 font-bold">{{ $j->examAttendance?->exam?->module?->name }}</div>
                            </div>
                            <div class="flex gap-2">
                                <form action="{{ route('admin.exam_justifications.approve', $j) }}" method="POST">
                                    @csrf
                                    <button class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-lg text-xs font-black hover:bg-emerald-200 transition-all">✓ Valider</button>
                                </form>
                                <form action="{{ route('admin.exam_justifications.reject', $j) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="admin_comment" value="Refus rapide depuis le tableau de bord.">
                                    <button class="bg-red-100 text-red-700 px-3 py-1 rounded-lg text-xs font-black hover:bg-red-200 transition-all">✗ Refuser</button>
                                </form>
                            </div>
                        </div>
                        @empty
                        <div class="p-8 text-center text-gray-400 text-sm italic">Aucune justification en attente ✅</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Actions rapides --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                <h3 class="text-lg font-black text-gray-900 mb-6 italic">Actions Rapides</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <a href="{{ route('admin.discipline.index') }}" class="flex flex-col items-center p-5 rounded-2xl bg-red-50 border border-red-100 hover:bg-red-100 transition-all group text-center">
                        <span class="text-3xl mb-2">⚖️</span>
                        <span class="text-xs font-black text-red-700 uppercase tracking-wide">Conseil Discipline</span>
                    </a>
                    <a href="{{ route('admin.exam_justifications.index') }}" class="flex flex-col items-center p-5 rounded-2xl bg-amber-50 border border-amber-100 hover:bg-amber-100 transition-all group text-center">
                        <span class="text-3xl mb-2">📋</span>
                        <span class="text-xs font-black text-amber-700 uppercase tracking-wide">Justifications Examen</span>
                    </a>
                    <a href="{{ route('admin.retake.index') }}" class="flex flex-col items-center p-5 rounded-2xl bg-emerald-50 border border-emerald-100 hover:bg-emerald-100 transition-all group text-center">
                        <span class="text-3xl mb-2">🎓</span>
                        <span class="text-xs font-black text-emerald-700 uppercase tracking-wide">Liste Rattrapage</span>
                    </a>
                    <a href="{{ route('admin.absences.index') }}" class="flex flex-col items-center p-5 rounded-2xl bg-indigo-50 border border-indigo-100 hover:bg-indigo-100 transition-all group text-center">
                        <span class="text-3xl mb-2">📊</span>
                        <span class="text-xs font-black text-indigo-700 uppercase tracking-wide">Registre Absences</span>
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
