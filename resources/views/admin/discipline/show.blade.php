<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-upf-blue italic">⚖️ Dossier de Discipline — {{ $student->user?->name }}</h2>
            <a href="{{ route('admin.discipline.index') }}" class="text-xs font-bold text-gray-400 hover:text-upf-blue uppercase tracking-widest">← Retour</a>
        </div>
    </x-slot>

    <div class="py-10 bg-[#F8FAFC]">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-2xl font-bold text-sm">{{ session('success') }}</div>
            @endif

            {{-- Header Étudiant --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-5">
                        <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-rose-600 rounded-2xl flex items-center justify-center text-white text-2xl font-black">
                            {{ strtoupper(substr($student->user?->name ?? 'E', 0, 1)) }}
                        </div>
                        <div>
                            <h2 class="text-2xl font-black text-gray-900">{{ $student->user?->name }}</h2>
                            <p class="text-gray-400 font-bold text-sm">N° {{ $student->student_number }} • {{ $student->group?->name }} • {{ $student->group?->filiere?->name }}</p>
                            <p class="text-gray-400 text-xs mt-1">{{ $student->user?->email }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="px-4 py-2 rounded-2xl text-sm font-black border {{ $case->status_color }}">{{ $case->status_label }}</span>
                    </div>
                </div>

                {{-- Résumé absence --}}
                <div class="grid grid-cols-3 gap-4 mt-8">
                    <div class="bg-red-50 rounded-2xl p-5 border border-red-100 text-center">
                        <div class="text-3xl font-black text-red-600">{{ number_format($student->absence_score, 1) }}h</div>
                        <div class="text-xs font-bold text-red-400 uppercase tracking-widest mt-1">Non Justifiées</div>
                    </div>
                    <div class="bg-emerald-50 rounded-2xl p-5 border border-emerald-100 text-center">
                        <div class="text-3xl font-black text-emerald-600">{{ number_format($student->justified_hours, 1) }}h</div>
                        <div class="text-xs font-bold text-emerald-400 uppercase tracking-widest mt-1">Justifiées</div>
                    </div>
                    <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100 text-center">
                        <div class="text-3xl font-black text-gray-700">{{ number_format($student->total_absence_hours, 1) }}h</div>
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Total</div>
                    </div>
                </div>
            </div>

            {{-- Absences par module --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-black text-gray-900 italic">Détail des Absences par Module</h3>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($absencesByModule as $moduleName => $moduleAbsences)
                    <div class="p-5 flex items-center justify-between">
                        <div>
                            <div class="font-black text-gray-900 text-sm">{{ $moduleName ?? 'Module inconnu' }}</div>
                            <div class="text-xs text-gray-400 font-bold mt-1">{{ $moduleAbsences->count() }} séance(s)</div>
                        </div>
                        <div class="flex gap-3 items-center">
                            <span class="text-xs font-black {{ $moduleAbsences->where('is_justified', false)->sum('duration') > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                                {{ number_format($moduleAbsences->where('is_justified', false)->sum('duration'), 1) }}h NJ
                            </span>
                            <span class="text-xs font-bold text-gray-400">
                                {{ number_format($moduleAbsences->sum('duration'), 1) }}h total
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-gray-400 italic">Aucune absence enregistrée.</div>
                    @endforelse
                </div>
            </div>

            {{-- Traiter le dossier --}}
            @if($case->status !== 'treated')
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                <h3 class="font-black text-gray-900 text-lg italic mb-5">✅ Marquer comme Traité</h3>
                <form action="{{ route('admin.discipline.treat', $case) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Commentaire / Décision Administrative *</label>
                        <textarea name="admin_comment" rows="4" required minlength="10"
                            class="w-full border-gray-200 rounded-2xl p-4 font-bold text-gray-700 focus:ring-upf-blue focus:border-upf-blue bg-gray-50"
                            placeholder="Décrivez l'action prise : convocation de l'étudiant, avertissement écrit, suspension, etc..."></textarea>
                        @error('admin_comment')
                            <p class="text-red-500 text-xs font-bold">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="bg-emerald-600 text-white px-8 py-3 rounded-2xl font-black uppercase tracking-widest text-sm hover:bg-emerald-700 transition-all shadow-lg">
                        ✅ Marquer comme Traité
                    </button>
                </form>
            </div>
            @else
            <div class="bg-emerald-50 rounded-3xl border border-emerald-100 p-8">
                <div class="flex items-center gap-3 mb-3">
                    <span class="text-2xl">✅</span>
                    <h3 class="font-black text-emerald-800 text-lg">Dossier Traité</h3>
                </div>
                <p class="text-emerald-700 font-bold text-sm">{{ $case->admin_comment }}</p>
                <p class="text-emerald-500 text-xs font-bold mt-2">Traité le {{ $case->treated_at?->format('d/m/Y à H:i') }} par {{ $case->treatedBy?->name }}</p>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
