<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tight italic">
                🗓️ Gestion des Examens & Convocations
            </h2>
            <div class="flex items-center gap-3">
                <form method="GET" action="{{ route('admin.exams.index') }}" id="examFilterForm"
                      class="flex flex-wrap items-center gap-2 bg-indigo-50 px-3 py-2 rounded-2xl border border-indigo-100">
                    @csrf

                    {{-- Filière Selector --}}
                    <div class="flex flex-col">
                        <label class="text-[9px] font-black uppercase tracking-widest text-indigo-400 mb-0.5">Filière</label>
                        <select name="filiere_id" id="ag_filiere" onchange="this.form.method='GET'; this.form.action='{{ route('admin.exams.index') }}'; this.form.submit();"
                                class="text-sm border-indigo-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl py-1.5 pr-6 cursor-pointer">
                            <option value="">Toutes les filières</option>
                            @foreach($filieres as $filiere)
                                <option value="{{ $filiere->id }}" {{ request('filiere_id') == $filiere->id ? 'selected' : '' }}>{{ $filiere->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Session Selector --}}
                    <div class="flex flex-col">
                        <label class="text-[9px] font-black uppercase tracking-widest text-indigo-400 mb-0.5">Session</label>
                        <select name="exam_session_id" id="ag_session" onchange="this.form.method='GET'; this.form.action='{{ route('admin.exams.index') }}'; this.form.submit();"
                                class="text-sm border-indigo-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl py-1.5 pr-6 cursor-pointer">
                            <option value="">-- Session --</option>
                            @foreach($examSessions as $session)
                                @if($session->start_date && $session->end_date)
                                    <option value="{{ $session->id }}" {{ request('exam_session_id') == $session->id ? 'selected' : '' }}>{{ $session->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    {{-- Overwrite option --}}
                    <div class="flex items-center gap-1.5 mt-auto mb-2">
                        <input type="checkbox" name="overwrite" id="ag_overwrite" value="1" {{ request('overwrite') ? 'checked' : '' }}
                               class="rounded border-indigo-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                        <label for="ag_overwrite" class="text-[10px] font-bold text-indigo-700 cursor-pointer select-none">
                            🔄 Écraser
                        </label>
                    </div>

                    <button type="button" onclick="submitAutoGenerate();"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-black py-1.5 px-4 rounded-xl shadow-sm transition-all hover:scale-105 text-xs uppercase tracking-widest mt-auto">
                        ✨ Auto-Générer
                    </button>
                </form>

                <script>
                function submitAutoGenerate() {
                    const session = document.getElementById('ag_session');
                    const filiere = document.getElementById('ag_filiere');
                    const overwrite = document.getElementById('ag_overwrite').checked;
                    if (!session.value) { alert('Veuillez choisir une session.'); return; }
                    const fname = filiere.options[filiere.selectedIndex].text;
                    const sname = session.options[session.selectedIndex].text;
                    const scope = filiere.value ? `la filière "${fname}"` : 'toutes les filières';
                    
                    let confirmMsg = `Générer automatiquement les examens pour ${scope} — session "${sname}" ?`;
                    if (overwrite) {
                        confirmMsg += `\n\n⚠️ ATTENTION: Les examens existants pour cette session et filière seront SUPPRIMÉS et REGÉNÉRÉS.`;
                    }
                    
                    if (confirm(confirmMsg)) {
                        const form = document.getElementById('examFilterForm');
                        form.method = 'POST';
                        form.action = '{{ route('admin.exams.auto_generate') }}';
                        form.submit();
                    }
                }
                </script>


                <a href="{{ route('admin.exams.create') }}" class="bg-upf-blue hover:bg-upf-navy text-white font-black py-2 px-6 rounded-xl shadow-lg transition-all hover:scale-105 text-sm uppercase tracking-widest">
                    + Planifier
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-alert-messages />

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-6 py-4 rounded-2xl font-bold shadow-sm flex items-center gap-3">
                    <span class="text-2xl">✅</span>{{ session('success') }}
                </div>
            @endif

            @if(session('info'))
                <div class="bg-blue-50 border border-blue-200 text-blue-800 px-6 py-4 rounded-2xl font-bold shadow-sm flex items-center gap-3">
                    <span class="text-2xl">ℹ️</span>{{ session('info') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-2xl font-bold shadow-sm flex items-center gap-3">
                    <span class="text-2xl">❌</span>{{ session('error') }}
                </div>
            @endif

            {{-- Exam Cards --}}
            @forelse($exams as $exam)
                @php
                    $isPast = \Carbon\Carbon::parse($exam->date)->isPast();
                    $daysUntil = now()->diffInDays(\Carbon\Carbon::parse($exam->date), false);
                    $hasConvocations = $exam->convocations_count > 0;
                @endphp
                <div class="bg-white rounded-3xl border {{ $isPast ? 'border-gray-100 opacity-75' : 'border-gray-100 shadow-sm' }} overflow-hidden">
                    <div class="flex flex-col md:flex-row">

                        {{-- Date Block --}}
                        <div class="flex items-center justify-center md:w-32 py-6 px-4
                            {{ $exam->type === 'Final' ? 'bg-gradient-to-b from-red-500 to-red-700' : ($exam->type === 'CC2' ? 'bg-gradient-to-b from-amber-500 to-amber-700' : 'bg-gradient-to-b from-upf-blue to-upf-navy') }}
                            text-white text-center">
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-widest opacity-80">{{ \Carbon\Carbon::parse($exam->date)->isoFormat('MMM') }}</p>
                                <p class="text-4xl font-black leading-none">{{ \Carbon\Carbon::parse($exam->date)->format('d') }}</p>
                                <p class="text-[10px] font-black opacity-80 mt-1">{{ \Carbon\Carbon::parse($exam->date)->isoFormat('ddd') }}</p>
                                <span class="mt-2 inline-block bg-white/20 text-white text-[10px] font-black px-2 py-0.5 rounded-full uppercase">{{ $exam->type }}</span>
                            </div>
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 p-6">
                            <div class="flex flex-col md:flex-row justify-between gap-4">
                                <div>
                                    <h3 class="text-xl font-black text-gray-900 tracking-tight">{{ $exam->module->name }}</h3>
                                    <div class="flex flex-wrap items-center gap-3 mt-2 text-sm">
                                        <span class="text-upf-blue font-bold bg-blue-50 px-3 py-1 rounded-full text-xs">👥 {{ $exam->group->name }}</span>
                                        <span class="text-gray-600 font-bold">🕐 {{ date('H:i', strtotime($exam->start_time)) }} — {{ $exam->end_time }}</span>
                                        <span class="text-gray-600 font-bold">⏱ {{ $exam->duration }} min</span>
                                        <span class="text-gray-600 font-bold">📍 {{ $exam->room?->name ?? 'Salle TBD' }}</span>
                                    </div>
                                    <div class="mt-2 text-xs text-gray-400 font-bold">
                                        Surveillants :
                                        @forelse($exam->proctors as $p)
                                            <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded-md">{{ $p->user->name }}</span>
                                        @empty
                                            <span class="text-red-400">Aucun</span>
                                        @endforelse
                                    </div>
                                </div>

                                {{-- Convocation Status --}}
                                <div class="text-right shrink-0">
                                    <div class="inline-flex flex-col items-end gap-1">
                                        <div class="flex flex-col gap-1 items-end">
                                            <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 px-3 py-2 rounded-2xl">
                                                <span class="text-2xl font-black text-gray-900">{{ $exam->convocations_count }}</span>
                                                <span class="text-[9px] text-gray-400 font-black uppercase tracking-widest">Convocat.<br>générées</span>
                                            </div>
                                            @if($exam->convocations_count > 0)
                                                <div class="flex items-center gap-2 mt-1 text-[10px] font-bold">
                                                    <span class="text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">
                                                        ✉️ {{ $exam->sent_convocations_count }} envoyées
                                                    </span>
                                                    @if($exam->convocations_count - $exam->sent_convocations_count > 0)
                                                        <span class="text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">
                                                            ⏳ {{ $exam->convocations_count - $exam->sent_convocations_count }} en attente
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                        @if(!$isPast && !$hasConvocations)
                                            <span class="text-[10px] text-amber-600 font-black">⚠️ Convocations non générées</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex flex-row md:flex-col gap-2 p-4 bg-gray-50/50 border-t md:border-t-0 md:border-l border-gray-100 justify-center items-center">
                            {{-- Generate Convocations --}}
                            <form action="{{ route('admin.exams.generate_convocations', $exam) }}" method="POST">
                                @csrf
                                <input type="hidden" name="send_email" value="0">
                                <button type="submit" title="Générer les convocations (sans email)"
                                    class="flex items-center gap-1 text-xs font-black bg-indigo-50 text-indigo-700 hover:bg-indigo-100 px-3 py-2 rounded-xl transition-colors whitespace-nowrap">
                                    🎫 Générer
                                </button>
                            </form>

                            {{-- Send Emails --}}
                            <form action="{{ route('admin.exams.send_emails', $exam) }}" method="POST" onsubmit="return confirm('Envoyer les convocations par email à tous les étudiants ?');">
                                @csrf
                                <button type="submit" title="Envoyer emails + PDF"
                                    class="flex items-center gap-1 text-xs font-black bg-emerald-50 text-emerald-700 hover:bg-emerald-100 px-3 py-2 rounded-xl transition-colors whitespace-nowrap">
                                    ✉️ Envoyer mails
                                </button>
                            </form>

                            {{-- PDF Admin --}}
                            <a href="{{ route('admin.exams.pdf', $exam) }}"
                               title="Télécharger PDF global"
                               class="flex items-center gap-1 text-xs font-black bg-gray-100 text-gray-700 hover:bg-gray-200 px-3 py-2 rounded-xl transition-colors whitespace-nowrap">
                                📄 PDF global
                            </a>

                            {{-- Attendance Sheet --}}
                            <a href="{{ route('admin.exams.attendance_sheet', $exam) }}"
                               title="Feuille d'émargement pour les surveillants"
                               class="flex items-center gap-1 text-xs font-black bg-amber-50 text-amber-700 hover:bg-amber-100 px-3 py-2 rounded-xl transition-colors whitespace-nowrap">
                                📝 Émargement
                            </a>

                            {{-- Edit --}}
                            <a href="{{ route('admin.exams.edit', $exam) }}"
                               class="flex items-center gap-1 text-xs font-black bg-blue-50 text-upf-blue hover:bg-blue-100 px-3 py-2 rounded-xl transition-colors">
                                ✏️ Modifier
                            </a>

                            {{-- Delete --}}
                            <form action="{{ route('admin.exams.destroy', $exam) }}" method="POST" onsubmit="return confirm('Supprimer cet examen ?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="flex items-center gap-1 text-xs font-black bg-red-50 text-red-600 hover:bg-red-100 px-3 py-2 rounded-xl transition-colors">
                                    🗑️ Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-16 text-center">
                    <div class="text-6xl mb-6">🗓️</div>
                    <h3 class="text-2xl font-black text-gray-300 mb-3">Aucun examen planifié</h3>
                    <a href="{{ route('admin.exams.create') }}" class="inline-block bg-upf-blue text-white font-black py-3 px-8 rounded-2xl shadow-lg hover:bg-upf-navy transition-colors mt-4">
                        Planifier le premier examen
                    </a>
                </div>
            @endforelse

        </div>
    </div>
</x-app-layout>
