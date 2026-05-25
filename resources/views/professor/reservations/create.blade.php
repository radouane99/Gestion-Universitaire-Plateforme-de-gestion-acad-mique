<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('New Infrastructure Request') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-[#F8FAFC]">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden">
                <div class="bg-upf-navy p-10 text-white relative">
                    <h3 class="text-2xl font-black mb-2 italic">Venue Allocation</h3>
                    <p class="text-blue-200 text-sm opacity-80">Request access to specialized laboratories or lecture halls.</p>
                    <div class="absolute top-0 right-0 p-8 opacity-10">
                        <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 20 20"><path d="M10.394 2.827a1 1 0 00-.788 0l-7 3a1 1 0 000 1.846l7 3a1 1 0 00.788 0l7-3a1 1 0 000-1.846l-7-3zM3.11 8.11l7 3a1 1 0 00.78 0l7-3a1 1 0 000-1.414l-7-3a1 1 0 00-.78 0l-7 3a1 1 0 000 1.414z"></path></svg>
                    </div>
                </div>

                @if($errors->any())
                    <div class="m-6 p-4 text-sm text-rose-800 rounded-2xl bg-rose-50 border border-rose-100 font-bold">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('success'))
                    <div class="m-6 p-4 text-sm text-emerald-800 rounded-2xl bg-emerald-50 border border-emerald-100 font-bold">
                        {{ session('success') }}
                    </div>
                @endif

                <div x-data="availabilityChecker()" x-init="init()" class="p-10">
                    <form action="{{ route('professor.reservations.store') }}" method="POST" class="space-y-8">
                        @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-4">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Select Room</label>
                            <select name="room_id" x-model="roomId" @change="fetchAvailability()" class="w-full border-gray-100 rounded-xl bg-gray-50 font-bold p-4 focus:ring-upf-blue" required>
                                <option value="" disabled>-- Choisir une salle --</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}">{{ $room->name }} (Cap: {{ $room->capacity }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-4">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Date of Session</label>
                            <input type="date" name="date" x-model="date" @change="fetchAvailability()" class="w-full border-gray-100 rounded-xl bg-gray-50 font-bold p-4 focus:ring-upf-blue" required min="{{ date('Y-m-d') }}">
                        </div>

                        <div class="space-y-4">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Start Interval</label>
                            <input type="time" name="start_time" class="w-full border-gray-100 rounded-xl bg-gray-50 font-bold p-4 focus:ring-upf-blue" required>
                        </div>

                        <div class="space-y-4">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">End Interval</label>
                            <input type="time" name="end_time" class="w-full border-gray-100 rounded-xl bg-gray-50 font-bold p-4 focus:ring-upf-blue" required>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Pedagogical Purpose</label>
                        <textarea name="purpose" rows="3" class="w-full border-gray-100 rounded-xl bg-gray-50 font-bold p-4 focus:ring-upf-blue" placeholder="Explain the nature of the academic session..." required></textarea>
                    </div>

                        <div class="pt-6">
                            <button type="submit" class="w-full py-5 bg-upf-magenta text-white rounded-2xl font-black shadow-xl hover:bg-upf-blue hover:scale-[1.02] transform transition-all duration-300">
                                Submit Infrastructure Request
                            </button>
                        </div>
                    </form>

                    <!-- Disponibilités Section -->
                    <div class="mt-12 pt-10 border-t border-gray-100" x-show="roomId && date" x-cloak>
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
                                    <p class="text-xs text-emerald-600 font-bold mt-1">Vous pouvez réserver n'importe quel créneau.</p>
                                </div>
                            </template>

                            <template x-if="schedule.length > 0">
                                <div class="space-y-3">
                                    <p class="text-xs text-amber-600 bg-amber-50 px-4 py-2 rounded-xl font-bold border border-amber-100 mb-4 inline-block">
                                        ⚠️ Attention : Ne choisissez pas un créneau qui chevauche ceux-ci.
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

    <script>
        function availabilityChecker() {
            return {
                roomId: '',
                date: '{{ date('Y-m-d') }}',
                schedule: [],
                loading: false,
                init() {
                    // Check if room is already selected (e.g., from old input)
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
