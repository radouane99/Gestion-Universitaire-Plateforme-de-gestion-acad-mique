<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tighter italic">
                {{ __("Modifier la Réservation de Salle") }}
            </h2>
            <a href="{{ route('admin.reservations.index') }}" class="px-5 py-3 border border-gray-200 text-gray-700 hover:bg-gray-50 rounded-2xl font-black text-xs uppercase tracking-widest transition-all shadow-sm">
                Retour à la Liste
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-[#F8FAFC]">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <div class="bg-gradient-to-r from-emerald-500 to-emerald-700 rounded-3xl p-10 text-white shadow-2xl relative overflow-hidden">
                <div class="relative z-10">
                    <h2 class="text-3xl font-black mb-2 italic">Ajuster la Réservation</h2>
                    <p class="text-emerald-100 opacity-80">Modifiez le professeur, changez la salle allouée, ou déplacez le créneau horaire de la réservation.</p>
                </div>
            </div>

            <!-- Formulaire d'édition -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-xl font-black text-gray-900 italic">Modifier les Détails</h3>
                </div>

                <form method="POST" action="{{ route('admin.reservations.update', $reservation->id) }}" class="p-10 space-y-8">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Professeur -->
                        <div class="space-y-2">
                            <label for="professor_id" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Professeur Bénéficiaire</label>
                            <select name="professor_id" id="professor_id" required class="w-full border-gray-200 rounded-2xl focus:ring-emerald-500 focus:border-emerald-500 p-4 font-bold text-gray-900 bg-gray-50">
                                @foreach($professors as $prof)
                                    <option value="{{ $prof->id }}" {{ old('professor_id', $reservation->professor_id) == $prof->id ? 'selected' : '' }}>{{ $prof->user->name }} ({{ $prof->department }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Salle -->
                        <div class="space-y-2">
                            <label for="room_id" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Salle Allouée</label>
                            <select name="room_id" id="room_id" required class="w-full border-gray-200 rounded-2xl focus:ring-emerald-500 focus:border-emerald-500 p-4 font-bold text-gray-900 bg-gray-50">
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}" {{ old('room_id', $reservation->room_id) == $room->id ? 'selected' : '' }}>{{ $room->name }} (Capacité: {{ $room->capacity }} places)</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date -->
                        <div class="space-y-2">
                            <label for="date" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Date de la Séance</label>
                            <input type="date" name="date" id="date" value="{{ old('date', date('Y-m-d', strtotime($reservation->start_time))) }}" required class="w-full border-gray-200 rounded-2xl focus:ring-emerald-500 focus:border-emerald-500 p-4 font-bold text-gray-900 bg-gray-50">
                        </div>

                        <!-- Statut -->
                        <div class="space-y-2">
                            <label for="status" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Statut de la Réservation</label>
                            <select name="status" id="status" required class="w-full border-gray-200 rounded-2xl focus:ring-emerald-500 focus:border-emerald-500 p-4 font-bold text-gray-900 bg-gray-50">
                                <option value="approved" {{ old('status', $reservation->status) == 'approved' ? 'selected' : '' }}>Approuvé</option>
                                <option value="pending" {{ old('status', $reservation->status) == 'pending' ? 'selected' : '' }}>En attente (Pending)</option>
                                <option value="rejected" {{ old('status', $reservation->status) == 'rejected' ? 'selected' : '' }}>Rejeté</option>
                            </select>
                        </div>

                        <!-- Horaires Début & Fin -->
                        <div class="space-y-2">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="start_time" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block mb-2">Heure Début</label>
                                    <input type="time" name="start_time" id="start_time" value="{{ old('start_time', date('H:i', strtotime($reservation->start_time))) }}" required class="w-full border-gray-200 rounded-2xl focus:ring-emerald-500 focus:border-emerald-500 p-4 font-bold text-gray-900 bg-gray-50">
                                </div>
                                <div>
                                    <label for="end_time" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block mb-2">Heure Fin</label>
                                    <input type="time" name="end_time" id="end_time" value="{{ old('end_time', date('H:i', strtotime($reservation->end_time))) }}" required class="w-full border-gray-200 rounded-2xl focus:ring-emerald-500 focus:border-emerald-500 p-4 font-bold text-gray-900 bg-gray-50">
                                </div>
                            </div>
                        </div>

                        <!-- Motif pédagogique -->
                        <div class="space-y-2 md:col-span-2">
                            <label for="purpose" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Motif Académique / Pédagogique</label>
                            <textarea name="purpose" id="purpose" rows="3" required class="w-full border-gray-200 rounded-2xl focus:ring-emerald-500 focus:border-emerald-500 p-4 font-bold text-gray-900 bg-gray-50" placeholder="ex : Soutenance de Master, Séance exceptionnelle de rattrapage Flutter...">{{ old('purpose', $reservation->purpose) }}</textarea>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="p-4 text-xs text-rose-800 rounded-2xl bg-rose-50 border border-rose-100 font-bold">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="pt-6">
                        <button type="submit" class="w-full py-5 bg-emerald-600 text-white rounded-2xl font-black shadow-xl hover:bg-emerald-700 hover:scale-[1.02] transform transition-all duration-300 flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                            <span>Sauvegarder les Modifications</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
