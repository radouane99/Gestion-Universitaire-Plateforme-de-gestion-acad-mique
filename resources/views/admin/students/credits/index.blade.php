<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="font-black text-2xl text-slate-900 dark:text-white leading-tight tracking-tight italic">
                    🛡️ {{ __('Gestion des Crédits & Cas Spéciaux') }}
                </h2>
                <p class="text-xs text-slate-400 dark:text-slate-500 font-semibold uppercase tracking-wider mt-1">
                    {{ __('Régulation académique marocaine, dérogations et suivi des modules en crédit') }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-10 bg-[#F8FAFC] dark:bg-[#020617] transition-colors duration-300 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Alert Messages -->
            @if(session('success'))
                <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 rounded-2xl flex items-center gap-3 text-sm font-bold shadow-sm">
                    <span>✅</span> <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-rose-500/10 border border-rose-500/20 text-rose-500 rounded-2xl flex items-center gap-3 text-sm font-bold shadow-sm">
                    <span>❌</span> <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Business Rule Explainer Card (Moroccan University System) -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-3xl p-6 shadow-xl relative overflow-hidden">
                <div class="relative z-10 space-y-3">
                    <span class="px-2.5 py-1 bg-white/20 text-white text-[9px] font-black uppercase tracking-wider rounded-full">ℹ️ Cadre Réglementaire Marocain</span>
                    <h3 class="text-lg font-black italic">Règles Nationales de Progression & Cas Exceptionnels</h3>
                    <p class="text-xs text-blue-100 max-w-4xl leading-relaxed">
                        • <strong>Crédit de Module :</strong> Un étudiant n'ayant pas validé un ou plusieurs modules (note < 10) mais ayant une moyenne générale &ge; 10/20 (sans note éliminatoire < 5) est autorisé à s'inscrire en année supérieure avec <strong>crédit des modules restants</strong>.  
                        <br>• <strong>Dérogation Spéciale (Ajournements multiples) :</strong> Un étudiant ne peut normalement pas s'inscrire plus de deux fois dans le même niveau (double ajournement exclu). Une **Dérogation Administrative** exceptionnelle peut être accordée par le chef d'établissement pour accorder une **Dernière Chance** de réinscription.
                    </p>
                </div>
                <div class="absolute right-[-40px] bottom-[-40px] w-48 h-48 bg-white/5 rounded-full blur-2xl"></div>
            </div>

            <!-- Stats Dashboard Widget -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Students -->
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 shadow-sm flex items-center justify-between group hover:scale-[1.02] transition-all duration-300">
                    <div>
                        <p class="text-[10px] uppercase font-black tracking-widest text-slate-400 dark:text-slate-500">{{ __('Effectif Global') }}</p>
                        <p class="text-3xl font-black text-slate-850 dark:text-white mt-2 tracking-tighter">{{ $stats['total_students'] }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-xl group-hover:rotate-6 transition-transform">🎒</div>
                </div>

                <!-- With Credit Modules -->
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 shadow-sm flex items-center justify-between group hover:scale-[1.02] transition-all duration-300">
                    <div>
                        <p class="text-[10px] uppercase font-black tracking-widest text-slate-400 dark:text-slate-500">{{ __('Étudiants avec Crédits') }}</p>
                        <p class="text-3xl font-black text-slate-850 dark:text-white mt-2 tracking-tighter text-blue-600 dark:text-blue-400">{{ $stats['with_credits'] }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/40 flex items-center justify-center text-xl group-hover:rotate-6 transition-transform">📚</div>
                </div>

                <!-- With Derogations -->
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 shadow-sm flex items-center justify-between group hover:scale-[1.02] transition-all duration-300">
                    <div>
                        <p class="text-[10px] uppercase font-black tracking-widest text-slate-400 dark:text-slate-500">{{ __('Dérogations Accordées') }}</p>
                        <p class="text-3xl font-black text-slate-850 dark:text-white mt-2 tracking-tighter text-amber-500 dark:text-amber-400">{{ $stats['with_derogation'] }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/40 flex items-center justify-center text-xl group-hover:rotate-6 transition-transform">🛡️</div>
                </div>

                <!-- Last Chance -->
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 shadow-sm flex items-center justify-between group hover:scale-[1.02] transition-all duration-300">
                    <div>
                        <p class="text-[10px] uppercase font-black tracking-widest text-slate-400 dark:text-slate-500">{{ __('Dernière Chance') }}</p>
                        <p class="text-3xl font-black text-slate-850 dark:text-white mt-2 tracking-tighter text-rose-600 dark:text-rose-400">{{ $stats['last_chance'] }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 dark:bg-rose-950/40 flex items-center justify-center text-xl group-hover:rotate-6 transition-transform">⚠️</div>
                </div>
            </div>

            <!-- Filtering & Search Bar -->
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
                <form method="GET" action="{{ route('admin.student_credits.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search Input -->
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom, Matricule, CIN..." 
                               class="w-full pl-10 pr-4 py-2.5 text-xs bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-800 rounded-xl focus:border-blue-500 focus:ring-0 text-slate-700 dark:text-slate-350">
                        <span class="absolute left-3.5 top-3.5 opacity-40">🔍</span>
                    </div>

                    <!-- Filière Filter -->
                    <div>
                        <select name="filiere_id" class="w-full py-2.5 text-xs bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-800 rounded-xl focus:border-blue-500 focus:ring-0 text-slate-700 dark:text-slate-350">
                            <option value="">-- {{ __('Toutes les Filières') }} --</option>
                            @foreach($filieres as $filiere)
                                <option value="{{ $filiere->id }}" {{ request('filiere_id') == $filiere->id ? 'selected' : '' }}>{{ $filiere->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <select name="status" class="w-full py-2.5 text-xs bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-800 rounded-xl focus:border-blue-500 focus:ring-0 text-slate-700 dark:text-slate-350">
                            <option value="">-- {{ __('Tous les statuts') }} --</option>
                            <option value="with_credits" {{ request('status') === 'with_credits' ? 'selected' : '' }}>{{ __('Avec Crédits en cours') }}</option>
                            <option value="derogation" {{ request('status') === 'derogation' ? 'selected' : '' }}>{{ __('Avec Dérogation') }}</option>
                            <option value="last_chance" {{ request('status') === 'last_chance' ? 'selected' : '' }}>{{ __('En "Dernière Chance"') }}</option>
                        </select>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 px-4 py-2.5 text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-md transition-all duration-150">
                            {{ __('Filtrer') }}
                        </button>
                        <a href="{{ route('admin.student_credits.index') }}" class="px-4 py-2.5 text-xs font-bold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl transition-all duration-150 text-center flex items-center justify-center">
                            {{ __('Réinitialiser') }}
                        </a>
                    </div>
                </form>
            </div>

            <!-- Students List Table -->
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[2rem] shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-950 border-b border-slate-100 dark:border-slate-850">
                                <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-wider">{{ __('Étudiant') }}</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-wider">{{ __('Filière & Niveau') }}</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-wider">{{ __('Statut dérogatoire') }}</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-wider">{{ __('Crédits Modules') }}</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-wider text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-850">
                            @forelse($students as $student)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors">
                                    <!-- Student Information -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-black text-sm shadow-sm">
                                                {{ substr($student->user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <h4 class="text-xs font-black text-slate-850 dark:text-white uppercase leading-snug">{{ $student->user->name }}</h4>
                                                <p class="text-[9px] text-slate-400 mt-0.5 font-bold tracking-wider">
                                                    🎓 {{ $student->student_number }} &nbsp;•&nbsp; 💳 CIN : <span class="text-slate-500">{{ $student->cin ?? '—' }}</span>
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Major / Level -->
                                    <td class="px-6 py-4">
                                        <p class="text-xs font-black text-slate-700 dark:text-slate-300">{{ $student->group->filiere->name ?? 'Non assignée' }}</p>
                                        <span class="inline-block mt-1 px-2.5 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-[8px] font-black rounded-full uppercase tracking-wider">
                                            {{ $student->group->name ?? 'GI' }} ({{ $student->group->level ?? 'Licence' }})
                                        </span>
                                    </td>

                                    <!-- Derogation Status Badge -->
                                    <td class="px-6 py-4">
                                        <div class="space-y-1">
                                            @if($student->has_derogation)
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-amber-500/10 text-amber-500 border border-amber-500/20 text-[9px] font-black rounded-full uppercase tracking-wider">
                                                    <span class="w-1 h-1 bg-amber-500 rounded-full animate-pulse"></span>
                                                    🛡️ {{ __('Dérogation Active') }}
                                                </span>
                                            @endif
                                            @if($student->is_last_chance)
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-rose-500/10 text-rose-500 border border-rose-500/20 text-[9px] font-black rounded-full uppercase tracking-wider">
                                                    ⚠️ {{ __('Dernière Chance') }}
                                                </span>
                                            @endif
                                            @if(!$student->has_derogation && !$student->is_last_chance)
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 text-[9px] font-black rounded-full uppercase tracking-wider">
                                                    🟢 {{ __('Régulier / Normal') }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Credit Modules -->
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1.5 max-w-[280px]">
                                            @forelse($student->creditModules as $credit)
                                                @php
                                                    $badgeClass = match($credit->pivot->status) {
                                                        'validated' => 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20',
                                                        'not_validated' => 'bg-rose-500/10 text-rose-500 border-rose-500/20',
                                                        default => 'bg-blue-500/10 text-blue-500 border-blue-500/20'
                                                    };
                                                    $statusText = match($credit->pivot->status) {
                                                        'validated' => 'Valide',
                                                        'not_validated' => 'Echec',
                                                        default => 'En crédit'
                                                    };
                                                @endphp
                                                <span class="px-2 py-0.5 text-[8px] font-black rounded border {{ $badgeClass }} uppercase tracking-wider" title="{{ $credit->name }}">
                                                    {{ $credit->code }} : {{ $statusText }}
                                                </span>
                                            @empty
                                                <span class="text-[10px] text-slate-350 italic font-bold">Aucun crédit actif</span>
                                            @endforelse
                                        </div>
                                    </td>

                                    <!-- Actions button links -->
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.student_credits.manage', $student->id) }}" 
                                               class="px-3.5 py-1.5 text-[10px] font-black uppercase tracking-wider bg-slate-50 hover:bg-slate-100 dark:bg-slate-800 dark:hover:bg-slate-700/80 border border-slate-100 dark:border-slate-800 text-slate-700 dark:text-white rounded-lg transition-all duration-150">
                                                🛠️ {{ __('Gérer crédits') }}
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-slate-400 italic font-bold">
                                        Aucun étudiant correspondant à vos filtres.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Footer -->
                @if($students->hasPages())
                    <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-850 bg-slate-50 dark:bg-slate-950/50">
                        {{ $students->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
