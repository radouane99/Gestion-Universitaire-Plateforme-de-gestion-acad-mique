<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>UPF — Vérification de Document Officiel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Outfit', sans-serif; }
        .bg-upf-blue { background-color: #003893; }
        .text-upf-blue { color: #003893; }
        .bg-upf-pink { background-color: #b50060; }
        .text-upf-pink { color: #b50060; }
        .gradient-upf-dark { background: linear-gradient(135deg, #020617 0%, #0f172a 100%); }
    </style>
</head>
<body class="font-sans antialiased gradient-upf-dark text-slate-100 min-h-screen flex flex-col justify-between selection:bg-upf-pink selection:text-white">

    <!-- Header / Navbar Mock -->
    <header class="py-6 border-b border-slate-800 backdrop-blur-md sticky top-0 z-50 bg-slate-950/60">
        <div class="max-w-4xl mx-auto px-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="h-12 w-auto flex items-center justify-center bg-white rounded-xl p-2 shadow-md">
                    <img src="https://www.upf.ac.ma/images/logo_upf.png" alt="UPF Logo" class="h-8 object-contain" onerror="this.outerHTML='<div class=\'font-black text-xl text-[#003893]\'>UPF</div>'">
                </div>
                <div>
                    <h1 class="text-xs font-black uppercase tracking-widest text-slate-400">Université Privée de Fès</h1>
                    <p class="text-[10px] text-emerald-400 font-bold tracking-wider">🔒 Service Central de Vérification</p>
                </div>
            </div>
            <a href="/" class="text-xs font-black uppercase tracking-wider text-slate-400 hover:text-white transition-colors bg-slate-900 border border-slate-800 px-4 py-2 rounded-xl">
                Portail UPF
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="py-12 flex-grow flex items-center justify-center px-4">
        <div class="max-w-md w-full bg-slate-900/60 backdrop-blur-xl rounded-[2.5rem] p-8 sm:p-10 border border-slate-800/80 shadow-2xl relative overflow-hidden group">
            
            <!-- Glow decorative blobs -->
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-emerald-500 rounded-full opacity-10 blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-upf-blue rounded-full opacity-10 blur-3xl pointer-events-none"></div>

            <!-- Verification Success Header -->
            <div class="text-center mb-8 relative z-10">
                <div class="w-20 h-20 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 rounded-full flex items-center justify-center text-4xl mx-auto mb-5 shadow-inner animate-pulse">
                    ✅
                </div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-black text-[10px] uppercase tracking-widest mb-3">
                    Document Authentique & Certifié
                </div>
                <h2 class="text-2xl font-black tracking-tight text-white">Vérification Réussie</h2>
                <p class="text-slate-400 text-xs mt-1">Le document présenté est certifié conforme par le secrétariat académique de l'UPF.</p>
            </div>

            <!-- Document Details List -->
            <div class="space-y-5 relative z-10 bg-slate-950/40 p-6 rounded-3xl border border-slate-800/50">
                <div>
                    <span class="text-[9px] uppercase font-black tracking-widest text-slate-500 block">Étudiant(e)</span>
                    <span class="text-base font-black text-white block mt-0.5">{{ $student->user->name }}</span>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-[9px] uppercase font-black tracking-widest text-slate-500 block">Matricule</span>
                        <span class="text-xs font-black text-slate-200 block mt-0.5">{{ $student->student_number }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] uppercase font-black tracking-widest text-slate-500 block">CIN</span>
                        <span class="text-xs font-black text-slate-200 block mt-0.5">{{ $student->cin }}</span>
                    </div>
                </div>

                <div>
                    <span class="text-[9px] uppercase font-black tracking-widest text-slate-500 block">Filière d'études</span>
                    <span class="text-xs font-black text-upf-pink block mt-0.5">{{ $student->filiere->name }}</span>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-[9px] uppercase font-black tracking-widest text-slate-500 block">Niveau Validé</span>
                        <span class="text-xs font-bold text-slate-300 block mt-0.5">{{ $student->group?->level ?? 'Licence 1' }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] uppercase font-black tracking-widest text-slate-500 block">Moyenne Générale</span>
                        <span class="text-xs font-black text-emerald-400 block mt-0.5">{{ number_format($gpa, 2) }} / 20</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-[9px] uppercase font-black tracking-widest text-slate-500 block">Mention</span>
                        <span class="text-xs font-black text-upf-pink block mt-0.5">{{ $mention }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] uppercase font-black tracking-widest text-slate-500 block">Année Académique</span>
                        <span class="text-xs font-bold text-slate-300 block mt-0.5">{{ $student->academicYear?->name ?? '2025/2026' }}</span>
                    </div>
                </div>
            </div>

            <!-- Additional Certification Notice -->
            <div class="mt-6 text-center text-[10px] text-slate-500 font-bold relative z-10">
                🔒 Certificat numérique crypté. Université reconnue par l'État.<br>
                Réf : {{ strtoupper(sha1($student->id . '_' . $student->student_number)) }}
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="py-6 border-t border-slate-900 bg-slate-950/40 text-center text-xs text-slate-500">
        <div class="max-w-4xl mx-auto px-4">
            <p>&copy; 2026 Université Privée de Fès. Service d'Authentification Sécurisé des Diplômes.</p>
        </div>
    </footer>

</body>
</html>
