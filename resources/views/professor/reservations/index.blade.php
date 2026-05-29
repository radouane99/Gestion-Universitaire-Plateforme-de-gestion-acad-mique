<x-app-layout>
    <x-slot name="header">
        <x-page-header 
            title="{{ __('Réservations d\'Infrastructures') }}" 
            subtitle="{{ __('Réservez des salles et des laboratoires pour vos sessions académiques spécifiques.') }}"
            icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>'
        >
            <x-slot name="actions">
                <x-primary-button tag="a" href="{{ route('professor.reservations.create') }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"></path></svg>
                    {{ __('Nouvelle Réservation') }}
                </x-primary-button>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-6 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <x-alert-messages />

            <x-card class="p-0">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-100">
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ __('Salle Réservée') }}</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ __('Horaire') }}</th>
                                <th class="px-6 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ __('Code Sécurité') }}</th>
                                <th class="px-6 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ __('Statut') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 font-bold text-gray-700">
                            @foreach($reservations as $reservation)
                            <tr class="hover:bg-amber-50/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center font-black mr-4 shadow-inner">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        </div>
                                        <div>
                                            <p class="font-extrabold text-gray-900 leading-tight">{{ $reservation->room->name }}</p>
                                            <p class="text-[10px] text-gray-500 font-bold mt-1 uppercase tracking-tighter">{{ $reservation->purpose }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-bold text-gray-700">
                                    <p class="text-xs font-black text-gray-800 mb-1 italic">{{ date('d M Y', strtotime($reservation->start_time)) }}</p>
                                    <p class="text-[10px] font-bold text-gray-500">{{ date('H:i', strtotime($reservation->start_time)) }} - {{ date('H:i', strtotime($reservation->end_time)) }}</p>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-[10px] font-mono font-bold text-gray-400 opacity-60 uppercase bg-gray-50 px-2 py-1 rounded">UPF-{{ strtoupper(hexdec(substr($reservation->id, 0, 4))) }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($reservation->status == 'approved')
                                        <x-badge type="success">{{ __('approuvée') }}</x-badge>
                                    @elseif($reservation->status == 'pending')
                                        <x-badge type="warning">{{ __('en attente') }}</x-badge>
                                    @else
                                        <x-badge type="danger">{{ __('refusée') }}</x-badge>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($reservations->isEmpty())
                <div class="p-16 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <p class="text-gray-500 font-bold">{{ __('Aucune réservation trouvée.') }}</p>
                </div>
                @endif
            </x-card>
        </div>
    </div>
</x-app-layout>
