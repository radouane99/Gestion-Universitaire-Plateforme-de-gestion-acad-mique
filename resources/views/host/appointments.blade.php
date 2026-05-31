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

                    @if($routePrefix === 'admin.')
                    <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-[2.5rem] p-7 shadow-sm">
                        <h4 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest border-b border-slate-100 dark:border-slate-800 pb-3 mb-4">⚡ Générer la Journée Type</h4>
                        <p class="text-[10px] text-slate-400 font-bold mb-4">Génère automatiquement 11 créneaux de 30 min (10h00 - 16h30, avec pause de 13h00 à 14h00).</p>
                        
                        <form action="{{ route('admin.appointments.generate-slots') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="space-y-1">
                                <label class="block text-[8px] font-black uppercase text-slate-400 tracking-wider">Date à Générer</label>
                                <input type="date" name="generation_date" required min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                    class="w-full border-gray-100 dark:border-slate-800 rounded-xl bg-gray-50 dark:bg-slate-950 p-3 text-xs font-bold text-slate-900 dark:text-white">
                            </div>

                            <button type="submit" class="w-full py-3.5 bg-indigo-650 hover:bg-indigo-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-md">
                                Générer les créneaux
                            </button>
                        </form>
                    </div>
                    @endif
                </div>

                {{-- Right Side: Availability Slots & Booked Appointments --}}
                <div class="lg:col-span-2 space-y-6">
                    @if(isset($pendingRequests) && $pendingRequests->isNotEmpty())
                    <div class="bg-white dark:bg-slate-900 border border-emerald-100 dark:border-slate-800 rounded-[2.5rem] p-8 shadow-sm">
                        <div class="flex items-center gap-3 border-b border-slate-100 dark:border-slate-800 pb-5 mb-6">
                            <span class="text-3xl">📥</span>
                            <div>
                                <h3 class="text-lg font-black text-slate-900 dark:text-white italic">Demandes de Rendez-vous reçues</h3>
                                <p class="text-xs text-slate-400 font-bold">Demandes directes des étudiants en attente de traitement</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            @foreach($pendingRequests as $req)
                                <div class="p-5 bg-emerald-50/20 dark:bg-slate-950/20 border border-emerald-100/50 dark:border-slate-850 rounded-2xl flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-3">
                                            <span class="px-2 py-0.5 text-[8px] font-black border border-emerald-200 bg-emerald-50 text-emerald-700 rounded uppercase tracking-wider">
                                                Demande directe
                                              </span>
                                            <p class="text-[10px] text-slate-400 font-extrabold">{{ $req->slot->start_time->format('d/m/Y') }}</p>
                                        </div>
                                        <h4 class="text-sm font-black text-slate-905 dark:text-white">
                                            🎒 {{ $req->student->user->name }}
                                        </h4>
                                        <p class="text-xs text-slate-500 font-bold">
                                            🕒 {{ $req->slot->start_time->format('H:i') }} (30 min)
                                        </p>
                                        <p class="text-[11px] text-slate-600 font-semibold italic mt-1">"{{ $req->purpose }}"</p>
                                    </div>

                                    <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto" x-data="{ showAlt: false }">
                                        <div class="flex gap-2 w-full" x-show="!showAlt">
                                            <form action="{{ route('appointments.accept-request', $req) }}" method="POST" class="w-full sm:w-auto">
                                                @csrf
                                                <button type="submit" class="w-full sm:w-auto px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-[9px] font-black uppercase tracking-wider rounded-xl transition-all shadow-sm">
                                                    ✓ Accepter
                                                </button>
                                            </form>
                                            <button type="button" @click="showAlt = true" class="w-full sm:w-auto px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[9px] font-black uppercase tracking-wider rounded-xl transition-all">
                                                🔄 Autre date
                                            </button>
                                        </div>

                                        <form action="{{ route('appointments.suggest-alternative', $req) }}" method="POST" class="w-full sm:w-auto space-y-2" x-show="showAlt" x-cloak>
                                            @csrf
                                            <div class="space-y-1">
                                                <label class="block text-[8px] font-black uppercase text-slate-400 tracking-wider">Nouvelle Heure</label>
                                                <input type="datetime-local" name="suggested_time" required min="{{ date('Y-m-d\TH:i') }}"
                                                    class="w-full border-gray-200 dark:border-slate-800 rounded-xl bg-white dark:bg-slate-950 p-2 text-xs font-bold text-slate-900 dark:text-white">
                                            </div>
                                            <div class="flex gap-2">
                                                <button type="submit" class="flex-1 px-3 py-2 bg-indigo-650 hover:bg-indigo-700 text-white text-[8px] font-black uppercase tracking-wider rounded-lg transition-all shadow-sm">
                                                    Proposer
                                                </button>
                                                <button type="button" @click="showAlt = false" class="flex-1 px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-650 text-[8px] font-black uppercase tracking-wider rounded-lg transition-all">
                                                    Retour
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

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
                                                    <p class="text-xs text-slate-850 dark:text-white font-extrabold mt-0.5">{{ $appt->student?->user?->name ?? 'Étudiant inconnu' }} ({{ $appt->student?->group?->name ?? 'Sans groupe' }})</p>
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
