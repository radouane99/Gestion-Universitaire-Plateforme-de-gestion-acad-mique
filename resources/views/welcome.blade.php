<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'UPF Portal') }} - L'Université de Demain</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- PWA -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#4f46e5">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js" defer></script>
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-900 selection:bg-indigo-500 selection:text-white" x-data="landingPage()">

    <!-- Navbar -->
    <nav class="fixed w-full z-50 transition-all duration-300" :class="scrolled ? 'bg-white/80 backdrop-blur-lg shadow-sm py-3' : 'bg-transparent py-5'">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center gap-2">
                    <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-black text-xl shadow-lg shadow-indigo-500/30">
                        U
                    </div>
                    <span class="font-black text-2xl tracking-tighter text-slate-800">UPF<span class="text-indigo-600">.</span></span>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-sm font-bold text-slate-600 hover:text-indigo-600 transition-colors">Fonctionnalités</a>
                    <a href="#ai" class="text-sm font-bold text-slate-600 hover:text-indigo-600 transition-colors">Intelligence Artificielle</a>
                    <a href="#pwa" class="text-sm font-bold text-slate-600 hover:text-indigo-600 transition-colors">Application Mobile</a>
                </div>

                <!-- Auth / Install Buttons -->
                <div class="hidden md:flex items-center space-x-4">
                    <button x-show="showInstallBtn" @click="installPwa()" class="text-sm font-bold text-indigo-600 bg-indigo-50 px-4 py-2 rounded-xl hover:bg-indigo-100 transition">
                        Installer l'App
                    </button>
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-sm font-black text-white bg-slate-900 hover:bg-slate-800 px-6 py-2.5 rounded-xl transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">Mon Portail</a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-black text-white bg-indigo-600 hover:bg-indigo-700 px-6 py-2.5 rounded-xl transition shadow-lg shadow-indigo-500/30 hover:shadow-xl hover:shadow-indigo-500/40 transform hover:-translate-y-0.5">Connexion</a>
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative pt-32 pb-20 sm:pt-40 sm:pb-24 overflow-hidden">
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-indigo-50 via-slate-50 to-white"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 font-black text-xs uppercase tracking-widest mb-8 border border-indigo-100">
                <span class="relative flex h-2 w-2">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                </span>
                Soutenance PFE 2026
            </div>
            
            <h1 class="text-5xl sm:text-7xl font-black tracking-tighter text-slate-900 mb-6 leading-tight">
                L'Université de Demain, <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 italic">Dès Aujourd'hui.</span>
            </h1>
            
            <p class="mt-4 text-lg sm:text-xl text-slate-500 max-w-2xl mx-auto font-medium mb-10">
                La première plateforme académique au Maroc pilotée par l'Intelligence Artificielle. Gestion des notes, rattrapages automatiques, et relevés de notes sécurisés.
            </p>
            
            <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="w-full sm:w-auto px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-2xl shadow-lg shadow-indigo-500/30 transition transform hover:-translate-y-1 text-lg flex justify-center items-center gap-2">
                            Accéder à l'Espace <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-2xl shadow-lg shadow-indigo-500/30 transition transform hover:-translate-y-1 text-lg flex justify-center items-center gap-2">
                            Se connecter au Portail <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    @endauth
                @endif
                <button x-show="showInstallBtn" @click="installPwa()" class="w-full sm:w-auto px-8 py-4 bg-white hover:bg-slate-50 text-slate-800 border border-slate-200 font-black rounded-2xl shadow-sm transition flex justify-center items-center gap-2 text-lg">
                    📱 Installer l'App
                </button>
            </div>
            
            <!-- Dashboard Mockup Image (CSS styled div for safety) -->
            <div class="mt-20 relative max-w-5xl mx-auto">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-50 via-transparent to-transparent z-10 bottom-0 h-32"></div>
                <div class="rounded-3xl shadow-2xl border border-slate-200/60 bg-white overflow-hidden transform perspective-1000 rotate-x-12 scale-95 transition hover:rotate-0 hover:scale-100 duration-700">
                    <!-- Fake browser bar -->
                    <div class="bg-slate-100 px-4 py-3 flex items-center gap-2 border-b border-slate-200">
                        <div class="w-3 h-3 rounded-full bg-red-400"></div>
                        <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                        <div class="w-3 h-3 rounded-full bg-emerald-400"></div>
                        <div class="mx-auto bg-white rounded-md px-32 py-1 shadow-sm text-[10px] text-slate-400 font-mono">upf.ac.ma/dashboard</div>
                    </div>
                    <!-- Fake content -->
                    <div class="p-8 bg-slate-50 grid grid-cols-4 gap-6 opacity-80 h-96">
                        <div class="col-span-1 space-y-4">
                            <div class="h-8 bg-slate-200 rounded-lg w-3/4"></div>
                            <div class="h-4 bg-slate-200 rounded w-1/2"></div>
                            <div class="h-4 bg-slate-200 rounded w-full mt-8"></div>
                            <div class="h-4 bg-slate-200 rounded w-full"></div>
                            <div class="h-4 bg-slate-200 rounded w-5/6"></div>
                        </div>
                        <div class="col-span-3 space-y-6">
                            <div class="grid grid-cols-3 gap-4">
                                <div class="h-24 bg-indigo-100 rounded-2xl"></div>
                                <div class="h-24 bg-white border border-slate-200 rounded-2xl"></div>
                                <div class="h-24 bg-white border border-slate-200 rounded-2xl"></div>
                            </div>
                            <div class="h-64 bg-white border border-slate-200 rounded-3xl"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div id="features" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl font-black tracking-tighter text-slate-900 sm:text-4xl">Une plateforme complète, de l'inscription à la diplomation.</h2>
                <p class="mt-4 text-lg text-slate-500">Tout ce dont l'administration, les professeurs et les étudiants ont besoin, réuni au même endroit.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-slate-50 rounded-3xl p-8 border border-slate-100 hover:border-indigo-100 hover:shadow-xl transition-all duration-300">
                    <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center text-2xl mb-6">📄</div>
                    <h3 class="text-xl font-black text-slate-900 mb-3">Documents Officiels Sécurisés</h3>
                    <p class="text-slate-500 text-sm leading-relaxed">Génération automatique des relevés de notes et attestations de réussite en format PDF, authentifiés par un Code QR unique anti-fraude.</p>
                </div>
                <!-- Feature 2 -->
                <div class="bg-slate-50 rounded-3xl p-8 border border-slate-100 hover:border-emerald-100 hover:shadow-xl transition-all duration-300">
                    <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl mb-6">⚙️</div>
                    <h3 class="text-xl font-black text-slate-900 mb-3">Délibérations Automatisées</h3>
                    <p class="text-slate-500 text-sm leading-relaxed">Un moteur de règles puissant qui calcule la compensation, les crédits, et l'éligibilité aux rattrapages selon le strict règlement universitaire.</p>
                </div>
                <!-- Feature 3 -->
                <div class="bg-slate-50 rounded-3xl p-8 border border-slate-100 hover:border-amber-100 hover:shadow-xl transition-all duration-300">
                    <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center text-2xl mb-6">📊</div>
                    <h3 class="text-xl font-black text-slate-900 mb-3">Analyses & Pilotage</h3>
                    <p class="text-slate-500 text-sm leading-relaxed">Un tableau de bord complet avec des graphiques en temps réel (Chart.js) pour détecter les étudiants à risque et les modules difficiles.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Section -->
    <div id="ai" class="py-24 bg-slate-900 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 -mt-20 -mr-20 w-96 h-96 bg-indigo-500 rounded-full blur-3xl opacity-20"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-500/20 text-purple-300 font-black text-xs uppercase tracking-widest mb-6 border border-purple-500/30">
                        ✨ Next-Gen
                    </div>
                    <h2 class="text-4xl font-black tracking-tighter sm:text-5xl mb-6">Propulsé par <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-indigo-400 italic">LLaMA 3.3</span></h2>
                    <p class="text-lg text-slate-400 mb-8 leading-relaxed">
                        L'intelligence artificielle n'est plus le futur, c'est aujourd'hui. Notre plateforme intègre nativement un assistant virtuel surpuissant.
                    </p>
                    
                    <ul class="space-y-6">
                        <li class="flex gap-4">
                            <div class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center shrink-0">🎓</div>
                            <div>
                                <h4 class="font-bold text-white">Assistant Étudiant Intelligent</h4>
                                <p class="text-sm text-slate-400 mt-1">Un chatbot qui connaît les notes et le profil de l'étudiant pour lui répondre instantanément sur sa situation.</p>
                            </div>
                        </li>
                        <li class="flex gap-4">
                            <div class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center shrink-0">✍️</div>
                            <div>
                                <h4 class="font-bold text-white">Aide à la Correction</h4>
                                <p class="text-sm text-slate-400 mt-1">Génération automatique de brouillons de réponses aux réclamations étudiantes pour les professeurs.</p>
                            </div>
                        </li>
                        <li class="flex gap-4">
                            <div class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center shrink-0">📈</div>
                            <div>
                                <h4 class="font-bold text-white">Conseiller Pédagogique Admin</h4>
                                <p class="text-sm text-slate-400 mt-1">L'IA analyse le dossier complet d'un étudiant et génère un bilan textuel des forces et faiblesses.</p>
                            </div>
                        </li>
                    </ul>
                </div>
                
                <!-- Mockup Chat -->
                <div class="bg-slate-800 rounded-3xl p-6 border border-slate-700 shadow-2xl relative">
                    <div class="flex items-center gap-3 border-b border-slate-700 pb-4 mb-4">
                        <div class="w-10 h-10 rounded-full bg-purple-500/20 text-xl flex items-center justify-center">🤖</div>
                        <div>
                            <h4 class="font-bold">Smart UPF Assistant</h4>
                            <p class="text-xs text-green-400">En ligne</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="flex justify-end">
                            <div class="bg-indigo-600 text-white p-3 rounded-2xl rounded-tr-none text-sm max-w-[80%]">
                                Ai-je droit au rattrapage en Programmation Web ? J'ai eu 8.5/20.
                            </div>
                        </div>
                        <div class="flex justify-start">
                            <div class="bg-slate-700 text-white p-3 rounded-2xl rounded-tl-none text-sm max-w-[80%]">
                                Bonjour ! Oui, selon le règlement de l'UPF, toute note inférieure à 10/20 vous donne droit à un rattrapage. Votre demande a été signalée à l'administration.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PWA Install Section -->
    <div id="pwa" class="py-24 bg-indigo-600 text-white text-center">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-5xl mb-6">📱</div>
            <h2 class="text-3xl font-black tracking-tighter sm:text-4xl mb-4">Emportez l'Université dans votre poche</h2>
            <p class="text-indigo-100 text-lg mb-8">
                Installez l'application UPF sur votre smartphone. C'est rapide, léger, et ça ne prend pas de place (PWA). Vous recevrez vos alertes directement sur votre écran d'accueil.
            </p>
            <button x-show="showInstallBtn" @click="installPwa()" class="bg-white text-indigo-600 font-black px-8 py-4 rounded-2xl shadow-xl hover:scale-105 transition transform text-lg">
                Installer l'application maintenant
            </button>
            <p x-show="!showInstallBtn" class="text-sm text-indigo-200 bg-indigo-700/50 inline-block px-4 py-2 rounded-lg">
                L'application est déjà installée ou non supportée par votre navigateur.
            </p>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-100 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="flex justify-center items-center gap-2 mb-4">
                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-black text-sm">U</div>
                <span class="font-black tracking-tighter text-slate-800 text-xl">UPF.</span>
            </div>
            <p class="text-sm text-slate-400">Projet de Fin d'Études 2026. Réalisé avec Laravel, Alpine.js et LLaMA 3.3.</p>
        </div>
    </footer>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('landingPage', () => ({
                scrolled: false,
                deferredPrompt: null,
                showInstallBtn: false,
                
                init() {
                    window.addEventListener('scroll', () => {
                        this.scrolled = window.scrollY > 20;
                    });
                    
                    window.addEventListener('pwaMountPointReady', (e) => {
                        this.deferredPrompt = e.detail.deferredPrompt;
                        this.showInstallBtn = true;
                    });
                },
                
                installPwa() {
                    if (this.deferredPrompt) {
                        this.deferredPrompt.prompt();
                        this.deferredPrompt.userChoice.then((choiceResult) => {
                            if (choiceResult.outcome === 'accepted') {
                                console.log('User accepted the install prompt');
                                this.showInstallBtn = false;
                            }
                            this.deferredPrompt = null;
                        });
                    }
                }
            }));
        });
    </script>
</body>
</html>
