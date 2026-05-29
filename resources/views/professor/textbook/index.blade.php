<x-app-layout>
    <x-slot name="header">
        <x-page-header 
            title="{{ __('Mon Cahier de Textes') }}" 
            subtitle="{{ __('Suivi des Séances') }}"
            icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>'
        >
            <x-slot name="actions">
                <x-primary-button tag="a" href="{{ route('professor.textbook.create') }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"></path></svg>
                    {{ __('Nouvelle Séance') }}
                </x-primary-button>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-6 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <x-alert-messages />

            <div class="bg-gradient-to-r from-upf-navy to-upf-blue rounded-3xl p-10 text-white shadow-sm relative overflow-hidden flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div class="relative z-10">
                    <h2 class="text-3xl font-black mb-2 italic">{{ __('Suivi des Séances') }}</h2>
                    <p class="text-blue-100 opacity-80">{{ __('Consultez l\'historique complet et saisissez vos objectifs pédagogiques par séance de cours.') }}</p>
                </div>
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-upf-magenta/10 rounded-full blur-3xl"></div>
            </div>

            <x-card class="p-0">
                <div class="p-8 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-xl font-black text-gray-900 italic">{{ __('Historique des Séances') }}</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/70 border-b border-gray-100">
                                <th class="p-6 text-[10px] font-black uppercase tracking-widest text-gray-400">{{ __('Date & Créneau') }}</th>
                                <th class="p-6 text-[10px] font-black uppercase tracking-widest text-gray-400">{{ __('Module / Groupe') }}</th>
                                <th class="p-6 text-[10px] font-black uppercase tracking-widest text-gray-400">{{ __('Nature') }}</th>
                                <th class="p-6 text-[10px] font-black uppercase tracking-widest text-gray-400">{{ __('Objectif Pédagogique') }}</th>
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
                                        <div class="inline-block bg-indigo-50 text-upf-blue px-2 py-0.5 rounded-md text-[10px] font-black mt-1">{{ __('Groupe') }} : {{ $entry->group->name }}</div>
                                    </td>
                                    <td class="p-6">
                                        <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider
                                            {{ $entry->type == 'Cours' ? 'bg-blue-50 text-blue-600 border border-blue-100' : '' }}
                                            {{ $entry->type == 'TD' ? 'bg-amber-50 text-amber-600 border border-amber-100' : '' }}
                                            {{ $entry->type == 'TP' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : '' }}
                                        ">
                                            {{ __($entry->type) }}
                                        </span>
                                    </td>
                                    <td class="p-6 text-sm text-gray-500 font-medium max-w-md">
                                        {{ $entry->objective }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-16 text-center">
                                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                        </div>
                                        <p class="text-gray-500 font-bold italic">
                                            {{ __('Aucune séance enregistrée pour le moment.') }}
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
