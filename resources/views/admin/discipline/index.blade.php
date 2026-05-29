<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tighter italic">⚖️ Conseil de Discipline</h2>
            <a href="{{ route('admin.pilotage.index') }}" class="text-xs font-bold text-gray-400 hover:text-upf-blue uppercase tracking-widest">← Pilotage</a>
        </div>
    </x-slot>

    <div class="py-10 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Banner --}}
            <div class="bg-gradient-to-r from-red-600 to-rose-800 rounded-3xl p-8 text-white shadow-xl relative overflow-hidden">
                <div class="absolute top-0 right-0 w-48 h-48 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="relative z-10">
                    <h1 class="text-3xl font-black italic mb-2">Dossiers Conseil de Discipline</h1>
                    <p class="text-red-100">Étudiants ayant dépassé le seuil de <strong>{{ $discipline }}h</strong> d'absences non justifiées.</p>
                </div>
            </div>

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-2xl font-bold text-sm">{{ session('success') }}</div>
            @endif

            {{-- Stats --}}
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-red-100 text-center">
                    <div class="text-3xl font-black text-red-600">{{ $stats['open'] }}</div>
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Ouverts</div>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-amber-100 text-center">
                    <div class="text-3xl font-black text-amber-600">{{ $stats['notified'] }}</div>
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Notifiés</div>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-emerald-100 text-center">
                    <div class="text-3xl font-black text-emerald-600">{{ $stats['treated'] }}</div>
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Traités</div>
                </div>
            </div>

            {{-- Filtres --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                <form method="GET" class="flex gap-4 items-end">
                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Statut</label>
                        <select name="status" class="border-gray-200 rounded-xl p-3 text-sm font-bold bg-gray-50 focus:ring-red-400">
                            <option value="">Tous</option>
                            <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Ouvert</option>
                            <option value="notified" {{ request('status') === 'notified' ? 'selected' : '' }}>Notifié</option>
                            <option value="treated" {{ request('status') === 'treated' ? 'selected' : '' }}>Traité</option>
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
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Groupe / Filière</th>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Heures NJ</th>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Statut</th>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Date</th>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($cases as $case)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="p-5">
                                    <div class="font-black text-gray-900 text-sm">{{ $case->student?->user?->name }}</div>
                                    <div class="text-xs text-gray-400 font-bold">N° {{ $case->student?->student_number }}</div>
                                </td>
                                <td class="p-5">
                                    <div class="text-sm font-black text-gray-700">{{ $case->student?->group?->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-400 font-bold">{{ $case->student?->group?->filiere?->name ?? '' }}</div>
                                </td>
                                <td class="p-5">
                                    <span class="text-lg font-black text-red-600">{{ number_format($case->total_unjustified_hours, 1) }}h</span>
                                </td>
                                <td class="p-5">
                                    <span class="px-3 py-1 rounded-full text-xs font-black border {{ $case->status_color }}">
                                        {{ $case->status_label }}
                                    </span>
                                </td>
                                <td class="p-5">
                                    <div class="text-xs font-bold text-gray-500">{{ $case->created_at->format('d/m/Y') }}</div>
                                </td>
                                <td class="p-5 text-right">
                                    <a href="{{ route('admin.discipline.show', $case) }}" class="bg-upf-blue text-white px-4 py-2 rounded-xl text-xs font-black hover:bg-upf-navy transition-all">
                                        Voir Dossier
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="p-16 text-center text-gray-400 italic">Aucun dossier de discipline pour le moment. ✅</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($cases->hasPages())
                <div class="p-6 border-t border-gray-100">{{ $cases->links() }}</div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
