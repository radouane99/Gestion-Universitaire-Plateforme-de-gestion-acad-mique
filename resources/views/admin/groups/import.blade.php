<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tighter italic">
            {{ __("Importation Massive de Groupes (Excel / CSV)") }}
        </h2>
    </x-slot>

    <div class="py-12 bg-[#F8FAFC]">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <div class="bg-gradient-to-r from-upf-blue to-upf-navy rounded-3xl p-10 text-white shadow-2xl relative overflow-hidden">
                <div class="relative z-10">
                    <h2 class="text-3xl font-black mb-2 italic">Importateur de Cohortes & Groupes</h2>
                    <p class="text-blue-100 opacity-80">Gérez la structure académique de l'UPF en ajoutant des dizaines de classes d'élèves en un instant.</p>
                </div>
                <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-upf-magenta/10 rounded-full blur-3xl"></div>
            </div>

            <!-- Fichier Modèle -->
            <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm flex flex-col md:flex-row items-center justify-between hover:shadow-md transition-shadow gap-6">
                <div class="flex-1 space-y-2">
                    <span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">Modèle CSV Groupes</span>
                    <h3 class="text-lg font-black text-gray-900 mt-2">Fichier Modèle des Classes</h3>
                    <p class="text-xs text-gray-500 font-medium leading-relaxed">Téléchargez et remplissez le gabarit pré-formaté. Il contient les colonnes requises : <code class="bg-gray-100 px-1 py-0.5 rounded text-rose-600 font-bold">name</code> (nom complet ou trigramme du groupe, ex: GI-1), et <code class="bg-gray-100 px-1 py-0.5 rounded text-rose-600 font-bold">level</code> (niveau d'étude, ex: L1, L2, L3, M1, M2).</p>
                </div>
                <a href="{{ route('admin.groups.import.template') }}" class="w-full md:w-auto inline-flex items-center justify-center py-4 px-6 border border-gray-200 hover:bg-gray-50 text-gray-800 rounded-2xl font-black text-xs uppercase tracking-widest transition-all whitespace-nowrap shadow-sm">
                    Télécharger le Modèle (.CSV)
                </a>
            </div>

            <!-- Formulaire d'Import -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-black text-gray-900 italic">Téléverser le Fichier</h3>
                        <p class="text-gray-500 text-sm">Déposez votre fichier de groupes rempli pour l'intégrer en base de données.</p>
                    </div>
                    <a href="{{ route('admin.groups.index') }}" class="px-5 py-3 border border-gray-200 text-gray-700 hover:bg-gray-50 rounded-2xl font-black text-xs uppercase tracking-widest transition-all shadow-sm">
                        Retour à la Liste
                    </a>
                </div>

                <form action="{{ route('admin.groups.import') }}" method="POST" enctype="multipart/form-data" class="p-10 space-y-8">
                    @csrf
                    
                    <div class="space-y-3">
                        <label for="import_file" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Sélectionner le fichier CSV (Excel)</label>
                        <input type="file" name="import_file" id="import_file" required accept=".csv,.txt" class="w-full border-gray-200 rounded-2xl focus:ring-upf-blue focus:border-upf-blue p-4 font-bold text-gray-900 bg-gray-50">
                    </div>

                    <div class="p-4 bg-blue-50/50 rounded-2xl border border-blue-100/50 flex gap-3 text-xs text-upf-blue font-semibold">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div>
                            <p class="font-bold uppercase tracking-wider text-[10px] mb-1">💡 Niveaux d'étude recommandés</p>
                            <p class="leading-normal">Pour le champ <strong class="font-black text-blue-900">level</strong>, utilisez de préférence les formats académiques suivants : <strong class="font-black text-blue-900">L1</strong>, <strong class="font-black text-blue-900">L2</strong>, <strong class="font-black text-blue-900">L3</strong> (Licence) ou <strong class="font-black text-blue-900">M1</strong>, <strong class="font-black text-blue-900">M2</strong> (Master).</p>
                        </div>
                    </div>

                    <div class="pt-6">
                        <button type="submit" class="w-full py-5 bg-upf-blue text-white rounded-2xl font-black shadow-xl hover:bg-upf-navy hover:scale-[1.02] transform transition-all duration-300 flex items-center justify-center space-x-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            <span>Lancer l'Importation de Groupes</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
