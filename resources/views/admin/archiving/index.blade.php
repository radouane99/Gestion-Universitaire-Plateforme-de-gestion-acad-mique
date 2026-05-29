<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tighter italic">🗄️ Archivage Annuel & Basculement (Rollover)</h2>
    </x-slot>

    <div class="py-10 bg-[#F8FAFC]">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-2xl font-bold text-sm shadow-sm">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-red-50 border border-red-100 text-red-800 rounded-2xl font-bold text-sm shadow-sm">{{ session('error') }}</div>
            @endif

            {{-- Danger warning banner --}}
            <div class="bg-gradient-to-r from-red-600 to-rose-800 rounded-3xl p-8 text-white shadow-xl relative overflow-hidden">
                <div class="absolute top-0 right-0 w-48 h-48 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="relative z-10 space-y-3">
                    <span class="bg-red-900/50 text-white px-3 py-1 rounded-full text-xs font-black uppercase tracking-widest border border-red-500/30">Zone Critique</span>
                    <h1 class="text-3xl font-black italic">Opération de Basculement Annuel</h1>
                    <p class="text-red-100 text-sm leading-relaxed">
                        Cette opération clôture l'année académique active <strong>{{ $currentYear?->name ?? 'inconnue' }}</strong>. 
                        Toutes les notes, absences et examens actifs seront figés et archivés sous cette année.<br>
                        Les soldes actifs d'absences seront réinitialisés à <strong>0h</strong> pour permettre le démarrage propre de la rentrée.
                    </p>
                </div>
            </div>

            {{-- Rollover Form --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-6">
                <h3 class="font-black text-gray-900 text-lg italic border-b border-gray-100 pb-3">⚙️ Lancer le processus de Rollover</h3>

                <form action="{{ route('admin.archiving.rollover') }}" method="POST" class="space-y-6" onsubmit="return confirmRollover()">
                    @csrf

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Nouvelle Année Académique *</label>
                        <input type="text" name="next_year_name" required placeholder="Ex: 2026-2027"
                            class="w-full border-gray-200 rounded-2xl p-4 font-bold text-gray-700 focus:ring-red-400 focus:border-red-400 bg-gray-50">
                        <p class="text-gray-400 text-xs font-bold">Entrez le nom de la nouvelle année qui démarrera immédiatement après le rollover.</p>
                        @error('next_year_name')
                            <p class="text-red-500 text-xs font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-3 p-5 bg-red-50 rounded-2xl border border-red-100">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">⚠️</span>
                            <h4 class="font-black text-red-800 text-sm">Confirmation obligatoire de sécurité</h4>
                        </div>
                        <p class="text-red-700 text-xs font-bold">
                            Pour valider cette action irréversible, veuillez saisir textuellement la phrase suivante :<br>
                            <strong class="text-red-900 font-extrabold select-all">ARCHIVER {{ $settings?->academic_year ?? '2025-2026' }}</strong>
                        </p>
                        <input type="text" name="confirmation" id="confirmation-text" required autocomplete="off"
                            class="w-full border-red-200 rounded-xl p-3 text-sm font-black bg-white focus:ring-red-400 focus:border-red-400"
                            placeholder="Saisissez la phrase de confirmation de sécurité...">
                        @error('confirmation')
                            <p class="text-red-500 text-xs font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                        class="w-full bg-red-600 text-white py-4 rounded-2xl font-black uppercase tracking-widest text-sm hover:bg-red-700 transition-all shadow-lg">
                        🚀 Lancer l'archivage et le rollover annuel
                    </button>
                </form>
            </div>

        </div>
    </div>

    <script>
        const expectedPhrase = "ARCHIVER {{ $settings?->academic_year ?? '2025-2026' }}";
        
        function confirmRollover() {
            const inputVal = document.getElementById('confirmation-text').value.trim();
            if (inputVal !== expectedPhrase) {
                alert("La phrase de confirmation de sécurité est incorrecte. Veuillez la ressaisir.");
                return false;
            }
            return confirm("⚠️ Êtes-vous ABSOLUMENT certain de vouloir clôturer l'année académique ? Toutes les données actives seront archivées.");
        }
    </script>
</x-app-layout>
