<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tighter italic">
                📋 Liste d'Affichage
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.exams.planning.simulation', $exam->exam_session_id) }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition-all shadow-sm">
                    🔙 Retour au Planning
                </a>
                <a href="{{ route('admin.exams.display_list.pdf', $exam) }}" target="_blank" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-bold rounded-xl transition-all shadow-sm flex items-center gap-2">
                    <span>📄</span> Télécharger PDF
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-[#F8FAFC] min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Exam Info -->
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Module</p>
                        <p class="font-black text-lg text-gray-900">{{ $exam->module->name }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Date & Heure</p>
                        <p class="font-black text-lg text-upf-blue">{{ \Carbon\Carbon::parse($exam->date)->format('d/m/Y') }} à {{ $exam->start_time }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Salle</p>
                        <p class="font-black text-lg text-upf-magenta">{{ $exam->room->name }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Groupe / Filière</p>
                        <p class="font-black text-lg text-gray-900">{{ $exam->group->name }} ({{ $exam->group->filiere->name }})</p>
                    </div>
                </div>
                
                <div class="mt-6 pt-6 border-t border-gray-100">
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Surveillants assignés</p>
                    <div class="flex flex-wrap gap-2">
                        @forelse($exam->proctors as $proctor)
                            <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-lg text-sm font-bold border border-blue-100">
                                {{ $proctor->user->name }}
                            </span>
                        @empty
                            <span class="text-red-500 font-bold text-sm">Aucun surveillant</span>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Students List -->
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <h3 class="text-xl font-black text-gray-900 tracking-tight">Étudiants concernés ({{ $exam->convocations->count() }})</h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white border-b border-gray-100">
                                <th class="py-4 px-6 text-xs font-black text-gray-400 uppercase tracking-widest w-16">N° Place</th>
                                <th class="py-4 px-6 text-xs font-black text-gray-400 uppercase tracking-widest w-32">Matricule</th>
                                <th class="py-4 px-6 text-xs font-black text-gray-400 uppercase tracking-widest">Nom & Prénom</th>
                                <th class="py-4 px-6 text-xs font-black text-gray-400 uppercase tracking-widest text-right">Statut Convocation</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($exam->convocations->sortBy('student.user.name') as $convocation)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="py-4 px-6">
                                        <span class="font-black text-lg text-gray-900">{{ str_replace('Place ', '', $convocation->seat_number) }}</span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="font-mono text-sm text-gray-500">{{ $convocation->student->student_number }}</span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <p class="font-bold text-gray-900">{{ $convocation->student->user->name }}</p>
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <span class="{{ $convocation->status_color }} px-3 py-1 rounded-full text-xs font-bold">
                                            {{ $convocation->status_label }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-12 text-center text-gray-400 font-medium">
                                        Aucune convocation générée pour cet examen.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
