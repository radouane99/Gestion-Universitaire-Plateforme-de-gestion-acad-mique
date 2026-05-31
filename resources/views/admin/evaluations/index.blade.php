<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tight italic">
                {{ __('Évaluations & Qualité des Enseignements') }}
            </h2>
            <form method="POST" action="{{ route('admin.evaluations.toggle') }}">
                @csrf
                <button 
                    type="submit"
                    class="inline-flex items-center gap-2 px-6 py-3 {{ $isEvaluationOpen ? 'bg-rose-600 hover:bg-rose-700' : 'bg-emerald-600 hover:bg-emerald-700' }} text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all duration-200 shadow-md hover:-translate-y-0.5"
                >
                    <span>{{ $isEvaluationOpen ? '🔴 Clôturer la Campagne' : '🟢 Ouvrir la Campagne' }}</span>
                </button>
            </form>
        </div>
    </x-slot>

    <div class="py-10 bg-[#F8FAFC] dark:bg-[#020617] transition-colors duration-300">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">

            {{-- 1. Campaign Status Box --}}
            <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-3xl p-8 shadow-sm flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-5">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 shadow-inner {{ $isEvaluationOpen ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                        <span class="text-3xl">{{ $isEvaluationOpen ? '🔔' : '🔕' }}</span>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Statut de la campagne d'évaluation</p>
                        <h3 class="text-xl font-black mt-0.5">
                            {{ $isEvaluationOpen ? 'La campagne est actuellement OUVERTE aux étudiants.' : 'La campagne est actuellement FERMÉE.' }}
                        </h3>
                        <p class="text-xs text-slate-500 mt-1">
                            {{ $isEvaluationOpen ? 'Les étudiants peuvent évaluer leurs cours de façon anonyme depuis leur espace.' : 'Les étudiants ne peuvent pas remplir d\'évaluation pour le moment.' }}
                        </p>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="p-6 bg-emerald-50 text-emerald-700 rounded-3xl border border-emerald-100 flex items-center gap-4 shadow-sm">
                    <span class="text-2xl">🎉</span>
                    <p class="font-extrabold text-sm">{{ session('success') }}</p>
                </div>
            @endif

            {{-- 2. Aggregated Analytics Table --}}
            <div class="bg-white dark:bg-slate-900 overflow-hidden shadow-sm rounded-3xl border border-gray-100 dark:border-slate-800">
                <div class="p-8 border-b border-gray-50 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/20">
                    <h3 class="text-lg font-black text-slate-900 dark:text-white tracking-tight italic">📊 Résultats Agrégés des Enseignements</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Moyenne des évaluations anonymes soumises par les étudiants.</p>
                </div>
                <div class="p-0 overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 dark:bg-slate-950/40 text-[10px] font-black uppercase tracking-widest text-slate-450 border-b border-gray-100 dark:border-slate-800">
                                <th class="py-4 px-6">Module & Enseignant</th>
                                <th class="py-4 px-6 text-center">Évaluations</th>
                                <th class="py-4 px-6 text-center">Organisation (Q1)</th>
                                <th class="py-4 px-6 text-center">Clarté (Q2)</th>
                                <th class="py-4 px-6 text-center">Dispo Prof (Q3)</th>
                                <th class="py-4 px-6 text-center">Utilité (Q4)</th>
                                <th class="py-4 px-6 text-center bg-blue-50/10 text-upf-blue dark:text-blue-400">Score Global</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                            @forelse($analytics as $anal)
                                <tr class="hover:bg-slate-50/30 dark:hover:bg-slate-950/10 transition-colors">
                                    <td class="py-5 px-6">
                                        <div class="min-w-[200px]">
                                            <p class="font-extrabold text-sm text-slate-950 dark:text-white">{{ $anal->module->name }}</p>
                                            <p class="text-xs text-slate-500 mt-0.5">{{ $anal->professor->user->name }}</p>
                                        </div>
                                    </td>
                                    <td class="py-5 px-6 text-center font-bold text-slate-700 dark:text-slate-300">
                                        <span class="px-3 py-1 bg-slate-100 dark:bg-slate-800 rounded-full text-xs">
                                            {{ $anal->total_responses }}
                                        </span>
                                    </td>
                                    <td class="py-5 px-6 text-center">
                                        <div class="inline-flex flex-col items-center">
                                            <span class="font-extrabold text-sm text-slate-900 dark:text-white">{{ number_format($anal->q1_avg, 1) }} ★</span>
                                            <div class="w-16 bg-slate-100 dark:bg-slate-800 h-1 rounded-full mt-1 overflow-hidden">
                                                <div class="bg-amber-400 h-1" style="width: {{ ($anal->q1_avg / 5) * 100 }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-5 px-6 text-center">
                                        <div class="inline-flex flex-col items-center">
                                            <span class="font-extrabold text-sm text-slate-900 dark:text-white">{{ number_format($anal->q2_avg, 1) }} ★</span>
                                            <div class="w-16 bg-slate-100 dark:bg-slate-800 h-1 rounded-full mt-1 overflow-hidden">
                                                <div class="bg-amber-400 h-1" style="width: {{ ($anal->q2_avg / 5) * 100 }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-5 px-6 text-center">
                                        <div class="inline-flex flex-col items-center">
                                            <span class="font-extrabold text-sm text-slate-900 dark:text-white">{{ number_format($anal->q3_avg, 1) }} ★</span>
                                            <div class="w-16 bg-slate-100 dark:bg-slate-800 h-1 rounded-full mt-1 overflow-hidden">
                                                <div class="bg-amber-400 h-1" style="width: {{ ($anal->q3_avg / 5) * 100 }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-5 px-6 text-center">
                                        <div class="inline-flex flex-col items-center">
                                            <span class="font-extrabold text-sm text-slate-900 dark:text-white">{{ number_format($anal->q4_avg, 1) }} ★</span>
                                            <div class="w-16 bg-slate-100 dark:bg-slate-800 h-1 rounded-full mt-1 overflow-hidden">
                                                <div class="bg-amber-400 h-1" style="width: {{ ($anal->q4_avg / 5) * 100 }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-5 px-6 text-center bg-blue-50/10">
                                        <span class="font-black text-base text-upf-blue dark:text-blue-400">
                                            {{ number_format($anal->overall_avg, 2) }} / 5.0
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-16 text-center text-slate-400 font-bold italic">
                                        Aucune donnée d'évaluation disponible pour le moment.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 3. Qualitative Comments Section --}}
            <div class="bg-white dark:bg-slate-900 overflow-hidden shadow-sm rounded-3xl border border-gray-100 dark:border-slate-800">
                <div class="p-8 border-b border-gray-50 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/20">
                    <h3 class="text-lg font-black text-slate-900 dark:text-white tracking-tight italic">💬 Retours Qualitatifs & Commentaires</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Commentaires anonymes récents formulés par les étudiants.</p>
                </div>
                <div class="p-8 space-y-6">
                    @forelse($comments as $comment)
                        <div class="p-5 bg-slate-50/50 dark:bg-slate-950/20 rounded-2xl border border-gray-100 dark:border-slate-850/80">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">
                                <div>
                                    <span class="text-xs font-black text-upf-magenta uppercase tracking-wider block">
                                        📘 {{ $comment->module->name }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-bold">
                                        Enseignant : {{ $comment->professor->user->name }}
                                    </span>
                                </div>
                                <span class="text-[10px] text-slate-450 font-bold uppercase shrink-0">
                                    {{ $comment->created_at->translatedFormat('d F Y à H:i') }}
                                </span>
                            </div>
                            <p class="text-slate-700 dark:text-slate-300 text-sm font-semibold italic leading-relaxed">
                                "{{ $comment->comment }}"
                            </p>
                        </div>
                    @empty
                        <p class="text-center py-12 text-slate-400 font-bold italic">Aucun commentaire n'a été formulé pour l'instant.</p>
                    @endforelse

                    <div class="pt-4">
                        {{ $comments->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
