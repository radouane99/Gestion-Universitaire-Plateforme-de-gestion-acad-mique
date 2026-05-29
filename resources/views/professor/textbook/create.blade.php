<x-app-layout>
    <x-slot name="header">
        <x-page-header 
            title="{{ __('Cahier de Textes') }}" 
            subtitle="{{ __('Saisie de Nouvelle Séance') }}"
            icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>'
        >
            <x-slot name="actions">
                <a href="{{ route('professor.textbook.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-xl font-bold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 transition shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    {{ __('Retour à l\'Historique') }}
                </a>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-6 bg-[#F8FAFC]">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <x-alert-messages />

            <div class="bg-gradient-to-r from-upf-blue to-upf-navy rounded-3xl p-10 text-white shadow-sm relative overflow-hidden">
                <div class="relative z-10">
                    <h2 class="text-3xl font-black mb-2 italic">{{ __('Cahier de Textes numérique') }}</h2>
                    <p class="text-blue-100 opacity-80">{{ __('Renseignez les détails pédagogiques de la séance d\'aujourd\'hui pour le suivi administratif.') }}</p>
                </div>
                <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-upf-magenta/10 rounded-full blur-3xl"></div>
            </div>

            <x-card class="p-0">
                <div class="p-8 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-xl font-black text-gray-900 italic">{{ __('Saisie de Séance') }}</h3>
                    <p class="text-gray-500 text-sm">{{ __('Tous les champs sont requis.') }}</p>
                </div>

                <form action="{{ route('professor.textbook.store') }}" method="POST" class="p-10 space-y-8">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Date Saisie Automatique -->
                        <div class="space-y-3">
                            <x-input-label :value="__('Date de la Séance (Lecture Seule)')" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block" />
                            <input type="date" value="{{ date('Y-m-d') }}" disabled class="w-full border-gray-200 rounded-2xl p-4 font-bold text-gray-400 bg-gray-100 cursor-not-allowed border-dashed">
                        </div>

                        <!-- Nature Séance -->
                        <div class="space-y-3">
                            <x-input-label for="type" :value="__('Nature de la Séance')" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block" />
                            <select name="type" id="type" required class="w-full border-gray-200 rounded-2xl focus:ring-upf-blue focus:border-upf-blue p-4 font-bold text-gray-900 bg-gray-50 shadow-sm transition">
                                <option value="Cours">{{ __('Cours') }}</option>
                                <option value="TD">{{ __('TD') }}</option>
                                <option value="TP">{{ __('TP') }}</option>
                            </select>
                        </div>

                        <!-- Choix Groupe/Module enseignés -->
                        <div class="space-y-3 md:col-span-2">
                            <x-input-label for="group_module" :value="__('Cours / Groupe Assigné')" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block" />
                            <select name="group_module" id="group_module" required onchange="updateGroupModule(this.value)" class="w-full border-gray-200 rounded-2xl focus:ring-upf-blue focus:border-upf-blue p-4 font-bold text-gray-900 bg-gray-50 shadow-sm transition">
                                <option value="">{{ __('Sélectionnez un cours...') }}</option>
                                @foreach($taught as $session)
                                    <option value="{{ $session->group_id }}-{{ $session->module_id }}">{{ $session->module->name }} ({{ __('Groupe') }}: {{ $session->group->name }})</option>
                                @endforeach
                            </select>
                            
                            <!-- Hidden inputs for validation submission -->
                            <input type="hidden" name="group_id" id="group_id">
                            <input type="hidden" name="module_id" id="module_id">
                        </div>

                        <!-- Heure Début -->
                        <div class="space-y-3">
                            <x-input-label for="start_time" :value="__('Heure de Début')" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block" />
                            <x-text-input type="time" name="start_time" id="start_time" required class="w-full text-gray-900 bg-gray-50 p-4 font-bold shadow-sm" />
                        </div>

                        <!-- Heure Fin -->
                        <div class="space-y-3">
                            <x-input-label for="end_time" :value="__('Heure de Fin')" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block" />
                            <x-text-input type="time" name="end_time" id="end_time" required class="w-full text-gray-900 bg-gray-50 p-4 font-bold shadow-sm" />
                        </div>

                        <!-- Objectif de la Séance -->
                        <div class="space-y-3 md:col-span-2">
                            <x-input-label for="objective" :value="__('Objectif & Contenu Pédagogique de la Séance')" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block" />
                            <textarea name="objective" id="objective" rows="5" required minlength="5" placeholder="{{ __('Décrivez succinctement les chapitres traités, les exercices résolus, ou les objectifs atteints...') }}" class="w-full border-gray-200 rounded-2xl focus:ring-upf-blue focus:border-upf-blue p-4 font-bold text-gray-900 bg-gray-50 shadow-sm transition"></textarea>
                        </div>
                    </div>

                    <div class="pt-6">
                        <x-primary-button class="w-full justify-center py-4 text-sm tracking-widest">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ __('Valider la Séance') }}
                        </x-primary-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>

    <script>
        function updateGroupModule(val) {
            if (val) {
                const parts = val.split('-');
                document.getElementById('group_id').value = parts[0];
                document.getElementById('module_id').value = parts[1];
            } else {
                document.getElementById('group_id').value = '';
                document.getElementById('module_id').value = '';
            }
        }
    </script>
</x-app-layout>
