<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-upf-blue italic">📝 Procès-Verbal (PV) d'Examen</h2>
            <a href="{{ route('professor.proctor_convocations.index') }}" class="text-xs font-bold text-gray-400 hover:text-upf-blue uppercase tracking-widest">← Mes Surveillances</a>
        </div>
    </x-slot>

    <div class="py-10 bg-[#F8FAFC]">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Infos Examen --}}
            <div class="bg-gradient-to-r from-upf-blue to-indigo-700 rounded-3xl p-8 text-white shadow-xl">
                <h3 class="text-indigo-200 text-xs font-black uppercase tracking-widest mb-1">Examen surveillé</h3>
                <h1 class="text-2xl font-black italic mb-4">{{ $exam->module?->name }} ({{ $exam->module?->code }})</h1>
                <div class="grid grid-cols-3 gap-4 text-sm text-indigo-100 font-bold">
                    <div>📅 Date : {{ \Carbon\Carbon::parse($exam->date)->format('d/m/Y') }}</div>
                    <div>🕐 Horaire : {{ \Carbon\Carbon::parse($exam->start_time)->format('H:i') }} — {{ $exam->end_time }}</div>
                    <div>🏫 Salle : {{ $exam->room?->name ?? 'N/A' }}</div>
                </div>
            </div>

            @if($pv)
            <div class="bg-emerald-50 rounded-3xl border border-emerald-100 p-8 flex items-center justify-between">
                <div>
                    <h3 class="font-black text-emerald-800 text-lg">PV d'examen rédigé avec succès ! ✅</h3>
                    <p class="text-emerald-700 text-xs font-bold mt-1">Soumis le {{ $pv->submitted_at?->format('d/m/Y à H:i') }} par {{ $pv->submittedBy?->name }}</p>
                </div>
                <a href="{{ route('professor.exams.pv.pdf', $exam) }}" class="bg-emerald-600 text-white px-5 py-3 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-emerald-700 transition-all shadow-md">
                    📥 Télécharger PDF PV officiel
                </a>
            </div>
            @endif

            {{-- Wizard Form --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                <h3 class="font-black text-gray-900 text-lg italic mb-6">✏️ Rédiger le PV de Surveillance</h3>

                <form action="{{ route('professor.exams.pv.store', $exam) }}" method="POST" class="space-y-6">
                    @csrf

                    {{-- Step 1: Counts --}}
                    <div class="space-y-4">
                        <h4 class="font-black text-gray-700 uppercase tracking-widest text-xs border-b border-gray-100 pb-2">1. Décompte des Candidats</h4>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="space-y-1">
                                <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Présents *</label>
                                <input type="number" name="presents_count" value="{{ old('presents_count', $pv?->presents_count ?? 0) }}" min="0" required
                                    class="w-full border-gray-200 rounded-xl p-3 text-sm font-bold bg-gray-50 focus:ring-upf-blue">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Absents *</label>
                                <input type="number" name="absents_count" value="{{ old('absents_count', $pv?->absents_count ?? 0) }}" min="0" required
                                    class="w-full border-gray-200 rounded-xl p-3 text-sm font-bold bg-gray-50 focus:ring-upf-blue">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Retards *</label>
                                <input type="number" name="retards_count" value="{{ old('retards_count', $pv?->retards_count ?? 0) }}" min="0" required
                                    class="w-full border-gray-200 rounded-xl p-3 text-sm font-bold bg-gray-50 focus:ring-upf-blue">
                            </div>
                        </div>
                    </div>

                    {{-- Step 2: Incidents --}}
                    <div class="space-y-4 pt-4 border-t border-gray-50">
                        <h4 class="font-black text-gray-700 uppercase tracking-widest text-xs border-b border-gray-100 pb-2">2. Incidents & Fraudes</h4>
                        
                        <div class="flex items-center gap-3 bg-red-50 p-4 rounded-2xl border border-red-100">
                            <input type="checkbox" name="fraude_detected" id="fraude_detected" value="1" 
                                {{ old('fraude_detected', $pv?->fraude_detected) ? 'checked' : '' }}
                                class="rounded border-red-300 text-red-600 focus:ring-red-500 h-5 w-5 cursor-pointer">
                            <label for="fraude_detected" class="font-black text-red-800 text-sm cursor-pointer select-none">⚠️ Signaler une tentative ou constatation de fraude</label>
                        </div>

                        <div class="space-y-1 id-details hidden" id="fraude-details-container">
                            <label class="text-[10px] font-black uppercase tracking-widest text-red-500 block">Détails de la fraude (Matricule de l'étudiant, faits observés) *</label>
                            <textarea name="fraude_details" rows="3"
                                class="w-full border-red-200 rounded-xl p-3 text-sm font-bold bg-red-50/20 focus:ring-red-400 focus:border-red-400"
                                placeholder="Indiquez précisément l'identité de l'étudiant tricheur et les pièces à conviction saisies...">{{ old('fraude_details', $pv?->fraude_details) }}</textarea>
                        </div>

                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Incidents observés (Incendie, malaise étudiant, manque de copies...)</label>
                            <textarea name="incidents" rows="3"
                                class="w-full border-gray-200 rounded-xl p-3 text-sm font-bold bg-gray-50 focus:ring-upf-blue"
                                placeholder="Décrivez les perturbations éventuelles lors du déroulement de l'épreuve...">{{ old('incidents', $pv?->incidents) }}</textarea>
                        </div>
                    </div>

                    {{-- Step 3: Remarques --}}
                    <div class="space-y-4 pt-4 border-t border-gray-50">
                        <h4 class="font-black text-gray-700 uppercase tracking-widest text-xs border-b border-gray-100 pb-2">3. Remarques administratives</h4>
                        <div class="space-y-1">
                            <textarea name="remarques" rows="3"
                                class="w-full border-gray-200 rounded-xl p-3 text-sm font-bold bg-gray-50 focus:ring-upf-blue"
                                placeholder="Renseignez toute autre information utile à destination du secrétariat général...">{{ old('remarques', $pv?->remarques) }}</textarea>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-upf-blue text-white py-4 rounded-2xl font-black uppercase tracking-widest text-sm hover:bg-upf-navy transition-all shadow-lg">
                        💾 Enregistrer & Valider le Procès-Verbal
                    </button>
                </form>
            </div>

        </div>
    </div>

    <script>
        const fraudeCheckbox = document.getElementById('fraude_detected');
        const fraudeDetailsContainer = document.getElementById('fraude-details-container');
        
        function toggleFraudeDetails() {
            if (fraudeCheckbox.checked) {
                fraudeDetailsContainer.classList.remove('hidden');
            } else {
                fraudeDetailsContainer.classList.add('hidden');
            }
        }

        if (fraudeCheckbox) {
            fraudeCheckbox.addEventListener('change', toggleFraudeDetails);
            toggleFraudeDetails(); // Initial call
        }
    </script>
</x-app-layout>
