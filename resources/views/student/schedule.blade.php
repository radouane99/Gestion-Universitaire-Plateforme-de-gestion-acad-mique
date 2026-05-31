<x-app-layout>
    <x-slot name="header">
        <x-page-header 
            title="{{ __('Mon Emploi du Temps') }}" 
            subtitle="{{ __('Semaine du :start au :end', ['start' => now()->startOfWeek()->format('d/m'), 'end' => now()->endOfWeek()->format('d/m/Y')]) }}"
            icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>'
        >
            <x-slot name="actions">
                <x-primary-button tag="a" href="{{ route('schedules.pdf') }}" class="flex items-center gap-2 text-xs">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    {{ __('Exporter PDF') }}
                </x-primary-button>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-6 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-alert-messages />

            {{-- Hero --}}
            <div class="bg-gradient-to-br from-upf-blue via-upf-navy to-black rounded-[2.5rem] p-10 text-white shadow-sm relative overflow-hidden">
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div>
                        <p class="text-[11px] font-black uppercase tracking-[0.3em] text-upf-magenta mb-2">{{ __('Emploi du Temps Personnel') }}</p>
                        <h2 class="text-3xl font-black tracking-tighter">📅 {{ $group->name ?? __('Mon Groupe') }}</h2>
                        @if($group && $group->filiere)
                            <p class="text-blue-200 mt-1 text-sm font-bold">🏛️ {{ $group->filiere->name }}</p>
                        @endif
                        <p class="text-blue-300 text-xs mt-2 opacity-70">{{ __('Planning hebdomadaire récurrent — mis à jour par l\'administration.') }}</p>
                    </div>
                    <div class="flex gap-4">
                        <div class="bg-white/15 backdrop-blur border border-white/20 px-6 py-4 rounded-2xl text-center shadow-inner">
                            <p class="text-2xl font-black">{{ $schedules->count() }}</p>
                            <p class="text-[9px] uppercase font-black text-blue-200 tracking-widest">{{ __('Séances / sem.') }}</p>
                        </div>
                        <div class="bg-white/15 backdrop-blur border border-white/20 px-6 py-4 rounded-2xl text-center shadow-inner">
                            <p class="text-2xl font-black">{{ $schedules->pluck('module_id')->unique()->count() }}</p>
                            <p class="text-[9px] uppercase font-black text-blue-200 tracking-widest">{{ __('Modules') }}</p>
                        </div>
                    </div>
                </div>
                <div class="absolute -bottom-16 -right-16 w-56 h-56 bg-upf-magenta/10 rounded-full blur-3xl pointer-events-none"></div>
            </div>

            {{-- FullCalendar Container --}}
            <x-card class="p-6" wire:ignore>
                <div id="calendar"></div>
            </x-card>

            {{-- FullCalendar Scripts & Styles --}}
            <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.11/locales/fr.global.min.js"></script>
            
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const calendarEl = document.getElementById('calendar');
                    
                    const events = @json($schedules->map(function($s) {
                        $colors = ['#3b82f6', '#10b981', '#8b5cf6', '#f59e0b', '#f43f5e', '#14b8a6', '#ec4899', '#6366f1'];
                        $color = $colors[$s->module_id % count($colors)];
                        
                        return [
                            'id' => $s->id,
                            'title' => $s->module->name,
                            'start' => $s->date . 'T' . $s->start_time,
                            'end' => $s->date . 'T' . $s->end_time,
                            'backgroundColor' => $color,
                            'borderColor' => $color,
                            'extendedProps' => [
                                'module' => $s->module->name,
                                'professor' => $s->professor?->user?->name ?? '—',
                                'room' => $s->room->name ?? '—',
                                'duration' => round((strtotime($s->end_time) - strtotime($s->start_time)) / 3600, 1) . 'h'
                            ]
                        ];
                    }));

                    const calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: 'timeGridWeek',
                        locale: 'fr',
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                        },
                        buttonText: {
                            today: "{{ __('Aujourd\'hui') }}",
                            month: "{{ __('Mois') }}",
                            week: "{{ __('Semaine') }}",
                            day: "{{ __('Jour') }}",
                            list: "{{ __('Liste') }}"
                        },
                        allDaySlot: false,
                        slotMinTime: '08:00:00',
                        slotMaxTime: '20:00:00',
                        hiddenDays: [0], // Hide Sunday
                        events: events,
                        eventContent: function(arg) {
                            let timeText = document.createElement('div');
                            timeText.innerHTML = '<span class="font-bold text-[10px]">' + arg.timeText + '</span>';
                            
                            let titleText = document.createElement('div');
                            titleText.innerHTML = '<span class="font-black text-xs leading-tight">' + arg.event.extendedProps.module + '</span>';
                            
                            let detailText = document.createElement('div');
                            detailText.innerHTML = '<span class="text-[9px] font-bold opacity-90">👨‍🏫 ' + arg.event.extendedProps.professor + ' | 📍 ' + arg.event.extendedProps.room + '</span>';
                            
                            return { domNodes: [ timeText, titleText, detailText ] }
                        },
                        eventClick: function(info) {
                            const props = info.event.extendedProps;
                            alert(`{{ __('Module') }}: ${props.module}\n{{ __('Professeur') }}: ${props.professor}\n{{ __('Salle') }}: ${props.room}\n{{ __('Durée') }}: ${props.duration}`);
                        },
                        height: 'auto',
                        slotLabelFormat: { hour: 'numeric', minute: '2-digit', omitZeroMinute: false, meridiem: 'short' }
                    });
                    
                    calendar.render();
                });
            </script>
            
            <style>
                .fc-theme-standard td, .fc-theme-standard th { border-color: #f3f4f6; }
                .fc .fc-toolbar-title { font-weight: 900; font-size: 1.25rem; font-style: italic; color: #1e1b4b; }
                .fc .fc-button-primary { background-color: #fff !important; color: #4b5563 !important; border-color: #e5e7eb !important; font-weight: 800 !important; font-size: 0.75rem !important; text-transform: uppercase !important; border-radius: 0.75rem !important; padding: 0.5rem 1rem !important; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05) !important; transition: all 0.2s !important; }
                .fc .fc-button-primary:hover { background-color: #f9fafb !important; border-color: #d1d5db !important; }
                .fc .fc-button-primary:not(:disabled).fc-button-active, .fc .fc-button-primary:not(:disabled):active { background-color: #003399 !important; color: #fff !important; border-color: #003399 !important; }
                .fc-event { border: none !important; border-radius: 0.5rem !important; padding: 0.25rem 0.5rem !important; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05); }
                .fc-timegrid-slot { height: 3rem !important; }
                .fc-col-header-cell-cushion { padding: 0.75rem !important; font-weight: 900 !important; font-size: 0.75rem !important; text-transform: uppercase !important; color: #6b7280 !important; }
                .fc-day-today { background-color: #fffbeb !important; }
            </style>

            @if($schedules->isNotEmpty())
            <x-card class="p-0">
                <div class="px-8 py-5 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <h3 class="font-black text-gray-900 italic">📋 {{ __('Récapitulatif complet') }}</h3>
                    <span class="text-xs text-gray-400 font-bold">{{ $schedules->count() }} {{ __('séances hebdomadaires') }}</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50/50 text-[10px] font-black uppercase tracking-widest text-gray-400">
                            <tr>
                                <th class="px-6 py-4 text-left">{{ __('Jour') }}</th>
                                <th class="px-6 py-4 text-left">{{ __('Horaires') }}</th>
                                <th class="px-6 py-4 text-left">{{ __('Module') }}</th>
                                <th class="px-6 py-4 text-left">{{ __('Professeur') }}</th>
                                <th class="px-6 py-4 text-left">{{ __('Salle') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($schedules as $s)
                            <tr class="hover:bg-gray-50/50 transition-colors {{ $s->day_of_week === $today ? 'bg-blue-50/30' : '' }}">
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded-lg text-xs font-black {{ $s->day_of_week === $today ? 'bg-upf-blue text-white shadow-sm' : 'bg-gray-100 text-gray-700' }}">
                                        {{ $days[$s->day_of_week] ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-bold text-gray-600 text-xs">{{ date('H:i', strtotime($s->start_time)) }} – {{ date('H:i', strtotime($s->end_time)) }}</td>
                                <td class="px-6 py-4 font-black text-gray-900 text-xs">{{ $s->module->name }}</td>
                                <td class="px-6 py-4 text-xs font-bold text-gray-500">{{ $s->professor?->user?->name ?? '—' }}</td>
                                <td class="px-6 py-4"><span class="text-xs font-black text-upf-blue bg-blue-50 px-2 py-1 rounded-lg">{{ $s->room?->name ?? '—' }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>
            @endif

        </div>
    </div>
</x-app-layout>
