<x-app-layout>
    <x-slot name="header">
        <x-page-header 
            title="{{ __('Prise de Rendez-vous') }}" 
            subtitle="{{ __('Planifiez un entretien individuel avec vos professeurs ou l\'administration.') }}"
            icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>'
        />
    </x-slot>

    <div class="py-10 bg-[#F8FAFC] dark:bg-[#020617] transition-colors duration-300">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-10">

            @if(session('success'))
                <div class="p-6 bg-emerald-50 text-emerald-700 rounded-3xl border border-emerald-100 flex items-center gap-4 shadow-sm">
                    <span class="text-2xl">🎉</span>
                    <p class="font-extrabold text-sm">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="p-6 bg-rose-50 text-rose-700 rounded-3xl border border-rose-100 flex items-center gap-4 shadow-sm">
                    <span class="text-2xl">⚠️</span>
                    <p class="font-extrabold text-sm">{{ session('error') }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- Left: Available Slots list --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-[2.5rem] p-8 shadow-sm">
                        <div class="flex items-center gap-3 border-b border-slate-100 dark:border-slate-800 pb-5 mb-6">
                            <span class="text-3xl">🗓️</span>
                            <div>
                                <h3 class="text-lg font-black text-slate-900 dark:text-white italic">Créneaux Disponibles</h3>
                                <p class="text-xs text-slate-400 font-bold">Sélectionnez une plage horaire pour réserver</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @forelse($availableSlots as $slot)
                                <div x-data="{ open: false }" class="p-5 bg-slate-50/50 dark:bg-slate-950/30 border border-gray-100 dark:border-slate-850 rounded-2xl flex flex-col justify-between gap-4 hover:shadow-md transition-all">
                                    <div>
                                        <div class="flex justify-between items-start">
                                            <span class="px-2.5 py-1 text-[9px] font-black uppercase tracking-wider rounded bg-indigo-50 text-upf-blue border border-indigo-100">
                                                {{ $slot->host->isProfessor() ? '👨‍🏫 Enseignant' : '🏛️ Administration' }}
                                            </span>
                                            <span class="text-[10px] text-slate-400 font-extrabold">{{ $slot->start_time->format('d/m/Y') }}</span>
                                        </div>
                                        <h4 class="text-sm font-black text-slate-850 dark:text-white mt-3">{{ $slot->host->name }}</h4>
                                        <p class="text-xs text-slate-500 font-bold mt-1">🕒 {{ $slot->start_time->format('H:i') }} - {{ $slot->end_time->format('H:i') }}</p>
                                    </div>

                                    <button @click="open = true" class="w-full py-2.5 bg-upf-blue hover:bg-upf-navy text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-sm">
                                        Réserver ce créneau
                                    </button>

                                    {{-- Booking Modal --}}
                                    <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4" x-cloak>
                                        <div @click.away="open = false" class="bg-white dark:bg-slate-900 rounded-[2rem] max-w-md w-full p-8 space-y-6 shadow-2xl border border-slate-100 dark:border-slate-800">
                                            <div>
                                                <h3 class="text-lg font-black text-slate-950 dark:text-white italic">Confirmer la Réservation</h3>
                                                <p class="text-xs text-slate-400 font-bold mt-1">Avec {{ $slot->host->name }} le {{ $slot->start_time->format('d/m/Y \à H:i') }}</p>
                                            </div>

                                            <form action="{{ route('student.appointments.book', $slot) }}" method="POST" class="space-y-4">
                                                @csrf
                                                <div class="space-y-1">
                                                    <label class="block text-[8px] font-black uppercase text-slate-400 tracking-wider">Motif du rendez-vous</label>
                                                    <textarea name="purpose" rows="3" required placeholder="Expliquez brièvement l'objet de votre rendez-vous..."
                                                        class="w-full border-gray-100 dark:border-slate-800 rounded-xl bg-gray-50 dark:bg-slate-950 p-3 text-xs font-semibold text-slate-900 dark:text-white resize-none"></textarea>
                                                </div>

                                                <div class="flex gap-3 pt-2">
                                                    <button type="button" @click="open = false" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all">
                                                        Annuler
                                                    </button>
                                                    <button type="submit" class="flex-1 py-3 bg-upf-blue hover:bg-upf-navy text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-md">
                                                        Confirmer
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-2 py-12 text-center text-slate-400 font-bold italic text-xs">
                                    Aucun créneau de rendez-vous disponible pour le moment.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Right: My Booked Appointments --}}
                <div class="space-y-6">
                    <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-[2.5rem] p-7 shadow-sm">
                        <h4 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest border-b border-slate-100 dark:border-slate-800 pb-3 mb-5">📅 Mes Rendez-vous</h4>
                        
                        <div class="space-y-4">
                            @forelse($myAppointments as $appt)
                                <div class="p-4 bg-slate-50/50 dark:bg-slate-950/30 border border-gray-100 dark:border-slate-850 rounded-xl space-y-3">
                                    <div class="flex justify-between items-start gap-2">
                                        <div>
                                            <p class="text-xs font-black text-slate-850 dark:text-white">{{ $appt->slot->host->name }}</p>
                                            <p class="text-[10px] text-slate-400 font-bold">le {{ $appt->slot->start_time->format('d/m/Y \à H:i') }}</p>
                                        </div>
                                        <span class="px-2 py-0.5 text-[8px] font-black border rounded uppercase tracking-wider
                                            {{ $appt->status === 'scheduled' ? 'bg-blue-50 text-blue-650 border-blue-100' : ($appt->status === 'cancelled' ? 'bg-rose-50 text-rose-650 border-rose-100' : 'bg-emerald-50 text-emerald-650 border-emerald-100') }}">
                                            {{ $appt->status === 'scheduled' ? 'Confirmé' : ($appt->status === 'cancelled' ? 'Annulé' : 'Effectué') }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-slate-600 font-semibold leading-relaxed">
                                        <strong>Motif :</strong> {{ $appt->purpose }}
                                    </p>

                                    @if($appt->status === 'scheduled')
                                        <form action="{{ route('appointments.cancel', $appt) }}" method="POST" class="pt-2 border-t border-slate-100 dark:border-slate-800">
                                            @csrf
                                            <button type="submit" class="w-full py-2 bg-rose-50 hover:bg-rose-100 text-rose-600 text-[9px] font-black uppercase tracking-wider rounded-lg transition-all">
                                                🔴 Annuler le Rendez-vous
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @empty
                                <p class="text-center py-6 text-slate-400 font-bold italic text-xs">Vous n'avez aucun rendez-vous planifié.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
