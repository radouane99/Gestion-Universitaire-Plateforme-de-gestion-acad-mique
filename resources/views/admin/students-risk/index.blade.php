<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tighter italic">🚨 Étudiants à Risque (Predictive Monitoring)</h2>
    </x-slot>

    <div class="py-10 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-2xl font-bold text-sm">{{ session('success') }}</div>
            @endif

            {{-- Summary stats grid --}}
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 text-center">
                    <div class="text-3xl font-black text-gray-700">{{ $stats['total'] }}</div>
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Total Étudiants</div>
                </div>
                <div class="bg-emerald-50 rounded-2xl p-5 border border-emerald-100 text-center">
                    <div class="text-3xl font-black text-emerald-600">{{ $stats['normal'] }}</div>
                    <div class="text-xs font-bold text-emerald-500 uppercase tracking-widest mt-1">Normal</div>
                </div>
                <div class="bg-amber-50 rounded-2xl p-5 border border-amber-100 text-center">
                    <div class="text-3xl font-black text-amber-600">{{ $stats['to_watch'] }}</div>
                    <div class="text-xs font-bold text-amber-500 uppercase tracking-widest mt-1">À Surveiller</div>
                </div>
                <div class="bg-orange-50 rounded-2xl p-5 border border-orange-100 text-center">
                    <div class="text-3xl font-black text-orange-600">{{ $stats['pedagogical_risk'] }}</div>
                    <div class="text-xs font-bold text-orange-500 uppercase tracking-widest mt-1">Risque Pédagogique</div>
                </div>
                <div class="bg-rose-50 rounded-2xl p-5 border border-rose-100 text-center">
                    <div class="text-3xl font-black text-rose-600">{{ $stats['discipline_council'] }}</div>
                    <div class="text-xs font-bold text-rose-500 uppercase tracking-widest mt-1">Conseil Discipline</div>
                </div>
            </div>

            {{-- Filter bar --}}
            <div class="bg-white rounded-3xl p-5 shadow-sm border border-gray-100">
                <form method="GET" class="flex gap-4 items-end">
                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Filtrer par Risque</label>
                        <select name="risk_level" class="border-gray-200 rounded-xl p-3 text-sm font-bold bg-gray-50 focus:ring-red-400">
                            <option value="">Tous les risques</option>
                            <option value="normal" {{ request('risk_level') === 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="to_watch" {{ request('risk_level') === 'to_watch' ? 'selected' : '' }}>À Surveiller</option>
                            <option value="pedagogical_risk" {{ request('risk_level') === 'pedagogical_risk' ? 'selected' : '' }}>Risque Pédagogique</option>
                            <option value="discipline_council" {{ request('risk_level') === 'discipline_council' ? 'selected' : '' }}>Conseil de discipline</option>
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
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Étudiant</th>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Groupe & Filière</th>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Absences (NJ)</th>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Moyenne Active</th>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">Niveau de Risque</th>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($paginated as $item)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="p-5">
                                    <div class="font-black text-gray-900 text-sm">{{ $item->student->user?->name }}</div>
                                    <div class="text-xs text-gray-400 font-bold">N° {{ $item->student->student_number }}</div>
                                </td>
                                <td class="p-5">
                                    <div class="font-black text-gray-700 text-sm">{{ $item->student->group?->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-400 font-bold">{{ $item->student->group?->filiere?->name }}</div>
                                </td>
                                <td class="p-5 text-right font-bold text-red-600 text-sm">
                                    {{ number_format($item->absences, 1) }}h
                                </td>
                                <td class="p-5 text-right font-bold text-gray-700 text-sm">
                                    {{ $item->moyenne !== null ? number_format($item->moyenne, 2) . '/20' : '—' }}
                                </td>
                                <td class="p-5 text-center">
                                    <span class="px-3 py-1 rounded-full text-xs font-black border {{ $item->risk_color }}">
                                        {{ $item->risk_label }}
                                    </span>
                                </td>
                                <td class="p-5 text-right">
                                    @if($item->risk_level === 'discipline_council' || $item->risk_level === 'pedagogical_risk')
                                        @if(!$item->student->hasActiveDisciplineCase())
                                        <form action="{{ route('admin.students_risk.summon', $item->student) }}" method="POST" class="inline-block">
                                            @csrf
                                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-xl text-xs font-black hover:bg-red-700 transition-all shadow-sm">
                                                ⚖️ Convoquer Conseil
                                            </button>
                                        </form>
                                        @else
                                        <span class="text-xs font-bold text-gray-400 italic">Déjà convoqué (Dossier actif)</span>
                                        @endif
                                    @else
                                        <span class="text-xs text-gray-300 italic">—</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="p-16 text-center text-gray-400 italic">Aucun étudiant ne correspond aux filtres. ✅</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($paginated->hasPages())
                <div class="p-6 border-t border-gray-100">{{ $paginated->links() }}</div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
