<x-app-layout>
    <x-slot name="header">
        <x-page-header 
            title="{{ __('Gestion des Inscriptions & Réinscriptions') }}" 
            subtitle="{{ __('Validation des Candidatures & Dispatching des Groupes') }}"
            icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>'
        >
        </x-page-header>
    </x-slot>

    <div class="py-8 bg-[#F8FAFC] dark:bg-[#020617] min-h-screen transition-colors duration-300">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <x-alert-messages />

            <!-- Dispatching Console (Round-Robin balancer) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Dispatch card -->
                <div class="lg:col-span-1 bg-gradient-to-br from-upf-blue via-indigo-800 to-upf-navy dark:from-slate-900 dark:via-slate-950 dark:to-black rounded-[2.5rem] p-8 text-white shadow-xl relative overflow-hidden border border-white/5 flex flex-col justify-between">
                    <div class="relative z-10 space-y-4">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 text-white text-[10px] font-black uppercase tracking-widest w-fit">
                            ⚡ {{ __('Console de Dispatching') }}
                        </span>
                        <h3 class="text-2xl font-black italic tracking-tight">{{ __('Dispatching Équilibré') }} ⚖️</h3>
                        <p class="text-blue-100/90 text-xs leading-normal">
                            {{ __('Algorithme d\'affectation automatique : répartit équitablement tous les nouveaux étudiants approuvés dans les groupes du Semestre 1 de leur filière.') }}
                        </p>
                    </div>

                    <form action="{{ route('admin.registrations.auto_dispatch') }}" method="POST" class="mt-8 space-y-4 relative z-10 font-bold">
                        @csrf
                        <div class="space-y-2">
                            <label for="filiere_id" class="text-[9px] uppercase font-black text-blue-200 tracking-widest">{{ __('Sélectionnez la Filière') }}</label>
                            <select name="filiere_id" id="filiere_id" required
                                    class="w-full bg-slate-950/80 border-white/15 focus:border-pink-500 focus:ring-pink-500 rounded-xl text-xs text-white p-3.5 transition-all outline-none">
                                <option value="" disabled selected>{{ __('Choisir une filière') }}</option>
                                @foreach($filieres as $filiere)
                                    <option value="{{ $filiere->id }}">{{ $filiere->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="w-full py-4 bg-gradient-to-r from-upf-magenta to-pink-500 hover:from-pink-600 hover:to-upf-magenta text-white text-xs font-black uppercase tracking-widest rounded-2xl shadow-lg transition-all hover:scale-[1.02] transform">
                            ⚖️ {{ __('Affecter les groupes') }}
                        </button>
                    </form>
                    
                    <div class="absolute -bottom-20 -right-20 w-48 h-48 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
                </div>

                <!-- Stats Summary Cards -->
                <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <!-- Pending card -->
                    <x-card class="p-6 flex flex-col justify-between border-2 border-amber-500/20 bg-amber-500/[0.01]">
                        <div>
                            <span class="text-[9px] uppercase font-black text-amber-600 dark:text-amber-400 tracking-wider">{{ __('En Attente') }}</span>
                            <h4 class="text-4xl font-black mt-2 text-slate-850 dark:text-white">
                                {{ \App\Models\Student::where('registration_status', 'pending')->count() }}
                            </h4>
                        </div>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-4">{{ __('Dossiers nécessitant une vérification administrative.') }}</p>
                    </x-card>

                    <!-- Approved card -->
                    <x-card class="p-6 flex flex-col justify-between border-2 border-emerald-500/20 bg-emerald-500/[0.01]">
                        <div>
                            <span class="text-[9px] uppercase font-black text-emerald-600 dark:text-emerald-400 tracking-wider">{{ __('Inscrits Validés') }}</span>
                            <h4 class="text-4xl font-black mt-2 text-slate-850 dark:text-white">
                                {{ \App\Models\Student::where('registration_status', 'approved')->count() }}
                            </h4>
                        </div>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-4">{{ __('Candidatures approuvées et inscrites définitivement.') }}</p>
                    </x-card>

                    <!-- Rejected card -->
                    <x-card class="p-6 flex flex-col justify-between border-2 border-rose-500/20 bg-rose-500/[0.01]">
                        <div>
                            <span class="text-[9px] uppercase font-black text-rose-600 dark:text-rose-400 tracking-wider">{{ __('Rejetés / Suspendus') }}</span>
                            <h4 class="text-4xl font-black mt-2 text-slate-850 dark:text-white">
                                {{ \App\Models\Student::where('registration_status', 'rejected')->count() }}
                            </h4>
                        </div>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-4">{{ __('Dossiers incomplets ou non conformes aux prérequis.') }}</p>
                    </x-card>
                </div>
            </div>

            <!-- Filters Toolbar -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm p-6 font-bold">
                <form action="{{ route('admin.registrations.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                    <div class="space-y-2">
                        <label for="filter_filiere" class="text-[9px] uppercase font-black text-slate-400 dark:text-blue-400 tracking-widest">{{ __('Filière') }}</label>
                        <select name="filiere_id" id="filter_filiere" 
                                class="w-full bg-slate-50 dark:bg-slate-950 border-slate-200 dark:border-slate-800 rounded-xl text-xs p-3 transition-all outline-none font-bold text-slate-700 dark:text-white">
                            <option value="">{{ __('Toutes les filières') }}</option>
                            @foreach($filieres as $filiere)
                                <option value="{{ $filiere->id }}" {{ request('filiere_id') == $filiere->id ? 'selected' : '' }}>{{ $filiere->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label for="filter_status" class="text-[9px] uppercase font-black text-slate-400 dark:text-blue-400 tracking-widest">{{ __('Statut') }}</label>
                        <select name="status" id="filter_status" 
                                class="w-full bg-slate-50 dark:bg-slate-950 border-slate-200 dark:border-slate-800 rounded-xl text-xs p-3 transition-all outline-none font-bold text-slate-700 dark:text-white">
                            <option value="pending" {{ request('status', 'pending') == 'pending' ? 'selected' : '' }}>{{ __('En Attente') }}</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>{{ __('Approuvé') }}</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>{{ __('Rejeté') }}</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label for="filter_type" class="text-[9px] uppercase font-black text-slate-400 dark:text-blue-400 tracking-widest">{{ __('Type') }}</label>
                        <select name="type" id="filter_type" 
                                class="w-full bg-slate-50 dark:bg-slate-950 border-slate-200 dark:border-slate-800 rounded-xl text-xs p-3 transition-all outline-none font-bold text-slate-700 dark:text-white">
                            <option value="">{{ __('Tous') }}</option>
                            <option value="new" {{ request('type') == 'new' ? 'selected' : '' }}>{{ __('Inscription') }}</option>
                            <option value="reinscription" {{ request('type') == 'reinscription' ? 'selected' : '' }}>{{ __('Réinscription') }}</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full py-3.5 bg-slate-100 dark:bg-slate-800 hover:bg-upf-blue hover:text-white dark:hover:bg-blue-600 rounded-xl text-xs uppercase tracking-widest transition-all">
                        🔍 {{ __('Filtrer') }}
                    </button>
                </form>
            </div>

            <!-- Inscription Table Board -->
            <x-card class="p-0 overflow-hidden">
                <div class="border-b border-slate-100 dark:border-slate-800 px-8 py-5 flex items-center justify-between bg-slate-50/50 dark:bg-slate-950/20">
                    <h3 class="font-black text-slate-900 dark:text-white text-lg italic">{{ __('Registre des Inscriptions') }}</h3>
                    <span class="text-xs text-slate-400 font-bold">{{ $students->count() }} {{ __('dossier(s) affiché(s)') }}</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/70 border-b border-gray-100">
                                <th class="p-6 text-[10px] font-black uppercase tracking-widest text-gray-400">{{ __('Étudiant / Candidat') }}</th>
                                <th class="p-6 text-[10px] font-black uppercase tracking-widest text-gray-400">{{ __('Filière demandée') }}</th>
                                <th class="p-6 text-[10px] font-black uppercase tracking-widest text-gray-400">{{ __('Type / Inscription') }}</th>
                                <th class="p-6 text-[10px] font-black uppercase tracking-widest text-gray-400">{{ __('Cursus Bac') }}</th>
                                <th class="p-6 text-[10px] font-black uppercase tracking-widest text-gray-400">{{ __('Parents / CIN') }}</th>
                                <th class="p-6 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-bold text-slate-700 dark:text-slate-350">
                            @forelse($students as $student)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-950/40 transition-colors">
                                    
                                    <!-- Candidate identity -->
                                    <td class="p-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-upf-blue text-white flex items-center justify-center font-black text-sm flex-shrink-0 shadow-sm">
                                                {{ strtoupper(substr($student->user->name ?? '?', 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="font-black text-slate-900 dark:text-white text-sm leading-none">{{ $student->user->name ?? 'N/A' }}</p>
                                                <p class="text-[10px] text-slate-400 mt-1">CIN: {{ $student->cin }} · Prov: <code>{{ $student->student_number }}</code></p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Selected Filiere -->
                                    <td class="p-6 text-xs">
                                        <p class="font-black text-slate-850 dark:text-white">{{ $student->filiere->name ?? '—' }}</p>
                                        <span class="text-[9px] text-slate-400 uppercase font-black tracking-wider block mt-1">CODE: {{ $student->filiere->code ?? '—' }}</span>
                                    </td>

                                    <!-- Reg Type -->
                                    <td class="p-6">
                                        <span class="px-2.5 py-1 rounded-full text-[9px] font-black uppercase tracking-wider
                                            {{ $student->registration_type === 'reinscription' ? 'bg-indigo-50 text-indigo-600 border border-indigo-100 dark:bg-indigo-950/30 dark:text-indigo-400' : 'bg-pink-50 text-pink-600 border border-pink-100 dark:bg-pink-950/30 dark:text-pink-400' }}
                                        ">
                                            {{ $student->registration_type === 'reinscription' ? __('Réinscription') : __('Inscription') }}
                                        </span>
                                        @if($student->group)
                                            <span class="block text-[10px] text-slate-400 mt-1.5">Actuel: <strong>{{ $student->group->name }}</strong></span>
                                        @endif
                                    </td>

                                    <!-- Bac Details -->
                                    <td class="p-6 text-xs">
                                        @if($student->bac_grade)
                                            <p class="font-black text-slate-800 dark:text-white">Moy: <span class="text-emerald-500">{{ $student->bac_grade }}</span></p>
                                            <p class="text-[10px] text-slate-400 mt-0.5">{{ $student->bac_filiere }} ({{ $student->bac_mention }})</p>
                                        @else
                                            <span class="text-slate-400 italic">—</span>
                                        @endif
                                    </td>

                                    <!-- Parent & Birth -->
                                    <td class="p-6 text-xs">
                                        @if($student->father_name)
                                            <p class="font-bold">👨 {{ $student->father_name }} ({{ $student->father_occupation }})</p>
                                            <p class="font-bold mt-1">👩 {{ $student->mother_name }} ({{ $student->mother_occupation }})</p>
                                        @else
                                            <span class="text-slate-400 italic">—</span>
                                        @endif
                                    </td>

                                    <!-- Operations -->
                                    <td class="p-6 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            @if($student->registration_status === 'pending')
                                                <!-- Approve Form -->
                                                <form action="{{ route('admin.registrations.approve', $student) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="w-8 h-8 rounded-lg bg-emerald-50 hover:bg-emerald-500 text-emerald-600 hover:text-white flex items-center justify-center transition-all shadow-sm" title="{{ __('Approuver le dossier') }}">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                    </button>
                                                </form>

                                                <!-- Reject action -->
                                                <button type="button" 
                                                        onclick="
                                                            document.getElementById('reject-form').action = '{{ route('admin.registrations.reject', $student) }}';
                                                            document.getElementById('reject-student-name').textContent = '{{ $student->user->name }}';
                                                            document.getElementById('reject-modal').classList.remove('hidden');
                                                        "
                                                        class="w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-500 text-rose-600 hover:text-white flex items-center justify-center transition-all shadow-sm" title="{{ __('Rejeter le dossier') }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                            @else
                                                <span class="text-[9px] uppercase font-black px-2 py-0.5 border rounded
                                                    {{ $student->registration_status === 'approved' ? 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20' : 'bg-rose-500/10 text-rose-500 border-rose-500/20' }}
                                                ">
                                                    {{ $student->registration_status === 'approved' ? __('Validé') : __('Rejeté') }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-16 text-center">
                                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 dark:bg-slate-950 mb-4 shadow-inner">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        </div>
                                        <p class="text-slate-500 font-bold italic">{{ __('Aucune candidature en attente de validation.') }}</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>

    <!-- Rejection Dialog Modal -->
    <div id="reject-modal" class="fixed inset-0 z-50 overflow-y-auto hidden bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-8 max-w-md w-full shadow-2xl relative font-bold">
            <h3 class="text-lg font-black text-slate-850 dark:text-white italic tracking-tighter mb-2">{{ __('Rejeter la candidature') }}</h3>
            <p class="text-xs text-slate-400 dark:text-slate-500 mb-6">
                {{ __('Veuillez indiquer le motif du refus pour l\'étudiant :') }} <strong id="reject-student-name" class="text-slate-800 dark:text-slate-200">Ahmed</strong>.
            </p>

            <form id="reject-form" action="" method="POST" class="space-y-4">
                @csrf
                <div class="space-y-2">
                    <label for="rejection_reason" class="text-[9px] uppercase font-black text-slate-400 tracking-wider block">{{ __('Motif du refus') }}</label>
                    <textarea name="rejection_reason" id="rejection_reason" rows="4" required
                              class="w-full bg-slate-50 dark:bg-slate-950 border-slate-200 dark:border-slate-800 rounded-2xl p-4 text-xs font-bold text-slate-800 dark:text-white outline-none focus:ring-rose-500 focus:border-rose-500 shadow-sm"
                              placeholder="{{ __('Ex: Dossier incomplet, Bac non conforme...') }}"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" onclick="document.getElementById('reject-modal').classList.add('hidden')"
                            class="px-4 py-2 border border-slate-200 dark:border-slate-800 hover:bg-slate-100 rounded-xl text-xs uppercase tracking-widest text-slate-700 dark:text-slate-300">
                        {{ __('Annuler') }}
                    </button>
                    <button type="submit" class="px-6 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs uppercase tracking-widest shadow-md">
                        {{ __('Rejeter') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
