<x-app-layout>
    <x-slot name="header">
        <x-page-header 
            title="{{ __('Suivi de Candidature') }}" 
            subtitle="{{ __('Secrétariat Académique') }}"
            icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>'
        >
        </x-page-header>
    </x-slot>

    <div class="py-10 bg-[#F8FAFC] dark:bg-[#020617] min-h-screen transition-colors duration-300">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Welcome Hero Banner -->
            <div class="bg-gradient-to-br from-upf-blue via-upf-navy to-black dark:from-slate-900 dark:via-slate-950 dark:to-black rounded-[3rem] p-10 lg:p-12 text-white shadow-2xl relative overflow-hidden border border-white/5">
                <div class="relative z-10 space-y-4">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500/20 text-amber-400 text-[10px] font-black uppercase tracking-widest w-fit">
                        ⏳ {{ __('Dossier en Cours d\'Examen') }}
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-black tracking-tighter leading-none italic">{{ __('Félicitations, :name !', ['name' => Auth::user()->name]) }} 🎉</h2>
                    <p class="text-blue-100 text-sm max-w-2xl leading-relaxed">
                        {{ __('Votre candidature pour rejoindre la filière :filiere a été enregistrée avec succès. Notre secrétariat académique étudie actuellement vos pièces justificatives.', ['filiere' => $student->filiere->name ?? __('Non définie')]) }}
                    </p>
                </div>
                <div class="absolute -top-20 -right-20 w-72 h-72 bg-upf-magenta/15 rounded-full blur-3xl pointer-events-none"></div>
            </div>

            <!-- Stepper Timeline Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <!-- Timeline status -->
                <x-card class="p-8 md:col-span-1 border-2 border-amber-500/20 bg-amber-500/[0.01] flex flex-col justify-between">
                    <div class="space-y-6">
                        <h4 class="text-xs uppercase font-black text-amber-600 dark:text-amber-400 tracking-widest">🔄 {{ __('État d\'Avancement') }}</h4>
                        
                        <!-- Step 1: Submission -->
                        <div class="flex gap-4">
                            <div class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center font-black flex-shrink-0 shadow-md">✓</div>
                            <div>
                                <p class="text-xs font-black text-slate-800 dark:text-white">{{ __('Dossier Soumis') }}</p>
                                <p class="text-[10px] text-slate-400 font-bold">{{ $student->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        <!-- Step 2: Verification -->
                        <div class="flex gap-4">
                            <div class="w-8 h-8 rounded-full bg-amber-500 text-white flex items-center justify-center font-black flex-shrink-0 animate-pulse shadow-md">⌛</div>
                            <div>
                                <p class="text-xs font-black text-slate-800 dark:text-white">{{ __('Revue Académique') }}</p>
                                <p class="text-[10px] text-amber-500 font-bold">{{ __('En cours de vérification') }}</p>
                            </div>
                        </div>

                        <!-- Step 3: Dispatching -->
                        <div class="flex gap-4 opacity-50">
                            <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-800 text-slate-450 flex items-center justify-center font-black flex-shrink-0">3</div>
                            <div>
                                <p class="text-xs font-black text-slate-800 dark:text-white">{{ __('Affectation Groupe') }}</p>
                                <p class="text-[10px] text-slate-400 font-bold">{{ __('En attente de validation') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-100 dark:border-slate-800/80 mt-6">
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 leading-normal">
                            {{ __('Dès que l\'administration validera votre dossier, vous recevrez automatiquement votre numéro d\'étudiant définitif et serez affecté à votre groupe d\'études.') }}
                        </p>
                    </div>
                </x-card>

                <!-- Dossier Details Summary -->
                <x-card class="p-8 md:col-span-2 space-y-6">
                    <h3 class="text-lg font-black text-slate-850 dark:text-white italic tracking-tighter">{{ __('Récapitulatif de votre Dossier') }} 📂</h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs text-slate-700 dark:text-slate-350">
                        <!-- Birth & Personal -->
                        <div class="space-y-2 p-4 bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-850 rounded-2xl">
                            <p class="text-[9px] uppercase font-black text-slate-400 tracking-wider mb-2">👤 {{ __('Données Personnelles') }}</p>
                            <p><strong>{{ __('CIN') }}:</strong> {{ $student->cin }}</p>
                            <p><strong>{{ __('Date de Naissance') }}:</strong> {{ $student->birth_date ? $student->birth_date->format('d/m/Y') : '—' }}</p>
                            <p><strong>{{ __('Lieu de Naissance') }}:</strong> {{ $student->birth_place }}</p>
                            <p><strong>{{ __('Numéro Provisoire') }}:</strong> <code class="bg-amber-500/10 text-amber-600 dark:text-amber-400 px-1 rounded">{{ $student->student_number }}</code></p>
                        </div>

                        <!-- Cursus Bac -->
                        <div class="space-y-2 p-4 bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-850 rounded-2xl">
                            <p class="text-[9px] uppercase font-black text-slate-400 tracking-wider mb-2">🎓 {{ __('Cursus Scolaire (Baccalauréat)') }}</p>
                            <p><strong>{{ __('Série du Bac') }}:</strong> {{ $student->bac_filiere }}</p>
                            <p><strong>{{ __('Note du Bac') }}:</strong> {{ $student->bac_grade }} / 20</p>
                            <p><strong>{{ __('Mention du Bac') }}:</strong> {{ $student->bac_mention }}</p>
                            <p><strong>{{ __('Année du Bac') }}:</strong> {{ $student->bac_year }}</p>
                        </div>

                        <!-- Parents details -->
                        <div class="space-y-2 p-4 bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-850 rounded-2xl sm:col-span-2">
                            <p class="text-[9px] uppercase font-black text-slate-400 tracking-wider mb-2">👪 {{ __('Coordonnées des Parents') }}</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <p class="text-[10px] text-pink-400"><strong>Père:</strong></p>
                                    <p><strong>{{ __('Nom') }}:</strong> {{ $student->father_name }}</p>
                                    <p><strong>{{ __('CIN') }}:</strong> {{ $student->father_cin }}</p>
                                    <p><strong>{{ __('Profession') }}:</strong> {{ $student->father_occupation }}</p>
                                </div>
                                <div class="space-y-1 border-t sm:border-t-0 sm:border-l border-slate-200 dark:border-slate-800 pt-3 sm:pt-0 sm:pl-4">
                                    <p class="text-[10px] text-pink-400"><strong>Mère:</strong></p>
                                    <p><strong>{{ __('Nom') }}:</strong> {{ $student->mother_name }}</p>
                                    <p><strong>{{ __('CIN') }}:</strong> {{ $student->mother_cin }}</p>
                                    <p><strong>{{ __('Profession') }}:</strong> {{ $student->mother_occupation }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-card>
            </div>

        </div>
    </div>
</x-app-layout>
