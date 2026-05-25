<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tighter italic">
            {{ __("Importation Massive d'Utilisateurs (Excel / CSV)") }}
        </h2>
    </x-slot>

    <div class="py-12 bg-[#F8FAFC]">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <div class="bg-gradient-to-r from-upf-blue to-upf-navy rounded-3xl p-10 text-white shadow-2xl relative overflow-hidden">
                <div class="relative z-10">
                    <h2 class="text-3xl font-black mb-2 italic">Importateur Excel / CSV</h2>
                    <p class="text-blue-100 opacity-80">Ajoutez des dizaines d'étudiants ou d'enseignants instantanément en téléversant un simple fichier tableur.</p>
                </div>
                <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-upf-magenta/10 rounded-full blur-3xl"></div>
            </div>

            <!-- Templates de Téléchargement -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Modèle Étudiant -->
                <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow">
                    <div>
                        <span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">Modèle Étudiants</span>
                        <h3 class="text-lg font-black text-gray-900 mt-4 mb-2">Fichier Modèle Étudiants</h3>
                        <p class="text-xs text-gray-500 font-medium">Contient les colonnes nécessaires : <code class="bg-gray-100 px-1 py-0.5 rounded text-rose-600 font-bold">name</code>, <code class="bg-gray-100 px-1 py-0.5 rounded text-rose-600 font-bold">email</code>, <code class="bg-gray-100 px-1 py-0.5 rounded text-rose-600 font-bold">password</code>, <code class="bg-gray-100 px-1 py-0.5 rounded text-rose-600 font-bold">group_name</code>, <code class="bg-gray-100 px-1 py-0.5 rounded text-rose-600 font-bold">group_level</code>, et <code class="bg-gray-100 px-1 py-0.5 rounded text-rose-600 font-bold">student_number</code>.</p>
                    </div>
                    <a href="{{ route('admin.users.import.template', 'student') }}" class="mt-6 inline-flex items-center justify-center py-3 px-6 border border-gray-200 hover:bg-gray-50 text-gray-800 rounded-2xl font-black text-xs uppercase tracking-widest transition-all">
                        Télécharger le Modèle (.CSV)
                    </a>
                </div>

                <!-- Modèle Professeur -->
                <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow">
                    <div>
                        <span class="bg-amber-50 text-amber-600 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">Modèle Enseignants</span>
                        <h3 class="text-lg font-black text-gray-900 mt-4 mb-2">Fichier Modèle Professeurs</h3>
                        <p class="text-xs text-gray-500 font-medium">Contient les colonnes nécessaires : <code class="bg-gray-100 px-1 py-0.5 rounded text-rose-600 font-bold">name</code>, <code class="bg-gray-100 px-1 py-0.5 rounded text-rose-600 font-bold">email</code>, <code class="bg-gray-100 px-1 py-0.5 rounded text-rose-600 font-bold">password</code>, et <code class="bg-gray-100 px-1 py-0.5 rounded text-rose-600 font-bold">department</code>.</p>
                    </div>
                    <a href="{{ route('admin.users.import.template', 'professor') }}" class="mt-6 inline-flex items-center justify-center py-3 px-6 border border-gray-200 hover:bg-gray-50 text-gray-800 rounded-2xl font-black text-xs uppercase tracking-widest transition-all">
                        Télécharger le Modèle (.CSV)
                    </a>
                </div>
            </div>

            <!-- Formulaire d'Import -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-black text-gray-900 italic">Téléverser le Fichier</h3>
                        <p class="text-gray-500 text-sm">Veuillez renseigner le type de compte avant de soumettre.</p>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="px-5 py-3 border border-gray-200 text-gray-700 hover:bg-gray-50 rounded-2xl font-black text-xs uppercase tracking-widest transition-all">
                        Retour à la Liste
                    </a>
                </div>

                <form action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data" class="p-10 space-y-8">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Choix du Rôle -->
                        <div class="space-y-3">
                            <label for="role_type" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Type d'Utilisateurs à Importer</label>
                            <select name="role_type" id="role_type" required class="w-full border-gray-200 rounded-2xl focus:ring-upf-blue focus:border-upf-blue p-4 font-bold text-gray-900 bg-gray-50">
                                <option value="student">Étudiants</option>
                                <option value="professor">Enseignants (Professeurs)</option>
                            </select>
                        </div>

                        <!-- Fichier CSV -->
                        <div class="space-y-3">
                            <label for="import_file" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Fichier CSV (Excel)</label>
                            <input type="file" name="import_file" id="import_file" required accept=".csv,.txt" class="w-full border-gray-200 rounded-2xl focus:ring-upf-blue focus:border-upf-blue p-3 font-bold text-gray-900 bg-gray-50">
                        </div>
                    </div>

                    <div class="p-4 bg-blue-50/50 rounded-2xl border border-blue-100/50 flex gap-3 text-xs text-upf-blue font-semibold">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div>
                            <p class="font-bold uppercase tracking-wider text-[10px] mb-1">💡 Conseil d'importation</p>
                            <p class="leading-normal">Pour exporter correctement depuis Microsoft Excel, choisissez le format <strong class="font-black text-blue-900">"CSV (séparateur : virgule) (*.csv)"</strong> lors de l'enregistrement de votre fichier pour assurer une parfaite compatibilité de lecture.</p>
                        </div>
                    </div>

                    <div class="pt-6">
                        <button type="submit" class="w-full py-5 bg-upf-blue text-white rounded-2xl font-black shadow-xl hover:bg-upf-navy hover:scale-[1.02] transform transition-all duration-300 flex items-center justify-center space-x-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            <span>Lancer l'Importation</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
