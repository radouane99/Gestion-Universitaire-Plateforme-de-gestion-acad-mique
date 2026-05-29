<x-app-layout>
    <x-slot name="header">
        <x-page-header 
            title="{{ __('Gestion des Examens & Convocations') }}" 
            subtitle="{{ __('Planification des sessions et génération des convocations.') }}"
            icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>'
        >
            <x-slot name="actions">
                <form method="GET" action="{{ route('admin.exams.index') }}" id="examFilterForm"
                      class="flex flex-wrap items-center gap-2 bg-indigo-50/50 px-3 py-2 rounded-2xl border border-indigo-100">
                    @csrf

                    <div class="flex flex-col">
                        <label class="text-[9px] font-black uppercase tracking-widest text-indigo-400 mb-0.5">{{ __('Filière') }}</label>
                        <select name="filiere_id" id="ag_filiere" onchange="this.form.method='GET'; this.form.action='{{ route('admin.exams.index') }}'; this.form.submit();"
                                class="text-sm border-indigo-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl py-1.5 pr-6 cursor-pointer bg-white">
                            <option value="">{{ __('Toutes les filières') }}</option>
                            @foreach($filieres as $filiere)
                                <option value="{{ $filiere->id }}" {{ request('filiere_id') == $filiere->id ? 'selected' : '' }}>{{ $filiere->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col">
                        <label class="text-[9px] font-black uppercase tracking-widest text-indigo-400 mb-0.5">{{ __('Session') }}</label>
                        <select name="exam_session_id" id="ag_session" onchange="this.form.method='GET'; this.form.action='{{ route('admin.exams.index') }}'; this.form.submit();"
                                class="text-sm border-indigo-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl py-1.5 pr-6 cursor-pointer bg-white">
                            <option value="">-- {{ __('Session') }} --</option>
                            @foreach($examSessions as $session)
                                @if($session->start_date && $session->end_date)
                                    <option value="{{ $session->id }}" {{ request('exam_session_id') == $session->id ? 'selected' : '' }}>{{ $session->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-1.5 mt-auto mb-2 px-2">
                        <input type="checkbox" name="overwrite" id="ag_overwrite" value="1" {{ request('overwrite') ? 'checked' : '' }}
                               class="rounded border-indigo-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                        <label for="ag_overwrite" class="text-[10px] font-bold text-indigo-700 cursor-pointer select-none">
                            🔄 {{ __('Écraser') }}
                        </label>
                    </div>

                    <button type="button" onclick="submitAutoGenerate();"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-black py-1.5 px-4 rounded-xl shadow-sm transition-all hover:scale-105 text-xs uppercase tracking-widest mt-auto">
                        ✨ {{ __('Auto-Générer') }}
                    </button>
                </form>

                <script>
                function submitAutoGenerate() {
                    const session = document.getElementById('ag_session');
                    const filiere = document.getElementById('ag_filiere');
                    const overwrite = document.getElementById('ag_overwrite').checked;
                    if (!session.value) { alert('{{ __('Veuillez choisir une session.') }}'); return; }
                    const fname = filiere.options[filiere.selectedIndex].text;
                    const sname = session.options[session.selectedIndex].text;
                    const scope = filiere.value ? `la filière "${fname}"` : 'toutes les filières';
                    
                    let confirmMsg = `{{ __('Générer automatiquement les examens pour') }} ${scope} — {{ __('session') }} "${sname}" ?`;
                    if (overwrite) {
                        confirmMsg += `\n\n⚠️ {{ __('ATTENTION: Les examens existants pour cette session et filière seront SUPPRIMÉS et REGÉNÉRÉS.') }}`;
                    }
                    
                    if (confirm(confirmMsg)) {
                        const form = document.getElementById('examFilterForm');
                        form.method = 'POST';
                        form.action = '{{ route('admin.exams.auto_generate') }}';
                        form.submit();
                    }
                }
                </script>

                <x-primary-button tag="a" href="{{ route('admin.exams.create') }}" class="ml-2">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"></path></svg>
                    {{ __('Manuel') }}
                </x-primary-button>

                @if(request('exam_session_id'))
                    <a href="{{ route('admin.exams.planning.simulation', request('exam_session_id')) }}" class="ml-2 bg-upf-magenta hover:bg-pink-700 text-white font-black py-1.5 px-4 rounded-xl shadow-sm transition-all hover:scale-105 text-xs uppercase tracking-widest flex items-center">
                        ⚙️ {{ __('Gérer le Planning') }}
                    </a>
                @endif
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-6 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-alert-messages />

            {{-- Exam Cards --}}
            @forelse($exams as $exam)
                @php
                    $isPast = \Carbon\Carbon::parse($exam->date)->isPast();
                    $hasConvocations = $exam->convocations_count > 0;
                @endphp
                <x-card class="{{ $isPast ? 'border-gray-100 opacity-75' : '' }} p-0">
                    <div class="flex flex-col md:flex-row">
                        {{-- Date Block --}}
                        <div class="flex items-center justify-center md:w-32 py-6 px-4
                            {{ $exam->type === 'Final' ? 'bg-gradient-to-b from-rose-500 to-rose-700' : ($exam->type === 'CC2' ? 'bg-gradient-to-b from-amber-500 to-amber-700' : 'bg-gradient-to-b from-upf-blue to-upf-navy') }}
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
                                        <span class="text-gray-600 font-bold">📍 {{ $exam->room?->name ?? __('Salle TBD') }}</span>
                                    </div>
                                    <div class="mt-3 text-xs text-gray-500 font-bold flex flex-wrap items-center gap-2">
                                        <span class="uppercase tracking-widest text-[9px]">{{ __('Surveillants') }} :</span>
                                        @forelse($exam->proctors as $p)
                                            <span class="bg-gray-100 text-gray-700 border border-gray-200 px-2 py-0.5 rounded-lg">{{ $p->user->name }}</span>
                                        @empty
                                            <span class="text-rose-400">{{ __('Aucun') }}</span>
                                        @endforelse
                                    </div>
                                </div>

                                {{-- Convocation Status --}}
                                <div class="text-right shrink-0">
                                    <div class="inline-flex flex-col items-end gap-1">
                                        <div class="flex flex-col gap-1 items-end">
                                            <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 px-3 py-2 rounded-2xl">
                                                <span class="text-2xl font-black text-gray-900">{{ $exam->convocations_count }}</span>
                                                <span class="text-[9px] text-gray-400 font-black uppercase tracking-widest leading-tight text-right">{{ __('Convocat.') }}<br>{{ __('générées') }}</span>
                                            </div>
                                            @if($exam->convocations_count > 0)
                                                <div class="flex items-center gap-2 mt-1 text-[10px] font-bold">
                                                    <x-badge type="success">✉️ {{ $exam->sent_convocations_count }} {{ __('envoyées') }}</x-badge>
                                                    @if($exam->convocations_count - $exam->sent_convocations_count > 0)
                                                        <x-badge type="warning">⏳ {{ $exam->convocations_count - $exam->sent_convocations_count }} {{ __('en attente') }}</x-badge>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                        @if(!$isPast && !$hasConvocations)
                                            <span class="text-[10px] text-amber-600 font-black mt-1">⚠️ {{ __('Convocations non générées') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex flex-row md:flex-col gap-2 p-4 bg-gray-50/50 border-t md:border-t-0 md:border-l border-gray-100 justify-center items-center shrink-0">
                            {{-- Generate Convocations --}}
                            <form action="{{ route('admin.exams.generate_convocations', $exam) }}" method="POST" class="w-full">
                                @csrf
                                <input type="hidden" name="send_email" value="0">
                                <button type="submit" title="{{ __('Générer les convocations (sans email)') }}"
                                    class="w-full flex justify-center items-center gap-2 text-xs font-black bg-indigo-50 text-indigo-700 hover:bg-indigo-100 px-3 py-2.5 rounded-xl transition-colors">
                                    🎫 {{ __('Générer') }}
                                </button>
                            </form>

                            {{-- Send Emails --}}
                            <form action="{{ route('admin.exams.send_emails', $exam) }}" method="POST" onsubmit="return confirm('{{ __('Envoyer les convocations par email à tous les étudiants ?') }}');" class="w-full">
                                @csrf
                                <button type="submit" title="{{ __('Envoyer emails + PDF') }}"
                                    class="w-full flex justify-center items-center gap-2 text-xs font-black bg-emerald-50 text-emerald-700 hover:bg-emerald-100 px-3 py-2.5 rounded-xl transition-colors">
                                    ✉️ {{ __('Envoyer mails') }}
                                </button>
                            </form>

                            {{-- PDF Admin --}}
                            <a href="{{ route('admin.exams.pdf', $exam) }}"
                               title="{{ __('Télécharger PDF global') }}"
                               class="w-full flex justify-center items-center gap-2 text-xs font-black bg-gray-100 text-gray-700 hover:bg-gray-200 px-3 py-2.5 rounded-xl transition-colors">
                                📄 {{ __('PDF global') }}
                            </a>

                            {{-- Display List --}}
                            <a href="{{ route('admin.exams.display_list.show', $exam) }}"
                               title="{{ __('Liste d\'affichage') }}"
                               class="w-full flex justify-center items-center gap-2 text-xs font-black bg-blue-50 text-blue-700 hover:bg-blue-100 px-3 py-2.5 rounded-xl transition-colors">
                                📋 {{ __('Affichage') }}
                            </a>

                            {{-- Attendance Sheet --}}
                            <a href="{{ route('admin.exams.attendance_sheet', $exam) }}"
                               title="{{ __('Feuille d\'émargement pour les surveillants') }}"
                               class="w-full flex justify-center items-center gap-2 text-xs font-black bg-amber-50 text-amber-700 hover:bg-amber-100 px-3 py-2.5 rounded-xl transition-colors">
                                📝 {{ __('Émargement') }}
                            </a>

                            {{-- PV Download --}}
                            @if($exam->pvExamen)
                                <a href="{{ route('admin.exams.pv.pdf', $exam) }}"
                                   title="{{ __('Télécharger le Procès-Verbal (PV) officiel') }}"
                                   class="w-full flex justify-center items-center gap-2 text-xs font-black bg-purple-50 text-purple-700 hover:bg-purple-100 px-3 py-2.5 rounded-xl transition-colors">
                                    🔏 {{ __('Télécharger PV') }}
                                </a>
                            @endif

                            {{-- Actions (Edit/Delete) --}}
                            <div class="flex gap-2 w-full mt-1">
                                <a href="{{ route('admin.exams.edit', $exam) }}"
                                   class="flex-1 flex justify-center items-center text-xs font-black bg-blue-50 text-upf-blue hover:bg-blue-100 px-3 py-2.5 rounded-xl transition-colors" title="{{ __('Modifier') }}">
                                    ✏️
                                </a>
                                <form action="{{ route('admin.exams.destroy', $exam) }}" method="POST" onsubmit="return confirm('{{ __('Supprimer cet examen ?') }}');" class="flex-1 flex">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-full flex justify-center items-center text-xs font-black bg-rose-50 text-rose-600 hover:bg-rose-100 px-3 py-2.5 rounded-xl transition-colors" title="{{ __('Supprimer') }}">
                                        🗑️
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </x-card>
            @empty
                <div class="bg-white rounded-3xl border border-dashed border-gray-200 p-16 text-center shadow-sm">
                    <div class="text-6xl mb-6">🗓️</div>
                    <h3 class="text-2xl font-black text-gray-900 mb-2">{{ __('Aucun examen planifié') }}</h3>
                    <p class="text-gray-400 font-medium mb-6">{{ __('Commencez par planifier une nouvelle session d\'examen.') }}</p>
                    <x-primary-button tag="a" href="{{ route('admin.exams.create') }}">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        {{ __('Planifier un examen') }}
                    </x-primary-button>
                </div>
            @endforelse

        </div>
    </div>
</x-app-layout>
