<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tight italic">
                📅 Calendrier des Examens
            </h2>
            <a href="{{ route('admin.exams.index') }}"
               class="bg-upf-blue hover:bg-upf-navy text-white font-black py-2 px-5 rounded-xl shadow-lg transition-all hover:scale-105 text-sm uppercase tracking-widest">
                ← Retour aux Examens
            </a>
        </div>
    </x-slot>

    {{-- FullCalendar CDN --}}
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>

    <div class="py-8 bg-[#F8FAFC] min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Filter Bar --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-4">
                <div class="flex flex-wrap items-end gap-4">

                    {{-- Filière --}}
                    <div class="flex flex-col min-w-[160px]">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Filière</label>
                        <select id="filter_filiere"
                                class="text-sm border-gray-200 focus:border-indigo-400 focus:ring-indigo-400 rounded-xl py-1.5 px-3 bg-gray-50">
                            <option value="">Toutes les filières</option>
                            @foreach($filieres as $f)
                                <option value="{{ $f->id }}">{{ $f->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Groupe (chargé via AJAX) --}}
                    <div class="flex flex-col min-w-[160px]">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Groupe</label>
                        <select id="filter_group"
                                class="text-sm border-gray-200 focus:border-indigo-400 focus:ring-indigo-400 rounded-xl py-1.5 px-3 bg-gray-50">
                            <option value="">Tous les groupes</option>
                        </select>
                    </div>

                    {{-- Module (chargé via AJAX) --}}
                    <div class="flex flex-col min-w-[180px]">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Module</label>
                        <select id="filter_module"
                                class="text-sm border-gray-200 focus:border-indigo-400 focus:ring-indigo-400 rounded-xl py-1.5 px-3 bg-gray-50">
                            <option value="">Tous les modules</option>
                        </select>
                    </div>

                    {{-- Reset --}}
                    <button id="resetFilters"
                            class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-black py-2 px-4 rounded-xl text-sm transition-all hover:scale-105">
                        🔄 Réinitialiser
                    </button>
                </div>
            </div>

            {{-- Calendar Container --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div id="calendar"></div>
            </div>

        </div>
    </div>

    {{-- Inline custom styles --}}
    <style>
        #calendar .fc-toolbar-title {
            font-family: 'Inter', sans-serif;
            font-weight: 800;
            color: #1e3a5f;
        }
        #calendar .fc-button-primary {
            background-color: #4f46e5 !important;
            border-color: #4338ca !important;
            font-weight: 700;
            border-radius: 10px !important;
        }
        #calendar .fc-button-primary:hover {
            background-color: #4338ca !important;
        }
        #calendar .fc-event {
            border-radius: 8px !important;
            border: none !important;
            padding: 2px 6px;
            font-weight: 700;
            font-size: 0.78rem;
            transition: transform 0.15s, opacity 0.15s;
        }
        #calendar .fc-event:hover {
            transform: scale(1.04);
            opacity: 0.92;
        }
        #calendar .fc-daygrid-day-number,
        #calendar .fc-col-header-cell-cushion {
            color: #374151;
            font-weight: 700;
        }
        #calendar .fc-day-today {
            background-color: #eef2ff !important;
        }
    </style>

    {{-- FullCalendar JS --}}
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const calendarEl = document.getElementById('calendar');

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'fr',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listWeek'
            },
            height: 'auto',
            eventClick: function(info) {
                if (info.event.url) {
                    info.jsEvent.preventDefault();
                    window.location.href = info.event.url;
                }
            },
            events: function (info, successCallback, failureCallback) {
                const params = new URLSearchParams();
                const filiere = document.getElementById('filter_filiere').value;
                const group   = document.getElementById('filter_group').value;
                const module  = document.getElementById('filter_module').value;
                if (filiere) params.append('filiere_id', filiere);
                if (group)   params.append('group_id', group);
                if (module)  params.append('module_id', module);

                fetch(`{{ route('admin.exams.api.calendar') }}?${params.toString()}`)
                    .then(res => res.json())
                    .then(data => successCallback(data))
                    .catch(err => failureCallback(err));
            }
        });

        calendar.render();

        // ── Filter handlers ──────────────────────────────────
        document.getElementById('resetFilters').addEventListener('click', () => {
            document.getElementById('filter_filiere').value = '';
            document.getElementById('filter_group').innerHTML = '<option value="">Tous les groupes</option>';
            document.getElementById('filter_module').innerHTML = '<option value="">Tous les modules</option>';
            calendar.refetchEvents();
        });

        // Filière → charge groupes
        document.getElementById('filter_filiere').addEventListener('change', function () {
            const filiereId = this.value;
            const groupSel  = document.getElementById('filter_group');
            groupSel.innerHTML = '<option value="">Tous les groupes</option>';
            document.getElementById('filter_module').innerHTML = '<option value="">Tous les modules</option>';

            if (filiereId) {
                fetch(`{{ url('admin/api/filieres') }}/${filiereId}/groups`)
                    .then(r => r.json())
                    .then(groups => {
                        groups.forEach(g => {
                            const o = document.createElement('option');
                            o.value = g.id; o.textContent = g.name;
                            groupSel.appendChild(o);
                        });
                    });
            }
            calendar.refetchEvents();
        });

        // Groupe → charge modules
        document.getElementById('filter_group').addEventListener('change', function () {
            const groupId  = this.value;
            const modSel   = document.getElementById('filter_module');
            modSel.innerHTML = '<option value="">Tous les modules</option>';

            if (groupId) {
                fetch(`{{ url('admin/api/groups') }}/${groupId}/modules`)
                    .then(r => r.json())
                    .then(mods => {
                        mods.forEach(m => {
                            const o = document.createElement('option');
                            o.value = m.id; o.textContent = m.name;
                            modSel.appendChild(o);
                        });
                    });
            }
            calendar.refetchEvents();
        });

        // Module → refetch
        document.getElementById('filter_module').addEventListener('change', () => calendar.refetchEvents());
    });
    </script>

</x-app-layout>
