<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tighter italic">
                {{ __("Créer une Réservation de Salle") }}
            </h2>
            <a href="{{ route('admin.reservations.index') }}" class="px-5 py-3 border border-gray-200 text-gray-700 hover:bg-gray-50 rounded-2xl font-black text-xs uppercase tracking-widest transition-all shadow-sm">
                Retour à la Liste
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-[#F8FAFC]">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <div class="bg-gradient-to-r from-upf-blue to-upf-navy rounded-3xl p-10 text-white shadow-2xl relative overflow-hidden">
                <div class="relative z-10">
                    <h2 class="text-3xl font-black mb-2 italic">Réservation Administrative</h2>
                    <p class="text-blue-100 opacity-80">Planifiez une séance exceptionnelle pour un professeur dans l'une des salles disponibles de l'UPF.</p>
                </div>
            </div>

            <!-- Formulaire de création -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-xl font-black text-gray-900 italic">Détails de la Réservation</h3>
                </div>

                <div x-data="availabilityChecker()" x-init="init()">
                    <form method="POST" action="{{ route('admin.reservations.store') }}" class="p-10 space-y-8">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Professeur -->
                        <div class="space-y-2">
                            <label for="professor_id" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Professeur Bénéficiaire</label>
                            <select name="professor_id" id="professor_id" required class="w-full border-gray-200 rounded-2xl focus:ring-upf-blue focus:border-upf-blue p-4 font-bold text-gray-900 bg-gray-50">
                                @foreach($professors as $prof)
                                    <option value="{{ $prof->id }}" {{ old('professor_id') == $prof->id ? 'selected' : '' }}>{{ $prof->user->name }} ({{ $prof->department }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Salle -->
                        <div class="space-y-2">
                            <label for="room_id" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Salle Allouée</label>
                            <select name="room_id" id="room_id" x-model="roomId" @change="fetchAvailability()" required class="w-full border-gray-200 rounded-2xl focus:ring-upf-blue focus:border-upf-blue p-4 font-bold text-gray-900 bg-gray-50">
                                <option value="" disabled>-- Choisir une salle --</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}">{{ $room->name }} (Capacité: {{ $room->capacity }} places)</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date -->
                        <div class="space-y-2">
                            <label for="date" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Date de la Séance</label>
                            <input type="date" name="date" id="date" x-model="date" @change="fetchAvailability()" required class="w-full border-gray-200 rounded-2xl focus:ring-upf-blue focus:border-upf-blue p-4 font-bold text-gray-900 bg-gray-50">
                        </div>

                        <!-- Statut -->
                        <div class="space-y-2">
                            <label for="status" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Statut de la Réservation</label>
                            <select name="status" id="status" required class="w-full border-gray-200 rounded-2xl focus:ring-upf-blue focus:border-upf-blue p-4 font-bold text-gray-900 bg-gray-50">
                                <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Approuvé</option>
                                <option value="pending" {{ old('status', 'approved') == 'pending' ? 'selected' : '' }}>En attente (Pending)</option>
                                <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>Rejeté</option>
                            </select>
                        </div>

                        <!-- Horaires Début & Fin -->
                        <div class="space-y-2">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="start_time" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block mb-2">Heure Début</label>
                                    <input type="time" name="start_time" id="start_time" value="{{ old('start_time') }}" required class="w-full border-gray-200 rounded-2xl focus:ring-upf-blue focus:border-upf-blue p-4 font-bold text-gray-900 bg-gray-50">
                                </div>
                                <div>
                                    <label for="end_time" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block mb-2">Heure Fin</label>
                                    <input type="time" name="end_time" id="end_time" value="{{ old('end_time') }}" required class="w-full border-gray-200 rounded-2xl focus:ring-upf-blue focus:border-upf-blue p-4 font-bold text-gray-900 bg-gray-50">
                                </div>
                            </div>
                        </div>

                        <!-- Motif pédagogique -->
                        <div class="space-y-2 md:col-span-2">
                            <label for="purpose" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Motif Académique / Pédagogique</label>
                            <textarea name="purpose" id="purpose" rows="3" required class="w-full border-gray-200 rounded-2xl focus:ring-upf-blue focus:border-upf-blue p-4 font-bold text-gray-900 bg-gray-50" placeholder="ex : Soutenance de Master, Séance exceptionnelle de rattrapage Flutter...">{{ old('purpose') }}</textarea>
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
                            <button type="submit" class="w-full py-5 bg-upf-blue text-white rounded-2xl font-black shadow-xl hover:bg-upf-navy hover:scale-[1.02] transform transition-all duration-300 flex items-center justify-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                                <span>Créer la Réservation</span>
                            </button>
                        </div>
                    </form>

                    <!-- Disponibilités Section -->
                    <div class="p-10 pt-0" x-show="roomId && date" x-cloak>
                        <div class="border-t border-gray-100 pt-10">
                            <h4 class="font-black text-gray-900 text-lg mb-4 italic">📅 Planning de la salle le <span x-text="formatDate(date)" class="text-upf-blue"></span></h4>
                            
                            <div x-show="loading" class="py-10 text-center">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-upf-blue mx-auto"></div>
                                <p class="text-xs text-gray-400 mt-3 font-bold uppercase tracking-widest">Vérification des disponibilités...</p>
                            </div>

                            <div x-show="!loading">
                                <template x-if="schedule.length === 0">
                                    <div class="bg-emerald-50 border border-emerald-100 p-6 rounded-2xl text-center">
                                        <span class="text-2xl mb-2 block">✨</span>
                                        <p class="text-emerald-800 font-black">La salle est totalement libre ce jour-là !</p>
                                        <p class="text-xs text-emerald-600 font-bold mt-1">Aucun cours ni réservation ne sont programmés.</p>
                                    </div>
                                </template>

                                <template x-if="schedule.length > 0">
                                    <div class="space-y-3">
                                        <p class="text-xs text-amber-600 bg-amber-50 px-4 py-2 rounded-xl font-bold border border-amber-100 mb-4 inline-block">
                                            ⚠️ Attention : En tant qu'administrateur, assurez-vous de ne pas créer de chevauchement.
                                        </p>
                                        <template x-for="item in schedule" :key="item.start">
                                            <div class="flex items-center gap-4 bg-gray-50 p-4 rounded-xl border border-gray-100">
                                                <div class="w-1.5 h-10 rounded-full" :class="item.color"></div>
                                                <div>
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-sm font-black text-gray-900" x-text="item.start + ' - ' + item.end"></span>
                                                        <span class="text-[9px] font-black uppercase text-white px-2 py-0.5 rounded-full tracking-widest" :class="item.color" x-text="item.type"></span>
                                                    </div>
                                                    <p class="text-xs font-bold text-gray-500 mt-1" x-text="item.details"></p>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function availabilityChecker() {
            return {
                roomId: '{{ old('room_id', '') }}',
                date: '{{ old('date', date('Y-m-d')) }}',
                schedule: [],
                loading: false,
                init() {
                    if (this.roomId) {
                        this.fetchAvailability();
                    }
                },
                formatDate(dateString) {
                    if (!dateString) return '';
                    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                    return new Date(dateString).toLocaleDateString('fr-FR', options);
                },
                async fetchAvailability() {
                    if (!this.roomId || !this.date) return;
                    this.loading = true;
                    try {
                        const response = await fetch(`/api/rooms/${this.roomId}/availability?date=${this.date}`);
                        if (response.ok) {
                            this.schedule = await response.json();
                        } else {
                            console.error('Failed to fetch availability');
                        }
                    } catch (error) {
                        console.error('Error fetching availability:', error);
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</x-app-layout>
