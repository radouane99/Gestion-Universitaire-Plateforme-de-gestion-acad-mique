<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tighter italic">🎓 Mon Droit au Rattrapage</h2>
    </x-slot>

    <div class="py-10 bg-[#F8FAFC]">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Banner --}}
            <div class="bg-gradient-to-br from-emerald-600 to-teal-700 rounded-3xl p-8 text-white shadow-xl">
                <h1 class="text-3xl font-black italic mb-2">Mon Droit au Rattrapage</h1>
                <p class="text-emerald-100">Consultez votre éligibilité au rattrapage pour chaque module.</p>
                <div class="mt-4 text-xs text-emerald-200 font-bold">
                    💡 Vous êtes éligible si : absent à l'examen avec justification approuvée <strong class="text-white">OU</strong> note finale &lt; {{ $settings?->retake_min_grade ?? 10 }}/20
                </div>
            </div>

            {{-- Liste rattrapages --}}
            <div class="space-y-4">
                @forelse($retakes as $retake)
                <div class="bg-white rounded-2xl shadow-sm border {{ $retake->admin_decision === 'approved' ? 'border-emerald-200' : ($retake->admin_decision === 'rejected' ? 'border-red-200' : 'border-gray-100') }} overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="font-black text-gray-900 text-lg">{{ $retake->exam?->module?->name }}</h3>
                                <p class="text-sm text-gray-400 font-bold mt-1">
                                    📅 Examen du {{ \Carbon\Carbon::parse($retake->exam?->date)->format('d/m/Y') }}
                                    • {{ $retake->exam?->type }}
                                </p>
                                <div class="mt-3 flex items-center gap-3">
                                    <span class="px-3 py-1 rounded-full text-xs font-black border border-gray-200 bg-gray-100 text-gray-600">
                                        {{ $retake->reason_label }}
                                    </span>
                                    <span class="px-3 py-1 rounded-full text-xs font-black border {{ $retake->status_color }}">
                                        {{ $retake->status_label }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-right">
                                @if($retake->admin_decision === 'approved')
                                    <div class="bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-2xl px-5 py-3 text-center">
                                        <div class="text-2xl font-black">✅</div>
                                        <div class="text-xs font-black uppercase tracking-widest mt-1">Rattrapage Autorisé</div>
                                    </div>
                                @elseif($retake->admin_decision === 'rejected')
                                    <div class="bg-red-50 text-red-700 border border-red-200 rounded-2xl px-5 py-3 text-center">
                                        <div class="text-2xl font-black">❌</div>
                                        <div class="text-xs font-black uppercase tracking-widest mt-1">Rattrapage Refusé</div>
                                    </div>
                                @else
                                    <div class="bg-amber-50 text-amber-700 border border-amber-200 rounded-2xl px-5 py-3 text-center">
                                        <div class="text-2xl font-black">⏳</div>
                                        <div class="text-xs font-black uppercase tracking-widest mt-1">En Attente de Décision</div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if($retake->admin_comment)
                        <div class="mt-4 p-3 bg-gray-50 rounded-xl border border-gray-100">
                            <p class="text-xs font-bold text-gray-500">💬 Commentaire administration :</p>
                            <p class="text-sm text-gray-700 font-bold mt-1">{{ $retake->admin_comment }}</p>
                        </div>
                        @endif

                        @if($retake->decided_at)
                        <p class="text-xs text-gray-300 font-bold mt-3">Décidé le {{ $retake->decided_at->format('d/m/Y à H:i') }}</p>
                        @endif
                    </div>
                </div>
                @empty
                <div class="bg-white rounded-3xl p-16 text-center shadow-sm border border-gray-100">
                    <div class="text-5xl mb-4">🎓</div>
                    <p class="text-gray-400 italic font-bold">Aucune information de rattrapage disponible.</p>
                    <p class="text-gray-300 text-sm mt-2">Votre éligibilité sera calculée après les examens.</p>
                </div>
                @endforelse
            </div>

            {{-- Lien examens --}}
            <div class="text-center">
                <a href="{{ route('student.exams.index') }}" class="text-upf-blue font-black text-sm hover:underline">← Retour à mes examens</a>
            </div>

        </div>
    </div>
</x-app-layout>
