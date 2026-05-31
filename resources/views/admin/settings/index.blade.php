<x-app-layout>
    <x-slot name="header">
        <x-page-header 
            title="{{ __('Paramètres de l\'Institution') }}" 
            subtitle="{{ __('Administration Générale') }}"
            icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>'
            :breadcrumbs="[
                ['label' => 'Tableau de bord', 'url' => route('admin.dashboard')],
                ['label' => 'Paramètres', 'url' => '']
            ]"
        />
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <x-alert-messages />

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="p-8 space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            
                            <!-- Informations générales -->
                            <div class="space-y-6">
                                <h3 class="text-lg font-black text-upf-blue uppercase tracking-widest border-b border-gray-100 pb-2">Informations Générales</h3>
                                
                                <div>
                                    <x-input-label for="institution_name" :value="__('Nom de l\'Institution')" />
                                    <x-text-input id="institution_name" name="institution_name" type="text" class="mt-1 block w-full" :value="old('institution_name', $setting->institution_name)" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('institution_name')" />
                                </div>

                                <div>
                                    <x-input-label for="academic_year" :value="__('Année Académique Active')" />
                                    <x-text-input id="academic_year" name="academic_year" type="text" class="mt-1 block w-full" :value="old('academic_year', $setting->academic_year ?? '2025-2026')" required placeholder="Ex: 2025-2026" />
                                    <x-input-error class="mt-2" :messages="$errors->get('academic_year')" />
                                </div>

                                <div>
                                    <x-input-label for="official_email" :value="__('Email Officiel')" />
                                    <x-text-input id="official_email" name="official_email" type="email" class="mt-1 block w-full" :value="old('official_email', $setting->official_email)" />
                                </div>

                                <div>
                                    <x-input-label for="phone" :value="__('Téléphone')" />
                                    <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $setting->phone)" />
                                </div>

                                <div>
                                    <x-input-label for="address" :value="__('Adresse complète')" />
                                    <textarea id="address" name="address" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-2xl shadow-sm transition-all duration-300">{{ old('address', $setting->address) }}</textarea>
                                </div>
                            </div>

                            <!-- Fichiers -->
                            <div class="space-y-6">
                                <h3 class="text-lg font-black text-upf-blue uppercase tracking-widest border-b border-gray-100 pb-2">Identité Visuelle</h3>
                                
                                <div>
                                    <x-input-label for="logo" :value="__('Logo de l\'Institution')" />
                                    @if($setting->logo_path)
                                        <div class="my-3 p-3 bg-gray-50 rounded-xl inline-block">
                                            <img src="{{ Storage::url($setting->logo_path) }}" alt="Logo" class="h-16 object-contain">
                                        </div>
                                    @endif
                                    <input type="file" id="logo" name="logo" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-black file:bg-pink-50 file:text-upf-magenta hover:file:bg-pink-100 transition-colors" accept="image/*">
                                    <p class="mt-1 text-xs text-gray-400">JPEG, PNG, SVG (Max 2MB)</p>
                                </div>

                                <div>
                                    <x-input-label for="signature" :value="__('Cachet et Signature (pour les PDFs)')" />
                                    @if($setting->signature_path)
                                        <div class="my-3 p-3 bg-gray-50 rounded-xl inline-block">
                                            <img src="{{ Storage::url($setting->signature_path) }}" alt="Signature" class="h-20 object-contain">
                                        </div>
                                    @endif
                                    <input type="file" id="signature" name="signature" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-black file:bg-blue-50 file:text-upf-blue hover:file:bg-blue-100 transition-colors" accept="image/*">
                                    <p class="mt-1 text-xs text-gray-400">JPEG, PNG (Max 2MB). Fond transparent recommandé.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Campagnes d'Inscription & Réinscription -->
                        <div class="pt-6 border-t border-gray-100">
                            <h3 class="text-lg font-black text-upf-blue uppercase tracking-widest mb-4">Dates des Campagnes Académiques</h3>
                            <p class="text-sm text-slate-500 mb-4">Définissez les périodes d'ouverture et de fermeture des campagnes d'inscription (nouveaux étudiants) et de réinscription (étudiants actuels).</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6">
                                <!-- Inscription Période -->
                                <div class="space-y-4 p-5 bg-slate-50 rounded-2xl border border-gray-150">
                                    <p class="text-xs font-black text-pink-500 uppercase tracking-wider">📝 {{ __('Campagne d\'Inscription (Nouveaux Candidats)') }}</p>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <x-input-label for="inscription_start_date" :value="__('Date de Début')" />
                                            <input type="datetime-local" id="inscription_start_date" name="inscription_start_date" 
                                                   value="{{ old('inscription_start_date', $setting->inscription_start_date ? \Carbon\Carbon::parse($setting->inscription_start_date)->format('Y-m-d\TH:i') : '') }}"
                                                   class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm">
                                        </div>
                                        <div>
                                            <x-input-label for="inscription_end_date" :value="__('Date de Fin')" />
                                            <input type="datetime-local" id="inscription_end_date" name="inscription_end_date" 
                                                   value="{{ old('inscription_end_date', $setting->inscription_end_date ? \Carbon\Carbon::parse($setting->inscription_end_date)->format('Y-m-d\TH:i') : '') }}"
                                                   class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm">
                                        </div>
                                    </div>
                                </div>

                                <!-- Réinscription Période -->
                                <div class="space-y-4 p-5 bg-slate-50 rounded-2xl border border-gray-150">
                                    <p class="text-xs font-black text-upf-blue uppercase tracking-wider">🎓 {{ __('Campagne de Réinscription (Étudiants Actuels)') }}</p>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <x-input-label for="reinscription_start_date" :value="__('Date de Début')" />
                                            <input type="datetime-local" id="reinscription_start_date" name="reinscription_start_date" 
                                                   value="{{ old('reinscription_start_date', $setting->reinscription_start_date ? \Carbon\Carbon::parse($setting->reinscription_start_date)->format('Y-m-d\TH:i') : '') }}"
                                                   class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm">
                                        </div>
                                        <div>
                                            <x-input-label for="reinscription_end_date" :value="__('Date de Fin')" />
                                            <input type="datetime-local" id="reinscription_end_date" name="reinscription_end_date" 
                                                   value="{{ old('reinscription_end_date', $setting->reinscription_end_date ? \Carbon\Carbon::parse($setting->reinscription_end_date)->format('Y-m-d\TH:i') : '') }}"
                                                   class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Règlement -->
                        <div class="pt-6 border-t border-gray-100">
                            <h3 class="text-lg font-black text-upf-blue uppercase tracking-widest mb-4">Règlement des Examens</h3>
                            <p class="text-sm text-gray-500 mb-4">Ce texte sera affiché au bas des convocations d'examens imprimées.</p>
                            <textarea id="exam_rules" name="exam_rules" rows="6" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-2xl shadow-sm transition-all duration-300">{{ old('exam_rules', $setting->exam_rules) }}</textarea>
                        </div>

                    </div>

                    <div class="px-8 py-5 bg-gray-50 flex items-center justify-end border-t border-gray-100">
                        <x-primary-button>
                            {{ __('Enregistrer les Paramètres') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>

            <!-- Maintenance & Système -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mt-8">
                <div class="p-8 space-y-6">
                    <h3 class="text-lg font-black text-upf-blue uppercase tracking-widest border-b border-gray-100 pb-2">🛠️ Maintenance du Système &amp; Base de données</h3>
                    <p class="text-sm text-slate-500">
                        Exécutez les dernières mises à jour de structure de la base de données (migrations) en ligne pour activer et synchroniser toutes les nouvelles fonctionnalités de la plateforme (Prise de RDV, Messagerie, Devoirs, etc.).
                    </p>
                    
                    <div class="flex items-center gap-4 p-5 bg-amber-50/60 rounded-2xl border border-amber-100">
                        <span class="text-2xl">⚠️</span>
                        <div class="text-xs text-amber-800">
                            <strong>Note de sécurité :</strong> Cette action applique les changements de table en toute sécurité. Les données existantes (étudiants, professeurs, notes, etc.) ne seront pas altérées ni modifiées.
                        </div>
                    </div>

                    <form action="{{ route('admin.settings.run_migrations') }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir mettre à jour la base de données en ligne ?');">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-5 py-3 bg-upf-blue text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-700 transition-all shadow-md gap-2">
                            🔄 Mettre à jour la base de données
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
