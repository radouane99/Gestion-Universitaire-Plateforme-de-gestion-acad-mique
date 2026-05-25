<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tighter italic">
                {{ __("Modifier le Module : ") }} {{ $module->name }}
            </h2>
            <a href="{{ route('admin.modules.index') }}" class="px-5 py-3 border border-gray-200 text-gray-700 hover:bg-gray-50 rounded-2xl font-black text-xs uppercase tracking-widest transition-all shadow-sm">
                Retour à la Liste
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-[#F8FAFC]">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <div class="bg-gradient-to-r from-emerald-500 to-emerald-700 rounded-3xl p-10 text-white shadow-2xl relative overflow-hidden">
                <div class="relative z-10">
                    <h2 class="text-3xl font-black mb-2 italic">Paramètres du Cours</h2>
                    <p class="text-emerald-100 opacity-80">Modifiez le code, l'intitulé ou le coefficient d'évaluation du module académique.</p>
                </div>
            </div>

            <!-- Formulaire d'édition -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-xl font-black text-gray-900 italic">Modifier les Paramètres</h3>
                </div>

                <form method="POST" action="{{ route('admin.modules.update', $module->id) }}" class="p-10 space-y-8">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Code Module -->
                        <div class="space-y-2">
                            <label for="code" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Code Unique</label>
                            <input type="text" name="code" id="code" value="{{ old('code', $module->code) }}" required class="w-full border-gray-200 rounded-2xl focus:ring-emerald-500 focus:border-emerald-500 p-4 font-bold text-gray-900 bg-gray-50">
                        </div>

                        <!-- Coefficient -->
                        <div class="space-y-2">
                            <label for="coefficient" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Coefficient (Poids)</label>
                            <input type="number" step="0.01" name="coefficient" id="coefficient" value="{{ old('coefficient', $module->coefficient) }}" required class="w-full border-gray-200 rounded-2xl focus:ring-emerald-500 focus:border-emerald-500 p-4 font-bold text-gray-900 bg-gray-50">
                        </div>

                        <!-- Désignation -->
                        <div class="space-y-2 md:col-span-2">
                            <label for="name" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Désignation du Module</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $module->name) }}" required class="w-full border-gray-200 rounded-2xl focus:ring-emerald-500 focus:border-emerald-500 p-4 font-bold text-gray-900 bg-gray-50">
                        </div>

                        <!-- Filière -->
                        <div class="space-y-2 md:col-span-2">
                            <label for="filiere_id" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Filière (Requis)</label>
                            <select name="filiere_id" id="filiere_id" required class="w-full border-gray-200 rounded-2xl focus:ring-emerald-500 focus:border-emerald-500 p-4 font-bold text-gray-900 bg-gray-50">
                                <option value="" disabled {{ old('filiere_id', $module->filiere_id) ? '' : 'selected' }}>-- Sélectionner la Filière --</option>
                                @foreach($filieres as $filiere)
                                    <option value="{{ $filiere->id }}" {{ old('filiere_id', $module->filiere_id) == $filiere->id ? 'selected' : '' }}>
                                        {{ $filiere->code }} - {{ $filiere->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="p-4 text-xs text-rose-800 rounded-2xl bg-rose-50 border border-rose-100 font-bold">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="pt-6">
                        <button type="submit" class="w-full py-5 bg-emerald-600 text-white rounded-2xl font-black shadow-xl hover:bg-emerald-700 hover:scale-[1.02] transform transition-all duration-300 flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                            <span>Sauvegarder les Modifications</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
