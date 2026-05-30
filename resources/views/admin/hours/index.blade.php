<x-app-layout>
    <x-slot name="header">
        <x-page-header 
            title="{{ __('Contrôle des Heures d\'Enseignement') }}" 
            subtitle="{{ __('Suivez et contrôlez le volume horaire d\'enseignement validé pour l\'ensemble du corps enseignant.') }}"
            icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
        >
        </x-page-header>
    </x-slot>

    <div class="py-10 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Filters Card -->
            <x-card class="p-6">
                <form method="GET" action="{{ route('admin.hours.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                    <div>
                        <x-input-label for="search" :value="__('Rechercher un enseignant')" class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2" />
                        <x-text-input type="text" name="search" id="search" value="{{ request('search') }}" class="block w-full border-gray-200 rounded-2xl bg-white shadow-sm p-3 font-semibold text-sm" placeholder="Nom du professeur..." />
                    </div>
                    <div>
                        <x-input-label for="status" :value="__('Type de contrat')" class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2" />
                        <select name="status" id="status" class="block w-full border-gray-200 rounded-2xl bg-white shadow-sm p-3 font-semibold text-sm text-slate-700">
                            <option value="">{{ __('Tous les statuts') }}</option>
                            <option value="permanent" {{ request('status') === 'permanent' ? 'selected' : '' }}>{{ __('Permanent') }}</option>
                            <option value="vacataire" {{ request('status') === 'vacataire' ? 'selected' : '' }}>{{ __('Vacataire') }}</option>
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="w-full px-6 py-3.5 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-black shadow-md transition-all duration-150 uppercase tracking-widest text-xs">
                            🔍 {{ __('Filtrer et actualiser') }}
                        </button>
                    </div>
                </form>
            </x-card>

            <!-- Professors Hours List Table -->
            <x-card class="p-0 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="px-8 py-5 text-left text-[9px] font-black text-gray-400 uppercase tracking-widest">{{ __('Enseignant') }}</th>
                                <th class="px-8 py-5 text-center text-[9px] font-black text-gray-400 uppercase tracking-widest">{{ __('Statut Contrat') }}</th>
                                <th class="px-8 py-5 text-center text-[9px] font-black text-gray-400 uppercase tracking-widest">{{ __('Cette Semaine') }}</th>
                                <th class="px-8 py-5 text-center text-[9px] font-black text-gray-400 uppercase tracking-widest">{{ __('Ce Mois') }}</th>
                                <th class="px-8 py-5 text-center text-[9px] font-black text-gray-400 uppercase tracking-widest">{{ __('Total Cumulé') }}</th>
                                <th class="px-8 py-5 text-center text-[9px] font-black text-gray-400 uppercase tracking-widest">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 font-sans">
                            @forelse($professors as $prof)
                            <tr class="hover:bg-gray-50/20 transition-colors">
                                <td class="px-8 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-black text-sm mr-4 border border-blue-100/50">
                                            {{ substr($prof->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-extrabold text-gray-900 leading-tight">{{ $prof->user->name }}</p>
                                            <p class="text-[10px] text-gray-400 font-bold uppercase mt-0.5">{{ $prof->department ?? 'Département académique' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-4 text-center">
                                    @if($prof->status === 'vacataire')
                                        <span class="inline-block bg-pink-50 border border-pink-100 text-pink-600 px-3 py-1 rounded-xl text-[9px] font-black uppercase tracking-wider">
                                            {{ __('Vacataire') }}
                                        </span>
                                    @else
                                        <span class="inline-block bg-blue-50 border border-blue-100 text-blue-600 px-3 py-1 rounded-xl text-[9px] font-black uppercase tracking-wider">
                                            {{ __('Permanent') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-8 py-4 text-center">
                                    <span class="font-black text-slate-800 text-sm">
                                        {{ number_format($prof->hours_week, 2) }} h
                                    </span>
                                </td>
                                <td class="px-8 py-4 text-center">
                                    <span class="font-black text-slate-800 text-sm">
                                        {{ number_format($prof->hours_month, 2) }} h
                                    </span>
                                </td>
                                <td class="px-8 py-4 text-center">
                                    <span class="font-black text-blue-600 text-sm">
                                        {{ number_format($prof->hours_total, 2) }} h
                                    </span>
                                </td>
                                <td class="px-8 py-4 text-center">
                                    <a href="{{ route('admin.hours.show', $prof->id) }}" class="inline-flex items-center px-4 py-2 bg-slate-100 border border-slate-200 hover:bg-blue-650 hover:text-white rounded-xl font-bold text-xs text-slate-700 uppercase tracking-wider transition">
                                        🔍 {{ __('Détails') }}
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-20 text-gray-400 italic text-xs font-semibold">
                                    {{ __('Aucun enseignant trouvé pour les critères de filtrage sélectionnés.') }}
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
