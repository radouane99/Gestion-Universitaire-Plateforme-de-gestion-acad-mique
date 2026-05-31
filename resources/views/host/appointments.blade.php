<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tight italic">
            {{ __('Gestion de mes Rendez-vous & Créneaux') }}
        </h2>
    </x-slot>

    <div class="py-10 bg-[#F8FAFC] dark:bg-[#020617] transition-colors duration-300">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-10">

            @if(session('success'))
                <div class="p-6 bg-emerald-50 text-emerald-700 rounded-3xl border border-emerald-100 flex items-center gap-4 shadow-sm animate-fade-in">
                    <span class="text-2xl">🎉</span>
                    <p class="font-extrabold text-sm">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="p-6 bg-rose-50 text-rose-700 rounded-3xl border border-rose-100 flex items-center gap-4 shadow-sm animate-fade-in">
                    <span class="text-2xl">⚠️</span>
                    <p class="font-extrabold text-sm">{{ session('error') }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- Left Side: Add Availability Slot Form --}}
                <div class="space-y-6">
                    <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-[2.5rem] p-7 shadow-sm">
                        <h4 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest border-b border-slate-100 dark:border-slate-800 pb-3 mb-5">➕ Déclarer une Disponibilité</h4>
                        
                        <form action="{{ route($routePrefix . 'appointments.slot.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="space-y-1">
                                <label class="block text-[8px] font-black uppercase text-slate-400 tracking-wider">Date & Heure de Début</label>
                                <input type="datetime-local" name="start_time" required
                                    class="w-full border-gray-100 dark:border-slate-800 rounded-xl bg-gray-50 dark:bg-slate-950 p-3 text-xs font-bold text-slate-900 dark:text-white">
                            </div>

                            <div class="space-y-1">
                                <label class="block text-[8px] font-black uppercase text-slate-400 tracking-wider">Date & Heure de Fin</label>
                                <input type="datetime-local" name="end_time" required
                                    class="w-full border-gray-100 dark:border-slate-800 rounded-xl bg-gray-50 dark:bg-slate-950 p-3 text-xs font-bold text-slate-900 dark:text-white">
                            </div>

                            <button type="submit" class="w-full py-3.5 bg-upf-blue hover:bg-upf-navy text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-md">
                                Ajouter le Créneau
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Right Side: Availability Slots & Booked Appointments --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-[2.5rem] p-8 shadow-sm">
                        <div class="flex items-center gap-3 border-b border-slate-100 dark:border-slate-800 pb-5 mb-6">
                            <span class="text-3xl">🗓️</span>
                            <div>
                                <h3 class="text-lg font-black text-slate-900 dark:text-white italic">Mon Agenda de Créneaux</h3>
                                <p class="text-xs text-slate-400 font-bold">Suivez vos plages de disponibilité déclarées et les réservations</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            @forelse($mySlots as $slot)
                                <div class="p-5 bg-slate-50/50 dark:bg-slate-950/30 border border-gray-100 dark:border-slate-850 rounded-2xl flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-3">
                                            <span class="px-2.5 py-1 text-[8px] font-black border rounded uppercase tracking-wider
                                                {{ $slot->status === 'booked' ? 'bg-amber-50 text-amber-650 border-amber-100' : 'bg-emerald-50 text-emerald-650 border-emerald-100' }}">
                                                {{ $slot->status === 'booked' ? 'Réservé' : 'Libre' }}
                                            </span>
                                            <p class="text-[10px] text-slate-400 font-extrabold">{{ $slot->start_time->format('d/m/Y') }}</p>
                                        </div>
                                        <h4 class="text-sm font-black text-slate-900 dark:text-white">
                                            🕒 {{ $slot->start_time->format('H:i') }} - {{ $slot->end_time->format('H:i') }}
                                        </h4>

                                        @if($slot->status === 'booked')
                                            @php 
                                                $appt = $slot->appointments->where('status', 'scheduled')->first();
                                            @endphp
                                            @if($appt)
                                                <div class="mt-2 p-3 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-850 rounded-xl">
                                                    <p class="text-[9px] font-black uppercase text-slate-400">Étudiant :</p>
                                                    <p class="text-xs text-slate-850 dark:text-white font-extrabold mt-0.5">{{ $appt->student->user->name }} ({{ $appt->student->group->name }})</p>
                                                    <p class="text-[11px] text-slate-600 font-semibold italic mt-1">"{{ $appt->purpose }}"</p>
                                                </div>
                                            @endif
                                        @endif
                                    </div>

                                    <div class="flex gap-2 w-full sm:w-auto">
                                        @if($slot->status === 'booked' && isset($appt))
                                            <form action="{{ route('appointments.cancel', $appt) }}" method="POST" class="w-full sm:w-auto">
                                                @csrf
                                                <button type="submit" class="w-full sm:w-auto px-4 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-600 text-[9px] font-black uppercase tracking-wider rounded-xl transition-all">
                                                    Annuler le RDV
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route($routePrefix . 'appointments.slot.destroy', $slot) }}" method="POST" class="w-full sm:w-auto">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-full sm:w-auto px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-[9px] font-black uppercase tracking-wider rounded-xl transition-all">
                                                    Supprimer
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="text-center py-10 text-slate-400 font-bold italic text-xs">Vous n'avez déclaré aucune disponibilité.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
