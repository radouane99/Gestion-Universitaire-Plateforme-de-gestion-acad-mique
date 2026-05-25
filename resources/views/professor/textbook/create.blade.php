<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Cahier de Textes - Nouvelle Séance') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-[#F8FAFC]">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <div class="bg-gradient-to-r from-upf-blue to-upf-navy rounded-3xl p-10 text-white shadow-2xl relative overflow-hidden">
                <div class="relative z-10">
                    <h2 class="text-3xl font-black mb-2 italic">Cahier de Textes numérique</h2>
                    <p class="text-blue-100 opacity-80">Renseignez les détails pédagogiques de la séance d'aujourd'hui pour le suivi administratif.</p>
                </div>
                <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-upf-magenta/10 rounded-full blur-3xl"></div>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-black text-gray-900 italic">Saisie de Séance</h3>
                        <p class="text-gray-500 text-sm">Tous les champs sont requis.</p>
                    </div>
                    <a href="{{ route('professor.textbook.index') }}" class="px-5 py-3 border border-gray-200 text-gray-700 hover:bg-gray-50 rounded-2xl font-black text-xs uppercase tracking-widest transition-all">
                        Retour à l'Historique
                    </a>
                </div>

                <form action="{{ route('professor.textbook.store') }}" method="POST" class="p-10 space-y-8">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Date Saisie Automatique -->
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Date de la Séance (Lecture Seule)</label>
                            <input type="date" value="{{ date('Y-m-d') }}" disabled class="w-full border-gray-200 rounded-2xl focus:ring-upf-blue focus:border-upf-blue p-4 font-bold text-gray-400 bg-gray-100 cursor-not-allowed">
                        </div>

                        <!-- Nature Séance -->
                        <div class="space-y-3">
                            <label for="type" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Nature de la Séance</label>
                            <select name="type" id="type" required class="w-full border-gray-200 rounded-2xl focus:ring-upf-blue focus:border-upf-blue p-4 font-bold text-gray-900 bg-gray-50">
                                <option value="Cours">Cours</option>
                                <option value="TD">TD</option>
                                <option value="TP">TP</option>
                            </select>
                        </div>

                        <!-- Choix Groupe/Module enseignés -->
                        <div class="space-y-3 md:col-span-2">
                            <label for="group_module" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Cours / Groupe Assigné</label>
                            <select name="group_module" id="group_module" required onchange="updateGroupModule(this.value)" class="w-full border-gray-200 rounded-2xl focus:ring-upf-blue focus:border-upf-blue p-4 font-bold text-gray-900 bg-gray-50">
                                <option value="">Sélectionnez un cours...</option>
                                @foreach($taught as $session)
                                    <option value="{{ $session->group_id }}-{{ $session->module_id }}">{{ $session->module->name }} (Groupe: {{ $session->group->name }})</option>
                                @endforeach
                            </select>
                            
                            <!-- Hidden inputs for validation submission -->
                            <input type="hidden" name="group_id" id="group_id">
                            <input type="hidden" name="module_id" id="module_id">
                        </div>

                        <!-- Heure Début -->
                        <div class="space-y-3">
                            <label for="start_time" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Heure de Début</label>
                            <input type="time" name="start_time" id="start_time" required class="w-full border-gray-200 rounded-2xl focus:ring-upf-blue focus:border-upf-blue p-4 font-bold text-gray-900 bg-gray-50">
                        </div>

                        <!-- Heure Fin -->
                        <div class="space-y-3">
                            <label for="end_time" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Heure de Fin</label>
                            <input type="time" name="end_time" id="end_time" required class="w-full border-gray-200 rounded-2xl focus:ring-upf-blue focus:border-upf-blue p-4 font-bold text-gray-900 bg-gray-50">
                        </div>

                        <!-- Objectif de la Séance -->
                        <div class="space-y-3 md:col-span-2">
                            <label for="objective" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Objectif & Contenu Pédagogique de la Séance</label>
                            <textarea name="objective" id="objective" rows="5" required minlength="5" placeholder="Décrivez succinctement les chapitres traités, les exercices résolus, ou les objectifs atteints..." class="w-full border-gray-200 rounded-2xl focus:ring-upf-blue focus:border-upf-blue p-4 font-bold text-gray-900 bg-gray-50"></textarea>
                        </div>
                    </div>

                    <div class="pt-6">
                        <button type="submit" class="w-full py-5 bg-upf-blue text-white rounded-2xl font-black shadow-xl hover:bg-upf-navy hover:scale-[1.02] transform transition-all duration-300 flex items-center justify-center space-x-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Valider la Séance</span>
                        </button>
                    </div>
                </form>
            </div>
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
