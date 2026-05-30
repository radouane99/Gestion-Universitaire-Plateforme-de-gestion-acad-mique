<x-app-layout>
    <x-slot name="header">
        <x-page-header 
            title="{{ __('Saisie des Notes — :module', ['module' => $module->name]) }}" 
            subtitle="{{ __('Groupe :group — :count étudiants', ['group' => $group->name, 'count' => $allStudents->count()]) }}"
            icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>'
        >
            <x-slot name="actions">
                <a href="{{ route('professor.grades.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-xl font-bold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 transition shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    {{ __('Retour aux Modules') }}
                </a>
            </x-slot>
        </x-page-header>
    </x-slot>

    <style>
        .grade-input {
            width: 70px;
            text-align: center;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            padding: 6px 4px;
            font-weight: 800;
            font-size: 14px;
            color: #1e293b;
            background: #f8fafc;
            transition: all 0.2s;
            outline: none;
        }
        .grade-input:focus {
            border-color: #003399;
            background: white;
            box-shadow: 0 0 0 4px rgba(0,51,153,0.1);
        }
        .grade-input.invalid { border-color: #ef4444; background: #fef2f2; }
        .grade-input.filled { border-color: #10b981; background: #f0fdf4; }

        .moyenne-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 64px;
            padding: 6px 12px;
            border-radius: 999px;
            font-weight: 900;
            font-size: 14px;
        }
        .moyenne-pass { background: #d1fae5; color: #065f46; }
        .moyenne-warn { background: #fef3c7; color: #92400e; }
        .moyenne-fail { background: #fee2e2; color: #991b1b; }
        .moyenne-empty { background: #f1f5f9; color: #94a3b8; }

        /* Keyboard nav highlight */
        tr.active-row td { background: rgba(0,51,153,0.03); }
    </style>

    <div class="py-8 bg-[#F8FAFC] min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Hero -->
            <div class="bg-gradient-to-br from-upf-blue via-indigo-700 to-upf-magenta rounded-[2rem] p-8 text-white shadow-sm relative overflow-hidden">
                <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <p class="text-blue-200 text-xs font-black uppercase tracking-widest mb-1">{{ __('Évaluation des Étudiants') }}</p>
                        <h2 class="text-3xl font-black italic leading-tight">{{ $module->name }}</h2>
                        <p class="text-blue-100 text-sm mt-1">{{ __('Groupe') }} : <strong>{{ $group->name }}</strong> · {{ $allStudents->count() }} {{ __('étudiants') }}</p>
                    </div>
                    <div class="flex gap-3">
                        <div class="text-center bg-white/10 backdrop-blur rounded-2xl px-5 py-3">
                            <p class="text-2xl font-black" id="stat-pass">—</p>
                            <p class="text-[10px] font-black uppercase text-blue-200">{{ __('Admis') }}</p>
                        </div>
                        <div class="text-center bg-white/10 backdrop-blur rounded-2xl px-5 py-3">
                            <p class="text-2xl font-black" id="stat-fail">—</p>
                            <p class="text-[10px] font-black uppercase text-blue-200">{{ __('Ajournés') }}</p>
                        </div>
                        <div class="text-center bg-white/10 backdrop-blur rounded-2xl px-5 py-3">
                            <p class="text-2xl font-black" id="stat-avg">—</p>
                            <p class="text-[10px] font-black uppercase text-blue-200">{{ __('Moy. Classe') }}</p>
                        </div>
                    </div>
                </div>
                <div class="absolute -bottom-16 -right-16 w-48 h-48 bg-white/5 rounded-full blur-3xl"></div>
            </div>

            <!-- Toolbar -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex flex-col md:flex-row md:items-center gap-3 justify-between">
                <div class="flex items-center gap-2 text-sm text-gray-500">
                    <span class="font-black">⌨️ {{ __('Navigation rapide') }} :</span>
                    <span class="bg-gray-100 rounded-lg px-2 py-0.5 font-mono text-xs font-bold">{{ __('Tab') }}</span> {{ __('suivant') }}
                    <span class="bg-gray-100 rounded-lg px-2 py-0.5 font-mono text-xs font-bold">{{ __('Shift+Tab') }}</span> {{ __('précédent') }}
                    <span class="bg-gray-100 rounded-lg px-2 py-0.5 font-mono text-xs font-bold">{{ __('Enter') }}</span> {{ __('valider ligne') }}
                </div>
                <div class="flex gap-2">
                    <button type="button" id="fill-demo" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-xl text-sm font-black text-gray-600 transition-all">
                        🎲 {{ __('Remplir aléatoire (test)') }}
                    </button>
                    <button type="button" id="clear-all" class="px-4 py-2 bg-red-50 hover:bg-red-100 rounded-xl text-sm font-black text-red-500 transition-all">
                        🗑 {{ __('Effacer tout') }}
                    </button>
                </div>
            </div>

            <!-- Grade Table -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="border-b border-gray-100 px-8 py-5 flex items-center justify-between">
                    <div>
                        <h3 class="font-black text-gray-900 text-lg italic">{{ __('Registre des Notes') }}</h3>
                        <p class="text-gray-400 text-xs font-semibold">{{ __('Formule : Moy = ((CC1 + CC2) / 2 × 0.4) + (Exam × 0.6)') }}</p>
                    </div>
                    <div class="flex items-center gap-2 text-xs font-bold hidden md:flex">
                        <span class="w-3 h-3 rounded-full bg-emerald-400 inline-block"></span><span class="text-gray-500">≥ 10 {{ __('Admis') }}</span>
                        <span class="w-3 h-3 rounded-full bg-amber-400 inline-block ml-2"></span><span class="text-gray-500">8–10 {{ __('Rattrapage') }}</span>
                        <span class="w-3 h-3 rounded-full bg-red-400 inline-block ml-2"></span><span class="text-gray-500">< 8 {{ __('Ajourné') }}</span>
                    </div>
                </div>

                <form action="{{ route('professor.grades.store') }}" method="POST" id="grades-form">
                    @csrf
                    <input type="hidden" name="module_id" value="{{ $module->id }}">

                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-gray-50/80 border-b border-gray-100">
                                    <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest w-12">#</th>
                                    <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ __('Étudiant') }}</th>
                                    <th class="px-4 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ __('N° Étu.') }}</th>
                                    <th class="px-4 py-4 text-center text-[10px] font-black text-upf-blue uppercase tracking-widest">{{ __('CC1') }} <span class="text-gray-400">/20</span></th>
                                    <th class="px-4 py-4 text-center text-[10px] font-black text-upf-blue uppercase tracking-widest">{{ __('CC2') }} <span class="text-gray-400">/20</span></th>
                                    <th class="px-4 py-4 text-center text-[10px] font-black text-upf-magenta uppercase tracking-widest">{{ __('Examen') }} <span class="text-gray-400">/20</span></th>
                                    <th class="px-6 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ __('Moyenne') }}</th>
                                    <th class="px-4 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ __('Statut') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50" id="grades-body">
                                @foreach($allStudents as $index => $student)
                                @php
                                    $g = $grades[$student->id] ?? null;
                                @endphp
                                <tr class="hover:bg-blue-50/30 transition-colors duration-150 grade-row" data-index="{{ $index }}">
                                    <td class="px-6 py-4 text-gray-400 font-black text-sm">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-upf-blue/10 text-upf-blue flex items-center justify-center font-black text-sm flex-shrink-0">
                                                {{ strtoupper(substr($student->user->name ?? '?', 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="font-black text-gray-900 text-sm leading-none">
                                                    {{ $student->user->name ?? 'N/A' }}
                                                    @if($student->group_id != $group->id)
                                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400 text-[9px] font-black uppercase tracking-wider ml-2">
                                                            ⚠️ {{ __('Crédit') }} ({{ $student->group->name ?? 'Dette' }})
                                                        </span>
                                                    @endif
                                                </p>
                                                <p class="text-[10px] text-gray-400 font-semibold mt-0.5">{{ $student->user->email ?? '' }}</p>
                                            </div>
                                        </div>
                                        <input type="hidden" name="grades[{{ $index }}][student_id]" value="{{ $student->id }}">
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="text-xs font-bold text-gray-400 bg-gray-50 px-2 py-1 rounded-lg">{{ $student->student_number }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <input type="number" step="0.01" min="0" max="20"
                                            name="grades[{{ $index }}][cc1]"
                                            value="{{ $g?->cc1 ?? '' }}"
                                            class="grade-input cc1-input {{ $g?->cc1 ? 'filled' : '' }}"
                                            placeholder="—"
                                            data-row="{{ $index }}">
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <input type="number" step="0.01" min="0" max="20"
                                            name="grades[{{ $index }}][cc2]"
                                            value="{{ $g?->cc2 ?? '' }}"
                                            class="grade-input cc2-input {{ $g?->cc2 ? 'filled' : '' }}"
                                            placeholder="—"
                                            data-row="{{ $index }}">
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <input type="number" step="0.01" min="0" max="20"
                                            name="grades[{{ $index }}][exam]"
                                            value="{{ $g?->exam ?? '' }}"
                                            class="grade-input exam-input {{ $g?->exam ? 'filled' : '' }}"
                                            placeholder="—"
                                            data-row="{{ $index }}">
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="moyenne-badge moyenne-empty moy-display" data-row="{{ $index }}">
                                            {{ $g?->final_grade ? number_format($g->final_grade, 2) : '—' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="status-badge text-[10px] font-black uppercase tracking-wide px-2 py-1 rounded-lg" data-row="{{ $index }}">
                                            @if($g?->final_grade)
                                                @if($g->final_grade >= 10)
                                                    <span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded-lg">✅ {{ __('Admis') }}</span>
                                                @elseif($g->final_grade >= 8)
                                                    <span class="bg-amber-100 text-amber-700 px-2 py-1 rounded-lg">⚠️ {{ __('Ratt.') }}</span>
                                                @else
                                                    <span class="bg-red-100 text-red-700 px-2 py-1 rounded-lg">❌ {{ __('Ajourné') }}</span>
                                                @endif
                                            @else
                                                <span class="text-gray-300">—</span>
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="p-6 border-t border-gray-100 bg-gray-50/50 flex flex-col md:flex-row justify-between items-center gap-4">
                        <p class="text-xs text-gray-400 font-semibold">{{ __('Les moyennes sont calculées automatiquement. Appuyez sur "Enregistrer" pour sauvegarder.') }}</p>
                        <button type="submit" class="px-10 py-4 bg-upf-blue text-white rounded-2xl font-black shadow-lg hover:bg-indigo-700 hover:scale-105 transform transition-all duration-300 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            {{ __('Enregistrer toutes les notes') }}
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('.grade-row');

        function calcMoyenne(cc1, cc2, exam) {
            if (cc1 === '' || cc2 === '' || exam === '') return null;
            return ((parseFloat(cc1) + parseFloat(cc2)) / 2 * 0.4) + (parseFloat(exam) * 0.6);
        }

        function updateRow(rowIndex) {
            const row = document.querySelector(`tr[data-index="${rowIndex}"]`);
            if (!row) return;
            const cc1 = row.querySelector('.cc1-input').value;
            const cc2 = row.querySelector('.cc2-input').value;
            const exam = row.querySelector('.exam-input').value;
            const moy = calcMoyenne(cc1, cc2, exam);
            const moyDisplay = row.querySelector('.moy-display');
            const statusBadge = row.querySelector('.status-badge');

            if (moy !== null) {
                moyDisplay.textContent = moy.toFixed(2);
                moyDisplay.className = 'moyenne-badge moy-display ' + (moy >= 10 ? 'moyenne-pass' : moy >= 8 ? 'moyenne-warn' : 'moyenne-fail');
                statusBadge.innerHTML = moy >= 10
                    ? '<span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded-lg">✅ {{ __('Admis') }}</span>'
                    : moy >= 8
                    ? '<span class="bg-amber-100 text-amber-700 px-2 py-1 rounded-lg">⚠️ {{ __('Ratt.') }}</span>'
                    : '<span class="bg-red-100 text-red-700 px-2 py-1 rounded-lg">❌ {{ __('Ajourné') }}</span>';
            } else {
                moyDisplay.textContent = '—';
                moyDisplay.className = 'moyenne-badge moy-display moyenne-empty';
                statusBadge.innerHTML = '<span class="text-gray-300">—</span>';
            }

            updateStats();
        }

        function updateStats() {
            let pass = 0, fail = 0, total = 0, sum = 0;
            document.querySelectorAll('.moy-display').forEach(el => {
                const v = parseFloat(el.textContent);
                if (!isNaN(v)) {
                    total++;
                    sum += v;
                    if (v >= 10) pass++;
                    else fail++;
                }
            });
            document.getElementById('stat-pass').textContent = pass;
            document.getElementById('stat-fail').textContent = fail;
            document.getElementById('stat-avg').textContent = total > 0 ? (sum / total).toFixed(1) : '—';
        }

        // Attach input listeners
        document.querySelectorAll('.grade-input').forEach(input => {
            input.addEventListener('input', function() {
                const val = parseFloat(this.value);
                if (this.value === '') {
                    this.classList.remove('filled', 'invalid');
                } else if (isNaN(val) || val < 0 || val > 20) {
                    this.classList.add('invalid');
                    this.classList.remove('filled');
                } else {
                    this.classList.add('filled');
                    this.classList.remove('invalid');
                }
                updateRow(parseInt(this.dataset.row));
            });

            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    // Move to next row same column
                    const allSameCol = document.querySelectorAll('.' + this.classList[1]);
                    const idx = Array.from(allSameCol).indexOf(this);
                    if (allSameCol[idx + 1]) allSameCol[idx + 1].focus();
                }
            });
        });

        // Fill random (test)
        const fillDemoBtn = document.getElementById('fill-demo');
        if (fillDemoBtn) {
            fillDemoBtn.addEventListener('click', function() {
                document.querySelectorAll('.grade-row').forEach((row, i) => {
                    const cc1 = (Math.random() * 12 + 8).toFixed(1);
                    const cc2 = (Math.random() * 12 + 8).toFixed(1);
                    const exam = (Math.random() * 12 + 6).toFixed(1);
                    row.querySelector('.cc1-input').value = cc1;
                    row.querySelector('.cc2-input').value = cc2;
                    row.querySelector('.exam-input').value = exam;
                    ['.cc1-input','.cc2-input','.exam-input'].forEach(c => {
                        const el = row.querySelector(c);
                        if(el) {
                            el.classList.add('filled'); 
                            el.classList.remove('invalid');
                        }
                    });
                    updateRow(i);
                });
            });
        }

        // Clear all
        const clearAllBtn = document.getElementById('clear-all');
        if (clearAllBtn) {
            clearAllBtn.addEventListener('click', function() {
                document.querySelectorAll('.grade-input').forEach(el => {
                    el.value = '';
                    el.classList.remove('filled','invalid');
                });
                document.querySelectorAll('.grade-row').forEach((row, i) => updateRow(i));
            });
        }

        // Initial stats
        updateStats();
    });
    </script>
</x-app-layout>
