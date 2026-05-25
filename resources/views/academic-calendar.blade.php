<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tighter italic">
            🗓️ Calendrier Académique
        </h2>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

    <style>
        /* FullCalendar Core Reset */
        .fc { font-family: 'Inter', 'Outfit', system-ui, sans-serif; }
        .fc-theme-standard td, .fc-theme-standard th { border-color: #f1f5f9; }
        .fc-theme-standard .fc-scrollgrid { border: none !important; }

        /* Header Toolbar */
        .fc-header-toolbar { margin-bottom: 1.5rem !important; }
        .fc-toolbar-title { 
            font-weight: 900 !important; 
            font-size: 1.4rem !important; 
            letter-spacing: -1px; 
            color: #003399 !important;
            text-transform: uppercase;
        }

        /* Navigation Buttons */
        .fc-button-primary { 
            background-color: #003399 !important; 
            border-color: #003399 !important; 
            border-radius: 10px !important; 
            font-weight: 800 !important; 
            font-size: 11px !important;
            padding: 8px 16px !important;
            letter-spacing: 0.5px;
            text-transform: uppercase !important;
            box-shadow: 0 2px 8px rgba(0,51,153,0.15) !important;
            transition: all 0.2s !important;
        }
        .fc-button-primary:hover { 
            background-color: #002070 !important; 
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,51,153,0.3) !important;
        }
        .fc-button-active {
            background-color: #B00D5D !important;
            border-color: #B00D5D !important;
        }

        /* Column Headers (Mon, Tue, etc.) */
        .fc-col-header-cell { 
            background: #f8fafc; 
            border-bottom: 2px solid #e2e8f0 !important;
        }
        .fc-col-header-cell-cushion {
            font-weight: 900 !important;
            font-size: 12px !important;
            text-transform: uppercase !important;
            letter-spacing: 1px;
            color: #64748b !important;
            padding: 12px 8px !important;
            text-decoration: none !important;
        }
        .fc-day-today .fc-col-header-cell-cushion { color: #003399 !important; }

        /* Today Highlight */
        .fc-day-today { background: rgba(0, 51, 153, 0.03) !important; }

        /* Time Axis */
        .fc-timegrid-slot-label-cushion {
            font-size: 11px !important;
            font-weight: 700 !important;
            color: #94a3b8 !important;
            text-transform: uppercase !important;
        }

        /* Events */
        .fc-timegrid-event {
            border-radius: 12px !important;
            border: none !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.12) !important;
            cursor: pointer;
            transition: all 0.25s ease !important;
            margin: 1px 2px !important;
        }
        .fc-timegrid-event:hover {
            transform: scale(1.03) !important;
            box-shadow: 0 8px 24px rgba(0,0,0,0.2) !important;
            z-index: 100 !important;
        }
        .fc-event-main { padding: 0 !important; overflow: hidden; }

        /* Modal Overlay */
        #event-modal-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }
        #event-modal-overlay.show { display: flex; }
        #event-modal { 
            background: white; 
            border-radius: 24px; 
            padding: 32px; 
            max-width: 420px; 
            width: 90%;
            box-shadow: 0 25px 60px rgba(0,0,0,0.25);
            animation: slideUp 0.3s ease;
        }
        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>

    <div class="py-8 bg-[#F8FAFC] min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Stats Bar -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-2xl">📅</div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Séances / Semaine</p>
                        <p id="stat-weekly" class="text-2xl font-black text-upf-blue">—</p>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 bg-pink-50 rounded-xl flex items-center justify-center text-2xl">📚</div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Modules</p>
                        <p id="stat-modules" class="text-2xl font-black text-upf-magenta">—</p>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center text-2xl">⏱️</div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">H / Semaine</p>
                        <p id="stat-hours" class="text-2xl font-black text-emerald-600">—</p>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center text-2xl">🏫</div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Salles</p>
                        <p id="stat-rooms" class="text-2xl font-black text-amber-600">—</p>
                    </div>
                </div>
            </div>

            <!-- Calendar Card -->
            <div class="bg-white rounded-[2rem] shadow-xl border border-gray-100 overflow-hidden">
                <div class="border-b border-gray-100 px-8 py-5 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-black text-upf-blue italic tracking-tight">Mon Emploi du Temps</h3>
                        <p class="text-xs text-gray-400 font-semibold">Cliquez sur un cours pour voir les détails</p>
                    </div>
                    <div id="loading-badge" class="flex items-center gap-2 text-xs font-bold text-gray-400">
                        <span class="w-2 h-2 rounded-full bg-gray-300 animate-pulse"></span> Chargement...
                    </div>
                </div>
                <div class="p-6">
                    <div id="calendar" class="min-h-[680px]"></div>
                </div>
            </div>

        </div>
    </div>

    <!-- Event Detail Modal -->
    <div id="event-modal-overlay">
        <div id="event-modal">
            <div class="flex justify-between items-start mb-6">
                <div id="modal-color-dot" class="w-4 h-4 rounded-full mt-1 mr-3 flex-shrink-0"></div>
                <div class="flex-1">
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1" id="modal-time">—</p>
                    <h3 class="text-xl font-black text-gray-900 leading-tight" id="modal-title">—</h3>
                </div>
                <button onclick="closeModal()" class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-red-50 hover:text-red-500 flex items-center justify-center font-black text-gray-400 transition-colors text-lg ml-4">✕</button>
            </div>
            <div class="space-y-3">
                <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-xl">
                    <span class="text-xl">👨‍🏫</span>
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-widest text-blue-400">Professeur</p>
                        <p class="text-sm font-black text-blue-800" id="modal-prof">—</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 bg-pink-50 rounded-xl">
                    <span class="text-xl">📍</span>
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-widest text-pink-400">Salle</p>
                        <p class="text-sm font-black text-pink-800" id="modal-room">—</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 bg-emerald-50 rounded-xl">
                    <span class="text-xl">👥</span>
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-widest text-emerald-400">Groupe</p>
                        <p class="text-sm font-black text-emerald-800" id="modal-group">—</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');

        const COLORS = [
            { bg: '#003399', light: '#dbeafe' },
            { bg: '#B00D5D', light: '#fce7f3' },
            { bg: '#059669', light: '#d1fae5' },
            { bg: '#d97706', light: '#fef3c7' },
            { bg: '#7c3aed', light: '#ede9fe' },
            { bg: '#0891b2', light: '#cffafe' },
            { bg: '#dc2626', light: '#fee2e2' },
        ];

        function getColor(str) {
            let hash = 0;
            for (let i = 0; i < str.length; i++) hash = str.charCodeAt(i) + ((hash << 5) - hash);
            return COLORS[Math.abs(hash) % COLORS.length];
        }

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'timeGridWeek',
            slotMinTime: '08:00:00',
            slotMaxTime: '20:00:00',
            allDaySlot: false,
            expandRows: true,
            height: 700,
            slotDuration: '00:30:00',
            nowIndicator: true,
            locale: 'fr',
            firstDay: 1,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'timeGridWeek,timeGridDay'
            },
            buttonText: {
                today: "Auj.",
                week: "Semaine",
                day: "Jour"
            },
            eventContent: function(arg) {
                const color = getColor(arg.event.extendedProps.moduleName || '');
                return {
                    html: `
                        <div style="background: ${color.bg}; height:100%; padding: 8px 10px; border-radius: 12px; overflow: hidden; display:flex; flex-direction:column; justify-content:space-between;">
                            <div>
                                <div style="font-size:9px; font-weight:900; color:rgba(255,255,255,0.7); text-transform:uppercase; letter-spacing:1px; margin-bottom:4px;">${arg.timeText}</div>
                                <div style="font-size:12px; font-weight:900; color:white; line-height:1.3; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden;">${arg.event.extendedProps.moduleName}</div>
                            </div>
                            <div style="display:flex; align-items:center; gap:4px; margin-top:6px;">
                                <div style="width:6px; height:6px; border-radius:50%; background:rgba(255,255,255,0.5);"></div>
                                <div style="font-size:10px; font-weight:700; color:rgba(255,255,255,0.85); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${arg.event.extendedProps.roomName}</div>
                            </div>
                        </div>
                    `
                };
            },
            eventClick: function(info) {
                const p = info.event.extendedProps;
                const color = getColor(p.moduleName || '');
                document.getElementById('modal-title').textContent = p.moduleName || info.event.title;
                document.getElementById('modal-time').textContent = info.event.startStr ? formatTime(info.event.start) + ' → ' + formatTime(info.event.end) : '';
                document.getElementById('modal-prof').textContent = p.profName || 'Non assigné';
                document.getElementById('modal-room').textContent = p.roomName || '—';
                document.getElementById('modal-group').textContent = p.groupName || '—';
                document.getElementById('modal-color-dot').style.background = color.bg;
                document.getElementById('event-modal-overlay').classList.add('show');
            },
            events: function(fetchInfo, successCallback, failureCallback) {
                fetch('/api/schedule', {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + '{{ Auth::user()->createToken("cal-view")->plainTextToken }}'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.status === 'success' && Array.isArray(data.schedules)) {
                        const color = getColor(data.schedules[0]?.module?.name || 'default');
                        
                        // Stats
                        const uniqueModules = new Set(data.schedules.map(s => s.module_id || s.module?.id)).size;
                        const uniqueRooms = new Set(data.schedules.map(s => s.room?.name)).size;
                        let totalHours = 0;

                        const events = data.schedules.map(item => {
                            const fcDay = item.day_of_week == 7 ? 0 : item.day_of_week;
                            const c = getColor(item.module?.name || '');

                            // Calculate hours
                            if (item.start_time && item.end_time) {
                                const [sh, sm] = item.start_time.split(':').map(Number);
                                const [eh, em] = item.end_time.split(':').map(Number);
                                totalHours += ((eh * 60 + em) - (sh * 60 + sm)) / 60;
                            }

                            return {
                                title: item.module?.name || 'Cours',
                                startTime: item.start_time,
                                endTime: item.end_time,
                                daysOfWeek: [fcDay],
                                backgroundColor: c.bg,
                                borderColor: 'transparent',
                                extendedProps: {
                                    moduleName: item.module?.name || 'Cours',
                                    roomName: item.room?.name || 'Salle non définie',
                                    profName: item.professor?.user?.name || 'Non assigné',
                                    groupName: item.group?.name || '—',
                                }
                            };
                        });

                        // Update stats
                        document.getElementById('stat-weekly').textContent = data.schedules.length;
                        document.getElementById('stat-modules').textContent = uniqueModules;
                        document.getElementById('stat-hours').textContent = totalHours.toFixed(0) + 'h';
                        document.getElementById('stat-rooms').textContent = uniqueRooms;
                        document.getElementById('loading-badge').innerHTML = '<span class="w-2 h-2 rounded-full bg-emerald-400"></span> <span class="text-emerald-600">' + events.length + ' cours chargés</span>';

                        successCallback(events);
                    } else {
                        successCallback([]);
                        document.getElementById('loading-badge').innerHTML = '<span class="text-gray-400">Aucun cours trouvé</span>';
                    }
                })
                .catch(err => {
                    console.error(err);
                    failureCallback(err);
                });
            }
        });

        calendar.render();
    });

    function formatTime(date) {
        if (!date) return '';
        return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
    }

    function closeModal() {
        document.getElementById('event-modal-overlay').classList.remove('show');
    }
    document.getElementById('event-modal-overlay').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
    </script>

</x-app-layout>
