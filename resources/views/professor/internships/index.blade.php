<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tight italic">
            {{ __('Mes Encadrements & Tutorats (Stages)') }}
        </h2>
    </x-slot>

    <div class="py-10 bg-[#F8FAFC] dark:bg-[#020617] transition-colors duration-300">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">

            {{-- Summary Stats Header --}}
            <div class="bg-gradient-to-br from-upf-navy to-black text-white rounded-[2.5rem] p-10 shadow-xl relative overflow-hidden">
                <div class="relative z-10">
                    <span class="text-[10px] font-black uppercase tracking-widest bg-white/20 px-3 py-1 rounded-full border border-white/10">ESPACE ENSEIGNANT</span>
                    <h3 class="text-3xl font-black mt-3 mb-2 italic tracking-tight">💼 Suivi des Stages Universitaires</h3>
                    <p class="text-blue-105 opacity-90 text-sm max-w-xl">Supervisez l'insertion professionnelle de vos étudiants, apportez du feedback sur leurs rapports et clôturez leurs évaluations.</p>
                </div>
                <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>
            </div>

            @if(session('success'))
                <div class="p-6 bg-emerald-50 text-emerald-700 rounded-3xl border border-emerald-100 flex items-center gap-4 shadow-sm">
                    <span class="text-2xl">🎉</span>
                    <p class="font-extrabold text-sm">{{ session('success') }}</p>
                </div>
            @endif

            {{-- Internships Table --}}
            <div class="bg-white dark:bg-slate-900 shadow-sm rounded-3xl border border-gray-100 dark:border-slate-800 overflow-hidden">
                <div class="p-8 border-b border-gray-50 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/20">
                    <h3 class="text-lg font-black text-slate-900 dark:text-white tracking-tight italic">📋 Liste des stagiaires encadrés</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Étudiants dont vous êtes désigné comme tuteur académique.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 dark:bg-slate-950/40 text-[10px] font-black uppercase tracking-widest text-slate-450 border-b border-gray-100 dark:border-slate-800">
                                <th class="py-4 px-6">Étudiant</th>
                                <th class="py-4 px-6">Groupe / Filière</th>
                                <th class="py-4 px-6">Entreprise</th>
                                <th class="py-4 px-6">Dates du Stage</th>
                                <th class="py-4 px-6 text-center">Rapports déposés</th>
                                <th class="py-4 px-6 text-center font-bold text-upf-blue dark:text-blue-400">Statut / Note</th>
                                <th class="py-4 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                            @forelse($internships as $intern)
                                <tr class="hover:bg-slate-50/30 dark:hover:bg-slate-950/10 transition-colors">
                                    <td class="py-5 px-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-950/30 text-upf-blue dark:text-blue-400 flex items-center justify-center font-black text-xs">{{ strtoupper(substr($intern->student->user->name, 0, 1)) }}</div>
                                            <div>
                                                <p class="font-extrabold text-sm text-slate-950 dark:text-white leading-tight">{{ $intern->student->user->name }}</p>
                                                <p class="text-[10px] text-slate-450 mt-0.5">N° {{ $intern->student->id }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-5 px-6">
                                        <div>
                                            <p class="text-xs font-black text-slate-700 dark:text-slate-350">{{ $intern->student->group->name }}</p>
                                            <p class="text-[10px] text-slate-400 mt-0.5">{{ $intern->student->group->filiere->code ?? 'N/A' }}</p>
                                        </div>
                                    </td>
                                    <td class="py-5 px-6">
                                        <div>
                                            <p class="text-xs font-black text-slate-800 dark:text-slate-200">{{ $intern->company_name }}</p>
                                            <p class="text-[10px] text-slate-400 mt-0.5">Tuteur Pro : {{ $intern->tutor_name }}</p>
                                        </div>
                                    </td>
                                    <td class="py-5 px-6">
                                        <div class="text-[11px] font-bold text-slate-700 dark:text-slate-350">
                                            <p>Début : {{ $intern->start_date->format('d/m/Y') }}</p>
                                            <p class="text-slate-400 mt-0.5">Fin : {{ $intern->end_date->format('d/m/Y') }}</p>
                                        </div>
                                    </td>
                                    <td class="py-5 px-6 text-center font-black text-slate-700 dark:text-slate-300">
                                        <span class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 rounded-full text-xs">
                                            {{ $intern->reports->count() }}
                                        </span>
                                    </td>
                                    <td class="py-5 px-6 text-center">
                                        @if($intern->status === 'completed')
                                            <span class="px-3 py-1 bg-emerald-50 text-emerald-600 border border-emerald-100 rounded-full text-xs font-black uppercase tracking-wider block">
                                                ★ {{ $intern->grade }} / 20
                                            </span>
                                        @else
                                            <span class="px-3 py-1 bg-blue-50 text-blue-600 border border-blue-100 rounded-full text-xs font-black uppercase tracking-wider block">
                                                En Cours
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-5 px-6 text-right">
                                        <a href="{{ route('professor.internships.show', $intern) }}" class="inline-flex items-center px-4 py-2 bg-upf-blue hover:bg-upf-navy text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-sm transform hover:-translate-y-0.5">
                                            Évaluer
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-16 text-center text-slate-400 font-bold italic">
                                        Aucun stage ne vous est actuellement affecté pour encadrement.
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
