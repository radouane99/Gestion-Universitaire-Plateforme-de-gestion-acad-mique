<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tight italic">
                📋 Gestion des Convocations d'Examens
            </h2>
            <span class="text-xs font-black text-gray-400 uppercase tracking-widest">
                {{ now()->format('d/m/Y') }}
            </span>
        </div>
    </x-slot>

    <div class="py-10 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <x-alert-messages />

            @if(session('assignment_errors'))
                <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
                    <p class="font-black text-amber-700 text-sm mb-2">⚠️ Examens sans surveillant disponible :</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach(session('assignment_errors') as $err)
                            <li class="text-amber-600 text-xs font-bold">{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- SESSION SELECTOR --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                <form method="GET" action="{{ route('admin.convocations.index') }}" class="flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Session d'examens</label>
                        <select name="session_id" onchange="this.form.submit()"
                            class="w-full rounded-xl border-gray-200 focus:ring-upf-blue text-sm font-bold shadow-sm">
                            @foreach($examSessions as $session)
                                <option value="{{ $session->id }}" {{ $selectedSessionId == $session->id ? 'selected' : '' }}>
                                    {{ $session->name }} — {{ $session->academicYear->name ?? '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1 min-w-[160px]">
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Filière</label>
                        <select name="filiere_id" onchange="this.form.submit()"
                            class="w-full rounded-xl border-gray-200 focus:ring-upf-blue text-sm font-bold shadow-sm">
                            <option value="">Toutes les filières</option>
                            @foreach($filieres as $f)
                                <option value="{{ $f->id }}" {{ request('filiere_id') == $f->id ? 'selected' : '' }}>{{ $f->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1 min-w-[160px]">
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Statut</label>
                        <select name="status" onchange="this.form.submit()"
                            class="w-full rounded-xl border-gray-200 focus:ring-upf-blue text-sm font-bold shadow-sm">
                            <option value="">Tous les statuts</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="generated" {{ request('status') === 'generated' ? 'selected' : '' }}>Générées</option>
                            <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Envoyées</option>
                            <option value="downloaded" {{ request('status') === 'downloaded' ? 'selected' : '' }}>Téléchargées</option>
                        </select>
                    </div>
                </form>
            </div>

            @if($selectedSession)

            {{-- STATS CARDS --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach([
                    ['label' => 'Total Étudiants', 'value' => $studentStats['total'], 'icon' => '🎓', 'color' => 'from-upf-blue to-upf-navy'],
                    ['label' => 'Générées', 'value' => $studentStats['generated'], 'icon' => '📄', 'color' => 'from-blue-500 to-blue-700'],
                    ['label' => 'Envoyées', 'value' => $studentStats['sent'], 'icon' => '✉️', 'color' => 'from-emerald-500 to-emerald-700'],
                    ['label' => 'Téléchargées', 'value' => $studentStats['downloaded'], 'icon' => '✅', 'color' => 'from-purple-500 to-purple-700'],
                ] as $stat)
                    <div class="bg-gradient-to-br {{ $stat['color'] }} rounded-2xl p-5 text-white shadow-lg">
                        <div class="text-2xl mb-1">{{ $stat['icon'] }}</div>
                        <div class="text-3xl font-black">{{ $stat['value'] }}</div>
                        <div class="text-[10px] uppercase font-black tracking-widest opacity-80 mt-1">{{ $stat['label'] }}</div>
                    </div>
                @endforeach
            </div>

            {{-- TABS --}}
            <div x-data="{ tab: 'students' }">
                <div class="flex gap-1 bg-gray-100 p-1 rounded-2xl w-fit">
                    <button @click="tab = 'students'"
                        :class="tab === 'students' ? 'bg-white shadow text-upf-blue' : 'text-gray-500 hover:text-gray-700'"
                        class="px-5 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest transition-all">
                        🎓 Étudiants ({{ $studentStats['total'] }})
                    </button>
                    <button @click="tab = 'professors'"
                        :class="tab === 'professors' ? 'bg-white shadow text-upf-blue' : 'text-gray-500 hover:text-gray-700'"
                        class="px-5 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest transition-all">
                        👨‍🏫 Surveillants ({{ $professorStats['total'] }})
                    </button>
                    <button @click="tab = 'availabilities'"
                        :class="tab === 'availabilities' ? 'bg-white shadow text-upf-blue' : 'text-gray-500 hover:text-gray-700'"
                        class="px-5 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest transition-all">
                        📅 Disponibilités ({{ $professorAvailabilities->count() }})
                    </button>
                </div>

                {{-- TAB: STUDENT CONVOCATIONS --}}
                <div x-show="tab === 'students'" class="mt-6 space-y-6">

                    {{-- Actions --}}
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-black text-gray-900 mb-5 flex items-center gap-2">
                            <span class="w-7 h-7 rounded-lg bg-upf-blue text-white flex items-center justify-center text-xs">⚡</span>
                            Actions rapides — Étudiants
                        </h3>
                        <div class="flex flex-wrap gap-3">
                            <form action="{{ route('admin.convocations.generate_session') }}" method="POST"
                                onsubmit="return confirm('Générer les convocations pour tous les étudiants de la session {{ $selectedSession->name }} ?');">
                                @csrf
                                <input type="hidden" name="session_id" value="{{ $selectedSession->id }}">
                                <button type="submit"
                                    class="flex items-center gap-2 bg-upf-blue hover:bg-upf-navy text-white font-black py-2.5 px-5 rounded-2xl shadow-md transition-all hover:scale-105 text-xs uppercase tracking-widest">
                                    📄 Générer toutes les convocations
                                </button>
                            </form>

                            <form action="{{ route('admin.convocations.send_session') }}" method="POST"
                                onsubmit="return confirm('Envoyer les emails à tous les étudiants avec convocations non envoyées ?');">
                                @csrf
                                <input type="hidden" name="session_id" value="{{ $selectedSession->id }}">
                                <button type="submit"
                                    class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-black py-2.5 px-5 rounded-2xl shadow-md transition-all hover:scale-105 text-xs uppercase tracking-widest">
                                    ✉️ Envoyer tous les emails
                                </button>
                            </form>
                        </div>

                        {{-- Progress bar --}}
                        @if($studentStats['total'] > 0)
                        @php $sentPct = round(($studentStats['sent'] + $studentStats['downloaded']) / $studentStats['total'] * 100); @endphp
                        <div class="mt-5">
                            <div class="flex justify-between text-xs font-black text-gray-500 mb-1">
                                <span>Progression des envois</span>
                                <span>{{ $sentPct }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2.5">
                                <div class="bg-emerald-500 h-2.5 rounded-full transition-all" style="width: {{ $sentPct }}%"></div>
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- Table --}}
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="font-black text-gray-900">Liste des convocations étudiants</h3>
                            <span class="bg-upf-blue/10 text-upf-blue text-xs font-black px-3 py-1 rounded-full">
                                {{ $studentConvocations->total() }} au total
                            </span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 border-b border-gray-100">
                                    <tr>
                                        <th class="px-5 py-3 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Étudiant</th>
                                        <th class="px-5 py-3 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Filière / Groupe</th>
                                        <th class="px-5 py-3 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Examen</th>
                                        <th class="px-5 py-3 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Date</th>
                                        <th class="px-5 py-3 text-center text-xs font-black text-gray-400 uppercase tracking-widest">Statut</th>
                                        <th class="px-5 py-3 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Référence</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @forelse($studentConvocations as $conv)
                                        <tr class="hover:bg-gray-50/50 transition-colors">
                                            <td class="px-5 py-3 font-bold text-gray-900">{{ $conv->student->user->name ?? '—' }}</td>
                                            <td class="px-5 py-3 text-gray-500 text-xs">
                                                {{ $conv->student->group->filiere->name ?? '—' }}<br>
                                                <span class="font-bold">{{ $conv->student->group->name ?? '—' }}</span>
                                            </td>
                                            <td class="px-5 py-3 font-bold text-gray-900">{{ $conv->exam->module->name ?? '—' }}</td>
                                            <td class="px-5 py-3 text-gray-600 font-bold text-xs">
                                                {{ \Carbon\Carbon::parse($conv->exam->date)->format('d/m/Y') }}<br>
                                                {{ date('H:i', strtotime($conv->exam->start_time)) }}
                                            </td>
                                            <td class="px-5 py-3 text-center">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black {{ $conv->status_color }}">
                                                    {{ $conv->status_label }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-3 text-gray-400 text-xs font-mono">{{ $conv->reference }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-5 py-12 text-center text-gray-400 font-bold">
                                                Aucune convocation générée pour cette session.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($studentConvocations->hasPages())
                        <div class="px-6 py-4 border-t border-gray-100">
                            {{ $studentConvocations->links() }}
                        </div>
                        @endif
                    </div>
                </div>

                {{-- TAB: PROFESSOR CONVOCATIONS --}}
                <div x-show="tab === 'professors'" class="mt-6 space-y-6">

                    {{-- Prof stats --}}
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        @foreach([
                            ['label' => 'Total Profs', 'value' => $professorStats['total'], 'color' => 'bg-indigo-500'],
                            ['label' => 'Générées', 'value' => $professorStats['generated'], 'color' => 'bg-blue-500'],
                            ['label' => 'Envoyées', 'value' => $professorStats['sent'], 'color' => 'bg-emerald-500'],
                            ['label' => 'Confirmées', 'value' => $professorStats['confirmed'], 'color' => 'bg-green-600'],
                        ] as $stat)
                            <div class="{{ $stat['color'] }} rounded-2xl p-4 text-white shadow-md text-center">
                                <div class="text-2xl font-black">{{ $stat['value'] }}</div>
                                <div class="text-[10px] uppercase font-black tracking-widest opacity-90 mt-1">{{ $stat['label'] }}</div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Actions --}}
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-black text-gray-900 mb-5 flex items-center gap-2">
                            <span class="w-7 h-7 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-xs">⚙️</span>
                            Actions — Surveillance
                        </h3>

                        @if($examsWithoutProctors->isNotEmpty())
                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-5">
                            <p class="font-black text-amber-700 text-sm">⚠️ {{ $examsWithoutProctors->count() }} examen(s) sans surveillant affecté</p>
                            <div class="mt-2 space-y-1">
                                @foreach($examsWithoutProctors->take(5) as $e)
                                    <p class="text-xs text-amber-600">• {{ $e->module->name }} — {{ $e->group->name }} — {{ \Carbon\Carbon::parse($e->date)->format('d/m/Y') }}</p>
                                @endforeach
                                @if($examsWithoutProctors->count() > 5)
                                    <p class="text-xs text-amber-500 font-bold">... et {{ $examsWithoutProctors->count() - 5 }} autres</p>
                                @endif
                            </div>
                        </div>
                        @endif

                        <div class="flex flex-wrap gap-3">
                            <form action="{{ route('admin.convocations.auto_assign') }}" method="POST"
                                onsubmit="return confirm('Lancer l\'affectation automatique des surveillants ? Les professeurs déjà affectés ne seront pas modifiés.');">
                                @csrf
                                <input type="hidden" name="session_id" value="{{ $selectedSession->id }}">
                                <button type="submit"
                                    class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-black py-2.5 px-5 rounded-2xl shadow-md transition-all hover:scale-105 text-xs uppercase tracking-widest">
                                    🤖 Affectation auto des surveillants
                                </button>
                            </form>

                            <form action="{{ route('admin.convocations.generate_professors') }}" method="POST"
                                onsubmit="return confirm('Générer les convocations pour tous les professeurs surveillants de la session ?');">
                                @csrf
                                <input type="hidden" name="session_id" value="{{ $selectedSession->id }}">
                                <button type="submit"
                                    class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-black py-2.5 px-5 rounded-2xl shadow-md transition-all hover:scale-105 text-xs uppercase tracking-widest">
                                    📄 Générer convocations profs
                                </button>
                            </form>

                            <form action="{{ route('admin.convocations.send_professors') }}" method="POST"
                                onsubmit="return confirm('Envoyer les emails aux professeurs avec convocations non envoyées ?');">
                                @csrf
                                <input type="hidden" name="session_id" value="{{ $selectedSession->id }}">
                                <button type="submit"
                                    class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-black py-2.5 px-5 rounded-2xl shadow-md transition-all hover:scale-105 text-xs uppercase tracking-widest">
                                    ✉️ Envoyer emails profs
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Professor convocations table --}}
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="font-black text-gray-900">Convocations de surveillance</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 border-b border-gray-100">
                                    <tr>
                                        <th class="px-5 py-3 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Professeur</th>
                                        <th class="px-5 py-3 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Examen</th>
                                        <th class="px-5 py-3 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Date / Heure</th>
                                        <th class="px-5 py-3 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Salle</th>
                                        <th class="px-5 py-3 text-center text-xs font-black text-gray-400 uppercase tracking-widest">Rôle</th>
                                        <th class="px-5 py-3 text-center text-xs font-black text-gray-400 uppercase tracking-widest">Statut</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @forelse($professorConvocations as $pconv)
                                        <tr class="hover:bg-gray-50/50 transition-colors">
                                            <td class="px-5 py-3 font-bold text-gray-900">{{ $pconv->professor->user->name ?? '—' }}</td>
                                            <td class="px-5 py-3 font-bold">{{ $pconv->exam->module->name ?? '—' }}</td>
                                            <td class="px-5 py-3 text-xs text-gray-600 font-bold">
                                                {{ \Carbon\Carbon::parse($pconv->exam->date)->format('d/m/Y') }}<br>
                                                {{ date('H:i', strtotime($pconv->exam->start_time)) }}
                                            </td>
                                            <td class="px-5 py-3 text-gray-600">{{ $pconv->exam->room->name ?? '—' }}</td>
                                            <td class="px-5 py-3 text-center">
                                                <span class="text-[10px] font-black px-2 py-1 rounded-full {{ $pconv->role === 'principal' ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-600' }}">
                                                    {{ $pconv->role === 'principal' ? 'Principal' : 'Assistant' }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-3 text-center">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black {{ $pconv->status_color }}">
                                                    {{ $pconv->status_label }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-5 py-12 text-center text-gray-400 font-bold">
                                                Aucune convocation professeur générée. Affectez les surveillants d'abord.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- TAB: AVAILABILITIES --}}
                <div x-show="tab === 'availabilities'" class="mt-6">
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="font-black text-gray-900">Disponibilités déclarées par les professeurs</h3>
                            <a href="{{ route('admin.convocations.professor_availabilities', ['session_id' => $selectedSession->id]) }}"
                                class="text-upf-blue hover:text-upf-navy font-black text-xs uppercase tracking-widest transition-colors">
                                Vue détaillée →
                            </a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 border-b border-gray-100">
                                    <tr>
                                        <th class="px-5 py-3 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Professeur</th>
                                        <th class="px-5 py-3 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Département</th>
                                        <th class="px-5 py-3 text-center text-xs font-black text-gray-400 uppercase tracking-widest">Jours disponibles</th>
                                        <th class="px-5 py-3 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Dates</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @forelse($professorAvailabilities as $prof)
                                        <tr class="hover:bg-gray-50/50 transition-colors">
                                            <td class="px-5 py-3 font-bold text-gray-900">{{ $prof->user->name ?? '—' }}</td>
                                            <td class="px-5 py-3 text-gray-500 text-xs">{{ $prof->department ?? '—' }}</td>
                                            <td class="px-5 py-3 text-center">
                                                <span class="bg-emerald-100 text-emerald-700 font-black text-xs px-2.5 py-1 rounded-full">
                                                    {{ $prof->availabilities->count() }} jour(s)
                                                </span>
                                            </td>
                                            <td class="px-5 py-3">
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach($prof->availabilities->take(5) as $av)
                                                        <span class="bg-blue-50 text-blue-700 text-[10px] font-black px-2 py-0.5 rounded-lg">
                                                            {{ \Carbon\Carbon::parse($av->available_date)->format('d/m') }}
                                                        </span>
                                                    @endforeach
                                                    @if($prof->availabilities->count() > 5)
                                                        <span class="text-gray-400 text-[10px] font-black">+{{ $prof->availabilities->count() - 5 }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-5 py-12 text-center text-gray-400 font-bold">
                                                Aucune disponibilité déclarée pour cette session.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

            @else
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-12 text-center">
                    <div class="text-5xl mb-4">📋</div>
                    <p class="font-black text-gray-400">Sélectionnez une session d'examens pour gérer les convocations.</p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
