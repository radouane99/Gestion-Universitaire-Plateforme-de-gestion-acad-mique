<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tighter italic">
                {{ __('Emploi du Temps Global') }}
            </h2>
            <a href="{{ route('admin.schedules.create') }}" class="px-4 py-2.5 bg-upf-blue text-white rounded-2xl hover:bg-upf-navy flex items-center gap-2 text-xs font-black uppercase tracking-wider shadow-md hover:scale-[1.02] transform transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"></path></svg>
                Planifier une Séance
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-[#F8FAFC]" x-data="{ selectedGroup: '', selectedModule: '', selectedDay: '' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            @if(session('success'))
                <div class="p-5 text-sm text-emerald-800 rounded-2xl bg-emerald-50 border border-emerald-100 font-bold shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('warning'))
                <div class="p-5 text-sm text-amber-800 rounded-2xl bg-amber-50 border border-amber-100 font-bold shadow-sm">
                    {{ session('warning') }}
                </div>
            @endif

            <!-- Explication du Fonctionnement -->
            <div class="bg-gradient-to-r from-upf-blue via-upf-navy to-upf-magenta rounded-3xl p-8 text-white shadow-2xl relative overflow-hidden">
                <div class="relative z-10 grid grid-cols-1 lg:grid-cols-3 gap-6 items-center">
                    <div class="lg:col-span-2">
                        <h2 class="text-3xl font-black mb-2 italic">Planning Académique Hebdomadaire</h2>
                        <p class="text-blue-100 opacity-90 text-sm leading-relaxed">
                            L'<strong>Emploi du Temps</strong> définit le planning récurrent de la semaine (Mardi, Mercredi...). 
                            Pour des réservations ponctuelles à une date précise (ex: <strong>01/06/2026</strong>), veuillez utiliser le module 
                            <a href="{{ route('admin.reservations.index') }}" class="underline font-black hover:text-amber-300">Réservations</a>.
                        </p>
                    </div>
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('admin.reservations.index') }}" class="px-6 py-3 bg-amber-500 hover:bg-amber-600 text-white font-black rounded-xl text-xs uppercase tracking-wider transition-all shadow-md">
                            Gérer les Réservations (Dates)
                        </a>
                    </div>
                </div>
                <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
            </div>

            <!-- Barre de Filtrage Alpine.js -->
            <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm flex flex-col md:flex-row gap-6 items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-upf-blue flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 8.293A1 1 0 013 7.586V4z"></path></svg>
                    </div>
                    <div>
                        <h4 class="font-black text-gray-900 text-sm italic">Filtres de l'EDT</h4>
                        <p class="text-xs text-gray-400 font-semibold">Affichage immédiat par filière ou par module.</p>
                    </div>
                </div>

                <div class="w-full md:w-auto flex flex-col md:flex-row gap-4 items-center flex-1 max-w-2xl justify-end">
                    <!-- Groupe / Filière -->
                    <div class="w-full md:w-64 space-y-1">
                        <label class="text-[9px] font-black uppercase tracking-wider text-gray-400">Filtrer par Groupe</label>
                        <select x-model="selectedGroup" class="w-full border-gray-200 rounded-xl focus:ring-upf-blue focus:border-upf-blue text-xs font-black text-gray-700 bg-gray-50/50">
                            <option value="">Tous les Groupes</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Module -->
                    <div class="w-full md:w-64 space-y-1">
                        <label class="text-[9px] font-black uppercase tracking-wider text-gray-400">Filtrer par Module</label>
                        <select x-model="selectedModule" class="w-full border-gray-200 rounded-xl focus:ring-upf-blue focus:border-upf-blue text-xs font-black text-gray-700 bg-gray-50/50">
                            <option value="">Tous les Modules</option>
                            @foreach($modules as $module)
                                <option value="{{ $module->id }}">{{ $module->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filtrer par Jour -->
                    <div class="w-full md:w-64 space-y-1">
                        <label class="text-[9px] font-black uppercase tracking-wider text-gray-400">Filtrer par Jour</label>
                        <div class="flex gap-2">
                            <select x-model="selectedDay" class="w-full border-gray-200 rounded-xl focus:ring-upf-blue focus:border-upf-blue text-xs font-black text-gray-700 bg-gray-50/50">
                                <option value="">Toute la semaine</option>
                                <option value="1">Lundi</option>
                                <option value="2">Mardi</option>
                                <option value="3">Mercredi</option>
                                <option value="4">Jeudi</option>
                                <option value="5">Vendredi</option>
                                <option value="6">Samedi</option>
                            </select>
                            <button @click="const d = (new Date()).getDay(); selectedDay = d === 0 ? '1' : d.toString()" class="px-3 bg-upf-magenta text-white text-[10px] font-black uppercase tracking-wider rounded-xl hover:bg-upf-navy transition-colors whitespace-nowrap" title="Afficher uniquement les séances d'aujourd'hui">
                                Aujourd'hui
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Timeline responsive et optimisée pour éviter les scrollbars horizontaux -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-lg font-black text-gray-900 italic">Timeline des Séances Hebdomadaires</h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full table-auto text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-100 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                <th class="px-6 py-4 text-center w-24">Jour</th>
                                <th class="px-6 py-4 text-center w-28">Horaires</th>
                                <th class="px-6 py-4">Module</th>
                                <th class="px-6 py-4 w-40">Groupe</th>
                                <th class="px-6 py-4">Professeur</th>
                                <th class="px-6 py-4 w-40">Salle</th>
                                <th class="px-6 py-4 text-right w-24">Management</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 font-bold text-gray-700 text-sm">
                            @php
                                $days = [1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 4 => 'Jeudi', 5 => 'Vendredi', 6 => 'Samedi'];
                                $startOfWeek = now()->startOfWeek();
                            @endphp
                            @foreach($schedules as $session)
                            @php
                                $sessionDate = clone $startOfWeek;
                                $sessionDate->addDays($session->day_of_week - 1);
                            @endphp
                             <tr x-show="(selectedGroup === '' || selectedGroup == '{{ $session->group_id }}') && (selectedModule === '' || selectedModule == '{{ $session->module_id }}') && (selectedDay === '' || selectedDay == '{{ $session->day_of_week }}')"
                                class="hover:bg-indigo-50/10 transition-colors duration-200">
                                
                                <!-- Jour -->
                                <td class="px-6 py-4 text-center">
                                    <div class="flex flex-col items-center">
                                        <span class="inline-block px-3 py-1.5 bg-upf-blue/10 text-upf-blue rounded-xl text-xs font-black border border-upf-blue/20">
                                            {{ $days[$session->day_of_week] ?? 'N/A' }}
                                        </span>
                                        <span class="text-[10px] text-gray-400 font-extrabold mt-1">
                                            {{ $session->date ? date('d/m/Y', strtotime($session->date)) : 'N/A' }}
                                        </span>
                                    </div>
                                </td>
                                
                                <!-- Horaires -->
                                <td class="px-6 py-4 text-center">
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-50 border border-gray-200/50 rounded-lg text-xs font-black text-gray-900">
                                        <span>{{ date('H:i', strtotime($session->start_time)) }}</span>
                                        <span class="text-gray-300">-</span>
                                        <span class="text-gray-500">{{ date('H:i', strtotime($session->end_time)) }}</span>
                                    </div>
                                </td>
                                
                                <!-- Module -->
                                <td class="px-6 py-4">
                                    <div class="max-w-[200px] truncate" title="{{ $session->module->name }}">
                                        <p class="font-extrabold text-gray-900 leading-tight uppercase">{{ $session->module->name }}</p>
                                    </div>
                                </td>
                                
                                <!-- Groupe -->
                                <td class="px-6 py-4">
                                    <span class="inline-block px-2.5 py-1 bg-gray-100 text-gray-900 rounded-lg font-black text-xs border border-gray-200 whitespace-nowrap">
                                        {{ $session->group->name }}
                                    </span>
                                </td>
                                
                                <!-- Professeur -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center max-w-[180px] truncate" title="{{ $session->professor->user->name }}">
                                        <div class="w-6 h-6 rounded-full bg-upf-magenta/10 text-upf-magenta flex items-center justify-center font-black text-[9px] mr-2 shrink-0">
                                            {{ substr($session->professor->user->name, 0, 1) }}
                                        </div>
                                        <p class="font-extrabold text-gray-700 text-xs truncate">{{ $session->professor->user->name }}</p>
                                    </div>
                                </td>
                                
                                <!-- Salle -->
                                <td class="px-6 py-4">
                                    <div class="inline-flex items-center text-upf-blue font-black text-[10px] uppercase bg-upf-blue/5 px-2.5 py-1 rounded-lg border border-upf-blue/10 whitespace-nowrap">
                                        <svg class="w-3 h-3 mr-1 text-upf-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        {{ $session->room->name }}
                                    </div>
                                </td>
                                
                                <!-- Management -->
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end items-center gap-1">
                                        <!-- Éditer -->
                                        <a href="{{ route('admin.schedules.edit', $session->id) }}" class="p-2 text-amber-500 hover:bg-amber-50 rounded-lg transition-all" title="Modifier la séance">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>

                                        <!-- Supprimer / Annuler -->
                                        <form action="{{ route('admin.schedules.destroy', $session->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-rose-500 hover:bg-rose-50 rounded-lg transition-all duration-300" onclick="return confirm('Voulez-vous vraiment annuler cette séance de cours ?')" title="Annuler la séance">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($schedules->isEmpty())
                <div class="p-20 text-center">
                    <div class="text-4xl mb-4">📅</div>
                    <p class="text-gray-400 italic">Aucune séance d'emploi du temps n'a encore été planifiée.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
