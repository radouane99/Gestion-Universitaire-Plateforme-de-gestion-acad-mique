<x-app-layout>
    <x-slot name="header">
        <x-page-header 
            title="{{ __('Suivi de l\'Assiduité & Absences') }}" 
            subtitle="{{ __('Espace Étudiant') }}"
            icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
        >
        </x-page-header>
    </x-slot>

    <div class="py-6 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <div class="bg-gradient-to-br from-rose-600 to-rose-900 rounded-[2.5rem] p-10 text-white shadow-sm relative overflow-hidden">
                <div class="relative z-10">
                    <h2 class="text-3xl font-black mb-2">{{ __('Mon Assiduité Académique') }}</h2>
                    <p class="text-rose-100 opacity-80">{{ __('Consultez vos absences cumulées par module et déposez vos justificatifs officiels en ligne.') }}</p>
                </div>
                <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            </div>

            <!-- Absences cumulées par module -->
            <div class="space-y-4">
                <h3 class="text-xl font-black text-gray-900">{{ __('Cumul des Absences par Module') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @php
                        $grouped = $absences->groupBy(function($abs) {
                            return $abs->module->name ?? __('Séance standard');
                        });
                    @endphp
                    @forelse($grouped as $moduleName => $moduleAbsences)
                        @php
                            $unjustifiedCount = $moduleAbsences->where('is_justified', false)->count();
                            $justifiedCount = $moduleAbsences->where('is_justified', true)->count();
                        @endphp
                        <x-card class="p-6 flex flex-col justify-between hover:shadow-md transition-shadow">
                            <div>
                                <h4 class="text-sm font-black text-gray-900 uppercase tracking-tight leading-snug mb-2">{{ $moduleName }}</h4>
                                <p class="text-xs text-gray-400 font-bold">{{ __('Total Absences') }}: <span class="text-gray-900 font-black text-sm">{{ $moduleAbsences->count() }}</span></p>
                            </div>
                            <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-50">
                                <span class="bg-emerald-50 text-emerald-600 px-3 py-1 rounded-full text-[10px] font-black uppercase">{{ __('Justifiées') }}: {{ $justifiedCount }}</span>
                                <span class="bg-rose-50 text-rose-600 px-3 py-1 rounded-full text-[10px] font-black uppercase">{{ __('Non Justifiées') }}: {{ $unjustifiedCount }}</span>
                            </div>
                        </x-card>
                    @empty
                        <div class="bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-2xl p-6 md:col-span-3 text-center font-bold">
                            {{ __('Félicitations ! Vous n\'avez aucune absence cumulée.') }}
                        </div>
                    @endforelse
                </div>
            </div>

            <x-alert-messages />

            <!-- Historique des Absences et formulaire de dépôt -->
            <x-card class="p-0" x-data="{ openUploadId: null }">
                <div class="p-8 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-xl font-black text-gray-900">{{ __('Historique des Absences') }}</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/80">
                                <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ __('Date d\'Absence') }}</th>
                                <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ __('Module & Type') }}</th>
                                <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ __('Statut') }}</th>
                                <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right font-black">{{ __('Justificatif') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 font-bold text-gray-700">
                            @foreach($absences as $absence)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="p-6">
                                    <div class="text-gray-900 text-sm font-black">{{ \Carbon\Carbon::parse($absence->date)->format('d/m/Y') }}</div>
                                    <div class="text-[10px] text-gray-400 font-semibold mt-1">{{ __('ID Absence') }}: #{{ $absence->id }}</div>
                                </td>
                                <td class="p-6">
                                    <div class="text-gray-900 text-sm font-black">{{ $absence->module->name ?? __('Séance standard') }}</div>
                                    <div class="inline-block bg-gray-100 text-gray-600 px-2 py-0.5 rounded-md text-[10px] font-black mt-1 uppercase">{{ $absence->session_type }}</div>
                                </td>
                                <td class="p-6">
                                    @if($absence->is_justified)
                                        <span class="text-[10px] font-black uppercase bg-emerald-50 text-emerald-600 px-3 py-1 rounded-full border border-emerald-100 tracking-widest">{{ __('Justifiée') }}</span>
                                    @else
                                        <span class="text-[10px] font-black uppercase bg-rose-50 text-rose-600 px-3 py-1 rounded-full border border-rose-100 tracking-widest">{{ __('Non Justifiée') }}</span>
                                    @endif
                                </td>
                                <td class="p-6 text-right">
                                    <div class="flex justify-end items-center gap-3">
                                        <!-- Justification Status Badge -->
                                        @if($absence->justification_status === 'pending')
                                            <span class="text-[10px] font-black text-amber-500 uppercase tracking-widest bg-amber-50 px-3 py-1 rounded-xl">{{ __('Justificatif en attente') }}</span>
                                        @elseif($absence->justification_status === 'approved')
                                            <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest bg-emerald-50 px-3 py-1 rounded-xl">{{ __('Documents validés') }}</span>
                                        @else
                                            @if($absence->justification_status === 'rejected')
                                                <span class="text-[10px] font-black text-rose-500 uppercase tracking-widest bg-rose-50 px-3 py-1 rounded-xl mr-2">{{ __('Justificatif Rejeté') }}</span>
                                            @endif
                                            
                                            <button @click="openUploadId = (openUploadId === {{ $absence->id }} ? null : {{ $absence->id }})" class="px-4 py-2 bg-upf-blue hover:bg-upf-navy text-white text-[10px] font-black rounded-xl transition-all shadow-sm uppercase tracking-wider">
                                                {{ __('Déposer Justificatif') }}
                                            </button>
                                        @endif
                                    </div>

                                    <!-- Upload Form Container -->
                                    <div x-show="openUploadId === {{ $absence->id }}" x-transition class="mt-4 p-6 bg-gray-50 border border-gray-100 rounded-2xl text-left space-y-3">
                                        <form action="{{ route('student.absences.justify', $absence->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 block mb-2">{{ __('Fichier Justificatif (PDF, Image - Max 5Mo)') }}</label>
                                            <div class="flex items-center gap-3">
                                                <input type="file" name="justification_file" required class="w-full text-xs font-bold text-gray-500 border border-gray-200 bg-white rounded-xl p-2.5">
                                                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-3 rounded-xl text-xs font-black uppercase tracking-widest shadow-sm">
                                                    {{ __('Envoyer') }}
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($absences->isEmpty())
                <div class="p-24 text-center">
                    <div class="text-4xl mb-4">🌟</div>
                    <p class="text-gray-400 italic font-bold">{{ __('Aucune absence enregistrée. Félicitations pour votre assiduité !') }}</p>
                </div>
                @endif
            </x-card>
        </div>
    </div>
</x-app-layout>
