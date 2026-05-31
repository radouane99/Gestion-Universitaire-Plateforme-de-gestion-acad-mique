<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            @if(Auth::user()->isAdmin())
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @if(session('success'))
                            <div class="p-4 mb-4 bg-emerald-50 text-emerald-700 rounded-2xl border border-emerald-100 text-xs font-bold">
                                🎉 {{ session('success') }}
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="p-4 mb-4 bg-rose-50 text-rose-700 rounded-2xl border border-rose-100 text-xs font-bold">
                                ⚠️ {{ session('error') }}
                            </div>
                        @endif

                        @if(Auth::user()->google2fa_enabled)
                            <div class="space-y-6">
                                <header>
                                    <h2 class="text-lg font-black text-slate-900 italic tracking-tight flex items-center gap-2">
                                        <span class="text-emerald-500">🔒</span> Double Authentification (2FA) Active
                                    </h2>
                                    <p class="mt-1 text-xs text-slate-500">
                                        La sécurité de votre compte administrateur est renforcée par un mot de passe à usage unique (TOTP).
                                    </p>
                                </header>

                                <div class="p-5 bg-emerald-50/50 text-emerald-800 rounded-2xl border border-emerald-100/50 flex items-center gap-3">
                                    <span class="text-2xl">🛡️</span>
                                    <p class="text-xs font-black uppercase tracking-wider">Votre compte est hautement sécurisé par authentificateur mobile.</p>
                                </div>

                                <form method="POST" action="{{ route('admin.2fa.disable') }}" class="space-y-4">
                                    @csrf
                                    <div>
                                        <x-input-label for="password_2fa" :value="__('Confirmer votre mot de passe pour désactiver le 2FA')" />
                                        <x-text-input id="password_2fa" name="password" type="password" class="mt-1 block w-full" placeholder="Saisir mot de passe..." required />
                                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                    </div>

                                    <x-danger-button>
                                        Désactiver la Double Authentification
                                    </x-danger-button>
                                </form>
                            </div>
                        @else
                            <div x-data="{ showSetup: false, qrCodeUrl: '', secret: '', code: '' }" class="space-y-6">
                                <div x-show="!showSetup" class="space-y-6">
                                    <header>
                                        <h2 class="text-lg font-black text-slate-900 italic tracking-tight">
                                            🔒 Activer la Double Authentification (2FA)
                                        </h2>
                                        <p class="mt-1 text-xs text-slate-500">
                                            Ajoutez une couche de sécurité supplémentaire à votre compte administrateur en exigeant un code à 6 chiffres lors de la connexion.
                                        </p>
                                    </header>

                                    <button type="button" @click="
                                        fetch('{{ route('admin.2fa.init') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
                                        .then(r => r.json())
                                        .then(data => {
                                            secret = data.secret;
                                            qrCodeUrl = data.qr_code_url;
                                            showSetup = true;
                                        })
                                    " class="inline-flex items-center px-6 py-3 bg-[#003399] hover:bg-[#002277] text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-md">
                                        Activer le 2FA
                                    </button>
                                </div>

                                <div x-show="showSetup" class="space-y-6" x-cloak style="display:none;">
                                    <header>
                                        <h2 class="text-lg font-black text-slate-900 italic tracking-tight">
                                            Configurez votre Authentificateur
                                        </h2>
                                        <p class="mt-1 text-xs text-slate-500">
                                            Suivez ces instructions simples pour lier votre compte administrateur :
                                        </p>
                                    </header>

                                    <div class="space-y-4">
                                        <p class="text-xs font-bold text-slate-700">1. Scannez ce QR Code avec Google Authenticator ou Microsoft Authenticator :</p>
                                        <div class="flex justify-center p-4 bg-gray-50 border border-slate-100 rounded-2xl w-fit">
                                            <img :src="qrCodeUrl" class="w-48 h-48" alt="2FA QR Code">
                                        </div>

                                        <p class="text-xs font-bold text-slate-700 mt-4">Ou saisissez manuellement cette clé secrète dans votre application :</p>
                                        <code class="block p-3.5 bg-slate-950 text-emerald-400 font-mono text-sm font-black tracking-widest rounded-xl uppercase border border-slate-800" x-text="secret"></code>

                                        <p class="text-xs font-bold text-slate-700 mt-6">2. Entrez le code de vérification à 6 chiffres généré par l'application :</p>
                                        <form method="POST" action="{{ route('admin.2fa.confirm') }}" class="space-y-4">
                                            @csrf
                                            <div class="max-w-xs">
                                                <x-text-input name="code" type="text" class="block w-full text-center text-lg font-black tracking-[0.4em]" placeholder="000000" maxlength="6" required />
                                            </div>

                                            <div class="flex gap-3 pt-2">
                                                <button type="submit" class="px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-black uppercase tracking-wider rounded-xl transition-all shadow-sm">
                                                    Confirmer & Activer
                                                </button>
                                                <button type="button" @click="showSetup = false" class="px-5 py-3 bg-slate-150 text-slate-650 hover:bg-slate-200 text-[10px] font-black uppercase tracking-wider rounded-xl transition-all border border-slate-200">
                                                    Annuler
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
