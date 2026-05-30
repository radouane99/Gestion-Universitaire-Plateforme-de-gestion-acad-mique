<x-app-layout>
    <x-slot name="header">
        <x-page-header 
            title="{{ __('Suivi de mes Heures d\'Enseignement') }}" 
            subtitle="{{ __('Consultez le décompte de vos heures validées par semaine, mois et cumul global.') }}"
            icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
        >
        </x-page-header>
    </x-slot>

    <div class="py-10 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">
            
            <!-- Statistics Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Weekly Hours -->
                <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-3xl p-8 text-white shadow-md relative overflow-hidden group hover:scale-[1.02] transform transition-all duration-300">
                    <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-white/10 rounded-full blur-2xl group-hover:scale-125 transition-transform"></div>
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-white/20 text-white text-[9px] font-black uppercase tracking-widest mb-4">
                        📅 {{ __('Cette Semaine') }}
                    </span>
                    <h3 class="text-white/80 text-[10px] uppercase font-black tracking-wider">{{ __('Heures Validées') }}</h3>
                    <div class="text-5xl font-black tracking-tight mt-1">{{ number_format($hoursWeek, 2) }} h</div>
                    <p class="text-xs text-blue-100 mt-4 leading-snug">{{ __('Basé sur vos feuilles de présence validées du lundi au dimanche.') }}</p>
                </div>

                <!-- Monthly Hours -->
                <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-3xl p-8 text-white shadow-md relative overflow-hidden group hover:scale-[1.02] transform transition-all duration-300">
                    <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-white/10 rounded-full blur-2xl group-hover:scale-125 transition-transform"></div>
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-white/20 text-white text-[9px] font-black uppercase tracking-widest mb-4">
                        🌙 {{ __('Ce Mois') }}
                    </span>
                    <h3 class="text-white/80 text-[10px] uppercase font-black tracking-wider">{{ __('Heures Validées') }}</h3>
                    <div class="text-5xl font-black tracking-tight mt-1">{{ number_format($hoursMonth, 2) }} h</div>
                    <p class="text-xs text-emerald-100 mt-4 leading-snug">{{ __('Cumul de vos enseignements pour le mois en cours.') }}</p>
                </div>

                <!-- Total Cumulative Hours -->
                <div class="bg-gradient-to-br from-amber-500 to-yellow-600 rounded-3xl p-8 text-white shadow-md relative overflow-hidden group hover:scale-[1.02] transform transition-all duration-300">
                    <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-white/10 rounded-full blur-2xl group-hover:scale-125 transition-transform"></div>
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-white/20 text-white text-[9px] font-black uppercase tracking-widest mb-4">
                        🎓 {{ __('Total Cumulé') }}
                    </span>
                    <h3 class="text-white/80 text-[10px] uppercase font-black tracking-wider">{{ __('Cumul des Heures') }}</h3>
                    <div class="text-5xl font-black tracking-tight mt-1">{{ number_format($hoursTotal, 2) }} h</div>
                    <p class="text-xs text-amber-100 mt-4 leading-snug">{{ __('Volume horaire global dispensé au cours de l\'année universitaire.') }}</p>
                </div>
            </div>

            <!-- Detailed Weekly Breakdown & History -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Left Column: Weekly Summaries -->
                <div class="space-y-6 lg:col-span-1">
                    <h3 class="font-black text-xl text-gray-900 italic pl-2 tracking-tight">{{ __('Synthèse Hebdomadaire') }}</h3>
                    
                    <div class="space-y-4">
                        @forelse($weeklyBreakdown as $weekName => $data)
                        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm hover:border-blue-500/30 transition-all duration-300">
                            <div class="flex justify-between items-start gap-4">
                                <div class="space-y-1">
                                    <p class="text-xs font-black text-gray-400 uppercase tracking-wider">{{ __('Période') }}</p>
                                    <p class="text-sm font-bold text-gray-800">{{ $weekName }}</p>
                                </div>
                                <span class="bg-blue-50 text-blue-600 border border-blue-100 px-3 py-1.5 rounded-2xl text-xs font-black">
                                    {{ number_format($data['hours'], 2) }} h
                                </span>
                            </div>
                            <div class="mt-4 border-t border-gray-50 pt-3 flex justify-between items-center text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                <span>📚 {{ $data['sessions']->count() }} {{ __('séances validées') }}</span>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-10 bg-white rounded-3xl border border-dashed border-gray-200">
                            <p class="text-gray-450 italic text-xs font-semibold">{{ __('Aucun décompte hebdomadaire disponible.') }}</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Right Column: Chronological Verified Sessions List -->
                <div class="space-y-6 lg:col-span-2">
                    <h3 class="font-black text-xl text-gray-900 italic pl-2 tracking-tight">{{ __('Historique des Séances Confirmées') }}</h3>
                    
                    <x-card class="p-0 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="bg-gray-50 border-b border-gray-100">
                                        <th class="px-6 py-4 text-left text-[9px] font-black text-gray-400 uppercase tracking-widest">{{ __('Date & Séance') }}</th>
                                        <th class="px-6 py-4 text-left text-[9px] font-black text-gray-400 uppercase tracking-widest">{{ __('Module') }}</th>
                                        <th class="px-6 py-4 text-center text-[9px] font-black text-gray-400 uppercase tracking-widest">{{ __('Groupe') }}</th>
                                        <th class="px-6 py-4 text-center text-[9px] font-black text-gray-400 uppercase tracking-widest">{{ __('Heures') }}</th>
                                        <th class="px-6 py-4 text-center text-[9px] font-black text-gray-400 uppercase tracking-widest">{{ __('Statut') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 font-sans">
                                    @forelse($sessions as $session)
                                    <tr class="hover:bg-gray-50/20 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="font-extrabold text-gray-900 text-sm">{{ $session->date->format('d/m/Y') }}</div>
                                            <div class="text-[10px] text-gray-450 font-bold uppercase mt-0.5">
                                                {{ date('H:i', strtotime($session->start_time)) }} - {{ date('H:i', strtotime($session->end_time)) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-extrabold text-gray-900 text-sm">{{ $session->module->name }}</div>
                                            <div class="text-[9px] font-black uppercase text-indigo-500 tracking-wider mt-0.5">{{ $session->module->code ?? 'MOD' }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="bg-slate-100 text-slate-700 px-3 py-1 rounded-xl text-[10px] font-black uppercase">
                                                {{ $session->group->name }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="font-black text-gray-900 text-sm">
                                                {{ number_format($session->duration, 2) }} h
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="inline-flex items-center gap-1.5 bg-emerald-50 border border-emerald-100 text-emerald-600 px-2.5 py-1 rounded-xl text-[9px] font-black uppercase tracking-wider">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                {{ __('Appel validé') }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-20 text-gray-400 italic text-xs font-semibold">
                                            {{ __('Aucune séance d\'enseignement n\'a été validée pour le moment.') }}
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </x-card>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
