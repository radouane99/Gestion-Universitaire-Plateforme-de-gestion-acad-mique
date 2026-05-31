<x-guest-layout>
    <div class="mb-6 text-center">
        <div class="inline-flex items-center justify-center w-14 h-14 bg-indigo-50 dark:bg-slate-800 rounded-2xl text-2xl mb-4 shadow-inner">
            🔒
        </div>
        <h3 class="text-lg font-black text-slate-900 dark:text-white tracking-tight italic">Double Authentification</h3>
        <p class="mt-2 text-xs text-slate-400 font-medium leading-relaxed">
            Saisissez le code à 6 chiffres généré par votre application d'authentification (Google/Microsoft Authenticator) pour confirmer votre identité administrateur.
        </p>
    </div>

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="p-4 mb-4 bg-rose-50 text-rose-700 rounded-2xl border border-rose-100 text-xs font-bold">
            @foreach ($errors->all() as $error)
                <p>⚠️ {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('admin.2fa.verify') }}" class="space-y-6">
        @csrf

        <!-- TOTP Code -->
        <div>
            <x-input-label for="code" :value="__('Code de sécurité')" class="text-center font-bold text-slate-400" />
            <input id="code" name="code" type="text" 
                   class="block mt-1 w-full text-center text-2xl font-black tracking-[0.5em] border-gray-200 dark:border-slate-800 rounded-xl bg-gray-50 dark:bg-slate-950 p-3 text-slate-900 dark:text-white focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                   placeholder="000000" maxlength="6" required autofocus autocomplete="off" />
        </div>

        <div class="flex items-center justify-between pt-2">
            <a href="{{ route('logout') }}" 
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
               class="text-xs font-black text-slate-400 hover:text-red-500 uppercase tracking-widest transition-colors">
                🚪 Annuler
            </a>
            
            <button type="submit" class="px-6 py-3.5 bg-[#003399] hover:bg-[#002277] text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-md">
                Vérifier & Valider
            </button>
        </div>
    </form>

    <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
        @csrf
    </form>
</x-guest-layout>
