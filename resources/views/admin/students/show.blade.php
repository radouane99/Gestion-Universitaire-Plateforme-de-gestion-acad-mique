<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-400 to-amber-600 text-white flex items-center justify-center font-black text-xl shadow-md">
                {{ substr($student->name, 0, 1) }}
            </div>
            <div>
                <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tighter italic">
                    Dossier Étudiant
                </h2>
                <p class="text-sm text-gray-500 font-bold mt-1">Consultation du profil et des accès</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-[#F8FAFC]">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-gray-100">
                <div class="p-8 sm:p-10">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Identité -->
                        <div>
                            <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Identité</h3>
                            <div class="space-y-4">
                                <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100">
                                    <div class="text-[10px] uppercase font-black tracking-widest text-gray-500 mb-1">Nom Complet</div>
                                    <div class="text-lg font-bold text-gray-900">{{ $student->name }}</div>
                                </div>
                                <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100">
                                    <div class="text-[10px] uppercase font-black tracking-widest text-gray-500 mb-1">Adresse Email</div>
                                    <div class="text-lg font-bold text-upf-blue">{{ $student->email }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Informations Pédagogiques -->
                        <div>
                            <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Scolarité</h3>
                            <div class="space-y-4">
                                <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100">
                                    <div class="text-[10px] uppercase font-black tracking-widest text-gray-500 mb-1">Matricule</div>
                                    <div class="text-lg font-bold text-gray-900">{{ $student->student->student_number ?? 'Non défini' }}</div>
                                </div>
                                <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100">
                                    <div class="text-[10px] uppercase font-black tracking-widest text-gray-500 mb-1">Classe Actuelle</div>
                                    <div class="text-lg font-bold text-gray-900">
                                        @if($student->student && $student->student->group)
                                            {{ $student->student->group->name }} ({{ $student->student->group->level }})
                                        @else
                                            <span class="text-gray-400 italic">Non assigné</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @php
                                $currentYear = \App\Models\AcademicYear::where('is_current', true)->first() ?? \App\Models\AcademicYear::latest()->first();
                            @endphp
                            
                            @if($currentYear && $student->student)
                            <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mt-8 mb-4">Documents Officiels ({{ $currentYear->name }})</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <a href="{{ route('admin.documents.releve', ['student' => $student->student->id, 'academicYear' => $currentYear->id]) }}" class="flex flex-col items-center justify-center p-4 bg-upf-blue text-white rounded-2xl hover:bg-blue-800 transition-colors shadow-md">
                                    <svg class="w-6 h-6 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <span class="text-xs font-bold uppercase text-center">Relevé de Notes</span>
                                </a>
                                <a href="{{ route('admin.documents.attestation', ['student' => $student->student->id, 'academicYear' => $currentYear->id]) }}" class="flex flex-col items-center justify-center p-4 bg-emerald-600 text-white rounded-2xl hover:bg-emerald-700 transition-colors shadow-md">
                                    <svg class="w-6 h-6 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                                    <span class="text-xs font-bold uppercase text-center">Attestation de Réussite</span>
                                </a>
                            </div>
                            @endif

                            @if($student->student)
                            <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mt-8 mb-4">Conseiller Pédagogique IA</h3>
                            <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-5 relative overflow-hidden">
                                <div class="absolute -top-4 -right-4 text-6xl opacity-10">🤖</div>
                                <h4 class="font-black text-indigo-900 mb-2">Bilan Pédagogique Intelligent</h4>
                                <p class="text-xs text-indigo-700 mb-4">LLaMA 3.3 analysera les notes et les absences pour rédiger un rapport professionnel.</p>
                                
                                <button onclick="generateAIReport({{ $student->student->id }})" id="btn-ai-report" class="bg-indigo-600 text-white px-4 py-2 rounded-xl text-xs font-black shadow-md hover:bg-indigo-700 transition-colors flex items-center gap-2">
                                    ✨ Générer le bilan IA
                                </button>
                                
                                <div id="ai-report-container" class="mt-4 hidden">
                                    <div class="p-4 bg-white rounded-xl text-sm text-gray-700 border border-indigo-100 shadow-sm prose prose-sm max-w-none prose-indigo" id="ai-report-content"></div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-10 pt-8 border-t border-gray-100 flex gap-4">
                        <a href="{{ route('admin.students.index') }}" class="px-6 py-3 border border-gray-300 text-gray-700 bg-white rounded-2xl hover:bg-gray-50 font-black text-xs uppercase tracking-wider transition-all duration-200">
                            Retour à la liste
                        </a>
                        <a href="{{ route('admin.students.edit', $student->id) }}" class="px-6 py-3 bg-amber-500 text-white rounded-2xl hover:bg-amber-600 font-black text-xs uppercase tracking-wider shadow-md hover:scale-[1.02] transform transition-all duration-200">
                            Modifier le profil
                        </a>
                    </div>
                </div>
            </div>
            
        </div>
    </div>

    <script>
        async function generateAIReport(studentId) {
            const btn = document.getElementById('btn-ai-report');
            const container = document.getElementById('ai-report-container');
            const content = document.getElementById('ai-report-content');
            
            const originalText = btn.innerHTML;
            btn.innerHTML = '⏳ Analyse du dossier en cours...';
            btn.disabled = true;
            container.classList.add('hidden');

            try {
                const response = await fetch(`/admin/students/${studentId}/ai-report`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (data.report) {
                    content.innerHTML = data.report;
                    container.classList.remove('hidden');
                }
            } catch (error) {
                alert("Erreur lors de la génération du bilan IA.");
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }
    </script>
</x-app-layout>
