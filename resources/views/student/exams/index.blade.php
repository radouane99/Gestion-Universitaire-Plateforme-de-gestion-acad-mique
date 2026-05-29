<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tighter italic">📝 Mes Examens</h2>
    </x-slot>

    <div class="py-10 bg-[#F8FAFC]">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-2xl font-bold text-sm">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-red-50 border border-red-100 text-red-800 rounded-2xl font-bold text-sm">{{ session('error') }}</div>
            @endif

            {{-- Banner --}}
            <div class="bg-gradient-to-br from-upf-blue to-indigo-700 rounded-3xl p-8 text-white shadow-xl">
                <h1 class="text-3xl font-black italic mb-2">Mes Examens & Présences</h1>
                <p class="text-indigo-200">Consultez vos statuts de présence aux examens et gérez vos justifications.</p>
                <div class="mt-4 flex gap-3">
                    <a href="{{ route('student.retake.index') }}"
                        class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-xl text-sm font-black transition-all">
                        🎓 Mon Rattrapage
                    </a>
                </div>
            </div>

            {{-- Résumé --}}
            @php
                $totalExams    = $exams->count();
                $presentCount  = $attendances->where('status', 'present')->count();
                $absentCount   = $attendances->where('status', 'absent')->count();
                $pendingJustif = collect($attendances)->filter(fn($a) => $a->isAbsent() && !$a->justification)->count();
            @endphp
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 text-center">
                    <div class="text-2xl font-black text-gray-700">{{ $totalExams }}</div>
                    <div class="text-xs font-bold text-gray-400 uppercase mt-1">Total Examens</div>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-emerald-100 text-center">
                    <div class="text-2xl font-black text-emerald-600">{{ $presentCount }}</div>
                    <div class="text-xs font-bold text-gray-400 uppercase mt-1">Présent</div>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-red-100 text-center">
                    <div class="text-2xl font-black text-red-600">{{ $absentCount }}</div>
                    <div class="text-xs font-bold text-gray-400 uppercase mt-1">Absent</div>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-amber-100 text-center">
                    <div class="text-2xl font-black text-amber-600">{{ $pendingJustif }}</div>
                    <div class="text-xs font-bold text-gray-400 uppercase mt-1">Justif. à déposer</div>
                </div>
            </div>

            {{-- Liste des examens --}}
            <div class="space-y-4">
                @forelse($exams as $exam)
                @php
                    $att     = $attendances->get($exam->id);
                    $retake  = $retakes->get($exam->id);
                @endphp
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                    <div class="p-6 flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="font-black text-gray-900 text-lg">{{ $exam->module?->name }}</h3>
                                <span class="px-2 py-0.5 rounded-lg text-xs font-black bg-indigo-50 text-indigo-600 border border-indigo-100">{{ $exam->type }}</span>
                            </div>
                            <div class="flex items-center gap-4 text-sm text-gray-500 font-bold">
                                <span>📅 {{ \Carbon\Carbon::parse($exam->date)->format('d/m/Y') }}</span>
                                <span>🕐 {{ \Carbon\Carbon::parse($exam->start_time)->format('H:i') }}</span>
                                <span>⏱ {{ $exam->duration }} min</span>
                            </div>
                        </div>

                        <div class="flex flex-col items-end gap-2">
                            {{-- Statut Présence --}}
                            @if($att)
                                <span class="px-3 py-1.5 rounded-xl text-xs font-black {{ $att->status_color }}">
                                    {{ $att->status_icon }} {{ $att->status_label }}
                                </span>
                            @else
                                <span class="px-3 py-1.5 rounded-xl text-xs font-black bg-gray-100 text-gray-400 border border-gray-200">
                                    ❓ Non renseigné
                                </span>
                            @endif

                            {{-- Rattrapage --}}
                            @if($retake)
                                <span class="px-3 py-1.5 rounded-xl text-xs font-black {{ $retake->status_color }}">
                                    🎓 {{ $retake->admin_decision_label }}
                                </span>
                            @endif

                            {{-- Actions --}}
                            @if($att && $att->isAbsent())
                                @if(!$att->justification)
                                    <a href="{{ route('student.exam_justification.create', $att) }}"
                                        class="bg-amber-500 text-white px-4 py-2 rounded-xl text-xs font-black hover:bg-amber-600 transition-all mt-1">
                                        📋 Déposer Justification
                                    </a>
                                @elseif($att->justification->status === 'pending')
                                    <span class="text-xs font-bold text-amber-600 bg-amber-50 px-3 py-1.5 rounded-xl border border-amber-100">
                                        ⏳ Justification en attente
                                    </span>
                                @elseif($att->justification->status === 'approved')
                                    <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-100">
                                        ✅ Justification approuvée
                                    </span>
                                @elseif($att->justification->status === 'rejected')
                                    <span class="text-xs font-bold text-red-600 bg-red-50 px-3 py-1.5 rounded-xl border border-red-100">
                                        ❌ Justification refusée
                                    </span>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-white rounded-3xl p-16 text-center shadow-sm border border-gray-100">
                    <div class="text-5xl mb-4">📝</div>
                    <p class="text-gray-400 italic font-bold">Aucun examen planifié pour votre groupe.</p>
                </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
