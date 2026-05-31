<x-app-layout>
    <x-slot name="header">
        <x-page-header 
            title="{{ __('Évaluation des Enseignements') }}" 
            subtitle="{{ __('Exprimez votre avis de manière 100% anonyme pour nous aider à améliorer la qualité de nos cours.') }}"
            icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>'
        />
    </x-slot>

    <div class="py-10 bg-[#F8FAFC] dark:bg-[#020617] transition-colors duration-300">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Info Alert Box --}}
            <div class="bg-gradient-to-r from-upf-blue to-upf-navy text-white rounded-3xl p-8 shadow-xl relative overflow-hidden">
                <div class="relative z-10 flex flex-col md:flex-row items-center gap-6">
                    <div class="w-16 h-16 bg-white/10 backdrop-blur rounded-2xl flex items-center justify-center shrink-0 shadow-inner">
                        <span class="text-3xl">🗳️</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-black mb-1">Anonymat Garanti à 100% 🛡️</h3>
                        <p class="text-blue-105/90 text-sm leading-relaxed">
                            Afin d'assurer une liberté d'expression totale, vos réponses sont stockées de façon totalement anonyme.
                            Le système enregistre uniquement une empreinte sécurisée à sens unique pour s'assurer que chaque étudiant n'évalue qu'une seule fois un cours.
                        </p>
                    </div>
                </div>
                <div class="absolute -bottom-16 -right-16 w-60 h-60 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            </div>

            @if(session('success'))
                <div class="p-6 bg-emerald-50 text-emerald-700 rounded-3xl border border-emerald-100 flex items-center gap-4 shadow-sm animate-fade-in-down">
                    <span class="text-2xl">🎉</span>
                    <p class="font-extrabold text-sm">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="p-6 bg-rose-50 text-rose-700 rounded-3xl border border-rose-100 flex items-center gap-4 shadow-sm animate-fade-in-down">
                    <span class="text-2xl">⚠️</span>
                    <p class="font-extrabold text-sm">{{ session('error') }}</p>
                </div>
            @endif

            {{-- Evaluation Cards --}}
            <div class="space-y-6">
                @forelse($modulesToEvaluate as $item)
                    <div 
                        x-data="{ 
                            expanded: false,
                            q1: 0, hoverQ1: 0,
                            q2: 0, hoverQ2: 0,
                            q3: 0, hoverQ3: 0,
                            q4: 0, hoverQ4: 0
                        }"
                        class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-3xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden"
                    >
                        <!-- Card Header -->
                        <div class="p-6 sm:p-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-4 min-w-0">
                                <div class="w-12 h-12 bg-upf-magenta/10 text-upf-magenta rounded-2xl flex items-center justify-center shrink-0 shadow-inner">
                                    <span class="text-xl">📘</span>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-black text-slate-900 dark:text-white text-lg tracking-tight">{{ $item['module']->name }}</h4>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                        {{ __('Enseignant(e) :') }} <strong class="text-slate-700 dark:text-slate-350">{{ $item['professor']->user->name }}</strong>
                                    </p>
                                </div>
                            </div>

                            @if($item['has_evaluated'])
                                <span class="px-4 py-2 bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-450 text-xs font-black uppercase tracking-wider rounded-full border border-emerald-100/50">
                                    ✅ Évalué
                                </span>
                            @else
                                <button 
                                    @click="expanded = !expanded"
                                    class="px-6 py-2.5 bg-upf-blue hover:bg-upf-navy text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all duration-200 transform hover:-translate-y-0.5 shadow-sm"
                                >
                                    <span x-text="expanded ? 'Réduire' : 'Évaluer'"></span>
                                </button>
                            @endif
                        </div>

                        <!-- Card Expansion Form -->
                        <div 
                            x-show="expanded" 
                            x-collapse
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 max-h-0"
                            x-transition:enter-end="opacity-100 max-h-[1000px]"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 max-h-[1000px]"
                            x-transition:leave-end="opacity-0 max-h-0"
                            class="border-t border-gray-50 dark:border-slate-800 bg-slate-50/40 dark:bg-slate-950/10 p-6 sm:p-8 space-y-6"
                            style="display: none;"
                        >
                            <form method="POST" action="{{ route('student.evaluations.store') }}" class="space-y-6">
                                @csrf
                                <input type="hidden" name="module_id" value="{{ $item['module']->id }}">
                                <input type="hidden" name="professor_id" value="{{ $item['professor']->id }}">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Q1 -->
                                    <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-gray-100 dark:border-slate-800">
                                        <label class="block text-xs font-black uppercase tracking-wider text-slate-450 mb-3">
                                            1. Organisation & Structure du Cours
                                        </label>
                                        <div class="flex gap-2">
                                            <input type="hidden" name="q1_rating" x-model="q1">
                                            <template x-for="i in 5">
                                                <button type="button" @click="q1 = i" @mouseenter="hoverQ1 = i" @mouseleave="hoverQ1 = 0" class="text-3xl focus:outline-none transition-transform hover:scale-110">
                                                    <span :class="i <= (hoverQ1 || q1) ? 'text-amber-400' : 'text-slate-200 dark:text-slate-800'">★</span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Q2 -->
                                    <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-gray-100 dark:border-slate-800">
                                        <label class="block text-xs font-black uppercase tracking-wider text-slate-450 mb-3">
                                            2. Clarté & Pédagogie des explications
                                        </label>
                                        <div class="flex gap-2">
                                            <input type="hidden" name="q2_rating" x-model="q2">
                                            <template x-for="i in 5">
                                                <button type="button" @click="q2 = i" @mouseenter="hoverQ2 = i" @mouseleave="hoverQ2 = 0" class="text-3xl focus:outline-none transition-transform hover:scale-110">
                                                    <span :class="i <= (hoverQ2 || q2) ? 'text-amber-400' : 'text-slate-200 dark:text-slate-800'">★</span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Q3 -->
                                    <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-gray-100 dark:border-slate-800">
                                        <label class="block text-xs font-black uppercase tracking-wider text-slate-450 mb-3">
                                            3. Disponibilité & Support du Professeur
                                        </label>
                                        <div class="flex gap-2">
                                            <input type="hidden" name="q3_rating" x-model="q3">
                                            <template x-for="i in 5">
                                                <button type="button" @click="q3 = i" @mouseenter="hoverQ3 = i" @mouseleave="hoverQ3 = 0" class="text-3xl focus:outline-none transition-transform hover:scale-110">
                                                    <span :class="i <= (hoverQ3 || q3) ? 'text-amber-400' : 'text-slate-200 dark:text-slate-800'">★</span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Q4 -->
                                    <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-gray-100 dark:border-slate-800">
                                        <label class="block text-xs font-black uppercase tracking-wider text-slate-450 mb-3">
                                            4. Intérêt & Utilité du contenu enseigné
                                        </label>
                                        <div class="flex gap-2">
                                            <input type="hidden" name="q4_rating" x-model="q4">
                                            <template x-for="i in 5">
                                                <button type="button" @click="q4 = i" @mouseenter="hoverQ4 = i" @mouseleave="hoverQ4 = 0" class="text-3xl focus:outline-none transition-transform hover:scale-110">
                                                    <span :class="i <= (hoverQ4 || q4) ? 'text-amber-400' : 'text-slate-200 dark:text-slate-800'">★</span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <!-- Comment -->
                                <div class="space-y-2">
                                    <label class="block text-xs font-black uppercase tracking-wider text-slate-450">
                                        Commentaires Additionnels (Optionnel)
                                    </label>
                                    <textarea 
                                        name="comment" 
                                        rows="3" 
                                        maxlength="1000"
                                        placeholder="Que pensez-vous des points forts du cours et des pistes d'amélioration ?"
                                        class="block w-full rounded-2xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 focus:border-upf-blue focus:ring-upf-blue text-sm p-4 font-semibold text-slate-700 dark:text-slate-200"
                                    ></textarea>
                                </div>

                                <!-- Submit evaluation -->
                                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                                    <button 
                                        type="button" 
                                        @click="expanded = false"
                                        class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-slate-700 text-xs font-black uppercase tracking-widest rounded-xl transition-all"
                                    >
                                        Annuler
                                    </button>
                                    <button 
                                        type="submit" 
                                        :disabled="q1 === 0 || q2 === 0 || q3 === 0 || q4 === 0"
                                        :class="(q1 && q2 && q3 && q4) ? 'bg-emerald-600 hover:bg-emerald-700 cursor-pointer shadow-md' : 'bg-gray-300 cursor-not-allowed text-gray-500'"
                                        class="px-6 py-2.5 text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all duration-200"
                                    >
                                        Soumettre l'évaluation
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-12 text-center border border-gray-100 dark:border-slate-800">
                        <span class="text-5xl block mb-4">🎉</span>
                        <h4 class="text-lg font-black text-slate-900 dark:text-white mb-2">{{ __('Aucune évaluation en attente') }}</h4>
                        <p class="text-sm text-slate-400 dark:text-slate-500 font-bold italic">
                            {{ __('Félicitations ! Vous avez complété toutes vos évaluations ou aucune n\'est requise pour le moment.') }}
                        </p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
