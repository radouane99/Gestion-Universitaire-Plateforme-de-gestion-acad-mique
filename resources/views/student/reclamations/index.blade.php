<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tighter italic">💬 Mes Réclamations</h2>
            <a href="{{ route('student.reclamations.create') }}" class="bg-upf-blue text-white px-5 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-upf-navy transition-all shadow-md">
                + Déposer une Réclamation
            </a>
        </div>
    </x-slot>

    <div class="py-10 bg-[#F8FAFC]">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-2xl font-bold text-sm">{{ session('success') }}</div>
            @endif

            {{-- Summary card --}}
            <div class="bg-gradient-to-r from-upf-blue to-indigo-700 rounded-3xl p-8 text-white shadow-xl">
                <h1 class="text-3xl font-black italic mb-2">Suivi des Contestations de Notes</h1>
                <p class="text-indigo-200">Si vous constatez une anomalie sur vos notes de contrôle ou d'examen, déposez un recours. Nos équipes pédagogiques analyseront votre demande.</p>
            </div>

            {{-- List --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Date dépôt</th>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Module / Examen</th>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Note Dispute</th>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Motif de dispute</th>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Statut</th>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Réponse Enseignant</th>
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
                                    <div class="font-black text-gray-900 text-sm">{{ $r->module?->name }}</div>
                                    <div class="text-xs text-gray-400 font-bold">Code: {{ $r->module?->code }}</div>
                                </td>
                                <td class="p-5 text-right font-black text-sm text-gray-700">
                                    {{ $r->grade ? number_format($r->grade->final_grade, 2) : 'N/A' }}/20
                                </td>
                                <td class="p-5">
                                    <p class="text-xs font-bold text-gray-600 max-w-xs truncate" title="{{ $r->reason }}">{{ $r->reason }}</p>
                                </td>
                                <td class="p-5">
                                    <span class="px-3 py-1 rounded-full text-xs font-black border {{ $r->status_color }}">
                                        {{ $r->status_label }}
                                    </span>
                                </td>
                                <td class="p-5">
                                    @if($r->prof_comment)
                                    <p class="text-xs text-gray-500 font-bold bg-gray-50 p-2 rounded-lg border border-gray-100 italic">"{{ $r->prof_comment }}"</p>
                                    @else
                                    <span class="text-xs text-gray-300 italic">En attente de réponse...</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="p-16 text-center text-gray-400 italic">Aucune réclamation de note déposée. ✅</td>
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
