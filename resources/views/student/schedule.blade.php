<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tight italic">
                Mon Emploi du Temps
            </h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('schedules.pdf') }}" class="bg-gray-800 hover:bg-black text-white font-bold py-2 px-4 rounded-xl shadow transition-colors text-xs flex items-center gap-2">
                    📄 Exporter PDF
                </a>
                <span class="text-xs font-black text-gray-400 uppercase tracking-widest">
                    Semaine du {{ now()->startOfWeek()->format('d/m') }} au {{ now()->endOfWeek()->format('d/m/Y') }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-10 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Hero --}}
            <div class="bg-gradient-to-br from-upf-blue via-upf-navy to-black rounded-[2.5rem] p-10 text-white shadow-2xl relative overflow-hidden">
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div>
                        <p class="text-[11px] font-black uppercase tracking-[0.3em] text-upf-magenta mb-2">Emploi du Temps Personnel</p>
                        <h2 class="text-3xl font-black tracking-tighter">📅 {{ $group->name ?? 'Mon Groupe' }}</h2>
                        @if($group && $group->filiere)
                            <p class="text-blue-200 mt-1 text-sm font-bold">🏛️ {{ $group->filiere->name }}</p>
                        @endif
                        <p class="text-blue-300 text-xs mt-2 opacity-70">Planning hebdomadaire récurrent — mis à jour par l'administration.</p>
                    </div>
                    <div class="flex gap-4">
                        <div class="bg-white/15 backdrop-blur border border-white/20 px-6 py-4 rounded-2xl text-center">
                            <p class="text-2xl font-black">{{ $schedules->count() }}</p>
                            <p class="text-[9px] uppercase font-black text-blue-200 tracking-widest">Séances / sem.</p>
                        </div>
                        <div class="bg-white/15 backdrop-blur border border-white/20 px-6 py-4 rounded-2xl text-center">
                            <p class="text-2xl font-black">{{ $schedules->pluck('module_id')->unique()->count() }}</p>
                            <p class="text-[9px] uppercase font-black text-blue-200 tracking-widest">Modules</p>
                        </div>
                    </div>
                </div>
                <div class="absolute -bottom-16 -right-16 w-56 h-56 bg-upf-magenta/10 rounded-full blur-3xl pointer-events-none"></div>
            </div>

            {{-- Weekly Calendar Grid --}}
            @php
                $days = [1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 4 => 'Jeudi', 5 => 'Vendredi', 6 => 'Samedi'];
                $today = (int) now()->dayOfWeekIso;
                $colors = ['bg-blue-500', 'bg-emerald-500', 'bg-violet-500', 'bg-amber-500', 'bg-rose-500', 'bg-teal-500', 'bg-pink-500', 'bg-indigo-500'];
                $moduleColors = [];
                $colorIdx = 0;
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($days as $dayNum => $dayName)
                @php $daySessions = $byDay->get($dayNum, collect())->sortBy('start_time'); @endphp
                <div class="bg-white rounded-[2rem] border shadow-sm overflow-hidden transition-all duration-300
                    {{ $dayNum === $today ? 'border-upf-blue ring-2 ring-upf-blue/20 shadow-lg' : 'border-gray-100 hover:shadow-md' }}">

                    <div class="px-6 py-4 flex items-center justify-between
                        {{ $dayNum === $today ? 'bg-upf-blue text-white' : 'bg-gray-50/70 text-gray-700' }}">
                        <div class="flex items-center gap-3">
                            <span class="font-black text-sm uppercase tracking-wider">{{ $dayName }}</span>
                            @if($dayNum === $today)
                                <span class="text-[9px] font-black uppercase tracking-widest bg-white/20 px-2 py-0.5 rounded-full">Aujourd'hui</span>
                            @endif
                        </div>
                        <span class="text-[10px] font-black opacity-60">{{ $daySessions->count() }} cours</span>
                    </div>

                    <div class="p-4 space-y-3">
                        @forelse($daySessions as $s)
                        @php
                            if (!isset($moduleColors[$s->module_id])) {
                                $moduleColors[$s->module_id] = $colors[$colorIdx % count($colors)];
                                $colorIdx++;
                            }
                            $color = $moduleColors[$s->module_id];
                        @endphp
                        <div class="flex gap-3 p-3 bg-gray-50 rounded-2xl border border-gray-100 hover:bg-white hover:shadow-sm transition-all group">
                            <div class="flex flex-col items-center">
                                <div class="w-1.5 flex-1 rounded-full {{ $color }} opacity-70"></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        {{ date('H:i', strtotime($s->start_time)) }} – {{ date('H:i', strtotime($s->end_time)) }}
                                    </span>
                                    <span class="text-[9px] font-black text-white {{ $color }} px-2 py-0.5 rounded-full">
                                        {{ round((strtotime($s->end_time) - strtotime($s->start_time)) / 3600, 1) }}h
                                    </span>
                                </div>
                                <p class="font-black text-gray-900 text-sm truncate group-hover:text-upf-blue transition-colors">{{ $s->module->name }}</p>
                                <div class="flex items-center gap-3 mt-1.5 text-[9px] font-bold text-gray-400 uppercase tracking-widest">
                                    <span>👨‍🏫 {{ $s->professor?->user?->name ?? '—' }}</span>
                                    <span>📍 {{ $s->room?->name ?? '—' }}</span>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8">
                            <span class="text-2xl">🎈</span>
                            <p class="text-[10px] font-black text-gray-300 uppercase tracking-widest mt-2">Pas de cours</p>
                        </div>
                        @endforelse
                    </div>
                </div>
                @endforeach
            </div>

            @if($schedules->isNotEmpty())
            <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-8 py-5 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <h3 class="font-black text-gray-900 italic">📋 Récapitulatif complet</h3>
                    <span class="text-xs text-gray-400 font-bold">{{ $schedules->count() }} séances hebdomadaires</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50/50 text-[10px] font-black uppercase tracking-widest text-gray-400">
                            <tr>
                                <th class="px-6 py-3 text-left">Jour</th>
                                <th class="px-6 py-3 text-left">Horaires</th>
                                <th class="px-6 py-3 text-left">Module</th>
                                <th class="px-6 py-3 text-left">Professeur</th>
                                <th class="px-6 py-3 text-left">Salle</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($schedules as $s)
                            <tr class="hover:bg-gray-50/50 transition-colors {{ $s->day_of_week === $today ? 'bg-blue-50/30' : '' }}">
                                <td class="px-6 py-3">
                                    <span class="px-2 py-1 rounded-lg text-xs font-black {{ $s->day_of_week === $today ? 'bg-upf-blue text-white' : 'bg-gray-100 text-gray-700' }}">
                                        {{ $days[$s->day_of_week] ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 font-bold text-gray-600 text-xs">{{ date('H:i', strtotime($s->start_time)) }} – {{ date('H:i', strtotime($s->end_time)) }}</td>
                                <td class="px-6 py-3 font-black text-gray-900 text-xs">{{ $s->module->name }}</td>
                                <td class="px-6 py-3 text-xs font-bold text-gray-500">{{ $s->professor?->user?->name ?? '—' }}</td>
                                <td class="px-6 py-3"><span class="text-xs font-black text-upf-blue bg-blue-50 px-2 py-1 rounded-lg">{{ $s->room?->name ?? '—' }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
