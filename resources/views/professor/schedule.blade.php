<x-app-layout>
    <x-slot name="header">
        <x-page-header 
            title="{{ __('Mon Planning d\'Enseignement') }}" 
            subtitle="{{ __('Semaine du :start au :end', ['start' => now()->startOfWeek()->format('d/m'), 'end' => now()->endOfWeek()->format('d/m/Y')]) }}"
            icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>'
        >
            <x-slot name="actions">
                <a href="{{ route('schedules.pdf') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm hover:shadow-md">
                    📄 {{ __('Exporter PDF') }}
                </a>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-6 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Hero --}}
            <div class="bg-gradient-to-br from-amber-600 via-orange-600 to-rose-600 rounded-[2.5rem] p-10 text-white shadow-sm relative overflow-hidden">
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div>
                        <p class="text-[11px] font-black uppercase tracking-[0.3em] text-yellow-200 mb-2">{{ __('Planning Personnel Enseignant') }}</p>
                        <h2 class="text-3xl font-black tracking-tighter">📅 {{ __('Mes Séances de Cours') }}</h2>
                        <p class="text-orange-100 text-xs mt-2 opacity-80">{{ __('Emploi du temps géré exclusivement par l\'administration — mis à jour en temps réel.') }}</p>
                    </div>
                    <div class="flex gap-4">
                        <div class="bg-white/15 backdrop-blur border border-white/20 px-6 py-4 rounded-2xl text-center shadow-inner">
                            <p class="text-2xl font-black">{{ $schedules->count() }}</p>
                            <p class="text-[9px] uppercase font-black text-yellow-200 tracking-widest">{{ __('Séances') }}</p>
                        </div>
                        <div class="bg-white/15 backdrop-blur border border-white/20 px-6 py-4 rounded-2xl text-center shadow-inner">
                            <p class="text-2xl font-black">{{ $totalGroups }}</p>
                            <p class="text-[9px] uppercase font-black text-yellow-200 tracking-widest">{{ __('Groupes') }}</p>
                        </div>
                        <div class="bg-white/15 backdrop-blur border border-white/20 px-6 py-4 rounded-2xl text-center shadow-inner">
                            <p class="text-2xl font-black">{{ number_format($totalHours, 1) }}h</p>
                            <p class="text-[9px] uppercase font-black text-yellow-200 tracking-widest">{{ __('Hebdo') }}</p>
                        </div>
                    </div>
                </div>
                <div class="absolute -bottom-16 -right-16 w-56 h-56 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>
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
                    
                    // Prepare events from PHP
                    const events = @json($schedules->map(function($s) {
                        // Determine color based on group ID for consistency
                        $colors = ['#3b82f6', '#10b981', '#8b5cf6', '#f59e0b', '#f43f5e', '#14b8a6', '#ec4899', '#6366f1'];
                        $color = $colors[$s->group_id % count($colors)];
                        
                        return [
                            'id' => $s->id,
                            'title' => $s->module->name . ' (' . $s->group->name . ')',
                            'start' => $s->date . 'T' . $s->start_time,
                            'end' => $s->date . 'T' . $s->end_time,
                            'backgroundColor' => $color,
                            'borderColor' => $color,
                            'extendedProps' => [
                                'module' => $s->module->name,
                                'group' => $s->group->name,
                                'filiere' => $s->group->filiere->name ?? '—',
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
                            let italicEl = document.createElement('div');
                            let timeText = document.createElement('div');
                            timeText.innerHTML = '<span class="font-bold text-[10px]">' + arg.timeText + '</span>';
                            
                            let titleText = document.createElement('div');
                            titleText.innerHTML = '<span class="font-black text-xs leading-tight">' + arg.event.extendedProps.module + '</span>';
                            
                            let groupText = document.createElement('div');
                            groupText.innerHTML = '<span class="text-[9px] font-bold opacity-90">' + arg.event.extendedProps.group + ' - ' + arg.event.extendedProps.room + '</span>';
                            
                            let arrayOfDomNodes = [ timeText, titleText, groupText ]
                            return { domNodes: arrayOfDomNodes }
                        },
                        eventClick: function(info) {
                            const props = info.event.extendedProps;
                            alert(`{{ __('Module') }}: ${props.module}\n{{ __('Groupe') }}: ${props.group}\n{{ __('Salle') }}: ${props.room}\n{{ __('Durée') }}: ${props.duration}`);
                        },
                        height: 'auto',
                        slotLabelFormat: {
                            hour: 'numeric',
                            minute: '2-digit',
                            omitZeroMinute: false,
                            meridiem: 'short'
                        }
                    });
                    
                    calendar.render();
                });
            </script>
            
            <style>
                /* Override some FullCalendar default styles to match our design system */
                .fc-theme-standard td, .fc-theme-standard th { border-color: #f3f4f6; }
                .fc .fc-toolbar-title { font-weight: 900; font-size: 1.25rem; font-style: italic; color: #1e1b4b; }
                .fc .fc-button-primary { 
                    background-color: #fff !important; 
                    color: #4b5563 !important; 
                    border-color: #e5e7eb !important; 
                    font-weight: 800 !important;
                    font-size: 0.75rem !important;
                    text-transform: uppercase !important;
                    letter-spacing: 0.05em !important;
                    border-radius: 0.75rem !important;
                    padding: 0.5rem 1rem !important;
                    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
                    transition: all 0.2s !important;
                }
                .fc .fc-button-primary:hover {
                    background-color: #f9fafb !important;
                    border-color: #d1d5db !important;
                }
                .fc .fc-button-primary:not(:disabled).fc-button-active, 
                .fc .fc-button-primary:not(:disabled):active {
                    background-color: #003399 !important;
                    color: #fff !important;
                    border-color: #003399 !important;
                }
                .fc-event { border: none !important; border-radius: 0.5rem !important; padding: 0.25rem 0.5rem !important; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05); }
                .fc-timegrid-slot { height: 3rem !important; }
                .fc-col-header-cell-cushion { padding: 0.75rem !important; font-weight: 900 !important; font-size: 0.75rem !important; text-transform: uppercase !important; color: #6b7280 !important; }
                .fc-day-today { background-color: #fffbeb !important; }
            </style>

        </div>
    </div>
</x-app-layout>
