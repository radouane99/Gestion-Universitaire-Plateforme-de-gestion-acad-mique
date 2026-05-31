<x-app-layout>
    <x-slot name="header">
        <x-page-header 
            title="{{ __('Secrétariat Enseignant') }}" 
            subtitle="{{ __('Demandes Administratives') }}"
            icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>'
        >
        </x-page-header>
    </x-slot>

    <div class="py-6 bg-[#F8FAFC]" x-data="{ docType: '' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <x-alert-messages />

            <div class="bg-gradient-to-r from-upf-blue to-upf-navy rounded-3xl p-10 text-white shadow-sm relative overflow-hidden">
                <div class="relative z-10">
                    <h2 class="text-3xl font-black mb-2 italic">{{ __('Demandes Administratives') }}</h2>
                    <p class="text-blue-100 opacity-80">{{ __('Demandez vos attestations de travail ou vos ordres de mission officiels en un instant.') }}</p>
                </div>
                <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-upf-magenta/10 rounded-full blur-3xl"></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Formulaire -->
                <x-card class="p-0 overflow-hidden lg:col-span-1 border-0">
                    <div class="p-8 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-xl font-black text-gray-900 italic">{{ __('Nouvelle Demande') }}</h3>
                        <p class="text-gray-500 text-sm">{{ __('Sélectionnez le document à générer.') }}</p>
                    </div>

                    <form action="{{ route('professor.requests.store') }}" method="POST" class="p-8 space-y-6">
                        @csrf
                        
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">{{ __('Type de Document') }}</label>
                            <div class="grid grid-cols-1 gap-3">
                                <label class="relative flex items-center p-4 border-2 border-gray-100 rounded-2xl cursor-pointer hover:border-upf-blue transition-all group shadow-sm">
                                    <input type="radio" name="type" value="Attestation de Travail" x-model="docType" class="sr-only" required>
                                    <div class="w-5 h-5 border-2 border-gray-200 rounded-full mr-3 flex items-center justify-center transition-all" :class="docType === 'Attestation de Travail' ? 'border-upf-blue bg-upf-blue' : ''">
                                        <div class="w-2 h-2 bg-white rounded-full"></div>
                                    </div>
                                    <div>
                                        <p class="font-extrabold text-xs text-gray-900 group-hover:text-upf-blue transition-colors">{{ __('Attestation de Travail') }}</p>
                                    </div>
                                </label>

                                <label class="relative flex items-center p-4 border-2 border-gray-100 rounded-2xl cursor-pointer hover:border-upf-blue transition-all group shadow-sm">
                                    <input type="radio" name="type" value="Ordre de Mission" x-model="docType" class="sr-only" required>
                                    <div class="w-5 h-5 border-2 border-gray-200 rounded-full mr-3 flex items-center justify-center transition-all" :class="docType === 'Ordre de Mission' ? 'border-upf-blue bg-upf-blue' : ''">
                                        <div class="w-2 h-2 bg-white rounded-full"></div>
                                    </div>
                                    <div>
                                        <p class="font-extrabold text-xs text-gray-900 group-hover:text-upf-blue transition-colors">{{ __('Ordre de Mission') }}</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Champs spécifiques à l'Ordre de Mission -->
                        <div x-show="docType === 'Ordre de Mission'" x-transition class="space-y-4 pt-4 border-t border-gray-100">
                            <div class="space-y-2">
                                <x-input-label for="destination" :value="__('Destination')" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block" />
                                <x-text-input type="text" name="destination" id="destination" placeholder="{{ __('Ex: Casablanca, Maroc') }}" class="w-full text-xs font-bold bg-gray-50" />
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <x-input-label for="start_date" :value="__('Date Début')" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block" />
                                    <x-text-input type="date" name="start_date" id="start_date" class="w-full text-xs font-bold bg-gray-50" />
                                </div>
                                <div class="space-y-2">
                                    <x-input-label for="end_date" :value="__('Date Fin')" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block" />
                                    <x-text-input type="date" name="end_date" id="end_date" class="w-full text-xs font-bold bg-gray-50" />
                                </div>
                            </div>

                            <div class="space-y-2">
                                <x-input-label for="mission_reason" :value="__('Motif de la Mission')" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block" />
                                <textarea name="mission_reason" id="mission_reason" rows="3" placeholder="{{ __('Ex: Participation au séminaire national de recherche...') }}" class="w-full border-gray-200 rounded-2xl focus:ring-upf-blue focus:border-upf-blue p-3 text-xs font-bold text-gray-900 bg-gray-50 shadow-sm"></textarea>
                            </div>
                        </div>

                        <div class="pt-4">
                            <x-primary-button class="w-full justify-center py-3 text-xs tracking-widest">
                                {{ __('Soumettre la Demande') }}
                            </x-primary-button>
                        </div>
                    </form>
                </x-card>

                <!-- Historique enrichi avec workflow -->
                <x-card class="p-0 overflow-hidden lg:col-span-2 border-0">
                    <div class="p-8 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                        <h3 class="text-xl font-black text-gray-900 italic">{{ __('Mes Demandes & Documents') }}</h3>
                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">{{ $requests->count() }} {{ __('demande(s)') }}</span>
                    </div>

                    {{-- Workflow Steps Banner --}}
                    <div class="px-8 py-5 bg-gradient-to-r from-upf-blue/5 to-upf-magenta/5 border-b border-gray-100">
                        <div class="flex items-center gap-0">
                            @foreach([
                                ['icon'=>'✏️','label'=>__('Soumission'),'color'=>'upf-blue'],
                                ['icon'=>'⏳','label'=>__('En Attente'),'color'=>'amber-500'],
                                ['icon'=>'✅','label'=>__('Validation Admin'),'color'=>'emerald-600'],
                                ['icon'=>'📥','label'=>__('Téléchargement PDF'),'color'=>'upf-magenta'],
                            ] as $i => $step)
                            <div class="flex items-center flex-1">
                                <div class="flex flex-col items-center text-center flex-1">
                                    <span class="text-lg">{{ $step['icon'] }}</span>
                                    <span class="text-[9px] font-black uppercase tracking-widest text-gray-500 mt-1">{{ $step['label'] }}</span>
                                </div>
                                @if($i < 3)
                                <div class="flex-1 h-px bg-gray-200 max-w-8 hidden sm:block"></div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>

                    @forelse($requests as $req)
                    <div class="p-6 border-b border-gray-50 hover:bg-gray-50/50 transition-all group">
                        <div class="flex items-start justify-between gap-4">
                            {{-- Left: Icon + Info --}}
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-inner
                                    {{ $req->status === 'approved' ? 'bg-emerald-50' : ($req->status === 'rejected' ? 'bg-rose-50' : 'bg-amber-50') }}">
                                    @if($req->status === 'approved')
                                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    @elseif($req->status === 'rejected')
                                        <svg class="w-6 h-6 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    @else
                                        <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-black text-gray-900 text-sm">{{ __($req->type) }}</p>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">
                                        {{ __('Soumis le') }} {{ $req->created_at->format('d/m/Y à H:i') }}
                                    </p>
                                    @if($req->type === 'Ordre de Mission' && is_array($req->data))
                                        <div class="flex gap-4 mt-2">
                                            <span class="text-[10px] bg-blue-50 text-upf-blue px-2 py-0.5 rounded-full font-black">📍 {{ $req->data['destination'] ?? 'N/A' }}</span>
                                            <span class="text-[10px] bg-gray-50 text-gray-500 px-2 py-0.5 rounded-full font-bold">
                                                {{ \Carbon\Carbon::parse($req->data['start_date'] ?? now())->format('d/m') }} → {{ \Carbon\Carbon::parse($req->data['end_date'] ?? now())->format('d/m/Y') }}
                                            </span>
                                        </div>
                                    @endif
                                    @if($req->status === 'rejected' && $req->reason)
                                        <div class="mt-2 text-[10px] text-rose-500 font-bold bg-rose-50 px-3 py-1 rounded-xl inline-block">
                                            ❌ {{ __('Motif du refus') }} : {{ $req->reason }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Right: Status + Action --}}
                            <div class="flex flex-col items-end gap-3 flex-shrink-0">
                                {{-- Status Badge --}}
                                @if($req->status === 'approved')
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-emerald-50 text-emerald-600 border border-emerald-100">✓ {{ __('Approuvée') }}</span>
                                @elseif($req->status === 'rejected')
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-rose-50 text-rose-600 border border-rose-100">✗ {{ __('Refusée') }}</span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-amber-50 text-amber-600 border border-amber-100 animate-pulse">⏳ {{ __('En Attente') }}</span>
                                @endif

                                {{-- Download Button --}}
                                @if($req->status === 'approved')
                                    <div class="flex items-center gap-1.5 mt-2">
                                        <a href="{{ route('documents.download', $req->id) }}?preview=1" target="_blank"
                                           class="inline-flex items-center gap-1.5 bg-indigo-50 text-upf-blue hover:bg-upf-blue hover:text-white px-3.5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm hover:shadow-md" title="{{ __('Aperçu avant téléchargement') }}">
                                            👁️ {{ __('Aperçu') }}
                                        </a>
                                        <a href="{{ route('documents.download', $req->id) }}"
                                           class="inline-flex items-center justify-center bg-upf-blue text-white hover:bg-upf-navy p-2.5 rounded-xl transition-all shadow-sm hover:shadow-md" title="{{ __('Télécharger directement') }}">
                                            ⬇️
                                        </a>
                                    </div>
                                @else
                                    <span class="text-[10px] text-gray-300 font-bold italic">{{ __('PDF indisponible') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="flex flex-col items-center justify-center py-20 text-center px-8">
                        <div class="text-5xl mb-4">📋</div>
                        <p class="font-black text-gray-400 text-sm">{{ __('Aucune demande soumise pour le moment.') }}</p>
                        <p class="text-gray-300 text-xs mt-2">{{ __('Utilisez le formulaire ci-contre pour soumettre votre première demande.') }}</p>
                    </div>
                    @endforelse
                </x-card>
            </div>
        </div>
    </div>
</x-app-layout>
