<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-upf-blue italic">🎓 Liste de Rattrapage</h2>
            <div class="flex gap-3">
                @if(isset($session))
                <a href="{{ route('admin.retake.export_pdf', $session) }}" target="_blank"
                    class="bg-red-600 text-white px-4 py-2 rounded-xl text-xs font-black hover:bg-red-700 transition-all">
                    📄 Export PDF
                </a>
                <a href="{{ route('admin.retake.export_excel', $session) }}"
                    class="bg-emerald-600 text-white px-4 py-2 rounded-xl text-xs font-black hover:bg-emerald-700 transition-all">
                    📊 Export Excel
                </a>
                @endif
                <a href="{{ route('admin.pilotage.index') }}" class="text-xs font-bold text-gray-400 hover:text-upf-blue uppercase tracking-widest">← Pilotage</a>
            </div>
        </div>
    </x-slot>

    <div class="py-10 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-2xl font-bold text-sm">{{ session('success') }}</div>
            @endif

            {{-- Banner --}}
            <div class="bg-gradient-to-r from-emerald-600 to-teal-700 rounded-3xl p-8 text-white shadow-xl">
                <h1 class="text-3xl font-black italic mb-2">Éligibilité au Rattrapage</h1>
                <p class="text-emerald-100">Gestion des droits de rattrapage : absent justifié ou note insuffisante.</p>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-amber-100 text-center">
                    <div class="text-3xl font-black text-amber-600">{{ $stats['pending'] }}</div>
                    <div class="text-xs font-bold text-gray-400 uppercase mt-1">En Attente</div>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-emerald-100 text-center">
                    <div class="text-3xl font-black text-emerald-600">{{ $stats['eligible'] }}</div>
                    <div class="text-xs font-bold text-gray-400 uppercase mt-1">Éligibles</div>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-blue-100 text-center">
                    <div class="text-3xl font-black text-blue-600">{{ $stats['approved'] }}</div>
                    <div class="text-xs font-bold text-gray-400 uppercase mt-1">Accordés</div>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-red-100 text-center">
                    <div class="text-3xl font-black text-red-600">{{ $stats['not_eligible'] }}</div>
                    <div class="text-xs font-bold text-gray-400 uppercase mt-1">Non Éligibles</div>
                </div>
            </div>

            {{-- Filtres --}}
            <div class="bg-white rounded-3xl p-5 shadow-sm border border-gray-100">
                <form method="GET" class="flex gap-4 items-end flex-wrap">
                    @if($sessions->count())
                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Session</label>
                        <select name="session_id" onchange="this.form.submit()" class="border-gray-200 rounded-xl p-3 text-sm font-bold bg-gray-50">
                            <option value="">Toutes sessions</option>
                            @foreach($sessions as $sess)
                            <option value="{{ $sess->id }}" {{ isset($session) && $session->id === $sess->id ? 'selected' : '' }}>
                                {{ $sess->name }} — {{ $sess->start_date?->format('Y') }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Raison</label>
                        <select name="reason" class="border-gray-200 rounded-xl p-3 text-sm font-bold bg-gray-50">
                            <option value="">Toutes</option>
                            <option value="exam_absence_justified" {{ request('reason') === 'exam_absence_justified' ? 'selected' : '' }}>Absence Justifiée</option>
                            <option value="low_grade" {{ request('reason') === 'low_grade' ? 'selected' : '' }}>Note Insuffisante</option>
                            <option value="admin_decision" {{ request('reason') === 'admin_decision' ? 'selected' : '' }}>Décision Admin</option>
                        </select>
                    </div>
                    <button type="submit" class="bg-upf-blue text-white px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest">Filtrer</button>
                </form>
            </div>

            {{-- Table --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400">#</th>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Étudiant</th>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Module / Examen</th>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Raison</th>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Éligibilité</th>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Décision Admin</th>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($eligibilities as $i => $e)
                            <tr class="hover:bg-gray-50/30 transition-colors">
                                <td class="p-5 text-gray-400 font-bold text-sm">{{ $eligibilities->firstItem() + $i }}</td>
                                <td class="p-5">
                                    <div class="font-black text-gray-900 text-sm">{{ $e->student?->user?->name }}</div>
                                    <div class="text-xs text-gray-400 font-bold">{{ $e->student?->group?->name }}</div>
                                </td>
                                <td class="p-5">
                                    <div class="font-black text-gray-900 text-sm">{{ $e->exam?->module?->name }}</div>
                                    <div class="text-xs text-gray-400 font-bold">{{ \Carbon\Carbon::parse($e->exam?->date)->format('d/m/Y') }}</div>
                                </td>
                                <td class="p-5">
                                    <span class="px-3 py-1 rounded-full text-xs font-black border border-gray-200 bg-gray-100 text-gray-700">
                                        {{ $e->reason_label }}
                                    </span>
                                </td>
                                <td class="p-5">
                                    <span class="px-3 py-1 rounded-full text-xs font-black border {{ $e->status_color }}">
                                        {{ $e->status_label }}
                                    </span>
                                </td>
                                <td class="p-5">
                                    @if($e->admin_decision === 'approved')
                                        <span class="px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-700 border border-emerald-200">✅ Accordé</span>
                                    @elseif($e->admin_decision === 'rejected')
                                        <span class="px-3 py-1 rounded-full text-xs font-black bg-red-100 text-red-700 border border-red-200">❌ Refusé</span>
                                    @else
                                        <span class="px-3 py-1 rounded-full text-xs font-black bg-gray-100 text-gray-500 border border-gray-200">— En attente</span>
                                    @endif
                                </td>
                                <td class="p-5 text-right">
                                    @if(!$e->admin_decision || $e->admin_decision === 'pending')
                                    <div class="flex justify-end gap-2">
                                        <form action="{{ route('admin.retake.approve', $e) }}" method="POST">
                                            @csrf
                                            <button class="bg-emerald-600 text-white px-3 py-1.5 rounded-xl text-xs font-black hover:bg-emerald-700 transition-all">✓ Accorder</button>
                                        </form>
                                        <form action="{{ route('admin.retake.reject', $e) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="admin_comment" value="Refus administratif.">
                                            <button class="bg-red-100 text-red-700 px-3 py-1.5 rounded-xl text-xs font-black hover:bg-red-200 transition-all">✗ Refuser</button>
                                        </form>
                                    </div>
                                    @else
                                    <span class="text-xs text-gray-300 italic">{{ $e->decided_at?->format('d/m/Y') }}</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="p-16 text-center text-gray-400 italic">Aucun dossier de rattrapage trouvé.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($eligibilities->hasPages())
                <div class="p-6 border-t border-gray-100">{{ $eligibilities->links() }}</div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
