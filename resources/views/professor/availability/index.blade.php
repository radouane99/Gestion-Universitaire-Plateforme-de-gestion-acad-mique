<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tight italic">
                📅 Mes Disponibilités — Examens
            </h2>
            <span class="text-xs font-black text-gray-400 uppercase tracking-widest">
                {{ now()->format('d/m/Y') }}
            </span>
        </div>
    </x-slot>

    <div class="py-10 bg-[#F8FAFC]">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <x-alert-messages />

            {{-- Hero Info Card --}}
            <div class="bg-gradient-to-br from-upf-blue via-indigo-700 to-purple-800 rounded-[2.5rem] p-10 text-white shadow-2xl relative overflow-hidden">
                <div class="relative z-10">
                    <p class="text-[11px] font-black uppercase tracking-[0.3em] text-purple-300 mb-2">Semaine d'Examens</p>
                    <h2 class="text-3xl font-black tracking-tighter mb-3">Soumettez vos disponibilités</h2>
                    <p class="text-blue-200 text-sm leading-relaxed max-w-xl">
                        Indiquez les jours où vous êtes disponible pour surveiller des examens. <br>
                        <span class="font-black text-white">⚠️ Minimum 3 jours obligatoires</span> par soumission.
                    </p>
                </div>
                <div class="absolute -bottom-10 -right-10 w-48 h-48 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                {{-- FORM --}}
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                    <h3 class="font-black text-gray-900 text-lg mb-6 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-full bg-upf-blue text-white flex items-center justify-center text-sm">✏️</span>
                        Nouvelle soumission
                    </h3>

                    <form action="{{ route('professor.availability.store') }}" method="POST" id="availabilityForm">
                        @csrf

                        <div class="mb-6">
                            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Libellé de la semaine d'examen</label>
                            <input type="text" name="exam_week" required
                                placeholder="Ex: Semaine d'examens Juin 2026"
                                class="w-full rounded-xl border-gray-200 focus:ring-upf-magenta focus:border-upf-magenta text-sm font-bold shadow-sm"
                                value="{{ old('exam_week') }}">
                            @error('exam_week') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-6">
                            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">
                                Jours disponibles <span class="text-upf-magenta">(min. 3)</span>
                            </label>
                            <div class="space-y-2" id="dateList">
                                <div class="flex gap-2 date-row">
                                    <input type="date" name="dates[]" required
                                        min="{{ now()->addDay()->format('Y-m-d') }}"
                                        class="flex-1 rounded-xl border-gray-200 focus:ring-upf-magenta focus:border-upf-magenta text-sm font-bold shadow-sm">
                                    <button type="button" onclick="removeDate(this)" class="text-red-400 hover:text-red-600 p-2 hover:bg-red-50 rounded-xl transition-colors text-lg hidden">✕</button>
                                </div>
                                <div class="flex gap-2 date-row">
                                    <input type="date" name="dates[]" required
                                        min="{{ now()->addDay()->format('Y-m-d') }}"
                                        class="flex-1 rounded-xl border-gray-200 focus:ring-upf-magenta focus:border-upf-magenta text-sm font-bold shadow-sm">
                                    <button type="button" onclick="removeDate(this)" class="text-red-400 hover:text-red-600 p-2 hover:bg-red-50 rounded-xl transition-colors text-lg hidden">✕</button>
                                </div>
                                <div class="flex gap-2 date-row">
                                    <input type="date" name="dates[]" required
                                        min="{{ now()->addDay()->format('Y-m-d') }}"
                                        class="flex-1 rounded-xl border-gray-200 focus:ring-upf-magenta focus:border-upf-magenta text-sm font-bold shadow-sm">
                                    <button type="button" onclick="removeDate(this)" class="text-red-400 hover:text-red-600 p-2 hover:bg-red-50 rounded-xl transition-colors text-lg hidden">✕</button>
                                </div>
                            </div>
                            @error('dates') <p class="text-red-500 text-xs mt-2 font-bold">{{ $message }}</p> @enderror

                            <button type="button" onclick="addDate()" class="mt-3 flex items-center gap-2 text-upf-blue font-black text-sm hover:text-upf-magenta transition-colors">
                                <span class="w-6 h-6 rounded-full bg-blue-50 flex items-center justify-center text-xs">+</span>
                                Ajouter un autre jour
                            </button>
                        </div>

                        <div class="mb-6">
                            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Notes (optionnel)</label>
                            <textarea name="notes" rows="3"
                                placeholder="Contraintes particulières, préférences horaires..."
                                class="w-full rounded-xl border-gray-200 focus:ring-upf-magenta focus:border-upf-magenta text-sm font-bold shadow-sm">{{ old('notes') }}</textarea>
                        </div>

                        <button type="submit" class="w-full bg-upf-blue hover:bg-upf-navy text-white font-black py-3 px-6 rounded-2xl shadow-lg transition-all hover:scale-[1.01] uppercase tracking-widest text-sm">
                            ✅ Soumettre mes disponibilités
                        </button>
                    </form>
                </div>

                {{-- Existing Availabilities --}}
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                    <h3 class="font-black text-gray-900 text-lg mb-6 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm">📆</span>
                        Mes disponibilités soumises
                    </h3>

                    @if($byWeek->isEmpty())
                        <div class="text-center py-12">
                            <div class="text-4xl mb-3">📭</div>
                            <p class="text-gray-400 font-bold text-sm">Aucune disponibilité soumise.</p>
                        </div>
                    @else
                        <div class="space-y-6 max-h-[500px] overflow-y-auto pr-2">
                            @foreach($byWeek as $weekLabel => $weekDates)
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">{{ $weekLabel }}</p>
                                    <div class="space-y-2">
                                        @foreach($weekDates as $av)
                                            <div class="flex items-center justify-between p-3 rounded-2xl border {{ $av->available_date->isPast() ? 'border-gray-100 bg-gray-50 opacity-60' : 'border-emerald-100 bg-emerald-50/40' }}">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-10 h-10 rounded-xl {{ $av->available_date->isPast() ? 'bg-gray-200' : 'bg-emerald-500' }} text-white flex flex-col items-center justify-center text-center leading-tight">
                                                        <span class="text-[9px] uppercase font-black">{{ $av->available_date->isoFormat('ddd') }}</span>
                                                        <span class="text-sm font-black">{{ $av->available_date->format('d') }}</span>
                                                    </div>
                                                    <div>
                                                        <p class="font-black text-gray-800 text-sm">{{ $av->available_date->isoFormat('D MMMM YYYY') }}</p>
                                                        @if($av->notes)
                                                            <p class="text-xs text-gray-400">{{ $av->notes }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                                @if(!$av->available_date->isPast())
                                                    <form action="{{ route('professor.availability.destroy', $av) }}" method="POST" onsubmit="return confirm('Supprimer cette disponibilité ?');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="text-red-400 hover:text-red-600 p-2 rounded-xl hover:bg-red-50 transition-colors">✕</button>
                                                    </form>
                                                @else
                                                    <span class="text-[9px] font-black text-gray-300 uppercase tracking-widest">Passé</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <script>
        function addDate() {
            const list = document.getElementById('dateList');
            const row = document.createElement('div');
            row.className = 'flex gap-2 date-row';
            row.innerHTML = `
                <input type="date" name="dates[]" required
                    min="{{ now()->addDay()->format('Y-m-d') }}"
                    class="flex-1 rounded-xl border-gray-200 focus:ring-upf-magenta focus:border-upf-magenta text-sm font-bold shadow-sm">
                <button type="button" onclick="removeDate(this)" class="text-red-400 hover:text-red-600 p-2 hover:bg-red-50 rounded-xl transition-colors text-lg">✕</button>
            `;
            list.appendChild(row);
        }

        function removeDate(btn) {
            const rows = document.querySelectorAll('.date-row');
            if (rows.length <= 3) {
                alert('Vous devez conserver au minimum 3 jours de disponibilité.');
                return;
            }
            btn.closest('.date-row').remove();
        }
    </script>
</x-app-layout>
