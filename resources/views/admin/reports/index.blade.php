<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tighter italic">📊 Rapports PDF Automatiques</h2>
    </x-slot>

    <div class="py-10 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Banner --}}
            <div class="bg-gradient-to-r from-upf-blue to-indigo-800 rounded-3xl p-8 text-white shadow-xl">
                <h1 class="text-3xl font-black italic mb-2">Centre d'Impression Administratif</h1>
                <p class="text-indigo-200">Générez et téléchargez des rapports officiels au format PDF A4. Tous les rapports intègrent automatiquement le branding institutionnel configuré.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                {{-- Card 1: Absences --}}
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-all">
                    <div>
                        <div class="text-3xl mb-4">📇</div>
                        <h3 class="font-black text-gray-900 text-lg mb-2">Rapport d'Absences</h3>
                        <p class="text-gray-400 text-xs font-bold leading-relaxed mb-6">Générez la fiche des absences globales des étudiants, triée par ordre décroissant de score d'absences.</p>
                    </div>
                    <form action="{{ route('admin.reports.absences') }}" method="GET" class="space-y-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Filtrer par Groupe</label>
                            <select name="group_id" class="w-full border-gray-200 rounded-xl p-3 text-sm font-bold bg-gray-50">
                                <option value="">Tous les groupes</option>
                                @foreach($groups as $g)
                                <option value="{{ $g->id }}">{{ $g->name }} ({{ $g->filiere?->name }})</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="w-full bg-upf-blue text-white py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-upf-navy transition-all">
                            📥 Générer PDF
                        </button>
                    </form>
                </div>

                {{-- Card 2: Notes --}}
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-all">
                    <div>
                        <div class="text-3xl mb-4">📝</div>
                        <h3 class="font-black text-gray-900 text-lg mb-2">Rapport des Notes</h3>
                        <p class="text-gray-400 text-xs font-bold leading-relaxed mb-6">Générez la feuille de notes officielle d'un module avec statistiques (moyenne de classe, taux de réussite, min/max).</p>
                    </div>
                    <form action="{{ route('admin.reports.grades') }}" method="GET" class="space-y-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Sélectionner le Module *</label>
                            <select name="module_id" required class="w-full border-gray-200 rounded-xl p-3 text-sm font-bold bg-gray-50">
                                <option value="">Choisir un module...</option>
                                @foreach($modules as $m)
                                <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="w-full bg-upf-blue text-white py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-upf-navy transition-all">
                            📥 Générer PDF
                        </button>
                    </form>
                </div>

                {{-- Card 3: Planning Examens --}}
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-all">
                    <div>
                        <div class="text-3xl mb-4">📅</div>
                        <h3 class="font-black text-gray-900 text-lg mb-2">Schedules & Salles d'Examens</h3>
                        <p class="text-gray-400 text-xs font-bold leading-relaxed mb-6">Téléchargez le calendrier complet des examens actifs avec affectation des salles et des surveillants affectés.</p>
                    </div>
                    <div class="pt-6">
                        <a href="{{ route('admin.reports.exams') }}" class="block text-center w-full bg-upf-blue text-white py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-upf-navy transition-all">
                            📥 Calendrier Examens
                        </a>
                    </div>
                </div>

                {{-- Card 4: Occupation Salles --}}
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-all">
                    <div>
                        <div class="text-3xl mb-4">🏫</div>
                        <h3 class="font-black text-gray-900 text-lg mb-2">Occupation des Salles</h3>
                        <p class="text-gray-400 text-xs font-bold leading-relaxed mb-6">Obtenez un rapport récapitulatif de toutes les salles de cours de l'établissement avec leur statut d'occupation actuel.</p>
                    </div>
                    <div class="pt-6">
                        <a href="{{ route('admin.reports.rooms') }}" class="block text-center w-full bg-upf-blue text-white py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-upf-navy transition-all">
                            📥 Rapport Salles
                        </a>
                    </div>
                </div>

                {{-- Card 5: Étudiants à Risque --}}
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-red-50 flex flex-col justify-between hover:shadow-md transition-all">
                    <div>
                        <div class="text-3xl mb-4">🚨</div>
                        <h3 class="font-black text-gray-900 text-lg mb-2">Étudiants à Risque</h3>
                        <p class="text-gray-400 text-xs font-bold leading-relaxed mb-6">Générez un rapport ciblé listant tous les étudiants en difficulté (absences élevées ou moyennes insuffisantes) pour intervention rapide.</p>
                    </div>
                    <div class="pt-6">
                        <a href="{{ route('admin.reports.at-risk') }}" class="block text-center w-full bg-red-600 text-white py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-red-700 transition-all">
                            📥 Rapport Alerte Risques
                        </a>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
