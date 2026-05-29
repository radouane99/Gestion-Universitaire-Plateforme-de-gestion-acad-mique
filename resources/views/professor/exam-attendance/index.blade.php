<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-upf-blue italic">📝 Feuille de Présence — Surveillance</h2>
            <a href="{{ route('professor.proctor_convocations.index') }}" class="text-xs font-bold text-gray-400 hover:text-upf-blue uppercase tracking-widest">← Mes Surveillances</a>
        </div>
    </x-slot>

    <div class="py-10 bg-[#F8FAFC]">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

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
            </div>

            {{-- Feuille de présence --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <h3 class="font-black text-gray-900 italic">Groupe {{ $exam->group?->name }} — {{ $students->count() }} étudiants</h3>
                </div>

                <form action="{{ route('professor.exam_attendance.store', $exam) }}" method="POST">
                    @csrf
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th class="p-4 text-[10px] font-black uppercase tracking-widest text-gray-400 w-8">#</th>
                                    <th class="p-4 text-[10px] font-black uppercase tracking-widest text-gray-400">N° Étudiant</th>
                                    <th class="p-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Nom Complet</th>
                                    <th class="p-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Statut</th>
                                    <th class="p-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Remarque</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($students as $i => $student)
                                @php $att = $attendances->get($student->id); @endphp
                                <tr class="hover:bg-gray-50/30 transition-colors">
                                    <td class="p-4 text-gray-400 font-bold text-sm">{{ $i + 1 }}</td>
                                    <td class="p-4">
                                        <span class="font-bold text-gray-600 text-sm">{{ $student->student_number }}</span>
                                    </td>
                                    <td class="p-4">
                                        <div class="font-black text-gray-900 text-sm">{{ $student->user?->name }}</div>
                                    </td>
                                    <td class="p-4">
                                        <select name="attendances[{{ $student->id }}][status]"
                                            class="border-gray-200 rounded-xl p-2 text-sm font-black focus:ring-upf-blue">
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
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="p-6 border-t border-gray-100 flex justify-end">
                        <button type="submit" class="bg-upf-blue text-white px-8 py-3 rounded-2xl font-black uppercase tracking-widest text-sm hover:bg-upf-navy transition-all shadow-lg">
                            💾 Valider la Feuille de Présence
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
