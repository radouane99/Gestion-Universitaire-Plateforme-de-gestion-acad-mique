<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Suivi Global des Cahiers de Textes') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <div class="bg-gradient-to-r from-upf-blue to-upf-navy rounded-3xl p-10 text-white shadow-2xl relative overflow-hidden">
                <div class="relative z-10">
                    <h2 class="text-3xl font-black mb-2 italic">Supervision Pédagogique</h2>
                    <p class="text-blue-100 opacity-80">Consultez et filtrez tous les cahiers de textes saisis par l'ensemble des professeurs de l'UPF.</p>
                </div>
            </div>

            <!-- Filtres -->
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                <form method="GET" action="{{ route('admin.textbooks.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <!-- Professeur -->
                    <div class="space-y-2">
                        <label for="professor_id" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Enseignant</label>
                        <select name="professor_id" id="professor_id" class="w-full border-gray-200 rounded-2xl focus:ring-upf-blue focus:border-upf-blue p-4 font-bold text-gray-900 bg-gray-50">
                            <option value="">Tous les professeurs</option>
                            @foreach($professors as $prof)
                                <option value="{{ $prof->id }}" {{ request('professor_id') == $prof->id ? 'selected' : '' }}>{{ $prof->user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Groupe -->
                    <div class="space-y-2">
                        <label for="group_id" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Groupe / Classe</label>
                        <select name="group_id" id="group_id" class="w-full border-gray-200 rounded-2xl focus:ring-upf-blue focus:border-upf-blue p-4 font-bold text-gray-900 bg-gray-50">
                            <option value="">Tous les groupes</option>
                            @foreach($groups as $grp)
                                <option value="{{ $grp->id }}" {{ request('group_id') == $grp->id ? 'selected' : '' }}>{{ $grp->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Nature -->
                    <div class="space-y-2">
                        <label for="type" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Nature de Séance</label>
                        <select name="type" id="type" class="w-full border-gray-200 rounded-2xl focus:ring-upf-blue focus:border-upf-blue p-4 font-bold text-gray-900 bg-gray-50">
                            <option value="">Toutes les natures</option>
                            <option value="Cours" {{ request('type') == 'Cours' ? 'selected' : '' }}>Cours</option>
                            <option value="TD" {{ request('type') == 'TD' ? 'selected' : '' }}>TD</option>
                            <option value="TP" {{ request('type') == 'TP' ? 'selected' : '' }}>TP</option>
                        </select>
                    </div>

                    <!-- Bouton de Soumission -->
                    <div class="flex items-end">
                        <button type="submit" class="w-full py-4 bg-upf-blue text-white rounded-2xl font-black shadow-lg hover:bg-upf-navy transition-all uppercase text-xs tracking-widest">
                            Filtrer
                        </button>
                    </div>
                </form>
            </div>

            <!-- Liste globale -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-xl font-black text-gray-900 italic">Saisies de Séances Générales</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/70 border-b border-gray-100">
                                <th class="p-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Date & Créneau</th>
                                <th class="p-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Enseignant</th>
                                <th class="p-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Module / Groupe</th>
                                <th class="p-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Nature</th>
                                <th class="p-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Objectif Pédagogique</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 font-bold text-gray-700">
                            @forelse($entries as $entry)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="p-6">
                                        <div class="text-gray-900 text-sm font-black">{{ \Carbon\Carbon::parse($entry->date)->format('d/m/Y') }}</div>
                                        <div class="text-xs text-gray-400 font-semibold">{{ substr($entry->start_time, 0, 5) }} - {{ substr($entry->end_time, 0, 5) }}</div>
                                    </td>
                                    <td class="p-6">
                                        <div class="text-gray-900 text-sm font-black">{{ $entry->professor->user->name }}</div>
                                        <div class="text-xs text-gray-400 font-semibold">{{ $entry->professor->department }}</div>
                                    </td>
                                    <td class="p-6">
                                        <div class="text-gray-900 text-sm font-black">{{ $entry->module->name }}</div>
                                        <div class="inline-block bg-indigo-50 text-upf-blue px-2 py-0.5 rounded-md text-[10px] font-black mt-1">{{ $entry->group->name }}</div>
                                    </td>
                                    <td class="p-6">
                                        <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider
                                            {{ $entry->type == 'Cours' ? 'bg-blue-50 text-blue-600' : '' }}
                                            {{ $entry->type == 'TD' ? 'bg-amber-50 text-amber-600' : '' }}
                                            {{ $entry->type == 'TP' ? 'bg-emerald-50 text-emerald-600' : '' }}
                                        ">
                                            {{ $entry->type }}
                                        </span>
                                    </td>
                                    <td class="p-6 text-sm text-gray-500 font-medium max-w-md">
                                        {{ $entry->objective }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-16 text-center text-gray-400 italic">
                                        Aucun cahier de textes ne correspond à votre recherche.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
