// public/js/calendar.js

document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        themeSystem: 'standard',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: function (info, successCallback, failureCallback) {
            const params = new URLSearchParams();
            const filiere = document.getElementById('filiere_id').value;
            const group   = document.getElementById('group_id').value;
            const module  = document.getElementById('module_id').value;
            if (filiere) params.append('filiere_id', filiere);
            if (group)   params.append('group_id', group);
            if (module)  params.append('module_id', module);

            fetch(`/admin/exams/api/calendar?${params.toString()}`)
                .then(res => res.json())
                .then(data => successCallback(data))
                .catch(err => failureCallback(err));
        }
    });

    calendar.render();

    // ------- Filter handling -------
    const resetBtn = document.getElementById('resetFilters');
    resetBtn.addEventListener('click', () => {
        document.getElementById('filiere_id').value = '';
        document.getElementById('group_id').value   = '';
        document.getElementById('module_id').value  = '';
        calendar.refetchEvents();
    });

    // Load groups when a filière is chosen
    document.getElementById('filiere_id').addEventListener('change', function () {
        const filiereId = this.value;
        const groupSelect = document.getElementById('group_id');
        groupSelect.innerHTML = '<option value="">Toutes</option>';
        if (!filiereId) {
            calendar.refetchEvents();
            return;
        }
        fetch(`/api/filieres/${filiereId}/groups`)
            .then(r => r.json())
            .then(groups => {
                groups.forEach(g => {
                    const opt = document.createElement('option');
                    opt.value = g.id;
                    opt.textContent = g.name;
                    groupSelect.appendChild(opt);
                });
            })
            .then(() => calendar.refetchEvents());
    });

    // Load modules when a group is chosen
    document.getElementById('group_id').addEventListener('change', function () {
        const groupId = this.value;
        const moduleSelect = document.getElementById('module_id');
        moduleSelect.innerHTML = '<option value="">Tous</option>';
        if (!groupId) {
            calendar.refetchEvents();
            return;
        }
        fetch(`/api/groups/${groupId}/modules`)
            .then(r => r.json())
            .then(mods => {
                mods.forEach(m => {
                    const opt = document.createElement('option');
                    opt.value = m.id;
                    opt.textContent = m.name;
                    moduleSelect.appendChild(opt);
                });
            })
            .then(() => calendar.refetchEvents());
    });

    // Refetch when any filter changes
    ['filiere_id', 'group_id', 'module_id'].forEach(id => {
        document.getElementById(id).addEventListener('change', () => calendar.refetchEvents());
    });
});
