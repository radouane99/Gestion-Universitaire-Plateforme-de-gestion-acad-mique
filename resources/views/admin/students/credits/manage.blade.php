<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.student_credits.index') }}" class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 hover:scale-105 transition-all shadow-sm">
                ⬅️
            </a>
            <div>
                <h2 class="font-black text-2xl text-slate-900 dark:text-white leading-tight tracking-tight italic">
                    {{ __('Dossier Crédits & Dérogation Exceptionnelle') }}
                </h2>
                <p class="text-xs text-slate-400 dark:text-slate-500 font-semibold uppercase tracking-wider mt-1">
                    {{ __('Fiche d\'aménagement et réinscription spéciale de ') }} <strong class="text-blue-600 dark:text-blue-400">{{ $student->user->name }}</strong>
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-10 bg-[#F8FAFC] dark:bg-[#020617] transition-colors duration-300 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left Side: Student Info & Derogation Form -->
            <div class="lg:col-span-1 space-y-8">
                <!-- Profile Summary Card -->
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[2rem] p-6 shadow-sm">
                    <h3 class="text-xs font-black uppercase text-slate-400 dark:text-slate-500 tracking-widest border-b border-slate-100 dark:border-slate-800 pb-3 mb-4">
                        👤 {{ __('Fiche Étudiant') }}
                    </h3>
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-base font-black shadow-md">
                            {{ substr($student->user->name, 0, 1) }}
                        </div>
                        <div>
                            <h4 class="text-sm font-black text-slate-850 dark:text-white uppercase leading-snug">{{ $student->user->name }}</h4>
                            <p class="text-[10px] text-slate-400 font-bold mt-0.5 tracking-wider">{{ $student->user->email }}</p>
                        </div>
                    </div>

                    <div class="space-y-4 text-xs">
                        <div class="flex justify-between py-2 border-b border-slate-50 dark:border-slate-800">
                            <span class="text-slate-400 dark:text-slate-500 font-bold uppercase">Matricule</span>
                            <span class="font-extrabold text-slate-700 dark:text-slate-350">{{ $student->student_number }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-50 dark:border-slate-800">
                            <span class="text-slate-400 dark:text-slate-500 font-bold uppercase">Code CIN</span>
                            <span class="font-extrabold text-slate-700 dark:text-slate-350">{{ $student->cin ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-50 dark:border-slate-800">
                            <span class="text-slate-400 dark:text-slate-500 font-bold uppercase">Filière</span>
                            <span class="font-extrabold text-slate-700 dark:text-slate-350">{{ $student->group->filiere->name ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="text-slate-400 dark:text-slate-500 font-bold uppercase">Groupe / Niveau</span>
                            <span class="font-extrabold text-slate-700 dark:text-slate-350">{{ $student->group->name ?? '—' }} ({{ $student->group->level ?? 'Licence' }})</span>
                        </div>
                    </div>
                </div>

                <!-- Derogation & Exception Form -->
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[2rem] p-6 shadow-sm">
                    <h3 class="text-xs font-black uppercase text-slate-400 dark:text-slate-500 tracking-widest border-b border-slate-100 dark:border-slate-800 pb-3 mb-4">
                        🛡️ {{ __('Cas Spéciaux / Dérogation') }}
                    </h3>
                    
                    <form method="POST" action="{{ route('admin.student_credits.update_derogation', $student->id) }}" class="space-y-5">
                        @csrf
                        @method('PUT')

                        <!-- Has Derogation Checkbox -->
                        <div class="flex items-start gap-3 p-3 bg-slate-50 dark:bg-slate-950 rounded-2xl border border-slate-100 dark:border-slate-850">
                            <input type="checkbox" name="has_derogation" id="has_derogation" value="1" {{ $student->has_derogation ? 'checked' : '' }}
                                   class="mt-1 text-blue-600 focus:ring-0 rounded border-slate-300 dark:border-slate-800 dark:bg-slate-900">
                            <div>
                                <label for="has_derogation" class="text-xs font-black text-slate-800 dark:text-white cursor-pointer select-none">
                                    {{ __('Accorder une Dérogation') }}
                                </label>
                                <p class="text-[9px] text-slate-400 mt-0.5 leading-snug font-bold">
                                    {{ __('Autoriser la réinscription exceptionnelle d\'un étudiant exclu pour double ajournement.') }}
                                </p>
                            </div>
                        </div>

                        <!-- Is Last Chance Checkbox -->
                        <div class="flex items-start gap-3 p-3 bg-slate-50 dark:bg-slate-950 rounded-2xl border border-slate-100 dark:border-slate-850">
                            <input type="checkbox" name="is_last_chance" id="is_last_chance" value="1" {{ $student->is_last_chance ? 'checked' : '' }}
                                   class="mt-1 text-rose-600 focus:ring-0 rounded border-slate-300 dark:border-slate-800 dark:bg-slate-900">
                            <div>
                                <label for="is_last_chance" class="text-xs font-black text-slate-800 dark:text-white cursor-pointer select-none">
                                    {{ __('Statut "Dernière Chance"') }}
                                </label>
                                <p class="text-[9px] text-slate-400 mt-0.5 leading-snug font-bold">
                                    {{ __('Marquer comme étudiant bénéficiant d\'une dernière chance absolue de validation.') }}
                                </p>
                            </div>
                        </div>

                        <!-- Derogation Administrative Note -->
                        <div class="space-y-1.5">
                            <label for="derogation_note" class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-500 tracking-wider">
                                {{ __('Note / Justification administrative') }}
                            </label>
                            <textarea name="derogation_note" id="derogation_note" rows="4" placeholder="Décision du conseil, raisons médicales, accord exceptionnel du doyen..."
                                      class="w-full text-xs bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-800 rounded-xl focus:border-blue-500 focus:ring-0 text-slate-700 dark:text-slate-300 p-3">{{ $student->derogation_note }}</textarea>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="w-full py-2.5 bg-slate-900 hover:bg-black dark:bg-blue-600 dark:hover:bg-blue-700 text-white text-xs font-black uppercase tracking-wider rounded-xl shadow-md transition-all duration-150">
                            {{ __('Enregistrer le statut dérogatoire') }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right Side: Credit Modules Management -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Add New Credit Module Form -->
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[2rem] p-6 shadow-sm">
                    <h3 class="text-xs font-black uppercase text-slate-400 dark:text-slate-500 tracking-widest border-b border-slate-100 dark:border-slate-800 pb-3 mb-5">
                        ➕ {{ __('Affecter un module en Crédit') }}
                    </h3>
                    
                    <form method="POST" action="{{ route('admin.student_credits.add', $student->id) }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        @csrf

                        <!-- Module Selection -->
                        <div class="space-y-1.5 md:col-span-2">
                            <label for="module_id" class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-500 tracking-wider">
                                {{ __('Sélectionner le module (Filière ') }}{{ $student->group->filiere->name ?? '' }}{{ ')' }}
                            </label>
                            <select name="module_id" id="module_id" required
                                    class="w-full py-2.5 text-xs bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-800 rounded-xl focus:border-blue-500 focus:ring-0 text-slate-700 dark:text-slate-350">
                                <option value="" disabled selected>-- Choisir le module --</option>
                                @forelse($filiereModules as $mod)
                                    <option value="{{ $mod->id }}">{{ $mod->code }} : {{ $mod->name }} (Coeff : {{ $mod->coefficient }})</option>
                                @empty
                                    <option value="" disabled>Aucun module restant disponible</option>
                                @endforelse
                            </select>
                        </div>

                        <!-- Status Selection -->
                        <input type="hidden" name="status" value="pending">

                        <!-- Submit Button -->
                        <div>
                            <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black uppercase tracking-wider rounded-xl shadow-md transition-all duration-150">
                                {{ __('Créer le crédit') }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Active Credits List Table -->
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[2rem] shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-slate-100 dark:border-slate-850">
                        <h3 class="text-xs font-black uppercase text-slate-400 dark:text-slate-500 tracking-widest">
                            📚 {{ __('Modules actuellement en Crédit') }}
                        </h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse text-left">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-950 border-b border-slate-100 dark:border-slate-850">
                                    <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-wider">{{ __('Module') }}</th>
                                    <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-wider">{{ __('Année d\'affectation') }}</th>
                                    <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-wider">{{ __('Statut') }}</th>
                                    <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-wider text-right">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-850">
                                @forelse($student->creditModules as $credit)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors">
                                        <!-- Module Details -->
                                        <td class="px-6 py-4">
                                            <span class="inline-block px-2.5 py-0.5 bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400 text-[9px] font-black rounded uppercase tracking-wider">
                                                {{ $credit->code }}
                                            </span>
                                            <h4 class="text-xs font-black text-slate-800 dark:text-white mt-1 leading-snug">{{ $credit->name }}</h4>
                                        </td>

                                        <!-- Academic Year -->
                                        <td class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400">
                                            {{ $credit->pivot->academic_year_id ? \App\Models\AcademicYear::find($credit->pivot->academic_year_id)->name : ($currentYear->name ?? '—') }}
                                        </td>

                                        <!-- Status Controller -->
                                        <td class="px-6 py-4">
                                            <form method="POST" action="{{ route('admin.student_credits.update', [$student->id, $credit->id]) }}" class="inline-block">
                                                @csrf
                                                @method('PUT')
                                                <select name="status" onchange="this.form.submit()" 
                                                        class="py-1.5 pl-2 pr-8 text-[10px] font-black uppercase tracking-wider bg-slate-50 dark:bg-slate-950 border border-slate-150 dark:border-slate-800 rounded-lg focus:ring-0 text-slate-700 dark:text-slate-350">
                                                    <option value="pending" {{ $credit->pivot->status === 'pending' ? 'selected' : '' }}>⌛ {{ __('En crédit') }}</option>
                                                    <option value="validated" {{ $credit->pivot->status === 'validated' ? 'selected' : '' }}>✅ {{ __('Validé') }}</option>
                                                    <option value="not_validated" {{ $credit->pivot->status === 'not_validated' ? 'selected' : '' }}>❌ {{ __('Non validé') }}</option>
                                                </select>
                                            </form>
                                        </td>

                                        <!-- Detach Credit Action -->
                                        <td class="px-6 py-4 text-right">
                                            <form method="POST" action="{{ route('admin.student_credits.remove', [$student->id, $credit->id]) }}" class="inline-block" onsubmit="return confirm('Voulez-vous vraiment retirer ce crédit de module ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-2.5 py-1.5 text-[9px] font-black uppercase tracking-wider bg-rose-500/10 text-rose-500 border border-rose-500/20 rounded-lg hover:bg-rose-500 hover:text-white transition-colors duration-150">
                                                    🗑️ {{ __('Retirer') }}
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-10 text-center text-slate-400 italic font-bold">
                                            Cet étudiant n'a aucun module en crédit en cours.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
