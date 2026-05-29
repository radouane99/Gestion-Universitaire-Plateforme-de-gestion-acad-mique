<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-upf-blue italic">💬 Réclamations de Notes Reçues</h2>
    </x-slot>

    <div class="py-10 bg-[#F8FAFC]">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-2xl font-bold text-sm">{{ session('success') }}</div>
            @endif

            {{-- Summary --}}
            <div class="bg-gradient-to-r from-upf-blue to-indigo-700 rounded-3xl p-8 text-white shadow-xl">
                <h1 class="text-3xl font-black italic mb-2">Centre de Traitement des Recours</h1>
                <p class="text-indigo-200">Consultez les réclamations d'étudiants pour vos cours. Si vous acceptez le recours, vous pouvez modifier directement sa note finale sur cette interface.</p>
            </div>

            {{-- List --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Étudiant</th>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Module / Code</th>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Note Actuelle</th>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Motif Rédigé</th>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Statut</th>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($reclamations as $r)
                            <tr class="hover:bg-gray-50/30 transition-colors">
                                <td class="p-5">
                                    <div class="font-black text-gray-900 text-sm">{{ $r->student?->user?->name }}</div>
                                    <div class="text-xs text-gray-400 font-bold">Matricule: {{ $r->student?->student_number }} • Groupe: {{ $r->student?->group?->name }}</div>
                                </td>
                                <td class="p-5">
                                    <div class="font-black text-gray-900 text-sm">{{ $r->module?->name }}</div>
                                    <div class="text-xs text-gray-400 font-bold">Code: {{ $r->module?->code }}</div>
                                </td>
                                <td class="p-5 text-right font-black text-sm text-gray-700">
                                    @if($r->grade)
                                    <div>Final : <strong class="text-indigo-600">{{ number_format($r->grade->final_grade, 2) }}</strong></div>
                                    <div class="text-[10px] text-gray-400">CC1: {{ $r->grade->cc1 ?? '—' }} | CC2: {{ $r->grade->cc2 ?? '—' }} | Exam: {{ $r->grade->exam ?? '—' }}</div>
                                    @else
                                    <span>N/A</span>
                                    @endif
                                </td>
                                <td class="p-5">
                                    <p class="text-xs font-bold text-gray-500 max-w-sm" style="white-space: normal;">"{{ $r->reason }}"</p>
                                </td>
                                <td class="p-5">
                                    <span class="px-3 py-1 rounded-full text-xs font-black border {{ $r->status_color }}">
                                        {{ $r->status_label }}
                                    </span>
                                </td>
                                <td class="p-5 text-right">
                                    @if($r->status === 'pending')
                                    <button onclick="openResolveModal({{ $r->id }})"
                                        class="bg-upf-blue text-white px-4 py-2 rounded-xl text-xs font-black hover:bg-upf-navy transition-all shadow-sm">
                                        ⚖️ Résoudre
                                    </button>
                                    @else
                                    <span class="text-xs font-bold text-gray-400 bg-gray-50 px-2 py-1 rounded border border-gray-100 block text-center">
                                        Traité
                                    </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="p-16 text-center text-gray-400 italic">Aucune réclamation de note reçue. ✅</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($reclamations->hasPages())
                <div class="p-6 border-t border-gray-100">{{ $reclamations->links() }}</div>
                @endif
            </div>

        </div>
    </div>

    {{-- Resolution Modals --}}
    @foreach($reclamations->where('status', 'pending') as $r)
    <div id="resolve-modal-{{ $r->id }}" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-8 max-w-lg w-full shadow-2xl overflow-y-auto max-h-[90vh]">
            <h3 class="font-black text-xl text-gray-900 mb-2">Résoudre la Réclamation de Note</h3>
            <p class="text-gray-400 text-xs font-bold mb-6">Étudiant : <strong>{{ $r->student?->user?->name }}</strong> • Module : {{ $r->module?->name }}</p>
            
            <form action="{{ route('professor.reclamations.resolve', $r) }}" method="POST" class="space-y-4">
                @csrf

                <div class="space-y-1">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Décision *</label>
                    <select name="status" id="decision-status-{{ $r->id }}" onchange="toggleGradeInputs({{ $r->id }})" required
                        class="w-full border-gray-200 rounded-xl p-3 text-sm font-bold bg-gray-50">
                        <option value="accepted">✅ Accepter la Réclamation (Modifier la note)</option>
                        <option value="rejected">❌ Refuser la Réclamation</option>
                    </select>
                </div>

                {{-- Adjustable Grade Inputs (Visible only when accepted is chosen) --}}
                <div id="grade-inputs-{{ $r->id }}" class="bg-indigo-50/50 border border-indigo-100 rounded-2xl p-4 space-y-3">
                    <h4 class="text-xs font-black text-indigo-700 uppercase tracking-widest mb-2">Nouvelles Notes</h4>
                    <div class="grid grid-cols-3 gap-3">
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">CC1</label>
                            <input type="number" name="cc1" step="0.25" min="0" max="20" value="{{ $r->grade?->cc1 }}"
                                class="w-full border-gray-200 rounded-xl p-2 text-xs font-bold bg-white">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">CC2</label>
                            <input type="number" name="cc2" step="0.25" min="0" max="20" value="{{ $r->grade?->cc2 }}"
                                class="w-full border-gray-200 rounded-xl p-2 text-xs font-bold bg-white">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Examen</label>
                            <input type="number" name="exam" step="0.25" min="0" max="20" value="{{ $r->grade?->exam }}"
                                class="w-full border-gray-200 rounded-xl p-2 text-xs font-bold bg-white">
                        </div>
                    </div>
                </div>

                <div class="space-y-1">
                    <div class="flex justify-between items-center">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Justification / Commentaire Enseignant *</label>
                        <button type="button" onclick="generateAIDraft({{ $r->id }})" class="text-[10px] font-black bg-purple-100 text-purple-700 px-2 py-1 rounded-lg hover:bg-purple-200 transition-colors flex items-center gap-1" id="btn-ai-{{ $r->id }}">
                            ✨ Suggérer avec l'IA
                        </button>
                    </div>
                    <textarea name="prof_comment" id="prof-comment-{{ $r->id }}" rows="3" required minlength="5"
                        class="w-full border-gray-200 rounded-xl p-3 text-sm font-bold bg-gray-50 focus:ring-purple-500 focus:border-purple-500"
                        placeholder="Indiquez à l'étudiant les motifs de votre décision administrative ou les modifications apportées..."></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeResolveModal({{ $r->id }})"
                        class="bg-gray-100 text-gray-700 px-5 py-2.5 rounded-xl font-black text-sm">Annuler</button>
                    <button type="submit" class="bg-upf-blue text-white px-5 py-2.5 rounded-xl font-black text-sm hover:bg-upf-navy transition-all shadow-md">Soumettre</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach

    <script>
        function openResolveModal(id) {
            document.getElementById('resolve-modal-' + id).classList.remove('hidden');
            toggleGradeInputs(id);
        }
        function closeResolveModal(id) {
            document.getElementById('resolve-modal-' + id).classList.add('hidden');
        }
        function toggleGradeInputs(id) {
            const statusSelect = document.getElementById('decision-status-' + id);
            const gradeInputs = document.getElementById('grade-inputs-' + id);
            if (statusSelect && statusSelect.value === 'accepted') {
                gradeInputs.classList.remove('hidden');
            } else if (gradeInputs) {
                gradeInputs.classList.add('hidden');
            }
        }

        async function generateAIDraft(id) {
            const btn = document.getElementById('btn-ai-' + id);
            const textarea = document.getElementById('prof-comment-' + id);
            
            const originalText = btn.innerHTML;
            btn.innerHTML = '⏳ Génération...';
            btn.disabled = true;

            try {
                const response = await fetch(`/professor/reclamations/${id}/ai-draft`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (data.draft) {
                    textarea.value = data.draft;
                    // Highlight animation
                    textarea.classList.add('bg-purple-50', 'border-purple-300');
                    setTimeout(() => textarea.classList.remove('bg-purple-50', 'border-purple-300'), 2000);
                }
            } catch (error) {
                alert("Erreur lors de la génération du brouillon IA.");
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }
    </script>
</x-app-layout>
