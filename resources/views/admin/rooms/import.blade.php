<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tighter italic">
            {{ __("Importation Massive de Salles (Excel / CSV)") }}
        </h2>
    </x-slot>

    <div class="py-12 bg-[#F8FAFC]">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <div class="bg-gradient-to-r from-upf-blue to-upf-navy rounded-3xl p-10 text-white shadow-2xl relative overflow-hidden">
                <div class="relative z-10">
                    <h2 class="text-3xl font-black mb-2 italic">Importateur de Locaux & Salles de Cours</h2>
                    <p class="text-blue-100 opacity-80">Gérez l'infrastructure physique de l'UPF en ajoutant des dizaines d'amphis, salles de TD ou laboratoires TP instantanément.</p>
                </div>
                <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-upf-magenta/10 rounded-full blur-3xl"></div>
            </div>

            <!-- Fichier Modèle -->
            <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm flex flex-col md:flex-row items-center justify-between hover:shadow-md transition-shadow gap-6">
                <div class="flex-1 space-y-2">
                    <span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">Modèle CSV Salles</span>
                    <h3 class="text-lg font-black text-gray-900 mt-2">Fichier Modèle d'Infrastructure</h3>
                    <p class="text-xs text-gray-500 font-medium leading-relaxed">Téléchargez et remplissez le gabarit pré-formaté. Il contient les colonnes requises : <code class="bg-gray-100 px-1 py-0.5 rounded text-rose-600 font-bold">name</code> (nom de la salle), <code class="bg-gray-100 px-1 py-0.5 rounded text-rose-600 font-bold">capacity</code> (nombre de places), et <code class="bg-gray-100 px-1 py-0.5 rounded text-rose-600 font-bold">type</code> (catégorie de salle).</p>
                </div>
                <a href="{{ route('admin.rooms.import.template') }}" class="w-full md:w-auto inline-flex items-center justify-center py-4 px-6 border border-gray-200 hover:bg-gray-50 text-gray-800 rounded-2xl font-black text-xs uppercase tracking-widest transition-all whitespace-nowrap shadow-sm">
                    Télécharger le Modèle (.CSV)
                </a>
            </div>

            <!-- Formulaire d'Import -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-black text-gray-900 italic">Téléverser le Fichier</h3>
                        <p class="text-gray-500 text-sm">Déposez votre fichier de salles rempli pour l'intégrer en base de données.</p>
                    </div>
                    <a href="{{ route('admin.rooms.index') }}" class="px-5 py-3 border border-gray-200 text-gray-700 hover:bg-gray-50 rounded-2xl font-black text-xs uppercase tracking-widest transition-all shadow-sm">
                        Retour à la Liste
                    </a>
                </div>

                <form action="{{ route('admin.rooms.import') }}" method="POST" enctype="multipart/form-data" class="p-10 space-y-8">
                    @csrf
                    
                    <div class="space-y-3">
                        <label for="import_file" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Sélectionner le fichier CSV (Excel)</label>
                        <input type="file" name="import_file" id="import_file" required accept=".csv,.txt" class="w-full border-gray-200 rounded-2xl focus:ring-upf-blue focus:border-upf-blue p-4 font-bold text-gray-900 bg-gray-50">
                    </div>

                    <div class="p-4 bg-blue-50/50 rounded-2xl border border-blue-100/50 flex gap-3 text-xs text-upf-blue font-semibold">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div>
                            <p class="font-bold uppercase tracking-wider text-[10px] mb-1">💡 Instructions de formatage</p>
                            <p class="leading-normal">Les types de salles autorisés sont : <strong class="font-black text-blue-900">course</strong> (Amphithéâtre / Cours magistral), <strong class="font-black text-blue-900">TD</strong> (Travaux Dirigés), et <strong class="font-black text-blue-900">TP</strong> (Laboratoire informatique ou technique).</p>
                        </div>
                    </div>

                    <div class="pt-6">
                        <button type="submit" class="w-full py-5 bg-upf-blue text-white rounded-2xl font-black shadow-xl hover:bg-upf-navy hover:scale-[1.02] transform transition-all duration-300 flex items-center justify-center space-x-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            <span>Lancer l'Importation de Salles</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
