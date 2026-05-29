<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-upf-blue italic">📝 Feuille de Présence — {{ $exam->module?->name }}</h2>
            <a href="{{ route('admin.exams.show', $exam) }}" class="text-xs font-bold text-gray-400 hover:text-upf-blue uppercase tracking-widest">← Examen</a>
        </div>
    </x-slot>

    <div class="py-10 bg-[#F8FAFC]">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-2xl font-bold text-sm">{{ session('success') }}</div>
            @endif

            {{-- Infos Examen --}}
            <div class="bg-gradient-to-r from-upf-blue to-indigo-700 rounded-3xl p-8 text-white shadow-xl">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <div class="text-indigo-200 text-xs font-bold uppercase tracking-widest mb-1">Module</div>
                        <div class="font-black text-lg">{{ $exam->module?->name }}</div>
                    </div>
                    <div>
                        <div class="text-indigo-200 text-xs font-bold uppercase tracking-widest mb-1">Date</div>
                        <div class="font-black text-lg">{{ \Carbon\Carbon::parse($exam->date)->format('d/m/Y') }}</div>
                    </div>
                    <div>
                        <div class="text-indigo-200 text-xs font-bold uppercase tracking-widest mb-1">Horaire</div>
                        <div class="font-black text-lg">{{ \Carbon\Carbon::parse($exam->start_time)->format('H:i') }} — {{ $exam->end_time }}</div>
                    </div>
                    <div>
                        <div class="text-indigo-200 text-xs font-bold uppercase tracking-widest mb-1">Salle</div>
                        <div class="font-black text-lg">{{ $exam->room?->name ?? 'N/A' }}</div>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-4 text-sm">
                    <span class="text-indigo-200">Groupe : <strong class="text-white">{{ $exam->group?->name }}</strong></span>
                    <span class="text-indigo-200">Surveillants : <strong class="text-white">{{ $exam->proctors->pluck('user.name')->join(', ') ?: 'Non affectés' }}</strong></span>
                </div>
            </div>

            {{-- Feuille de présence --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <h3 class="font-black text-gray-900 italic">Liste des Étudiants ({{ $students->count() }})</h3>
                    <div class="flex gap-2 text-xs font-black">
                        <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-lg">✅ {{ $attendances->where('status', 'present')->count() }} Présents</span>
                        <span class="bg-red-100 text-red-700 px-3 py-1 rounded-lg">❌ {{ $attendances->where('status', 'absent')->count() }} Absents</span>
                        <span class="bg-amber-100 text-amber-700 px-3 py-1 rounded-lg">⏰ {{ $attendances->where('status', 'late')->count() }} Retards</span>
                        <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-lg">🚨 {{ $attendances->where('status', 'fraud')->count() }} Fraudes</span>
                    </div>
                </div>

                <form action="{{ route('admin.exam_attendance.store', $exam) }}" method="POST">
                    @csrf
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th class="p-4 text-[10px] font-black uppercase tracking-widest text-gray-400 w-8">#</th>
                                    <th class="p-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Étudiant</th>
                                    <th class="p-4 text-[10px] font-black uppercase tracking-widest text-gray-400">N° Étudiant</th>
                                    <th class="p-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Statut Présence</th>
                                    <th class="p-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Notes</th>
                                    <th class="p-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Marqué par</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($students as $i => $student)
                                @php $att = $attendances->get($student->id); @endphp
                                <tr class="hover:bg-gray-50/30 transition-colors">
                                    <td class="p-4 text-gray-400 font-bold text-sm">{{ $i + 1 }}</td>
                                    <td class="p-4">
                                        <div class="font-black text-gray-900 text-sm">{{ $student->user?->name }}</div>
                                        <div class="text-xs text-gray-400">{{ $student->user?->email }}</div>
                                    </td>
                                    <td class="p-4">
                                        <span class="font-bold text-gray-600 text-sm">{{ $student->student_number }}</span>
                                    </td>
                                    <td class="p-4">
                                        <select name="attendances[{{ $student->id }}][status]"
                                            class="border-gray-200 rounded-xl p-2 text-sm font-black focus:ring-upf-blue
                                                {{ $att?->status === 'present' ? 'bg-emerald-50 text-emerald-700' : '' }}
                                                {{ $att?->status === 'absent' ? 'bg-red-50 text-red-700' : '' }}
                                                {{ $att?->status === 'late' ? 'bg-amber-50 text-amber-700' : '' }}
                                                {{ $att?->status === 'fraud' ? 'bg-purple-50 text-purple-700' : '' }}
                                            ">
                                            <option value="present" {{ $att?->status === 'present' ? 'selected' : '' }}>✅ Présent</option>
                                            <option value="absent"  {{ ($att?->status === 'absent' || !$att) ? 'selected' : '' }}>❌ Absent</option>
                                            <option value="late"    {{ $att?->status === 'late' ? 'selected' : '' }}>⏰ Retard</option>
                                            <option value="fraud"   {{ $att?->status === 'fraud' ? 'selected' : '' }}>🚨 Fraude</option>
                                        </select>
                                    </td>
                                    <td class="p-4">
                                        <input type="text" name="attendances[{{ $student->id }}][notes]"
                                            value="{{ $att?->notes }}"
                                            class="border-gray-200 rounded-xl p-2 text-sm font-bold focus:ring-upf-blue w-full bg-gray-50"
                                            placeholder="Remarque...">
                                    </td>
                                    <td class="p-4">
                                        @if($att)
                                        <div class="text-xs text-gray-400 font-bold">{{ $att->markedBy?->name ?? 'N/A' }}</div>
                                        <div class="text-[10px] text-gray-300">{{ $att->marked_at?->format('d/m H:i') }}</div>
                                        @else
                                        <span class="text-gray-300 text-xs italic">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="p-6 border-t border-gray-100 flex justify-end gap-3">
                        <button type="submit" class="bg-upf-blue text-white px-8 py-3 rounded-2xl font-black uppercase tracking-widest text-sm hover:bg-upf-navy transition-all shadow-lg">
                            💾 Enregistrer la Feuille de Présence
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
