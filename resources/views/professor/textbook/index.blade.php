<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mon Cahier de Textes') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <div class="bg-gradient-to-r from-upf-navy to-upf-blue rounded-3xl p-10 text-white shadow-2xl relative overflow-hidden flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div class="relative z-10">
                    <h2 class="text-3xl font-black mb-2 italic">Suivi des Séances</h2>
                    <p class="text-blue-100 opacity-80">Consultez l'historique complet et saisissez vos objectifs pédagogiques par séance de cours.</p>
                </div>
                <a href="{{ route('professor.textbook.create') }}" class="relative z-10 px-6 py-4 bg-upf-magenta text-white font-black text-xs uppercase tracking-widest rounded-2xl shadow-lg hover:bg-pink-700 hover:scale-105 transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    Nouvelle Séance
                </a>
            </div>

            @if(session('success'))
                <div class="p-4 mb-4 text-sm text-emerald-800 rounded-2xl bg-emerald-50 border border-emerald-100 font-bold">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-xl font-black text-gray-900 italic">Historique des Séances</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/70 border-b border-gray-100">
                                <th class="p-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Date & Créneau</th>
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
                                    <td colspan="4" class="p-16 text-center text-gray-400 italic">
                                        Aucune séance enregistrée pour le moment.
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
