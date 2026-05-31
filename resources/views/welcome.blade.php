<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'UPF Portal') }} - Université Privée de Fès</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- PWA -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#003893">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js" defer></script>

    <style>
        body { font-family: 'Outfit', sans-serif; }
        .bg-upf-blue { background-color: #003893; }
        .text-upf-blue { color: #003893; }
        .bg-upf-pink { background-color: #b50060; }
        .text-upf-pink { color: #b50060; }
        .gradient-upf { background: linear-gradient(135deg, #003893 0%, #001f54 100%); }
        
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .animated-announcement {
            background: linear-gradient(-45deg, #b50060, #003893, #b50060, #001f54);
            background-size: 400% 400%;
            animation: gradientShift 10s ease infinite;
        }
    </style>
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-900 selection:bg-upf-pink selection:text-white" x-data="landingPage()">

    <!-- Admissions Top Announcement Bar -->
    @if(\App\Models\Setting::isInscriptionOpen() || \App\Models\Setting::isReinscriptionOpen())
        <div class="animated-announcement text-white py-2.5 px-4 text-center relative z-[60] shadow-md flex flex-col md:flex-row items-center justify-center gap-2 md:gap-6 transition-all border-b border-white/10">
            <span class="flex items-center flex-wrap justify-center gap-2 text-white">
                <span class="inline-flex items-center gap-1.5 bg-yellow-400 text-slate-900 border border-yellow-300/40 px-2 py-0.5 rounded-full text-[9px] uppercase font-black tracking-widest shadow-sm animate-pulse">
                    🔥 {{ __('Ouvert') }}
                </span>
                <span class="text-white font-extrabold text-xs sm:text-sm tracking-wide drop-shadow-sm flex items-center gap-1">
                    📢 <strong>{{ __('Admissions & Campagnes Académiques :') }}</strong> {{ __('Les inscriptions et réinscriptions sont ouvertes !') }}
                </span>
            </span>
            <div class="flex gap-2">
                @if(\App\Models\Setting::isInscriptionOpen())
                    <a href="{{ route('inscription') }}" class="bg-white text-[#003893] hover:text-[#b50060] hover:bg-slate-100 px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wider transition-all shadow-md transform hover:scale-105">
                        📝 {{ __('Inscription') }}
                    </a>
                @endif
                @if(\App\Models\Setting::isReinscriptionOpen())
                    <a href="{{ route('student.reinscription.form') }}" class="bg-white text-[#b50060] hover:text-[#003893] hover:bg-slate-100 px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wider transition-all shadow-md transform hover:scale-105">
                        🎓 {{ __('Réinscription') }}
                    </a>
                @endif
            </div>
        </div>
    @endif

    <!-- Navbar -->
    <nav class="fixed w-full z-50 transition-all duration-300" :class="scrolled ? 'bg-white/90 backdrop-blur-md shadow-sm py-3' : 'bg-transparent py-5'">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center gap-3">
                    <div class="h-14 w-auto flex items-center justify-center bg-white rounded-xl p-2 shadow-sm transition transform hover:scale-105">
                        <img src="https://www.upf.ac.ma/images/logo_upf.png" alt="UPF Logo" class="h-10 object-contain" onerror="this.outerHTML='<div class=\'font-black text-2xl text-[#003893]\'>UPF<span class=\'text-[#b50060]\'>.</span></div>'">
                    </div>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8 bg-white/50 backdrop-blur-sm px-6 py-2.5 rounded-full border border-white/20 shadow-sm">
                    <a href="#features" class="text-sm font-semibold text-slate-800 hover:text-upf-pink transition-colors">{{ __('Notre Plateforme') }}</a>
                    <a href="#ai" class="text-sm font-semibold text-slate-800 hover:text-upf-pink transition-colors">{{ __('Intelligence Artificielle') }}</a>
                </div>

                <!-- Auth & Language Buttons -->
                <div class="hidden md:flex items-center space-x-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-sm font-bold text-white bg-upf-blue hover:bg-blue-900 px-6 py-3 rounded-full transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 border border-blue-800">{{ __('Mon Espace Académique') }}</a>
                        @else
                            @if(\App\Models\Setting::isInscriptionOpen())
                                <a href="{{ route('inscription') }}" class="text-sm font-bold text-slate-800 bg-white hover:bg-slate-100 hover:text-upf-pink px-6 py-3 rounded-full transition shadow-md hover:shadow-lg transform hover:-translate-y-0.5 border border-slate-200">{{ __('S\'inscrire en ligne') }}</a>
                            @endif
                            @if(\App\Models\Setting::isReinscriptionOpen())
                                <a href="{{ route('student.reinscription.form') }}" class="text-sm font-bold text-slate-800 bg-white hover:bg-slate-100 hover:text-upf-pink px-6 py-3 rounded-full transition shadow-md hover:shadow-lg transform hover:-translate-y-0.5 border border-slate-200">{{ __('Se Réinscrire') }}</a>
                            @endif
                            <a href="{{ route('login') }}" class="text-sm font-bold text-white bg-upf-pink hover:bg-pink-700 px-6 py-3 rounded-full transition shadow-lg shadow-pink-500/30 hover:shadow-xl transform hover:-translate-y-0.5 border border-pink-600">{{ __('Connexion au Portail') }}</a>
                        @endauth
                    @endif

                    <!-- Guest Language Switcher -->
                    <div x-data="{ open: false }" class="relative z-[60]">
                        <button @click="open = !open" class="inline-flex items-center gap-1.5 px-3 py-2.5 text-xs font-bold rounded-full text-slate-800 bg-white/60 hover:bg-white border border-slate-200/60 shadow-sm transition-all uppercase">
                            🌐 {{ strtoupper(App::getLocale()) }}
                            <svg class="fill-current h-3 w-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-transition class="absolute right-0 mt-2 w-40 bg-white rounded-2xl shadow-xl border border-slate-100 p-1 z-50">
                            <a href="{{ route('lang.switch', 'en') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 hover:text-upf-pink hover:bg-slate-50 transition-colors">
                                <span>🇺🇸 English</span><span class="text-[9px] font-black text-slate-350">EN</span>
                            </a>
                            <a href="{{ route('lang.switch', 'fr') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 hover:text-upf-pink hover:bg-slate-50 transition-colors">
                                <span>🇫🇷 Français</span><span class="text-[9px] font-black text-slate-350">FR</span>
                            </a>
                            <a href="{{ route('lang.switch', 'ar') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 hover:text-upf-pink hover:bg-slate-50 transition-colors">
                                <span dir="rtl">🇲🇦 العربية</span><span class="text-[9px] font-black text-slate-350">AR</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative pt-32 pb-20 sm:pt-40 sm:pb-32 overflow-hidden gradient-upf">
        <!-- Background Image overlay for UPF campus feeling -->
        <div class="absolute inset-0 bg-[url('https://www.upf.ac.ma/wp-content/uploads/2020/07/upf-campus.jpg')] bg-cover bg-center opacity-10 mix-blend-overlay"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-transparent to-[#001f54]/90"></div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 backdrop-blur-md text-white font-bold text-xs uppercase tracking-widest mb-8 border border-white/20 shadow-xl">
                <span class="relative flex h-2 w-2">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                </span>
                {{ __('Université Reconnue par l\'État') }}
            </div>
            
            <h1 class="text-5xl sm:text-7xl font-black tracking-tight mb-6 leading-tight drop-shadow-2xl">
                {{ __('Université Privée de Fès') }} <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-pink-400 to-rose-200">{{ __('L\'Excellence au Quotidien.') }}</span>
            </h1>
            
            <p class="mt-6 text-lg sm:text-xl text-blue-100 max-w-3xl mx-auto font-light mb-12 leading-relaxed drop-shadow-md">
                {{ __('Découvrez la nouvelle plateforme académique intelligente de l\'UPF. Gérée par l\'Intelligence Artificielle pour vous offrir une expérience universitaire moderne, fluide et connectée.') }}
            </p>
            
            <div class="flex flex-col sm:flex-row justify-center items-center gap-6">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="w-full sm:w-auto px-8 py-4 bg-white text-upf-blue hover:bg-blue-50 font-black rounded-full shadow-2xl transition transform hover:-translate-y-1 text-lg flex justify-center items-center gap-2">
                            {{ __('Accéder au Tableau de Bord') }} <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    @else
                        @if(\App\Models\Setting::isInscriptionOpen())
                            <a href="{{ route('inscription') }}" class="w-full sm:w-auto px-8 py-4 bg-white text-upf-blue hover:bg-blue-50 font-black rounded-full shadow-2xl transition transform hover:-translate-y-1 text-lg flex justify-center items-center gap-2">
                                📝 {{ __('S\'inscrire en Ligne') }}
                            </a>
                        @endif
                        @if(\App\Models\Setting::isReinscriptionOpen())
                            <a href="{{ route('student.reinscription.form') }}" class="w-full sm:w-auto px-8 py-4 bg-white text-upf-blue hover:bg-blue-50 font-black rounded-full shadow-2xl transition transform hover:-translate-y-1 text-lg flex justify-center items-center gap-2">
                                🎓 {{ __('Se Réinscrire en Ligne') }}
                            </a>
                        @endif
                        <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-4 bg-upf-pink hover:bg-pink-600 text-white font-black rounded-full shadow-2xl shadow-pink-500/40 transition transform hover:-translate-y-1 text-lg flex justify-center items-center gap-2 border border-pink-500">
                            {{ __('Accéder au Portail Étudiant / Professeur') }} <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    @endauth
                @endif
            </div>
        </div>
        
        <!-- Elegant Wave Separator -->
        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto text-slate-50 drop-shadow-sm">
                <path d="M0 120L60 105C120 90 240 60 360 45C480 30 600 30 720 37.5C840 45 960 60 1080 60C1200 60 1320 45 1380 37.5L1440 30V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="currentColor"/>
            </svg>
        </div>
    </div>

    <!-- UPF Statistics Section -->
    <div class="py-12 bg-slate-50 relative z-20 -mt-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 p-8 sm:p-12 border border-slate-100 flex flex-col sm:flex-row justify-around items-center gap-8">
                <div class="text-center group">
                    <div class="text-5xl font-black text-upf-blue mb-2 group-hover:scale-110 transition-transform">Reconnue</div>
                    <div class="text-sm font-bold text-slate-500 uppercase tracking-widest">Par l'État Marocain</div>
                </div>
                <div class="w-px h-20 bg-slate-200 hidden sm:block"></div>
                <div class="text-center group">
                    <div class="text-5xl font-black text-upf-blue mb-2 group-hover:scale-110 transition-transform">100%</div>
                    <div class="text-sm font-bold text-slate-500 uppercase tracking-widest">Plateforme Numérisée</div>
                </div>
                <div class="w-px h-20 bg-slate-200 hidden sm:block"></div>
                <div class="text-center group">
                    <div class="text-5xl font-black text-upf-pink mb-2 group-hover:scale-110 transition-transform">IA</div>
                    <div class="text-sm font-bold text-slate-500 uppercase tracking-widest">Intégration LLaMA 3</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Campaigns Banner Section -->
    @if(\App\Models\Setting::isInscriptionOpen() || \App\Models\Setting::isReinscriptionOpen())
        <div class="py-10 bg-slate-50 relative z-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-gradient-to-r from-upf-blue to-upf-pink rounded-[2.5rem] shadow-2xl p-8 sm:p-12 text-white relative overflow-hidden transition-all duration-500 hover:shadow-pink-500/10">
                    <!-- Background Glow Elements -->
                    <div class="absolute top-0 right-0 w-80 h-80 bg-white/10 rounded-full blur-3xl -mr-20 -mt-20"></div>
                    <div class="absolute bottom-0 left-0 w-80 h-80 bg-pink-500/20 rounded-full blur-3xl -ml-20 -mb-20"></div>

                    <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                        <div class="lg:col-span-8 space-y-4 text-left">
                            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white/20 backdrop-blur-md text-[10px] font-black uppercase tracking-wider text-pink-200 border border-white/10 animate-pulse">
                                📢 {{ __('Campagne Académique Ouverte') }}
                            </span>
                            <h2 class="text-3xl sm:text-4xl font-black tracking-tight leading-tight">
                                {{ __('Rejoignez l\'Université Privée de Fès') }}
                            </h2>
                            <p class="text-blue-100 text-sm sm:text-base max-w-2xl font-light">
                                {{ __('Les inscriptions pour les nouveaux bacheliers et les réinscriptions pour nos étudiants actuels sont officiellement ouvertes pour l\'année universitaire') }} <strong class="font-bold text-white">{{ \App\Models\Setting::first()->academic_year ?? '2025-2026' }}</strong>. {{ __('Profitez de notre accompagnement personnalisé pour bâtir votre avenir d\'excellence.') }}
                            </p>
                        </div>
                        <div class="lg:col-span-4 flex flex-col sm:flex-row lg:flex-col gap-4 justify-end">
                            @if(\App\Models\Setting::isInscriptionOpen())
                                <a href="{{ route('inscription') }}" class="px-6 py-4 bg-white text-upf-blue hover:bg-slate-100 hover:text-upf-pink font-black rounded-2xl shadow-xl transition transform hover:-translate-y-0.5 text-center text-sm flex items-center justify-center gap-2">
                                    📝 {{ __('Inscrire Nouveau Candidat') }}
                                </a>
                            @endif
                            @if(\App\Models\Setting::isReinscriptionOpen())
                                <a href="{{ route('student.reinscription.form') }}" class="px-6 py-4 bg-upf-pink text-white hover:bg-pink-700 font-black rounded-2xl shadow-xl transition transform hover:-translate-y-0.5 text-center text-sm flex items-center justify-center gap-2 border border-pink-500">
                                    🎓 {{ __('Faire ma Réinscription') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Features Section - 3 Espaces -->
    <div id="features" class="py-32 bg-white relative">
        <!-- Decorative background elements -->
        <div class="absolute inset-0 bg-[radial-gradient(#e5e7eb_1px,transparent_1px)] [background-size:16px_16px] opacity-30"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-24">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-blue-50 text-upf-blue font-bold text-xs uppercase tracking-widest mb-4 border border-blue-100">
                    Espaces Personnalisés
                </div>
                <h2 class="text-4xl font-black tracking-tight text-slate-900 sm:text-6xl mb-6">Une plateforme,<br><span class="text-transparent bg-clip-text bg-gradient-to-r from-upf-blue to-upf-pink">Trois Univers distincts.</span></h2>
                <p class="text-xl text-slate-500 font-light leading-relaxed">Une architecture centralisée offrant des interfaces sur-mesure et haut de gamme pour l'administration, le corps professoral et les étudiants.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Espace Administration -->
                <div class="group relative bg-white rounded-[2.5rem] p-10 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_20px_40px_rgb(0,56,147,0.1)] transition-all duration-500 hover:-translate-y-2 overflow-hidden flex flex-col h-full">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-slate-50 rounded-bl-full -z-10 transition-transform duration-700 group-hover:scale-150 group-hover:bg-blue-50/50"></div>
                    <div class="w-20 h-20 bg-gradient-to-br from-slate-800 to-slate-900 text-white rounded-3xl flex items-center justify-center text-4xl mb-8 shadow-xl shadow-slate-200 group-hover:rotate-6 transition-transform duration-500">
                        🛡️
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 mb-4 tracking-tight">Espace Administration</h3>
                    <p class="text-slate-500 text-base leading-relaxed mb-8 flex-grow">Le centre de contrôle absolu. Pilotage des filières, gestion des inscriptions massives, délibérations automatisées et surveillance globale via un tableau de bord analytique puissant.</p>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center gap-3 text-sm font-semibold text-slate-700"><span class="w-1.5 h-1.5 rounded-full bg-upf-blue"></span> PV & Délibérations</li>
                        <li class="flex items-center gap-3 text-sm font-semibold text-slate-700"><span class="w-1.5 h-1.5 rounded-full bg-upf-blue"></span> Emplois du temps</li>
                        <li class="flex items-center gap-3 text-sm font-semibold text-slate-700"><span class="w-1.5 h-1.5 rounded-full bg-upf-blue"></span> Statistiques IA</li>
                    </ul>
                </div>

                <!-- Espace Professeur -->
                <div class="group relative bg-white rounded-[2.5rem] p-10 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_20px_40px_rgb(181,0,96,0.15)] transition-all duration-500 hover:-translate-y-2 overflow-hidden flex flex-col h-full transform lg:-translate-y-8">
                    <!-- Glow effect unique to the middle card -->
                    <div class="absolute inset-0 bg-gradient-to-b from-upf-pink/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    
                    <div class="absolute top-0 right-0 w-64 h-64 bg-pink-50/50 rounded-bl-full -z-10 transition-transform duration-700 group-hover:scale-150"></div>
                    <div class="w-20 h-20 bg-gradient-to-br from-upf-pink to-rose-400 text-white rounded-3xl flex items-center justify-center text-4xl mb-8 shadow-xl shadow-pink-200 group-hover:-rotate-6 transition-transform duration-500">
                        👨‍🏫
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 mb-4 tracking-tight">Espace Professeur</h3>
                    <p class="text-slate-500 text-base leading-relaxed mb-8 flex-grow">Un outil pédagogique nouvelle génération. Saisie de notes fluide, gestion interactive de la classe, cahier de textes numérique et suivi individuel des étudiants facilité.</p>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center gap-3 text-sm font-semibold text-slate-700"><span class="w-1.5 h-1.5 rounded-full bg-upf-pink"></span> Saisie des Notes</li>
                        <li class="flex items-center gap-3 text-sm font-semibold text-slate-700"><span class="w-1.5 h-1.5 rounded-full bg-upf-pink"></span> Appels & Absences</li>
                        <li class="flex items-center gap-3 text-sm font-semibold text-slate-700"><span class="w-1.5 h-1.5 rounded-full bg-upf-pink"></span> Réclamations directes</li>
                    </ul>
                </div>

                <!-- Espace Étudiant -->
                <div class="group relative bg-white rounded-[2.5rem] p-10 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_20px_40px_rgb(0,191,165,0.1)] transition-all duration-500 hover:-translate-y-2 overflow-hidden flex flex-col h-full">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-50 rounded-bl-full -z-10 transition-transform duration-700 group-hover:scale-150"></div>
                    <div class="w-20 h-20 bg-gradient-to-br from-emerald-400 to-teal-500 text-white rounded-3xl flex items-center justify-center text-4xl mb-8 shadow-xl shadow-emerald-100 group-hover:scale-110 transition-transform duration-500">
                        🎓
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 mb-4 tracking-tight">Espace Étudiant</h3>
                    <p class="text-slate-500 text-base leading-relaxed mb-8 flex-grow">Une interface fluide et intuitive. L'étudiant au cœur du système avec un accès instantané à ses résultats, ses documents officiels et un assistant IA personnel.</p>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center gap-3 text-sm font-semibold text-slate-700"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Relevés de notes QR</li>
                        <li class="flex items-center gap-3 text-sm font-semibold text-slate-700"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Assistant Pédagogique IA</li>
                        <li class="flex items-center gap-3 text-sm font-semibold text-slate-700"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Suivi des absences</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Section -->
    <div id="ai" class="py-24 bg-white relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="bg-slate-900 rounded-[3rem] p-8 sm:p-16 overflow-hidden relative shadow-2xl border border-slate-800">
                <!-- Decorative blurred shapes -->
                <div class="absolute top-0 right-0 -mt-20 -mr-20 w-96 h-96 bg-upf-pink rounded-full blur-[120px] opacity-40"></div>
                <div class="absolute bottom-0 left-0 -mb-20 -ml-20 w-96 h-96 bg-upf-blue rounded-full blur-[120px] opacity-40"></div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center relative z-10">
                    <div>
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-pink-500/20 text-pink-300 font-bold text-xs uppercase tracking-widest mb-6 border border-pink-500/30">
                            ✨ UPF Smart AI
                        </div>
                        <h2 class="text-4xl font-black tracking-tight sm:text-5xl mb-6 text-white">L'Université du Futur, pilotée par <span class="text-transparent bg-clip-text bg-gradient-to-r from-pink-400 to-rose-300">LLaMA 3</span></h2>
                        <p class="text-lg text-slate-300 mb-10 leading-relaxed font-light">
                            Nous sommes fiers d'être la première université marocaine à intégrer un assistant d'Intelligence Artificielle au cœur de sa pédagogie et de son administration.
                        </p>
                        
                        <ul class="space-y-8">
                            <li class="flex gap-5">
                                <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center shrink-0 border border-white/10 text-2xl shadow-inner">💬</div>
                                <div>
                                    <h4 class="font-bold text-white text-xl mb-1">Assistant Étudiant 24/7</h4>
                                    <p class="text-base text-slate-400">Posez des questions sur vos notes, vos absences ou le règlement. L'IA analyse votre dossier et vous répond instantanément et précisément.</p>
                                </div>
                            </li>
                            <li class="flex gap-5">
                                <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center shrink-0 border border-white/10 text-2xl shadow-inner">📝</div>
                                <div>
                                    <h4 class="font-bold text-white text-xl mb-1">Aide Pédagogique aux Professeurs</h4>
                                    <p class="text-base text-slate-400">L'IA génère des brouillons de réponses aux réclamations étudiantes, faisant gagner un temps précieux au corps académique de l'UPF.</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                    
                    <!-- Mockup Chat -->
                    <div class="bg-slate-800/80 backdrop-blur-md rounded-3xl p-6 border border-slate-700 shadow-2xl transform hover:-translate-y-2 transition duration-500">
                        <div class="flex items-center gap-4 border-b border-slate-700 pb-5 mb-5">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-upf-blue to-upf-pink text-white text-xl flex items-center justify-center font-bold shadow-lg ring-2 ring-white/20">IA</div>
                            <div>
                                <h4 class="font-bold text-white text-lg">UPF Assistant Pédagogique</h4>
                                <p class="text-sm text-emerald-400 font-medium flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span> Connecté au système</p>
                            </div>
                        </div>
                        <div class="space-y-5">
                            <div class="flex justify-end">
                                <div class="bg-upf-blue text-white p-4 rounded-2xl rounded-tr-sm text-[15px] max-w-[85%] shadow-md leading-relaxed">
                                    Bonjour, ai-je validé mon semestre S1 en Génie Civil ? J'ai eu 9.5 en Matériaux de Construction.
                                </div>
                            </div>
                            <div class="flex justify-start">
                                <div class="bg-slate-700 text-slate-100 p-4 rounded-2xl rounded-tl-sm text-[15px] max-w-[90%] shadow-md border border-slate-600 leading-relaxed">
                                    Bonjour ! J'ai consulté votre dossier. Avec 9.5 en Matériaux et 14 en Résistance des Matériaux, votre moyenne du module est de 11.75/20. Vous validez le module par compensation selon l'article 12 du règlement de l'UPF. Félicitations pour votre S1 ! 🎉
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 py-16 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="h-14 w-auto bg-white rounded-xl p-2">
                            <img src="https://www.upf.ac.ma/images/logo_upf.png" alt="UPF Logo" class="h-10">
                        </div>
                        <span class="font-black text-white text-2xl tracking-tight">Université Privée de Fès</span>
                    </div>
                    <p class="text-base leading-relaxed max-w-md">
                        L'UPF est une institution d'enseignement supérieur reconnue par l'État marocain (depuis 2006), offrant des formations d'excellence en ingénierie, architecture et management.
                    </p>
                </div>
                <div>
                    <h4 class="text-white font-bold text-lg mb-6">Liens Rapides</h4>
                    <ul class="space-y-3 text-base">
                        <li><a href="https://www.upf.ac.ma" target="_blank" class="hover:text-upf-pink transition-colors flex items-center gap-2"><span>Site Officiel UPF</span></a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-upf-pink transition-colors">Portail Étudiant</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-upf-pink transition-colors">Espace Professeur</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-bold text-lg mb-6">Contact & Accès</h4>
                    <ul class="space-y-3 text-base">
                        <li class="flex items-start gap-2"><span>📍</span> <span>Lotissement Quaraouiyine, Université Privée de Fès, Ain chkf, Fès 30000</span></li>
                        <li class="flex items-center gap-2"><span>📞</span> <span>06 61 44 60 24</span></li>
                        <li class="flex items-center gap-2"><span>✉️</span> <span>contact@upf.ac.ma</span></li>
                    </ul>
                </div>
            </div>
            <div class="pt-8 border-t border-slate-800 text-sm flex flex-col md:flex-row justify-between items-center gap-4">
                <p>&copy; 2026 Université Privée de Fès. Tous droits réservés.</p>
                <p class="text-slate-500 font-medium">Plateforme conçue pour la Soutenance PFE 2026</p>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('landingPage', () => ({
                scrolled: false,
                init() {
                    window.addEventListener('scroll', () => {
                        this.scrolled = window.scrollY > 20;
                    });
                }
            }));
        });
    </script>
</body>
</html>
