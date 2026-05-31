<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('professor.internships.index') }}" class="text-xs font-black uppercase text-upf-blue hover:text-upf-navy flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Tutorats
            </a>
            <span class="text-gray-300">/</span>
            <h2 class="font-black text-xl text-gray-800 leading-tight tracking-tight">
                {{ __('Évaluation du Stage :') }} {{ $internship->student->user->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-10 bg-[#F8FAFC] dark:bg-[#020617] transition-colors duration-300">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            @if(session('success'))
                <div class="p-6 bg-emerald-50 text-emerald-700 rounded-3xl border border-emerald-100 flex items-center gap-4 shadow-sm">
                    <span class="text-2xl">🎉</span>
                    <p class="font-extrabold text-sm">{{ session('success') }}</p>
                </div>
            @endif

            {{-- 1. Student / Company Summary Card --}}
            <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-[2.5rem] p-8 shadow-sm">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-100 dark:border-slate-800 pb-5 mb-6">
                    <div>
                        <span class="text-[10px] font-black uppercase text-upf-magenta tracking-widest">Dossier de Stage</span>
                        <h3 class="text-xl font-black text-slate-900 dark:text-white mt-1">{{ $internship->company_name }}</h3>
                    </div>
                    <span class="px-4 py-2 text-xs font-black uppercase tracking-wider rounded-full border
                        {{ $internship->status === 'completed' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-blue-50 text-blue-600 border-blue-100' }}">
                        {{ $internship->status === 'completed' ? 'Évalué' : 'Actif' }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-slate-750 dark:text-slate-350">
                    <div class="space-y-2">
                        <p><strong>🎒 Stagiaire :</strong> {{ $internship->student->user->name }} ({{ $internship->student->group->name }})</p>
                        <p><strong>💼 Sujet :</strong> {{ $internship->subject }}</p>
                        <p><strong>📅 Période :</strong> du {{ $internship->start_date->format('d/m/Y') }} au {{ $internship->end_date->format('d/m/Y') }}</p>
                    </div>
                    <div class="space-y-2 border-t md:border-t-0 md:border-l border-slate-100 dark:border-slate-850 pt-4 md:pt-0 md:pl-6 rtl:md:pl-0 rtl:md:pr-6">
                        <p><strong>👤 Tuteur Professionnel :</strong> {{ $internship->tutor_name }}</p>
                        <p><strong>✉ Email Tuteur Pro :</strong> {{ $internship->tutor_email }}</p>
                        <p><strong>📞 Téléphone Tuteur Pro :</strong> {{ $internship->tutor_phone }}</p>
                    </div>
                </div>
            </div>

            {{-- 2. Main Grid: Reports list & Final Grade form --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- Reports Feed --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-[2.5rem] p-8 shadow-sm">
                        <h4 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest border-b border-slate-100 dark:border-slate-800 pb-4 mb-6">📄 Rapports Mensuels de Stage</h4>

                        <div class="space-y-6">
                            @forelse($internship->reports as $report)
                                <div class="p-6 bg-slate-50/50 dark:bg-slate-950/30 border border-gray-100 dark:border-slate-850 rounded-2xl space-y-4">
                                    <div class="flex items-center justify-between gap-4">
                                        <h5 class="text-xs font-black text-upf-blue dark:text-blue-400">Rapport Mensuel N°{{ $report->report_number }} : {{ $report->title }}</h5>
                                        <span class="px-2.5 py-1 text-[8px] font-black border rounded uppercase tracking-wider
                                            {{ $report->status === 'reviewed' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-amber-50 text-amber-600 border-amber-100' }}">
                                            {{ $report->status === 'reviewed' ? 'Évalué' : 'En attente' }}
                                        </span>
                                    </div>
                                    <p class="text-slate-700 dark:text-slate-350 text-xs font-semibold leading-relaxed">
                                        {!! nl2br(e($report->content)) !!}
                                    </p>
                                    
                                    @if($report->file_path)
                                    <a href="{{ route('professor.internships.report.download', $report) }}"
                                        class="inline-flex items-center gap-1.5 text-[9px] font-black text-slate-650 bg-white border border-slate-150 px-3.5 py-2 rounded-lg hover:bg-slate-50 transition-all uppercase tracking-wider shadow-sm">
                                        💾 Télécharger le fichier rendu
                                    </a>
                                    @endif

                                    {{-- Tutor feedback / review --}}
                                    @if($report->tutor_feedback)
                                        <div class="p-3 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-850 rounded-xl">
                                            <p class="text-[9px] font-black uppercase text-slate-400">Votre évaluation :</p>
                                            <p class="text-[11px] text-slate-600 italic mt-1 leading-normal">"{{ $report->tutor_feedback }}"</p>
                                        </div>
                                    @else
                                        <form action="{{ route('professor.internships.report.review', $report) }}" method="POST" class="pt-3 border-t border-slate-100 dark:border-slate-800 space-y-3">
                                            @csrf
                                            <div class="space-y-1">
                                                <label class="block text-[8px] font-black uppercase text-slate-400 tracking-wider">Laisser une remarque sur ce rapport</label>
                                                <textarea name="tutor_feedback" rows="2" required placeholder="Commentaires ou retours pédagogiques..."
                                                    class="w-full border-gray-100 dark:border-slate-800 rounded-xl bg-gray-50 dark:bg-slate-950 p-3 text-xs font-semibold text-slate-900 dark:text-white resize-none"></textarea>
                                            </div>
                                            <div class="flex justify-end">
                                                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-[9px] font-black uppercase tracking-widest rounded-lg shadow-sm">
                                                    Enregistrer mon avis
                                                </button>
                                            </div>
                                        </form>
                                    @endif
                                </div>
                            @empty
                                <p class="text-center py-10 text-slate-400 font-bold italic text-xs">Aucun rapport soumis par l'étudiant.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Final Grading Side Form --}}
                <div class="space-y-6">
                    <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-[2.5rem] p-7 shadow-sm">
                        <h4 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest border-b border-slate-100 dark:border-slate-800 pb-3 mb-5">💯 Évaluation Finale</h4>

                        @if($internship->status === 'completed')
                            <div class="space-y-4">
                                <div class="p-4 bg-emerald-50 text-emerald-700 rounded-2xl border border-emerald-100 text-center">
                                    <p class="text-[10px] font-black uppercase tracking-wider">Note Finale</p>
                                    <p class="text-3xl font-black mt-1">{{ $internship->grade }} / 20</p>
                                </div>
                                <div class="space-y-1 bg-slate-50 dark:bg-slate-950/20 p-4 border border-slate-100 dark:border-slate-850 rounded-2xl">
                                    <p class="text-[9px] font-black uppercase text-slate-400">Appréciation Générale</p>
                                    <p class="text-xs italic text-slate-650 mt-1 leading-relaxed">"{{ $internship->tutor_feedback }}"</p>
                                </div>
                            </div>
                        @else
                            <form action="{{ route('professor.internships.grade', $internship) }}" method="POST" class="space-y-4">
                                @csrf
                                <div class="space-y-1">
                                    <label class="block text-[8px] font-black uppercase text-slate-400 tracking-wider">Note sur 20</label>
                                    <input type="number" step="0.25" min="0" max="20" name="grade" required placeholder="Ex: 16.5"
                                        class="w-full border-gray-105 dark:border-slate-850 rounded-xl bg-gray-50 dark:bg-slate-950 p-3 text-xs font-bold text-slate-900 dark:text-white">
                                </div>

                                <div class="space-y-1">
                                    <label class="block text-[8px] font-black uppercase text-slate-400 tracking-wider">Appréciation Générale (Bilan)</label>
                                    <textarea name="tutor_feedback" rows="4" required placeholder="Bilan qualitatif de fin de stage..."
                                        class="w-full border-gray-105 dark:border-slate-850 rounded-xl bg-gray-50 dark:bg-slate-950 p-3 text-xs font-semibold text-slate-900 dark:text-white resize-none"></textarea>
                                </div>

                                <button type="submit" class="w-full py-3.5 bg-upf-magenta hover:bg-pink-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-md">
                                    Enregistrer la note & Clôturer
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
