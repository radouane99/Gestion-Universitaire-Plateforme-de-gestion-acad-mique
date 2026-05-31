<x-app-layout>
    <x-slot name="header">
        <x-page-header 
            title="{{ __('Mon Espace Stage') }}" 
            subtitle="{{ __('Suivi complet de votre expérience professionnelle, conventions et rapports mensuels.') }}"
            icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>'
        />
    </x-slot>

    <div class="py-10 bg-[#F8FAFC] dark:bg-[#020617] transition-colors duration-300">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">

            @if(session('success'))
                <div class="p-6 bg-emerald-50 text-emerald-700 rounded-3xl border border-emerald-100 flex items-center gap-4 shadow-sm">
                    <span class="text-2xl">🎉</span>
                    <p class="font-extrabold text-sm">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="p-6 bg-rose-50 text-rose-700 rounded-3xl border border-rose-100 flex items-center gap-4 shadow-sm">
                    <span class="text-2xl">⚠️</span>
                    <p class="font-extrabold text-sm">{{ session('error') }}</p>
                </div>
            @endif

            {{-- ==================== NO INTERNSHIP ==================== --}}
            @if(is_null($internship))
                <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-[2.5rem] p-8 sm:p-12 shadow-sm">
                    <div class="flex items-center gap-3 border-b border-slate-100 dark:border-slate-800 pb-5 mb-8">
                        <span class="text-3xl">📝</span>
                        <div>
                            <h3 class="text-xl font-black text-slate-900 dark:text-white italic">Déposer ma Fiche de Stage</h3>
                            <p class="text-xs text-slate-400 font-bold">Remplissez les informations de l'entreprise d'accueil pour validation</p>
                        </div>
                    </div>

                    <form action="{{ route('student.internships.store') }}" method="POST" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-1">
                                <label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider">Nom de l'Entreprise</label>
                                <input type="text" name="company_name" required placeholder="Ex: UPF Technologies"
                                    class="w-full border-gray-100 dark:border-slate-800 rounded-2xl bg-gray-50 dark:bg-slate-950 p-4 font-semibold text-slate-900 dark:text-white text-sm focus:ring-upf-blue">
                            </div>
                            <div class="space-y-1">
                                <label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider">Adresse de l'Entreprise</label>
                                <input type="text" name="company_address" required placeholder="Ex: Route d'Aïn Chkef, Fès"
                                    class="w-full border-gray-100 dark:border-slate-800 rounded-2xl bg-gray-50 dark:bg-slate-950 p-4 font-semibold text-slate-900 dark:text-white text-sm focus:ring-upf-blue">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-1">
                                <label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider">Nom du Tuteur Professionnel</label>
                                <input type="text" name="tutor_name" required placeholder="Ex: M. Kamal El Ouali"
                                    class="w-full border-gray-100 dark:border-slate-800 rounded-2xl bg-gray-50 dark:bg-slate-950 p-4 font-semibold text-slate-900 dark:text-white text-sm focus:ring-upf-blue">
                            </div>
                            <div class="space-y-1">
                                <label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider">Email du Tuteur</label>
                                <input type="email" name="tutor_email" required placeholder="tuteur@entreprise.com"
                                    class="w-full border-gray-100 dark:border-slate-800 rounded-2xl bg-gray-50 dark:bg-slate-950 p-4 font-semibold text-slate-900 dark:text-white text-sm focus:ring-upf-blue">
                            </div>
                            <div class="space-y-1">
                                <label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider">Téléphone du Tuteur</label>
                                <input type="text" name="tutor_phone" required placeholder="+212 600 000 000"
                                    class="w-full border-gray-100 dark:border-slate-800 rounded-2xl bg-gray-50 dark:bg-slate-950 p-4 font-semibold text-slate-900 dark:text-white text-sm focus:ring-upf-blue">
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider">Sujet du Stage</label>
                            <textarea name="subject" rows="3" required placeholder="Décrivez brièvement les missions qui vous seront confiées..."
                                class="w-full border-gray-100 dark:border-slate-800 rounded-2xl bg-gray-50 dark:bg-slate-950 p-4 font-semibold text-slate-900 dark:text-white text-sm focus:ring-upf-blue resize-none"></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-1">
                                <label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider">Date de Début</label>
                                <input type="date" name="start_date" required
                                    class="w-full border-gray-100 dark:border-slate-800 rounded-2xl bg-gray-50 dark:bg-slate-950 p-4 font-semibold text-slate-900 dark:text-white text-sm focus:ring-upf-blue">
                            </div>
                            <div class="space-y-1">
                                <label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider">Date de Fin</label>
                                <input type="date" name="end_date" required
                                    class="w-full border-gray-100 dark:border-slate-800 rounded-2xl bg-gray-50 dark:bg-slate-950 p-4 font-semibold text-slate-900 dark:text-white text-sm focus:ring-upf-blue">
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 border-t border-slate-100 dark:border-slate-800">
                            <button type="submit" class="px-10 py-4 bg-upf-blue hover:bg-upf-navy text-white rounded-2xl font-black shadow-lg transition-all text-xs uppercase tracking-widest transform hover:-translate-y-0.5">
                                Soumettre ma fiche de stage
                            </button>
                        </div>
                    </form>
                </div>

            {{-- ==================== INTERNSHIP PENDING ==================== --}}
            @elseif($internship->status === 'pending')
                <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-[2.5rem] p-12 text-center shadow-sm">
                    <span class="text-6xl block mb-4">⌛</span>
                    <h3 class="text-xl font-black text-slate-900 dark:text-white mb-2">Fiche de Stage en Cours de Validation</h3>
                    <p class="text-sm text-slate-400 font-bold max-w-md mx-auto leading-relaxed">
                        Votre fiche de stage pour <strong class="text-upf-blue">{{ $internship->company_name }}</strong> a été transmise à l'administration universitaire.
                        Dès validation, un professeur vous sera assigné comme tuteur académique.
                    </p>
                </div>

            {{-- ==================== INTERNSHIP REJECTED ==================== --}}
            @elseif($internship->status === 'rejected')
                <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-[2.5rem] p-12 text-center shadow-sm border-2 border-rose-200">
                    <span class="text-6xl block mb-4">❌</span>
                    <h3 class="text-xl font-black text-rose-600 mb-2">Demande de Stage Refusée</h3>
                    <p class="text-sm text-slate-400 font-bold max-w-md mx-auto mb-6 leading-relaxed">
                        Malheureusement, votre demande de stage pour {{ $internship->company_name }} a été refusée par la direction académique.
                    </p>
                    <a href="{{ route('student.dashboard') }}" class="px-6 py-3 bg-gray-100 text-slate-700 font-black text-xs uppercase tracking-widest rounded-xl hover:bg-gray-200 transition-all">
                        Retourner au Tableau de Bord
                    </a>
                </div>

            {{-- ==================== INTERNSHIP ACTIVE OR COMPLETED ==================== --}}
            @else
                {{-- Internship Summary Card --}}
                <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-[2.5rem] p-8 shadow-sm">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-100 dark:border-slate-800 pb-5 mb-6">
                        <div>
                            <span class="text-[10px] font-black uppercase text-upf-magenta tracking-widest">Mon Stage Actuel</span>
                            <h3 class="text-xl font-black text-slate-900 dark:text-white mt-1">{{ $internship->company_name }}</h3>
                        </div>
                        <span class="px-4 py-2 text-xs font-black uppercase tracking-wider rounded-full border
                            {{ $internship->status === 'completed' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-blue-50 text-blue-600 border-blue-100' }}">
                            {{ $internship->status === 'completed' ? 'Terminé & Évalué' : 'Actif' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-slate-750 dark:text-slate-350">
                        <div class="space-y-2">
                            <p><strong>📍 Adresse :</strong> {{ $internship->company_address }}</p>
                            <p><strong>💼 Sujet :</strong> {{ $internship->subject }}</p>
                            <p><strong>📅 Période :</strong> du {{ $internship->start_date->format('d/m/Y') }} au {{ $internship->end_date->format('d/m/Y') }}</p>
                        </div>
                        <div class="space-y-2 border-t md:border-t-0 md:border-l border-slate-100 dark:border-slate-850 pt-4 md:pt-0 md:pl-6 rtl:md:pl-0 rtl:md:pr-6">
                            <p><strong>👤 Tuteur Pro :</strong> {{ $internship->tutor_name }} ({{ $internship->tutor_phone }})</p>
                            <p><strong>🎓 Tuteur Académique :</strong> {{ $internship->academicTutor->user->name ?? 'Non Assigné' }}</p>
                            <p><strong>✉ Contact Tuteur :</strong> {{ $internship->academicTutor->user->email ?? '—' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Grading Results Banner if Completed --}}
                @if($internship->status === 'completed')
                    <div class="bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-[2.5rem] p-8 shadow-lg">
                        <div class="flex items-center gap-5 mb-4">
                            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center text-3xl shadow-inner shrink-0">👑</div>
                            <div>
                                <h4 class="text-lg font-black">Évaluation Finale Terminée !</h4>
                                <p class="text-emerald-100 text-xs font-bold mt-0.5">Votre note finale et les commentaires de votre tuteur académique.</p>
                            </div>
                        </div>
                        <div class="p-5 bg-white/10 backdrop-blur rounded-2xl border border-white/10 space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-black uppercase tracking-wider">Note de Stage</span>
                                <span class="text-2xl font-black">{{ $internship->grade }} / 20</span>
                            </div>
                            <p class="text-xs italic leading-relaxed border-t border-white/10 pt-3">
                                "{{ $internship->tutor_feedback }}"
                            </p>
                        </div>
                    </div>
                @endif

                {{-- Monthly Reports Section --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    
                    {{-- Reports Feed --}}
                    <div class="md:col-span-2 space-y-6">
                        <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-[2.5rem] p-8 shadow-sm">
                            <h4 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest border-b border-slate-100 dark:border-slate-800 pb-4 mb-6">📄 Rapports Mensuels de Stage</h4>

                            <div class="space-y-6">
                                @forelse($internship->reports as $report)
                                    <div class="p-5 bg-slate-50/50 dark:bg-slate-950/30 border border-gray-100 dark:border-slate-850 rounded-2xl">
                                        <div class="flex items-center justify-between gap-4 mb-3">
                                            <h5 class="text-xs font-black text-upf-blue dark:text-blue-400">Rapport Mensuel N°{{ $report->report_number }} : {{ $report->title }}</h5>
                                            <span class="px-2.5 py-1 text-[8px] font-black border rounded uppercase tracking-wider
                                                {{ $report->status === 'reviewed' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-amber-50 text-amber-600 border-amber-100' }}">
                                                {{ $report->status === 'reviewed' ? 'Évalué' : 'En attente' }}
                                            </span>
                                        </div>
                                        <p class="text-slate-700 dark:text-slate-350 text-xs font-semibold leading-relaxed mb-4">
                                            {!! nl2br(e($report->content)) !!}
                                        </p>
                                        
                                        @if($report->file_path)
                                        <a href="{{ route('student.internships.report.download', $report) }}"
                                            class="inline-flex items-center gap-1.5 text-[9px] font-black text-slate-650 bg-white border border-slate-150 px-3.5 py-2 rounded-lg hover:bg-slate-50 transition-all uppercase tracking-wider shadow-sm">
                                            💾 Télécharger le fichier rendu
                                        </a>
                                        @endif

                                        @if($report->tutor_feedback)
                                            <div class="mt-4 p-3 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-850 rounded-xl">
                                                <p class="text-[9px] font-black uppercase text-slate-400">Feedback de mon Tuteur :</p>
                                                <p class="text-[11px] text-slate-600 italic mt-1 leading-normal">"{{ $report->tutor_feedback }}"</p>
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <p class="text-center py-10 text-slate-400 font-bold italic text-xs">Aucun rapport soumis pour le moment.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- Submit Monthly Report (Active Only) --}}
                    @if($internship->status === 'active')
                    <div class="space-y-6">
                        <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-[2.5rem] p-7 shadow-sm">
                            <h4 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest border-b border-slate-100 dark:border-slate-800 pb-3 mb-5">📤 Déposer un Rapport</h4>
                            
                            <form action="{{ route('student.internships.report.store', $internship) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                <div class="space-y-1">
                                    <label class="block text-[8px] font-black uppercase text-slate-400 tracking-wider">Titre du Rapport</label>
                                    <input type="text" name="title" required placeholder="Ex: Rapport d'activité du 1er mois"
                                        class="w-full border-gray-100 dark:border-slate-800 rounded-xl bg-gray-50 dark:bg-slate-950 p-3 text-xs font-semibold text-slate-900 dark:text-white">
                                </div>

                                <div class="space-y-1">
                                    <label class="block text-[8px] font-black uppercase text-slate-400 tracking-wider">Résumé de l'Activité</label>
                                    <textarea name="content" rows="3" required placeholder="Décrivez brièvement vos accomplissements..."
                                        class="w-full border-gray-100 dark:border-slate-800 rounded-xl bg-gray-50 dark:bg-slate-950 p-3 text-xs font-semibold text-slate-900 dark:text-white resize-none"></textarea>
                                </div>

                                <div class="space-y-1">
                                    <label class="block text-[8px] font-black uppercase text-slate-400 tracking-wider">Fichier du Rapport (PDF / DOCX)</label>
                                    <input type="file" name="report_file" required
                                        class="w-full border border-dashed border-gray-200 dark:border-slate-800 rounded-xl bg-gray-50 dark:bg-slate-950 p-3 text-[10px] font-bold text-slate-500">
                                </div>

                                <button type="submit" class="w-full py-3 bg-upf-blue hover:bg-upf-navy text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-md">
                                    Soumettre le Rapport
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif

                </div>
            @endif

        </div>
    </div>
</x-app-layout>
