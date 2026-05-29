<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-upf-blue italic">💬 Supervision des Réclamations de Notes</h2>
    </x-slot>

    <div class="py-10 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Summary stats grid --}}
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 text-center">
                    <div class="text-3xl font-black text-gray-700">{{ $stats['total'] }}</div>
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Total Requêtes</div>
                </div>
                <div class="bg-amber-50 rounded-2xl p-5 border border-amber-100 text-center">
                    <div class="text-3xl font-black text-amber-600">{{ $stats['pending'] }}</div>
                    <div class="text-xs font-bold text-amber-500 uppercase tracking-widest mt-1">En attente</div>
                </div>
                <div class="bg-emerald-50 rounded-2xl p-5 border border-emerald-100 text-center">
                    <div class="text-3xl font-black text-emerald-600">{{ $stats['resolved'] }}</div>
                    <div class="text-xs font-bold text-emerald-500 uppercase tracking-widest mt-1">Traitées / Résolues</div>
                </div>
            </div>

            {{-- Filters --}}
            <div class="bg-white rounded-3xl p-5 shadow-sm border border-gray-100">
                <form method="GET" class="flex gap-4 items-end">
                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Statut de Traitement</label>
                        <select name="status" class="border-gray-200 rounded-xl p-3 text-sm font-bold bg-gray-50 focus:ring-red-400">
                            <option value="">Tous les recours</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="reviewed" {{ request('status') === 'reviewed' ? 'selected' : '' }}>En cours</option>
                            <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Acceptées</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Refusées</option>
                        </select>
                    </div>
                    <button type="submit" class="bg-upf-blue text-white px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-upf-navy transition-all">Filtrer</button>
                </form>
            </div>

            {{-- Table --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Date dépôt</th>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Étudiant</th>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Module / Examen</th>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Note Dispute</th>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Motif & Justification</th>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($reclamations as $r)
                            <tr class="hover:bg-gray-50/30 transition-colors">
                                <td class="p-5">
                                    <div class="text-sm font-bold text-gray-900">{{ $r->created_at->format('d/m/Y') }}</div>
                                    <div class="text-[10px] text-gray-400">{{ $r->created_at->diffForHumans() }}</div>
                                </td>
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
                                    <p class="text-xs font-bold text-gray-600">"{{ $r->reason }}"</p>
                                    @if($r->prof_comment)
                                    <div class="mt-2 p-2 bg-gray-50 border border-gray-100 rounded text-xs italic text-gray-500">
                                        <strong>Prof:</strong> "{{ $r->prof_comment }}"
                                    </div>
                                    @endif
                                </td>
                                <td class="p-5">
                                    <span class="px-3 py-1 rounded-full text-xs font-black border {{ $r->status_color }}">
                                        {{ $r->status_label }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="p-16 text-center text-gray-400 italic">Aucun litige ou réclamation enregistré. ✅</td>
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
</x-app-layout>
