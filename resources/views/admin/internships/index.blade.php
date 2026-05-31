<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tight italic">
            {{ __('Gestion Administrative des Stages') }}
        </h2>
    </x-slot>

    <div class="py-10 bg-[#F8FAFC] dark:bg-[#020617] transition-colors duration-300">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">

            {{-- Summary Stats Header --}}
            <div class="bg-gradient-to-br from-indigo-900 to-black text-white rounded-[2.5rem] p-10 shadow-xl relative overflow-hidden">
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-widest bg-white/20 px-3 py-1 rounded-full border border-white/10">CONSOLE DE PILOTAGE</span>
                        <h3 class="text-3xl font-black mt-3 mb-2 italic tracking-tight">🏛️ Conventions & Stages UPF</h3>
                        <p class="text-blue-105 opacity-90 text-sm max-w-xl">Validez les conventions de stage déposées par les étudiants et affectez-leur un enseignant-tuteur pour le suivi mensuel.</p>
                    </div>
                    <div class="flex gap-4">
                        <div class="bg-white/15 backdrop-blur border border-white/20 px-6 py-4 rounded-2xl text-center shadow-inner">
                            <p class="text-2xl font-black">{{ $internships->where('status', 'pending')->count() }}</p>
                            <p class="text-[9px] uppercase font-black text-blue-200 tracking-widest">En Attente</p>
                        </div>
                        <div class="bg-white/15 backdrop-blur border border-white/20 px-6 py-4 rounded-2xl text-center shadow-inner">
                            <p class="text-2xl font-black">{{ $internships->where('status', 'active')->count() }}</p>
                            <p class="text-[9px] uppercase font-black text-blue-200 tracking-widest">Actifs</p>
                        </div>
                    </div>
                </div>
                <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>
            </div>

            @if(session('success'))
                <div class="p-6 bg-emerald-50 text-emerald-700 rounded-3xl border border-emerald-100 flex items-center gap-4 shadow-sm animate-fade-in-down">
                    <span class="text-2xl">🎉</span>
                    <p class="font-extrabold text-sm">{{ session('success') }}</p>
                </div>
            @endif

            {{-- 1. Pending Applications Table --}}
            <div class="bg-white dark:bg-slate-900 shadow-sm rounded-3xl border border-rose-100 dark:border-slate-800 overflow-hidden">
                <div class="p-8 border-b border-rose-50 dark:border-slate-850 bg-rose-500/[0.01] dark:bg-slate-950/20">
                    <h3 class="text-lg font-black text-slate-900 dark:text-white tracking-tight italic flex items-center gap-2">
                        <span class="animate-pulse w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                        📥 Demandes de stage en attente de validation
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Validez les conventions de stage et affectez un enseignant encadrant.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 dark:bg-slate-950/40 text-[10px] font-black uppercase tracking-widest text-slate-450 border-b border-gray-100 dark:border-slate-800">
                                <th class="py-4 px-6">Étudiant</th>
                                <th class="py-4 px-6">Groupe</th>
                                <th class="py-4 px-6">Entreprise / Sujet</th>
                                <th class="py-4 px-6">Dates Prévues</th>
                                <th class="py-4 px-6 text-center">Affecter un Tuteur</th>
                                <th class="py-4 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                            @forelse($internships->where('status', 'pending') as $intern)
                                <tr class="hover:bg-slate-50/30 dark:hover:bg-slate-950/10 transition-colors">
                                    <td class="py-5 px-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-black text-xs shadow-inner">{{ strtoupper(substr($intern->student->user->name, 0, 1)) }}</div>
                                            <div>
                                                <p class="font-extrabold text-sm text-slate-950 dark:text-white leading-tight">{{ $intern->student->user->name }}</p>
                                                <p class="text-[10px] text-slate-450 mt-0.5">N° {{ $intern->student->id }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-5 px-6 font-bold text-xs text-slate-700 dark:text-slate-350">
                                        {{ $intern->student->group->name }}
                                    </td>
                                    <td class="py-5 px-6">
                                        <div class="min-w-[200px]">
                                            <p class="text-xs font-black text-slate-850 dark:text-slate-200">{{ $intern->company_name }}</p>
                                            <p class="text-[10px] text-slate-450 mt-0.5 italic truncate">"{{ $intern->subject }}"</p>
                                        </div>
                                    </td>
                                    <td class="py-5 px-6">
                                        <div class="text-[10px] font-bold text-slate-500">
                                            <p>Début : {{ $intern->start_date->format('d/m/Y') }}</p>
                                            <p class="mt-0.5">Fin : {{ $intern->end_date->format('d/m/Y') }}</p>
                                        </div>
                                    </td>
                                    <form action="{{ route('admin.internships.approve', $intern) }}" method="POST">
                                        @csrf
                                        <td class="py-5 px-6 text-center">
                                            <select name="academic_tutor_id" required class="block w-48 rounded-xl border border-gray-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 p-2 text-xs font-semibold text-slate-700 dark:text-slate-300">
                                                <option value="" disabled selected>— Sélectionner un Tuteur —</option>
                                                @foreach($professors as $prof)
                                                    <option value="{{ $prof->id }}">👨‍🏫 {{ $prof->user->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="py-5 px-6 text-right">
                                            <div class="flex justify-end gap-2">
                                                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-sm">
                                                    Approuver
                                                </button>
                                            </div>
                                        </td>
                                    </form>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-slate-400 font-bold italic text-xs">
                                        Aucune demande de convention de stage en attente.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 2. Overall Internships List --}}
            <div class="bg-white dark:bg-slate-900 shadow-sm rounded-3xl border border-gray-100 dark:border-slate-800 overflow-hidden">
                <div class="p-8 border-b border-gray-50 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/20">
                    <h3 class="text-lg font-black text-slate-900 dark:text-white tracking-tight italic">📁 Registre Général des Stages</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Suivi de tous les stages validés, actifs et clôturés.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 dark:bg-slate-950/40 text-[10px] font-black uppercase tracking-widest text-slate-450 border-b border-gray-100 dark:border-slate-800">
                                <th class="py-4 px-6">Étudiant</th>
                                <th class="py-4 px-6">Entreprise</th>
                                <th class="py-4 px-6">Tuteur Académique</th>
                                <th class="py-4 px-6 text-center">Rapports</th>
                                <th class="py-4 px-6 text-center">Score Final</th>
                                <th class="py-4 px-6 text-right">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                            @forelse($internships->where('status', '!=', 'pending') as $intern)
                                <tr class="hover:bg-slate-50/30 dark:hover:bg-slate-950/10 transition-colors">
                                    <td class="py-5 px-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-650 flex items-center justify-center font-black text-[10px]">{{ strtoupper(substr($intern->student->user->name, 0, 1)) }}</div>
                                            <div>
                                                <p class="font-extrabold text-xs text-slate-900 dark:text-white leading-tight">{{ $intern->student->user->name }}</p>
                                                <p class="text-[9px] text-slate-450 mt-0.5">{{ $intern->student->group->name }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-5 px-6 text-xs font-black text-slate-800 dark:text-slate-200">
                                        {{ $intern->company_name }}
                                    </td>
                                    <td class="py-5 px-6 text-xs font-semibold text-slate-700 dark:text-slate-350">
                                        👨‍🏫 {{ $intern->academicTutor->user->name ?? 'Non Affecté' }}
                                    </td>
                                    <td class="py-5 px-6 text-center font-black text-slate-700 dark:text-slate-300">
                                        <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 rounded text-[11px]">
                                            {{ $intern->reports->count() }}
                                        </span>
                                    </td>
                                    <td class="py-5 px-6 text-center font-black">
                                        @if($intern->status === 'completed')
                                            <span class="text-emerald-600 text-xs">
                                                {{ $intern->grade }} / 20
                                            </span>
                                        @else
                                            <span class="text-slate-400 text-xs">—</span>
                                        @endif
                                    </td>
                                    <td class="py-5 px-6 text-right">
                                        <span class="px-2.5 py-1 text-[8px] font-black border rounded uppercase tracking-wider
                                            {{ $intern->status === 'completed' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : ($intern->status === 'rejected' ? 'bg-rose-50 text-rose-600 border-rose-100' : 'bg-blue-50 text-blue-600 border-blue-100') }}">
                                            {{ $intern->status === 'completed' ? 'Terminé' : ($intern->status === 'rejected' ? 'Rejeté' : 'Actif') }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-slate-400 font-bold italic text-xs">
                                        Aucun stage validé dans le registre.
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
