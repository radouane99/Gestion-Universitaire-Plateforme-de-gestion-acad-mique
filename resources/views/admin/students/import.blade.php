<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tighter italic">📥 Importation massive des Étudiants</h2>
    </x-slot>

    <div class="py-10 bg-[#F8FAFC]">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-gradient-to-r from-upf-blue to-indigo-700 rounded-3xl p-8 text-white shadow-xl">
                <h1 class="text-3xl font-black italic mb-2">Import CSV avec validation en temps réel</h1>
                <p class="text-indigo-200">Importez des étudiants par groupe. Le fichier sera analysé côté client pour surélever les erreurs avant tout traitement.</p>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Groupe de destination *</label>
                        <select id="group_id" required class="w-full border-gray-200 rounded-xl p-3 text-sm font-bold bg-gray-50 focus:ring-upf-blue">
                            <option value="">Choisir un groupe...</option>
                            @foreach($groups as $g)
                            <option value="{{ $g->id }}">{{ $g->name }} ({{ $g->filiere?->name ?? 'N/A' }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="space-y-1 flex flex-col justify-end">
                        <a href="#" onclick="downloadTemplate()" class="text-xs font-black text-upf-blue hover:underline mb-2">
                            📥 Télécharger le modèle de fichier d'importation CSV
                        </a>
                    </div>
                </div>

                {{-- Drag and drop zone --}}
                <div class="border-2 border-dashed border-gray-200 rounded-2xl p-10 text-center hover:border-upf-blue transition-colors cursor-pointer" id="drop-zone">
                    <input type="file" id="csv-file" accept=".csv" class="hidden">
                    <label for="csv-file" class="cursor-pointer block">
                        <div class="text-5xl mb-3">📄</div>
                        <p class="font-black text-gray-700 text-sm">Glissez-déposez ou cliquez pour charger votre fichier CSV</p>
                        <p class="text-gray-400 text-xs mt-1">Format attendu : student_number, name, email</p>
                    </label>
                </div>

                {{-- Error log --}}
                <div id="general-error" class="p-4 bg-red-50 border border-red-100 text-red-800 rounded-2xl font-bold text-sm hidden"></div>

                {{-- Preview section --}}
                <div id="preview-section" class="space-y-4 hidden">
                    <h3 class="font-black text-gray-900 text-lg italic">👀 Aperçu & Validation des Données</h3>
                    
                    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                        <div class="overflow-x-auto max-h-[400px]">
                            <table class="w-full text-left">
                                <thead class="bg-gray-50 border-b border-gray-100 sticky top-0">
                                    <tr>
                                        <th class="p-4 text-[10px] font-black uppercase tracking-widest text-gray-400">#</th>
                                        <th class="p-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Matricule</th>
                                        <th class="p-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Nom Complet</th>
                                        <th class="p-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Email</th>
                                        <th class="p-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Statut / Erreurs</th>
                                    </tr>
                                </thead>
                                <tbody id="preview-table-body" class="divide-y divide-gray-50">
                                    {{-- Filled dynamically in JS --}}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="flex justify-between items-center bg-gray-50 p-4 rounded-2xl border border-gray-100">
                        <span class="text-xs text-gray-500 font-bold" id="import-stats"></span>
                        <button onclick="submitImport()" id="btn-confirm" disabled
                            class="bg-emerald-600 disabled:bg-gray-200 disabled:cursor-not-allowed text-white px-8 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-emerald-700 transition-all shadow-md">
                            Confirmer l'importation 🚀
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        const fileInput = document.getElementById('csv-file');
        const dropZone = document.getElementById('drop-zone');
        const previewSection = document.getElementById('preview-section');
        const previewTableBody = document.getElementById('preview-table-body');
        const btnConfirm = document.getElementById('btn-confirm');
        const importStatsDisplay = document.getElementById('import-stats');
        const generalError = document.getElementById('general-error');
        const groupIdSelect = document.getElementById('group_id');

        let parsedStudents = [];

        // Drag & drop styles
        dropZone.addEventListener('dragover', (e) => { e.preventDefault(); dropZone.classList.add('border-upf-blue'); });
        dropZone.addEventListener('dragleave', () => dropZone.classList.remove('border-upf-blue'));
        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-upf-blue');
            if (e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                handleFileSelect();
            }
        });

        fileInput.addEventListener('change', handleFileSelect);

        function handleFileSelect() {
            generalError.classList.add('hidden');
            previewSection.classList.add('hidden');
            btnConfirm.disabled = true;

            if (fileInput.files.length === 0) return;

            const file = fileInput.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                const text = e.target.result;
                parseCSV(text);
            };

            reader.readAsText(file);
        }

        function parseCSV(text) {
            const lines = text.split(/\r?\n/);
            parsedStudents = [];
            
            if (lines.length <= 1) {
                showGeneralError("Le fichier CSV semble vide ou invalide.");
                return;
            }

            // Headers are student_number, name, email
            const header = lines[0].split(/[;,]/).map(h => h.trim().toLowerCase());
            
            const numIdx = header.indexOf('student_number');
            const nameIdx = header.indexOf('name');
            const emailIdx = header.indexOf('email');

            if (numIdx === -1 || nameIdx === -1 || emailIdx === -1) {
                showGeneralError("L'en-tête du fichier est invalide. En-têtes attendus : student_number, name, email");
                return;
            }

            for (let i = 1; i < lines.length; i++) {
                const line = lines[i].trim();
                if (!line) continue;

                const cells = line.split(/[;,]/).map(c => c.trim().replace(/^["']|["']$/g, ''));
                if (cells.length < 3) continue;

                parsedStudents.push({
                    rowNum: i + 1,
                    student_number: cells[numIdx] || '',
                    name: cells[nameIdx] || '',
                    email: cells[emailIdx] || '',
                    errors: []
                });
            }

            validateAndPreview();
        }

        function validateAndPreview() {
            previewTableBody.innerHTML = '';
            let validCount = 0;
            let invalidCount = 0;

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            parsedStudents.forEach((student, index) => {
                student.errors = [];

                // Validations
                if (!student.student_number) student.errors.push("Matricule manquant");
                if (!student.name) student.errors.push("Nom manquant");
                if (!student.email) {
                    student.errors.push("Email manquant");
                } else if (!emailRegex.test(student.email)) {
                    student.errors.push("Email mal formé");
                }

                // Duplicate checking
                const isDupNum = parsedStudents.some((s, idx) => s.student_number === student.student_number && idx !== index);
                const isDupEmail = parsedStudents.some((s, idx) => s.email === student.email && idx !== index);

                if (isDupNum) student.errors.push("Matricule doublon dans le fichier");
                if (isDupEmail) student.errors.push("Email doublon dans le fichier");

                const hasErrors = student.errors.length > 0;
                if (hasErrors) {
                    invalidCount++;
                } else {
                    validCount++;
                }

                const tr = document.createElement('tr');
                tr.className = hasErrors ? 'bg-red-50/30' : 'hover:bg-gray-50/50';

                tr.innerHTML = `
                    <td class="p-4 text-xs font-bold text-gray-500">${student.rowNum}</td>
                    <td class="p-4 text-sm font-black ${student.errors.includes('Matricule manquant') ? 'text-red-600 font-extrabold underline' : 'text-gray-900'}">${student.student_number}</td>
                    <td class="p-4 text-sm font-bold ${student.errors.includes('Nom manquant') ? 'text-red-600 font-extrabold underline' : 'text-gray-700'}">${student.name}</td>
                    <td class="p-4 text-sm font-bold ${student.errors.includes('Email manquant') || student.errors.includes('Email mal formé') ? 'text-red-600 font-extrabold underline' : 'text-gray-700'}">${student.email}</td>
                    <td class="p-4 text-xs font-bold">
                        ${hasErrors 
                            ? `<span class="bg-red-100 text-red-700 border border-red-200 px-2 py-1 rounded">${student.errors.join(' | ')}</span>`
                            : `<span class="bg-emerald-100 text-emerald-700 border border-emerald-200 px-2 py-1 rounded">✓ Prêt</span>`
                        }
                    </td>
                `;

                previewTableBody.appendChild(tr);
            });

            importStatsDisplay.textContent = `Analyse : ${validCount} étudiants valides, ${invalidCount} en erreur.`;
            previewSection.classList.remove('hidden');

            if (validCount > 0 && invalidCount === 0) {
                btnConfirm.disabled = false;
            } else {
                btnConfirm.disabled = true;
                if (invalidCount > 0) {
                    showGeneralError("Veuillez corriger les lignes en rouge dans votre fichier avant de pouvoir valider l'importation.");
                }
            }
        }

        function submitImport() {
            const groupId = groupIdSelect.value;
            if (!groupId) {
                alert("Veuillez sélectionner un groupe de destination.");
                return;
            }

            btnConfirm.disabled = true;
            btnConfirm.textContent = 'Importation en cours...';

            fetch("{{ route('admin.students.import.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    group_id: groupId,
                    students: parsedStudents
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert("Importation en masse réussie !");
                    window.location.href = "{{ route('admin.discipline.index') }}";
                } else {
                    showGeneralError(data.message || "Erreur lors de l'importation.");
                    if (data.errors) {
                        showGeneralError(data.errors.join('<br>'));
                    }
                    btnConfirm.disabled = false;
                    btnConfirm.textContent = "Confirmer l'importation 🚀";
                }
            })
            .catch(err => {
                console.error(err);
                showGeneralError("Une erreur inattendue est survenue.");
                btnConfirm.disabled = false;
                btnConfirm.textContent = "Confirmer l'importation 🚀";
            });
        }

        function showGeneralError(msg) {
            generalError.innerHTML = msg;
            generalError.classList.remove('hidden');
        }

        function downloadTemplate() {
            const headers = "student_number,name,email\n";
            const sample = "20260001,Radouane El,radouane@example.com\n20260002,Amine Ben,amine@example.com\n";
            const blob = new Blob([headers + sample], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.setAttribute('href', url);
            a.setAttribute('download', 'modele_importation_etudiants.csv');
            a.click();
        }
    </script>
</x-app-layout>
