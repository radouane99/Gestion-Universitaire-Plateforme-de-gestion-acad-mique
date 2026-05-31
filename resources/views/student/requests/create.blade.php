<x-app-layout>
    <x-slot name="header">
        <x-page-header 
            title="{{ __('Demande Administrative') }}" 
            subtitle="{{ __('Secrétariat Numérique') }}"
            icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>'
        >
        </x-page-header>
    </x-slot>

    <div class="py-6 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-gradient-to-br from-upf-navy to-upf-blue rounded-3xl p-10 text-white shadow-sm relative overflow-hidden">
                <div class="relative z-10">
                    <h2 class="text-3xl font-black mb-2">{{ __('Secrétariat Numérique') }}</h2>
                    <p class="text-blue-100 opacity-80">{{ __('Demandez vos documents officiels, attestations et relevés de notes en un clic.') }}</p>
                </div>
                <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-upf-magenta/10 rounded-full blur-3xl pointer-events-none"></div>
            </div>

            <x-alert-messages />

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Submission Form -->
                <x-card class="p-0 overflow-hidden lg:col-span-1">
                    <div class="p-8 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-xl font-black text-gray-900">{{ __('Nouvelle Demande') }}</h3>
                        <p class="text-gray-500 text-sm">{{ __('Sélectionnez le type de document souhaité.') }}</p>
                    </div>

                    <form action="{{ route('student.requests.store') }}" method="POST" class="p-8 space-y-6">
                        @csrf
                        
                        <div class="space-y-4">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">{{ __('Catégorie du Document') }}</label>
                            <div class="grid grid-cols-1 gap-3">
                                @foreach([
                                    'Attestation de Scolarité' => __('Attestation de Scolarité'),
                                    'Relevé de Notes' => __('Relevé de Notes'),
                                    'Convention de Stage' => __('Convention de Stage'),
                                    'Demande de Dérogation (Réinscription Exceptionnelle)' => __('Demande de Dérogation (Réinscription Exceptionnelle)')
                                ] as $value => $label)
                                <label class="relative flex items-center p-4 border-2 border-gray-100 rounded-2xl cursor-pointer hover:border-upf-blue transition-all group shadow-sm">
                                    <input type="radio" name="type" value="{{ $value }}" class="sr-only peer" required>
                                    <div class="w-5 h-5 border-2 border-gray-200 rounded-full mr-3 flex items-center justify-center transition-all peer-checked:border-upf-blue peer-checked:bg-upf-blue">
                                        <div class="w-2 h-2 bg-white rounded-full"></div>
                                    </div>
                                    <div>
                                        <p class="font-extrabold text-xs text-gray-900 group-hover:text-upf-blue transition-colors">{{ $label }}</p>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="space-y-2">
                            <x-input-label for="reason" :value="__('Notes Additionnelles (Optionnel)')" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block" />
                            <textarea name="reason" id="reason" rows="4" 
                                class="w-full border-gray-200 rounded-2xl focus:ring-upf-blue focus:border-upf-blue p-4 font-bold text-gray-900 bg-gray-50 text-xs shadow-sm"
                                placeholder="{{ __('Précisez l\'année universitaire ou d\'autres détails...') }}"></textarea>
                        </div>

                        <div class="pt-4">
                            <x-primary-button class="w-full justify-center py-4 text-xs tracking-widest">
                                {{ __('Soumettre la Demande') }}
                            </x-primary-button>
                        </div>
                    </form>
                </x-card>

                <!-- History -->
                <x-card class="p-0 overflow-hidden lg:col-span-2">
                    <div class="p-8 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                        <h3 class="text-xl font-black text-gray-900">{{ __('Mes Demandes Soumises') }}</h3>
                        <span class="text-xs text-gray-400 font-bold">{{ $requests->count() }} {{ __('demande(s)') }}</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/70 border-b border-gray-100">
                                    <th class="p-6 text-[10px] font-black uppercase tracking-widest text-gray-400">{{ __('Date') }}</th>
                                    <th class="p-6 text-[10px] font-black uppercase tracking-widest text-gray-400">{{ __('Document') }}</th>
                                    <th class="p-6 text-[10px] font-black uppercase tracking-widest text-gray-400">{{ __('Statut') }}</th>
                                    <th class="p-6 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 font-bold text-gray-700">
                                @forelse($requests as $req)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="p-6 text-sm text-gray-500">
                                            {{ $req->created_at->format('d/m/Y') }}
                                        </td>
                                        <td class="p-6">
                                            <div class="text-gray-900 text-sm font-black">{{ __($req->type) }}</div>
                                            @if($req->reason)
                                                <div class="text-[10px] text-gray-400 font-semibold mt-1">{{ __('Note') }}: {{ $req->reason }}</div>
                                            @endif
                                        </td>
                                        <td class="p-6">
                                            <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider
                                                {{ $req->status === 'approved' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : '' }}
                                                {{ $req->status === 'pending' ? 'bg-amber-50 text-amber-600 border border-amber-100' : '' }}
                                                {{ $req->status === 'rejected' ? 'bg-rose-50 text-rose-600 border border-rose-100' : '' }}
                                            ">
                                                {{ $req->status === 'approved' ? __('Approuvée') : ($req->status === 'pending' ? __('En Attente') : __('Refusée')) }}
                                            </span>
                                            @if($req->status === 'rejected' && $req->reason)
                                                <div class="text-[10px] text-rose-400 font-bold mt-1">{{ __('Motif') }}: {{ $req->reason }}</div>
                                            @endif
                                        </td>
                                        <td class="p-6 text-right">
                                            @if($req->status === 'approved')
                                                <div class="flex items-center justify-end gap-1.5">
                                                    <a href="{{ route('documents.download', $req->id) }}?preview=1" target="_blank" class="inline-flex items-center text-upf-blue hover:text-white font-black text-[10px] uppercase tracking-widest gap-1 bg-indigo-50 hover:bg-upf-blue px-3.5 py-2.5 rounded-xl transition-colors shadow-sm hover:shadow-md" title="{{ __('Aperçu avant téléchargement') }}">
                                                        👁️ {{ __('Aperçu') }}
                                                    </a>
                                                    <a href="{{ route('documents.download', $req->id) }}" class="inline-flex items-center text-indigo-700 hover:text-white font-black text-[10px] uppercase tracking-widest bg-indigo-100 hover:bg-indigo-600 p-2.5 rounded-xl transition-colors shadow-sm hover:shadow-md" title="{{ __('Télécharger directement') }}">
                                                        ⬇️
                                                    </a>
                                                </div>
                                            @else
                                                <span class="text-[10px] text-gray-400 italic font-bold">{{ __('Indisponible') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="p-16 text-center">
                                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4 shadow-inner">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            </div>
                                            <p class="text-gray-500 font-bold italic">{{ __('Aucune demande soumise pour le moment.') }}</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-card>
            </div>
        </div>
    </div>
</x-app-layout>
