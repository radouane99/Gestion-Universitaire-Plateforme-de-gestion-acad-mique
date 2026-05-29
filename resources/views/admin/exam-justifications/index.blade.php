<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-upf-blue italic">📋 Justifications Examens</h2>
            <a href="{{ route('admin.pilotage.index') }}" class="text-xs font-bold text-gray-400 hover:text-upf-blue uppercase tracking-widest">← Pilotage</a>
        </div>
    </x-slot>

    <div class="py-10 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-2xl font-bold text-sm">{{ session('success') }}</div>
            @endif

            {{-- Banner --}}
            <div class="bg-gradient-to-r from-amber-500 to-orange-600 rounded-3xl p-8 text-white shadow-xl">
                <h1 class="text-3xl font-black italic mb-2">Justifications d'Absence aux Examens</h1>
                <p class="text-amber-100">Validez ou refusez les justifications soumises par les étudiants. Une justification approuvée accorde automatiquement le droit au rattrapage.</p>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-amber-100 text-center">
                    <div class="text-3xl font-black text-amber-600">{{ $stats['pending'] }}</div>
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">En Attente</div>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-emerald-100 text-center">
                    <div class="text-3xl font-black text-emerald-600">{{ $stats['approved'] }}</div>
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Approuvées</div>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-red-100 text-center">
                    <div class="text-3xl font-black text-red-600">{{ $stats['rejected'] }}</div>
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Refusées</div>
                </div>
            </div>

            {{-- Filtre --}}
            <div class="bg-white rounded-3xl p-5 shadow-sm border border-gray-100">
                <form method="GET" class="flex gap-4 items-end">
                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Statut</label>
                        <select name="status" class="border-gray-200 rounded-xl p-3 text-sm font-bold bg-gray-50 focus:ring-amber-400">
                            <option value="">Tous</option>
                            <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>En Attente</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approuvées</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Refusées</option>
                        </select>
                    </div>
                    <button type="submit" class="bg-upf-blue text-white px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest">Filtrer</button>
                </form>
            </div>

            {{-- Liste --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Étudiant</th>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Examen / Module</th>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Statut Présence</th>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Justification</th>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Date Dépôt</th>
                                <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($justifications as $j)
                            <tr class="hover:bg-gray-50/30 transition-colors">
                                <td class="p-5">
                                    <div class="font-black text-gray-900 text-sm">{{ $j->student?->user?->name }}</div>
                                    <div class="text-xs text-gray-400 font-bold">{{ $j->student?->group?->name }}</div>
                                </td>
                                <td class="p-5">
                                    <div class="font-black text-gray-900 text-sm">{{ $j->examAttendance?->exam?->module?->name }}</div>
                                    <div class="text-xs text-gray-400 font-bold">{{ \Carbon\Carbon::parse($j->examAttendance?->exam?->date)->format('d/m/Y') }}</div>
                                </td>
                                <td class="p-5">
                                    @php $att = $j->examAttendance; @endphp
                                    <span class="px-3 py-1 rounded-full text-xs font-black {{ $att?->status_color }}">
                                        {{ $att?->status_icon }} {{ $att?->status_label }}
                                    </span>
                                </td>
                                <td class="p-5">
                                    <span class="px-3 py-1 rounded-full text-xs font-black border {{ $j->status_color }}">{{ $j->status_label }}</span>
                                    @if($j->student_comment)
                                    <p class="text-xs text-gray-400 mt-1 italic">{{ Str::limit($j->student_comment, 60) }}</p>
                                    @endif
                                    @if($j->justification_path)
                                    <a href="{{ route('admin.exam_justifications.download', $j) }}" target="_blank"
                                        class="text-xs font-black text-upf-blue hover:underline mt-1 inline-block">📎 Voir Pièce</a>
                                    @endif
                                </td>
                                <td class="p-5">
                                    <div class="text-xs font-bold text-gray-500">{{ $j->created_at->format('d/m/Y') }}</div>
                                    <div class="text-[10px] text-gray-300">{{ $j->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="p-5 text-right">
                                    @if($j->status === 'pending')
                                    <div class="flex justify-end gap-2">
                                        <form action="{{ route('admin.exam_justifications.approve', $j) }}" method="POST">
                                            @csrf
                                            <button class="bg-emerald-600 text-white px-4 py-2 rounded-xl text-xs font-black hover:bg-emerald-700 transition-all shadow-sm">
                                                ✓ Approuver
                                            </button>
                                        </form>
                                        <button onclick="openRejectModal({{ $j->id }})"
                                            class="bg-red-100 text-red-700 px-4 py-2 rounded-xl text-xs font-black hover:bg-red-200 transition-all">
                                            ✗ Refuser
                                        </button>
                                    </div>
                                    @else
                                    <span class="text-xs text-gray-300 italic">{{ $j->reviewed_at?->format('d/m/Y') }}</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="p-16 text-center text-gray-400 italic">Aucune justification d'examen. ✅</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($justifications->hasPages())
                <div class="p-6 border-t border-gray-100">{{ $justifications->links() }}</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modals Refus --}}
    @foreach($justifications->where('status', 'pending') as $j)
    <div id="reject-modal-{{ $j->id }}" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-8 max-w-md w-full shadow-2xl">
            <h3 class="font-black text-xl text-gray-900 mb-2">Refuser la Justification</h3>
            <p class="text-gray-500 text-sm mb-5">Étudiant : <strong>{{ $j->student?->user?->name }}</strong></p>
            <form action="{{ route('admin.exam_justifications.reject', $j) }}" method="POST">
                @csrf
                <textarea name="admin_comment" rows="3" required minlength="5"
                    class="w-full border-gray-200 rounded-xl p-3 text-sm font-bold bg-gray-50 mb-4"
                    placeholder="Motif du refus (obligatoire)..."></textarea>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeRejectModal({{ $j->id }})"
                        class="bg-gray-100 text-gray-700 px-5 py-2 rounded-xl font-black text-sm">Annuler</button>
                    <button type="submit" class="bg-red-600 text-white px-5 py-2 rounded-xl font-black text-sm hover:bg-red-700">Refuser</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach

    <script>
        function openRejectModal(id) {
            document.getElementById('reject-modal-' + id).classList.remove('hidden');
        }
        function closeRejectModal(id) {
            document.getElementById('reject-modal-' + id).classList.add('hidden');
        }
    </script>
</x-app-layout>
