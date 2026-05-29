<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-upf-blue italic">💬 Déposer une Réclamation de Note</h2>
            <a href="{{ route('student.reclamations.index') }}" class="text-xs font-bold text-gray-400 hover:text-upf-blue uppercase tracking-widest">← Retour</a>
        </div>
    </x-slot>

    <div class="py-10 bg-[#F8FAFC]">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('error'))
                <div class="p-4 bg-red-50 border border-red-100 text-red-800 rounded-2xl font-bold text-sm">{{ session('error') }}</div>
            @endif

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                <h3 class="font-black text-gray-900 text-lg italic mb-6">✏️ Rédiger mon Recours</h3>

                <form action="{{ route('student.reclamations.store') }}" method="POST" class="space-y-5">
                    @csrf

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Note concernée *</label>
                        <select name="grade_id" required class="w-full border-gray-200 rounded-xl p-4 text-sm font-bold bg-gray-50 focus:ring-upf-blue focus:border-upf-blue">
                            <option value="">Sélectionner une note de module...</option>
                            @foreach($grades as $grade)
                            <option value="{{ $grade->id }}">
                                {{ $grade->module?->name }} (Note Finale : {{ number_format($grade->final_grade, 2) }}/20)
                            </option>
                            @endforeach
                        </select>
                        @error('grade_id')
                            <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Motif de la contestation * (Expliquez en détail)</label>
                        <textarea name="reason" rows="6" required minlength="10"
                            class="w-full border-gray-200 rounded-2xl p-4 font-bold text-gray-700 focus:ring-upf-blue focus:border-upf-blue bg-gray-50"
                            placeholder="Veuillez exposer précisément l'erreur constatée (ex: erreur de saisie sur l'examen, oubli d'une note de CC, etc.)...">{{ old('reason') }}</textarea>
                        @error('reason')
                            <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                        class="w-full bg-upf-blue text-white py-4 rounded-2xl font-black uppercase tracking-widest text-sm hover:bg-upf-navy transition-all shadow-lg">
                        📤 Soumettre mon Recours
                    </button>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
