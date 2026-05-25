<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tighter italic">
                {{ __('Gestion des Réservations de Salles') }}
            </h2>
            <a href="{{ route('admin.reservations.create') }}" class="px-4 py-2.5 bg-upf-blue text-white rounded-2xl hover:bg-upf-navy flex items-center gap-2 text-xs font-black uppercase tracking-wider shadow-md hover:scale-[1.02] transform transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"></path></svg>
                Créer une Réservation
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-[#F8FAFC]">
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

            <div class="bg-gradient-to-r from-amber-500 to-amber-700 rounded-3xl p-10 text-white shadow-2xl relative overflow-hidden">
                <div class="relative z-10">
                    <h2 class="text-3xl font-black mb-2 italic">Supervision des Réservations</h2>
                    <p class="text-amber-100 opacity-80">Gérez, approuvez, rejetez ou modifiez l'ensemble des réservations d'infrastructures de l'UPF.</p>
                </div>
                <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
            </div>

            <!-- Table of all reservations -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-xl font-black text-gray-900 italic">Demandes & Affectations Actives</h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-100">
                                <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Salle Réservée</th>
                                <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Professeur</th>
                                <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Timing & Créneau</th>
                                <th class="px-8 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Statut</th>
                                <th class="px-8 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest w-64">Actions de Management</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 font-bold text-gray-700">
                            @foreach($reservations as $reservation)
                            <tr class="hover:bg-amber-50/10 transition-colors duration-200">
                                <td class="px-8 py-6">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center font-black mr-3 shadow-inner">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        </div>
                                        <div>
                                            <p class="font-extrabold text-gray-900 leading-none text-sm">{{ $reservation->room->name }}</p>
                                            <p class="text-[10px] text-gray-400 font-bold mt-1 uppercase tracking-tighter">{{ $reservation->purpose }}</p>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-8 py-6">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-upf-blue/10 text-upf-blue flex items-center justify-center font-black text-[10px] mr-3">
                                            {{ substr($reservation->professor->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-extrabold text-gray-900 text-xs leading-none">{{ $reservation->professor->user->name }}</p>
                                            <p class="text-[9px] text-gray-400 font-bold mt-1 uppercase">{{ $reservation->professor->department }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-8 py-6">
                                    <p class="text-xs font-black text-gray-700 mb-1 italic">{{ date('d M Y', strtotime($reservation->start_time)) }}</p>
                                    <p class="text-[10px] font-bold text-gray-400">{{ date('H:i', strtotime($reservation->start_time)) }} - {{ date('H:i', strtotime($reservation->end_time)) }}</p>
                                </td>

                                <td class="px-8 py-6 text-center">
                                    <span class="px-3.5 py-1.5 rounded-full text-[9px] font-black uppercase tracking-wider whitespace-nowrap 
                                        {{ $reservation->status == 'approved' ? 'bg-emerald-100 text-emerald-700' : ($reservation->status == 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">
                                        {{ $reservation->status }}
                                    </span>
                                </td>

                                <td class="px-8 py-6 text-right">
                                    <div class="flex justify-end items-center gap-2">
                                        @if($reservation->status == 'pending')
                                            <!-- Approuver -->
                                            <form action="{{ route('admin.reservations.approve', $reservation->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-[9px] uppercase font-black tracking-wider transition-all" title="Approuver la demande">
                                                    Approuver
                                                </button>
                                            </form>
                                            
                                            <!-- Rejeter -->
                                            <form action="{{ route('admin.reservations.reject', $reservation->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-[9px] uppercase font-black tracking-wider transition-all" title="Rejeter la demande">
                                                    Rejeter
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Éditer -->
                                        <a href="{{ route('admin.reservations.edit', $reservation->id) }}" class="p-2.5 text-amber-500 hover:bg-amber-50 rounded-xl transition-all" title="Modifier la réservation">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>

                                        <!-- Supprimer / Annuler -->
                                        <form action="{{ route('admin.reservations.destroy', $reservation->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2.5 text-rose-500 hover:bg-rose-50 rounded-xl transition-all duration-300" onclick="return confirm('Voulez-vous vraiment annuler/supprimer cette réservation ?')" title="Supprimer la réservation">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($reservations->isEmpty())
                <div class="p-24 text-center">
                    <div class="text-4xl mb-4">🏛️</div>
                    <p class="text-gray-400 italic">Aucune réservation de salle n'est enregistrée pour le moment.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
