<x-app-layout>
    <x-slot name="header">
        <x-page-header 
            title="{{ __('Réinscription Annuelle') }}" 
            subtitle="{{ __('Campagne de Réinscription ' . date('Y') . '/' . (date('Y') + 1)) }}"
            icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>'
        >
        </x-page-header>
    </x-slot>

    <div class="py-10 bg-[#F8FAFC] dark:bg-[#020617] min-h-screen transition-colors duration-300">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <x-alert-messages />

            <!-- Welcome & Eligibility explanation -->
            <div class="bg-gradient-to-br from-upf-blue via-indigo-900 to-black rounded-[2.5rem] p-10 text-white shadow-xl relative overflow-hidden border border-white/5">
                <div class="relative z-10 space-y-3">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 text-[10px] font-black uppercase tracking-widest w-fit">
                        ✨ {{ __('Éligibilité Validée') }}
                    </span>
                    <h2 class="text-3xl font-black tracking-tighter italic">{{ __('Réinscription de :name', ['name' => Auth::user()->name]) }}</h2>
                    <p class="text-blue-150 text-sm leading-relaxed max-w-2xl">
                        {{ __('Selon les délibérations de votre année académique, vous êtes autorisé à vous réinscrire pour la session suivante.') }}
                    </p>
                </div>
                <div class="absolute -top-20 -right-20 w-64 h-64 bg-upf-magenta/15 rounded-full blur-3xl pointer-events-none"></div>
            </div>

            <!-- Main grid: Left Info, Right Actions -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <!-- Cursus review & credit carry-over details -->
                <x-card class="p-8 md:col-span-2 space-y-6">
                    <h3 class="text-lg font-black text-slate-850 dark:text-white italic tracking-tighter mb-4">{{ __('Bilan de l\'Année Écoulée') }} 📊</h3>
                    
                    <div class="space-y-4">
                        <!-- GPA summary -->
                        <div class="p-5 bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-850 rounded-2xl flex items-center justify-between">
                            <div>
                                <p class="text-[9px] uppercase font-black text-slate-400 tracking-wider">{{ __('Votre Moyenne Générale') }}</p>
                                <p class="text-2xl font-black mt-1 text-slate-850 dark:text-white">{{ number_format($gpa, 2) }} / 20</p>
                            </div>
                            <span class="px-3 py-1.5 rounded-xl text-xs font-black uppercase tracking-widest
                                {{ $gpa >= 10 ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20' : 'bg-amber-500/10 text-amber-500 border border-amber-500/20' }}
                            ">
                                {{ $gpa >= 10 ? __('Année Validée') : __('Session de Redoublement') }}
                            </span>
                        </div>

                        <!-- Promotion status description -->
                        <div class="p-5 bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-850 rounded-2xl">
                            <p class="text-[9px] uppercase font-black text-slate-400 tracking-wider mb-2">🎓 {{ __('Décision de Promotion') }}</p>
                            @if($gpa >= 10)
                                <p class="text-xs text-slate-600 dark:text-slate-450 leading-relaxed">
                                    {{ __('Félicitations ! Vous êtes promu au niveau académique supérieur.') }} 
                                    @if($failedModules->isNotEmpty())
                                        {{ __('Cependant, vous n\'avez pas validé l\'ensemble de vos modules.') }} 
                                        <strong>{{ $failedModules->count() }} {{ __('module(s) non validé(s)') }}</strong> {{ __('seront automatiquement reportés sous forme de dettes académiques (Crédits Modules) à valider l\'année prochaine.') }}
                                    @else
                                        {{ __('Vous passez en classe supérieure avec un parcours propre sans aucun crédit de module !') }}
                                    @endif
                                </p>
                            @else
                                <p class="text-xs text-slate-600 dark:text-slate-450 leading-relaxed">
                                    {{ __('Votre moyenne générale étant inférieure à 10/20, vous êtes autorisé à vous réinscrire au même niveau académique comme étudiant redoublant. Vous conserverez vos modules validés et repasserez uniquement les modules non validés.') }}
                                </p>
                            @endif
                        </div>

                        <!-- Debt modules (carrying over list) -->
                        @if($failedModules->isNotEmpty())
                            <div class="space-y-3 pt-2">
                                <p class="text-[9px] uppercase font-black text-slate-400 dark:text-slate-500 tracking-widest">{{ __('Modules Reportés en Crédit (Dettes) :') }}</p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach($failedModules as $module)
                                        <div class="flex items-center justify-between p-3.5 bg-amber-500/[0.02] border border-amber-500/20 rounded-xl">
                                            <div class="min-w-0">
                                                <p class="text-xs font-black text-slate-800 dark:text-slate-200 truncate">{{ $module->name }}</p>
                                                <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest block mt-0.5">{{ $module->code }}</span>
                                            </div>
                                            <span class="px-2 py-0.5 text-[8px] font-black bg-amber-500/10 text-amber-600 dark:text-amber-400 rounded uppercase tracking-wider">{{ __('Dette') }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </x-card>

                <!-- Action Confirmation -->
                <x-card class="p-8 md:col-span-1 border-2 border-indigo-500/30 bg-indigo-500/[0.01] flex flex-col justify-between">
                    <div class="space-y-6">
                        <h4 class="text-xs uppercase font-black text-indigo-600 dark:text-indigo-400 tracking-widest">📝 {{ __('Confirmation') }}</h4>
                        
                        <p class="text-xs text-slate-500 dark:text-slate-450 leading-normal">
                            {{ __('En soumettant ce formulaire, vous confirmez l\'exactitude de vos coordonnées académiques et acceptez d\'être réinscrit pour l\'année universitaire en cours de lancement.') }}
                        </p>

                        @if($student->group)
                            <div class="text-xs space-y-1">
                                <p class="text-[9px] uppercase font-black text-slate-400 tracking-wider">{{ __('Votre affectation actuelle') }}</p>
                                <p><strong>{{ __('Groupe d\'origine') }}:</strong> {{ $student->group->name }}</p>
                                <p><strong>{{ __('Filière') }}:</strong> {{ $student->filiere->name ?? 'Non définie' }}</p>
                            </div>
                        @endif
                    </div>

                    <form action="{{ route('student.reinscription.store') }}" method="POST" class="mt-8 space-y-4 font-bold">
                        @csrf
                        
                        <label class="flex items-start gap-3 p-3.5 bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-850 rounded-xl cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-900 transition-all select-none">
                            <input type="checkbox" name="confirm_details" value="1" required class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500 mt-0.5">
                            <span class="text-[10px] text-slate-600 dark:text-slate-450 leading-snug">{{ __('Je confirme mes informations et ma demande de réinscription.') }}</span>
                        </label>

                        <button type="submit" class="w-full py-4 bg-gradient-to-r from-upf-magenta to-indigo-600 hover:from-indigo-600 hover:to-upf-magenta text-white text-xs font-black uppercase tracking-widest rounded-2xl shadow-lg transition-all hover:scale-[1.02] transform">
                            🚀 {{ __('Valider ma réinscription') }}
                        </button>
                        
                        <a href="{{ route('student.dashboard') }}" class="block w-full py-3 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 text-center rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                            {{ __('Retourner') }}
                        </a>
                    </form>
                </x-card>
            </div>

        </div>
    </div>
</x-app-layout>
