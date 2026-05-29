<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-upf-blue italic">📋 Justification d'Absence — Examen</h2>
            <a href="{{ route('student.exams.index') }}" class="text-xs font-bold text-gray-400 hover:text-upf-blue uppercase tracking-widest">← Mes Examens</a>
        </div>
    </x-slot>

    <div class="py-10 bg-[#F8FAFC]">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Infos Examen --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                <h3 class="font-black text-gray-900 text-lg italic mb-5">📝 Examen concerné</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-2xl p-4">
                        <div class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Module</div>
                        <div class="font-black text-gray-900">{{ $attendance->exam?->module?->name }}</div>
                    </div>
                    <div class="bg-gray-50 rounded-2xl p-4">
                        <div class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Date</div>
                        <div class="font-black text-gray-900">{{ \Carbon\Carbon::parse($attendance->exam?->date)->format('d/m/Y') }}</div>
                    </div>
                    <div class="bg-red-50 rounded-2xl p-4 border border-red-100">
                        <div class="text-[10px] font-black uppercase tracking-widest text-red-400 mb-1">Votre Statut</div>
                        <div class="font-black {{ $attendance->status === 'fraud' ? 'text-purple-700' : 'text-red-700' }}">
                            {{ $attendance->status_icon }} {{ $attendance->status_label }}
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded-2xl p-4">
                        <div class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Salle</div>
                        <div class="font-black text-gray-700">{{ $attendance->exam?->room?->name ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            {{-- Justification existante --}}
            @if($existing && $existing->status !== 'rejected')
            <div class="bg-amber-50 rounded-3xl p-6 border border-amber-100">
                <h3 class="font-black text-amber-800 mb-2">⏳ Justification déjà déposée</h3>
                <p class="text-amber-700 text-sm font-bold">
                    Statut : <span class="px-2 py-0.5 rounded-lg {{ $existing->status_color }} border">{{ $existing->status_label }}</span>
                </p>
                @if($existing->student_comment)
                <p class="text-amber-600 text-xs mt-2">Votre message : {{ $existing->student_comment }}</p>
                @endif
                @if($existing->admin_comment && $existing->status !== 'pending')
                <div class="mt-3 p-3 bg-white rounded-xl border border-amber-100">
                    <p class="text-xs font-bold text-amber-700">Réponse administration :</p>
                    <p class="text-sm font-bold text-gray-700 mt-1">{{ $existing->admin_comment }}</p>
                </div>
                @endif
            </div>
            @endif

            {{-- Formulaire --}}
            @if(!$existing || $existing->status === 'rejected')
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                <h3 class="font-black text-gray-900 text-lg italic mb-5">📎 Déposer une Justification</h3>

                <div class="bg-indigo-50 rounded-2xl p-4 border border-indigo-100 mb-6 text-sm text-indigo-700 font-bold">
                    ℹ️ Une justification approuvée vous accordera automatiquement le droit au rattrapage pour ce module.
                </div>

                <form action="{{ route('student.exam_justification.store', $attendance) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Motif / Commentaire</label>
                        <textarea name="student_comment" rows="3"
                            class="w-full border-gray-200 rounded-2xl p-4 font-bold text-gray-700 focus:ring-upf-blue focus:border-upf-blue bg-gray-50"
                            placeholder="Expliquez la raison de votre absence (maladie, urgence familiale, etc.)...">{{ old('student_comment') }}</textarea>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Pièce Justificative * (PDF, JPG, PNG — Max 5Mo)</label>
                        <div class="border-2 border-dashed border-gray-200 rounded-2xl p-8 text-center hover:border-upf-blue transition-colors cursor-pointer" id="drop-zone">
                            <input type="file" name="justification_file" id="justif-file" accept=".pdf,.jpg,.jpeg,.png" required class="hidden">
                            <label for="justif-file" class="cursor-pointer">
                                <div class="text-4xl mb-3">📎</div>
                                <p class="font-black text-gray-700 text-sm">Cliquez ou déposez votre fichier ici</p>
                                <p class="text-gray-400 text-xs mt-1">PDF, JPG, PNG — Max 5 Mo</p>
                            </label>
                            <div id="file-name" class="mt-3 text-sm font-bold text-upf-blue hidden"></div>
                        </div>
                        @error('justification_file')
                            <p class="text-red-500 text-xs font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                        class="w-full bg-upf-blue text-white py-4 rounded-2xl font-black uppercase tracking-widest text-sm hover:bg-upf-navy transition-all shadow-lg">
                        📤 Soumettre la Justification
                    </button>
                </form>
            </div>
            @endif

        </div>
    </div>

    <script>
        const fileInput = document.getElementById('justif-file');
        const fileNameDisplay = document.getElementById('file-name');
        if (fileInput) {
            fileInput.addEventListener('change', () => {
                if (fileInput.files.length > 0) {
                    fileNameDisplay.textContent = '✅ ' + fileInput.files[0].name;
                    fileNameDisplay.classList.remove('hidden');
                }
            });
        }
    </script>
</x-app-layout>
