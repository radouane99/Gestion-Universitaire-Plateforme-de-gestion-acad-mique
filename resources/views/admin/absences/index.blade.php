<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tighter italic">
            {{ __("Validation des Justificatifs d'Absences") }}
        </h2>
    </x-slot>

    <div class="py-12 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <div class="bg-gradient-to-r from-rose-600 to-rose-900 rounded-3xl p-10 text-white shadow-2xl relative overflow-hidden">
                <div class="relative z-10">
                    <h2 class="text-3xl font-black mb-2 italic">Contrôle de l'Assiduité</h2>
                    <p class="text-rose-100 opacity-80">Gérez les demandes de justifications médicales ou administratives soumises par les étudiants.</p>
                </div>
            </div>

            @if(session('success'))
                <div class="p-4 text-sm text-emerald-800 rounded-2xl bg-emerald-50 border border-emerald-100 font-bold">
                    {{ session('success') }}
                </div>
            @endif

            @if(isset($studentsAtRisk) && $studentsAtRisk->isNotEmpty())
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-red-200 overflow-hidden mb-8">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-red-100 text-red-600 rounded-2xl flex items-center justify-center mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-red-600 uppercase tracking-wider">Alerte - Conseil de Discipline</h3>
                            <p class="text-sm text-gray-500 font-bold">Ces étudiants ont dépassé le seuil de 120 heures d'absences non justifiées.</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($studentsAtRisk as $riskStudent)
                        <div class="p-4 rounded-2xl border border-red-100 bg-red-50 flex justify-between items-center">
                            <div>
                                <div class="font-black text-red-900">{{ $riskStudent->user->name }}</div>
                                <div class="text-xs font-bold text-red-700">Groupe: {{ $riskStudent->group->name ?? 'N/A' }}</div>
                            </div>
                            <div class="text-right">
                                <span class="bg-red-600 text-white font-black text-sm px-3 py-1 rounded-xl shadow-sm">{{ $riskStudent->absence_score }} Heures</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Filtres -->
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                <form method="GET" action="{{ route('admin.absences.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="space-y-2">
                        <label for="status" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">État du Justificatif</label>
                        <select name="status" id="status" class="w-full border-gray-200 rounded-2xl focus:ring-rose-500 focus:border-rose-500 p-4 font-bold text-gray-900 bg-gray-50">
                            <option value="">Tous les états</option>
                            <option value="none" {{ request('status') === 'none' ? 'selected' : '' }}>Aucun justificatif déposé</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Justificatif en attente</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Justificatif approuvé</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Justificatif rejeté</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label for="filiere_id" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Filière</label>
                        <select name="filiere_id" id="filiere_id" class="w-full border-gray-200 rounded-2xl focus:ring-rose-500 focus:border-rose-500 p-4 font-bold text-gray-900 bg-gray-50">
                            <option value="">Toutes les filières</option>
                            @foreach($filieres as $filiere)
                                <option value="{{ $filiere->id }}" {{ request('filiere_id') == $filiere->id ? 'selected' : '' }}>{{ $filiere->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label for="group_id" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Groupe</label>
                        <select name="group_id" id="group_id" class="w-full border-gray-200 rounded-2xl focus:ring-rose-500 focus:border-rose-500 p-4 font-bold text-gray-900 bg-gray-50">
                            <option value="">Tous les groupes</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="w-full py-4 bg-upf-blue text-white rounded-2xl font-black shadow-lg hover:bg-upf-navy transition-all uppercase text-xs tracking-widest">
                            Filtrer
                        </button>
                    </div>
                </form>
            </div>

            <!-- Liste des absences -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-xl font-black text-gray-900 italic">Registre d'Absences Global</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/70 border-b border-gray-100">
                                <th class="p-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Date d'Absence</th>
                                <th class="p-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Étudiant</th>
                                <th class="p-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Module & Type</th>
                                <th class="p-6 text-[10px] font-black uppercase tracking-widest text-gray-400">État de Justification</th>
                                <th class="p-6 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 font-bold text-gray-700">
                            @forelse($absences as $abs)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="p-6">
                                        <div class="text-gray-900 text-sm font-black">{{ \Carbon\Carbon::parse($abs->date)->format('d/m/Y') }}</div>
                                        <div class="text-[10px] text-gray-400 font-semibold mt-1">ID Absence: #{{ $abs->id }}</div>
                                    </td>
                                    <td class="p-6">
                                        <div class="text-gray-900 text-sm font-black">{{ $abs->student->user->name ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-400 font-semibold mb-1">Groupe: {{ $abs->student->group->name ?? 'N/A' }}</div>
                                        @if(isset($abs->student) && $abs->student->absence_score >= 120)
                                            <span class="inline-block bg-red-100 text-red-700 px-2 py-0.5 rounded-md text-[10px] font-black uppercase border border-red-200">Score: {{ $abs->student->absence_score }}h (Alerte)</span>
                                        @else
                                            <span class="inline-block bg-gray-100 text-gray-600 px-2 py-0.5 rounded-md text-[10px] font-black uppercase">Score: {{ $abs->student->absence_score ?? 0 }}h</span>
                                        @endif
                                    </td>
                                    <td class="p-6">
                                        <div class="text-gray-900 text-sm font-black">{{ $abs->module->name ?? 'Séance standard' }}</div>
                                        <div class="inline-block bg-gray-100 text-gray-600 px-2 py-0.5 rounded-md text-[10px] font-black mt-1 uppercase">{{ $abs->session_type }}</div>
                                    </td>
                                    <td class="p-6">
                                        @if($abs->justification_status === 'none')
                                            <span class="text-[10px] font-black uppercase bg-gray-100 text-gray-400 px-3 py-1 rounded-full border border-gray-200 tracking-widest">Aucun</span>
                                        @elseif($abs->justification_status === 'pending')
                                            <span class="text-[10px] font-black uppercase bg-amber-50 text-amber-600 px-3 py-1 rounded-full border border-amber-100 tracking-widest">En Attente de Revue</span>
                                        @elseif($abs->justification_status === 'approved')
                                            <span class="text-[10px] font-black uppercase bg-emerald-50 text-emerald-600 px-3 py-1 rounded-full border border-emerald-100 tracking-widest">Approuvé & Justifié</span>
                                        @else
                                            <span class="text-[10px] font-black uppercase bg-pink-50 text-upf-magenta px-3 py-1 rounded-full border border-pink-100 tracking-widest">Rejeté</span>
                                        @endif
                                    </td>
                                    <td class="p-6 text-right">
                                        <div class="flex justify-end items-center gap-3">
                                            @if($abs->justification_path)
                                                <a href="{{ route('absences.justification', $abs->id) }}" target="_blank" class="inline-flex items-center text-upf-blue hover:text-upf-navy font-black text-xs uppercase tracking-widest gap-1 bg-indigo-50 px-3 py-2 rounded-xl">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                    Voir Pièce
                                                </a>
                                            @endif

                                            @if($abs->justification_status === 'pending')
                                                <form method="POST" action="{{ route('admin.absences.approve', $abs->id) }}">
                                                    @csrf
                                                    <button class="bg-emerald-600 text-white px-3 py-2 rounded-xl text-xs font-black hover:bg-emerald-700 transition-all shadow-md">Valider</button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.absences.reject', $abs->id) }}">
                                                    @csrf
                                                    <button class="bg-upf-magenta text-white px-3 py-2 rounded-xl text-xs font-black hover:bg-pink-700 transition-all shadow-md">Rejeter</button>
                                                </form>
                                            @elseif($abs->justification_status === 'none' || $abs->justification_status === 'rejected')
                                                <form method="POST" action="{{ route('admin.absences.force-justify', $abs->id) }}">
                                                    @csrf
                                                    <button class="bg-blue-600 text-white px-3 py-2 rounded-xl text-xs font-black hover:bg-blue-700 transition-all shadow-md">Justifier</button>
                                                </form>
                                            @endif

                                            <form method="POST" action="{{ route('admin.absences.destroy', $abs->id) }}" onsubmit="return confirm('Voulez-vous vraiment supprimer cette absence ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-gray-100 text-gray-500 px-3 py-2 rounded-xl text-xs font-black hover:bg-red-100 hover:text-red-600 transition-all shadow-sm" title="Supprimer l'absence">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-16 text-center text-gray-400 italic">
                                        Aucune absence enregistrée pour le moment.
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
