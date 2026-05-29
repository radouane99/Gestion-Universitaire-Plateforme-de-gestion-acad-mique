<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tight italic">
                📅 Disponibilités Professeurs — Session d'Examens
            </h2>
        </div>
    </x-slot>

    <div class="py-10 bg-[#F8FAFC]">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <x-alert-messages />

            {{-- Session filter --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                <form method="GET" class="flex items-end gap-4">
                    <div class="flex-1">
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Session</label>
                        <select name="session_id" onchange="this.form.submit()"
                            class="w-full rounded-xl border-gray-200 focus:ring-upf-blue text-sm font-bold shadow-sm">
                            @foreach($examSessions as $session)
                                <option value="{{ $session->id }}" {{ optional($selectedSession)->id == $session->id ? 'selected' : '' }}>
                                    {{ $session->name }} — {{ $session->academicYear->name ?? '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>

            {{-- Summary stats --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 text-center">
                    <div class="text-3xl font-black text-upf-blue">{{ $professors->count() }}</div>
                    <div class="text-[10px] uppercase font-black text-gray-400 tracking-widest mt-1">Profs disponibles</div>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 text-center">
                    <div class="text-3xl font-black text-emerald-600">{{ $professors->sum(fn($p) => $p->availabilities->count()) }}</div>
                    <div class="text-[10px] uppercase font-black text-gray-400 tracking-widest mt-1">Total jours déclarés</div>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 text-center">
                    <div class="text-3xl font-black text-amber-500">{{ $professors->filter(fn($p) => $p->availabilities->count() < 3)->count() }}</div>
                    <div class="text-[10px] uppercase font-black text-gray-400 tracking-widest mt-1">Profs < 3 jours</div>
                </div>
            </div>

            {{-- Table --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-black text-gray-900">Détail des disponibilités par professeur</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Professeur</th>
                                <th class="px-5 py-3 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Département</th>
                                <th class="px-5 py-3 text-center text-xs font-black text-gray-400 uppercase tracking-widest">Jours</th>
                                <th class="px-5 py-3 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Dates déclarées</th>
                                <th class="px-5 py-3 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($professors as $prof)
                                <tr class="hover:bg-gray-50/30 transition-colors {{ $prof->availabilities->count() < 3 ? 'bg-amber-50/30' : '' }}">
                                    <td class="px-5 py-3 font-bold text-gray-900">{{ $prof->user->name ?? '—' }}</td>
                                    <td class="px-5 py-3 text-gray-500 text-xs">{{ $prof->department ?? '—' }}</td>
                                    <td class="px-5 py-3 text-center">
                                        <span class="font-black text-sm {{ $prof->availabilities->count() >= 3 ? 'text-emerald-600' : 'text-amber-600' }}">
                                            {{ $prof->availabilities->count() }}
                                        </span>
                                        @if($prof->availabilities->count() < 3)
                                            <span class="ml-1 text-[9px] text-amber-500 font-black">⚠️</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($prof->availabilities as $av)
                                                <span class="bg-blue-50 text-blue-700 text-[10px] font-black px-2 py-0.5 rounded-lg whitespace-nowrap">
                                                    {{ \Carbon\Carbon::parse($av->available_date)->isoFormat('ddd D/MM') }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-5 py-3 text-xs text-gray-400">
                                        {{ $prof->availabilities->first()?->notes ?? '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-12 text-center text-gray-400 font-bold">
                                        Aucune disponibilité déclarée pour cette session.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="text-center">
                <a href="{{ route('admin.convocations.index', ['session_id' => optional($selectedSession)->id]) }}"
                   class="inline-flex items-center gap-2 text-upf-blue hover:text-upf-navy font-black text-sm transition-colors">
                    ← Retour au tableau de bord des convocations
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
