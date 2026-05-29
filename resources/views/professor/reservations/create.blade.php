<x-app-layout>
    <x-slot name="header">
        <x-page-header 
            title="{{ __('Demande d\'Infrastructure') }}" 
            subtitle="{{ __('Demandez l\'accès à des laboratoires ou des amphithéâtres spécifiques.') }}"
            icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>'
        >
            <x-slot name="actions">
                <a href="{{ route('professor.reservations.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-xl font-bold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 transition shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    {{ __('Mes réservations') }}
                </a>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-6 bg-[#F8FAFC]">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <x-alert-messages />

            <x-card>
                <div x-data="availabilityChecker()" x-init="init()">
                    <form action="{{ route('professor.reservations.store') }}" method="POST" class="space-y-8">
                        @csrf
                    
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-3">
                                <x-input-label for="room_id" :value="__('Sélectionner une salle')" class="text-[10px] font-black uppercase tracking-widest text-gray-400" />
                                <select name="room_id" x-model="roomId" @change="fetchAvailability()" class="w-full border-gray-200 rounded-xl bg-gray-50 font-bold p-4 focus:ring-upf-blue focus:border-upf-blue shadow-sm transition" required>
                                    <option value="" disabled>{{ __('-- Choisir une salle --') }}</option>
                                    @foreach($rooms as $room)
                                        <option value="{{ $room->id }}">{{ $room->name }} ({{ __('Cap:') }} {{ $room->capacity }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-3">
                                <x-input-label for="date" :value="__('Date de la réservation')" class="text-[10px] font-black uppercase tracking-widest text-gray-400" />
                                <input type="date" name="date" x-model="date" @change="fetchAvailability()" class="w-full border-gray-200 rounded-xl bg-gray-50 font-bold p-4 focus:ring-upf-blue focus:border-upf-blue shadow-sm transition" required min="{{ date('Y-m-d') }}">
                            </div>

                            <div class="space-y-3">
                                <x-input-label for="start_time" :value="__('Heure de début')" class="text-[10px] font-black uppercase tracking-widest text-gray-400" />
                                <input type="time" name="start_time" class="w-full border-gray-200 rounded-xl bg-gray-50 font-bold p-4 focus:ring-upf-blue focus:border-upf-blue shadow-sm transition" required>
                            </div>

                            <div class="space-y-3">
                                <x-input-label for="end_time" :value="__('Heure de fin')" class="text-[10px] font-black uppercase tracking-widest text-gray-400" />
                                <input type="time" name="end_time" class="w-full border-gray-200 rounded-xl bg-gray-50 font-bold p-4 focus:ring-upf-blue focus:border-upf-blue shadow-sm transition" required>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <x-input-label for="purpose" :value="__('Motif pédagogique')" class="text-[10px] font-black uppercase tracking-widest text-gray-400" />
                            <textarea name="purpose" rows="3" class="w-full border-gray-200 rounded-xl bg-gray-50 font-bold p-4 focus:ring-upf-blue focus:border-upf-blue shadow-sm transition" placeholder="{{ __('Expliquez la nature de la session académique...') }}" required></textarea>
                        </div>

                        <div class="pt-4 flex justify-end">
                            <x-primary-button>
                                {{ __('Soumettre la demande') }}
                            </x-primary-button>
                        </div>
                    </form>

                    <!-- Disponibilités Section -->
                    <div class="mt-10 pt-8 border-t border-gray-100" x-show="roomId && date" x-cloak>
                        <h4 class="font-black text-gray-900 text-lg mb-4 italic">📅 {{ __('Planning de la salle le') }} <span x-text="formatDate(date)" class="text-upf-blue"></span></h4>
                        
                        <div x-show="loading" class="py-10 text-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-upf-blue mx-auto"></div>
                            <p class="text-xs text-gray-400 mt-3 font-bold uppercase tracking-widest">{{ __('Vérification des disponibilités...') }}</p>
                        </div>

                        <div x-show="!loading">
                            <template x-if="schedule.length === 0">
                                <div class="bg-emerald-50 border border-emerald-100 p-6 rounded-2xl text-center shadow-sm">
                                    <span class="text-2xl mb-2 block">✨</span>
                                    <p class="text-emerald-800 font-black">{{ __('La salle est totalement libre ce jour-là !') }}</p>
                                    <p class="text-xs text-emerald-600 font-bold mt-1">{{ __('Vous pouvez réserver n\'importe quel créneau.') }}</p>
                                </div>
                            </template>

                            <template x-if="schedule.length > 0">
                                <div class="space-y-3">
                                    <p class="text-xs text-amber-700 bg-amber-50 px-4 py-3 rounded-xl font-bold border border-amber-200 mb-4 inline-block shadow-sm">
                                        ⚠️ {{ __('Attention : Ne choisissez pas un créneau qui chevauche ceux-ci.') }}
                                    </p>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <template x-for="item in schedule" :key="item.start">
                                            <div class="flex items-center gap-4 bg-gray-50 p-4 rounded-xl border border-gray-100 shadow-sm">
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
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    <script>
        function availabilityChecker() {
            return {
                roomId: '',
                date: '{{ date('Y-m-d') }}',
                schedule: [],
                loading: false,
                init() {
                    const select = document.querySelector('select[name="room_id"]');
                    if (select && select.value) {
                        this.roomId = select.value;
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
