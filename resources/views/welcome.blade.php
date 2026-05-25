<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>UPF | Université Privée de Fès - Excellence & Innovation</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Dark Mode Script -->
        <script>
            if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>
    </head>
    <body class="antialiased selection:bg-upf-magenta selection:text-white bg-white dark:bg-slate-900 transition-colors duration-300">

        <!-- Premium Header -->
        <header class="fixed w-full z-50 bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border-b border-gray-100 dark:border-slate-800 transition-all duration-500" id="main-header">
            <nav class="max-w-7xl mx-auto px-6 h-24 flex justify-between items-center">
                <div class="flex items-center gap-4 group">
                    <div class="w-12 h-12 bg-upf-blue rounded-2xl flex items-center justify-center text-white shadow-xl group-hover:rotate-6 transition-transform">
                        <span class="font-black text-xl">U</span>
                    </div>
                    <div>
                        <h1 class="text-xl font-black text-upf-blue dark:text-white leading-tight tracking-tighter transition-colors">UPF</h1>
                        <p class="text-[10px] uppercase font-bold text-upf-magenta tracking-widest mt-0.5">Excellence Hub</p>
                    </div>
                </div>

                <div class="hidden md:flex items-center space-x-6 text-sm font-bold text-gray-500 dark:text-gray-400">
                    <a href="#about" class="hover:text-upf-blue dark:hover:text-white transition-colors uppercase tracking-widest">{{ __('About') }}</a>
                    <a href="#academics" class="hover:text-upf-blue dark:hover:text-white transition-colors uppercase tracking-widest">{{ __('Academics') }}</a>
                    <a href="{{ route('contact') }}" class="hover:text-upf-blue transition-colors uppercase tracking-widest text-[#B00D5D] dark:text-pink-400">{{ __('Contact') }}</a>
                    
                    <div class="h-6 w-px bg-gray-200 dark:bg-slate-700 mx-2"></div>

                    <!-- Dark Mode Toggle -->
                    <div x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val)); if(darkMode) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark');">
                        <button @click="darkMode = !darkMode; if(darkMode) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark');" 
                                class="p-2 text-gray-400 hover:text-upf-blue dark:hover:text-amber-400 transition-colors focus:outline-none rounded-full">
                            <template x-if="!darkMode">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                            </template>
                            <template x-if="darkMode">
                                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </template>
                        </button>
                    </div>

                    <!-- Language Switcher Dropdown -->
                    <div class="relative" x-data="{ open: false }" @click.away="open = false" @close.stop="open = false">
                        <button @click="open = ! open" class="inline-flex items-center px-3 py-2 border border-transparent text-xs leading-4 font-black rounded-xl text-upf-magenta bg-pink-50 dark:bg-slate-800 dark:text-pink-400 hover:bg-pink-100 dark:hover:bg-slate-700 focus:outline-none transition ease-in-out duration-150 uppercase tracking-widest">
                            {{ strtoupper(App::getLocale()) }}
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>

                        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute z-50 mt-2 w-48 rounded-2xl shadow-xl border border-gray-100 dark:border-slate-700 bg-white dark:bg-slate-800" style="display: none;" @click="open = false">
                            <div class="py-1 rounded-2xl">
                                <a href="{{ route('lang.switch', 'en') }}" class="block w-full px-4 py-3 text-start text-sm leading-5 font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 focus:outline-none transition duration-150 ease-in-out flex items-center justify-between">
                                    <span>English</span><span class="text-[10px] font-black text-gray-400">USA</span>
                                </a>
                                <a href="{{ route('lang.switch', 'fr') }}" class="block w-full px-4 py-3 text-start text-sm leading-5 font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 focus:outline-none transition duration-150 ease-in-out flex items-center justify-between">
                                    <span>Français</span><span class="text-[10px] font-black text-gray-400">FRA</span>
                                </a>
                                <a href="{{ route('lang.switch', 'ar') }}" class="block w-full px-4 py-3 text-start text-sm leading-5 font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 focus:outline-none transition duration-150 ease-in-out flex items-center justify-between">
                                    <span dir="rtl">العربية</span><span class="text-[10px] font-black text-gray-400">MAR</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-8 py-3 bg-upf-blue text-white rounded-xl shadow-lg hover:bg-upf-navy dark:hover:bg-blue-600 transition-all transform hover:-translate-y-1">{{ __('Portal Access') }}</a>
                        @else
                            <a href="{{ route('login') }}" class="px-8 py-3 bg-gradient-to-r from-upf-blue to-upf-navy text-white rounded-xl shadow-xl hover:shadow-blue-200 dark:hover:shadow-none transition-all transform hover:-translate-y-1 text-center">{{ __('Academic Space') }}</a>
                        @endauth
                    @endif
                </div>

                <!-- Mobile Trigger -->
                <button class="md:hidden p-2 text-upf-blue dark:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                </button>
            </nav>
        </header>

        <!-- Dynamic Hero Section -->
        <section class="relative min-h-[90vh] flex items-center pt-24 overflow-hidden bg-white dark:bg-slate-900 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div class="relative z-10 space-y-8">
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-blue-50 dark:bg-blue-900/30 text-upf-blue dark:text-blue-300 border border-blue-100 dark:border-blue-800/50">
                        <span class="w-2 h-2 rounded-full bg-upf-magenta animate-ping mr-3"></span>
                        <span class="text-xs font-black uppercase tracking-widest">{{ __('Admissions 2024-2025 Open') }}</span>
                    </div>
                    <h2 class="text-6xl lg:text-7xl font-black text-gray-900 dark:text-white leading-[1.05] italic tracking-tighter transition-colors">
                        {{ __('Architecting') }} <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-upf-blue to-upf-magenta dark:from-blue-400 dark:to-pink-500">{{ __('Success') }}</span> <br>
                        {{ __('Through Innovation.') }}
                    </h2>
                    <p class="text-lg text-gray-500 dark:text-gray-400 font-medium max-w-lg leading-relaxed transition-colors">
                        {{ __("Join Morocco's elite academic ecosystem. The Université Privée de Fès empowers the next generation of leaders with world-class faculty and state-of-the-art infrastructure.") }}
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 pt-4">
                        <a href="#apply" class="px-10 py-5 bg-upf-magenta text-white rounded-2xl font-black shadow-2xl shadow-rose-200 dark:shadow-none hover:bg-upf-blue transition-all transform hover:scale-105 text-center leading-none">{{ __('Apply Now') }}</a>
                        <a href="#discover" class="px-10 py-5 bg-white dark:bg-slate-800 text-upf-blue dark:text-white border-2 border-gray-100 dark:border-slate-700 rounded-2xl font-black hover:bg-gray-50 dark:hover:bg-slate-700 transition-all text-center leading-none">{{ __('Discover Programs') }}</a>
                    </div>
                </div>
                <div class="relative">
                    <div class="aspect-square rounded-[3rem] overflow-hidden shadow-2xl dark:shadow-blue-900/20 transform lg:rotate-3 hover:rotate-0 transition-transform duration-700">
                        <img src="{{ asset('storage/hero.png') }}" class="w-full h-full object-cover" alt="UPF Campus">
                    </div>
                    <div class="absolute -bottom-8 -left-8 bg-white dark:bg-slate-800 p-8 rounded-3xl shadow-xl dark:shadow-2xl border border-gray-100 dark:border-slate-700 max-w-[240px] animate-bounce-slow transition-colors">
                        <p class="text-3xl font-black text-upf-blue dark:text-blue-400">98%</p>
                        <p class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mt-1">{{ __('Graduate Employment') }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Abstract background shape -->
            <div class="absolute -top-24 -right-24 w-[600px] h-[600px] bg-indigo-50 dark:bg-blue-900/20 rounded-full blur-[120px] opacity-50 dark:opacity-30"></div>
        </section>

        <!-- Animated Institutional Stats -->
        <section class="py-20 bg-white dark:bg-slate-900 border-y border-gray-100 dark:border-slate-800 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-6 grid grid-cols-2 lg:grid-cols-4 gap-10 text-center" x-data="{
                students: 0,
                professors: 0,
                modules: 0,
                successRate: 0,
                init() {
                    const duration = 2000;
                    const steps = 60;
                    const stepTime = duration / steps;
                    
                    let step = 0;
                    const timer = setInterval(() => {
                        step++;
                        this.students = Math.floor((2500 / steps) * step);
                        this.professors = Math.floor((150 / steps) * step);
                        this.modules = Math.floor((80 / steps) * step);
                        this.successRate = Math.floor((98 / steps) * step);
                        
                        if (step >= steps) {
                            this.students = 2500;
                            this.professors = 150;
                            this.modules = 80;
                            this.successRate = 98;
                            clearInterval(timer);
                        }
                    }, stepTime);
                }
            }">
                <div class="space-y-2 p-6 bg-gray-50 dark:bg-slate-800 rounded-3xl border border-gray-100 dark:border-slate-700 transition-all hover:scale-105">
                    <p class="text-4xl lg:text-5xl font-black text-upf-blue dark:text-blue-400 font-mono" x-text="'+' + students"></p>
                    <p class="text-xs uppercase font-black text-gray-400 tracking-widest mt-2">{{ __('Active Students') }}</p>
                </div>
                <div class="space-y-2 p-6 bg-gray-50 dark:bg-slate-800 rounded-3xl border border-gray-100 dark:border-slate-700 transition-all hover:scale-105">
                    <p class="text-4xl lg:text-5xl font-black text-upf-magenta dark:text-pink-400 font-mono" x-text="'+' + professors"></p>
                    <p class="text-xs uppercase font-black text-gray-400 tracking-widest mt-2">{{ __('Expert Professors') }}</p>
                </div>
                <div class="space-y-2 p-6 bg-gray-50 dark:bg-slate-800 rounded-3xl border border-gray-100 dark:border-slate-700 transition-all hover:scale-105">
                    <p class="text-4xl lg:text-5xl font-black text-gray-900 dark:text-white font-mono" x-text="'+' + modules"></p>
                    <p class="text-xs uppercase font-black text-gray-400 tracking-widest mt-2">{{ __('Active Modules') }}</p>
                </div>
                <div class="space-y-2 p-6 bg-gray-50 dark:bg-slate-800 rounded-3xl border border-gray-100 dark:border-slate-700 transition-all hover:scale-105">
                    <p class="text-4xl lg:text-5xl font-black text-emerald-600 dark:text-emerald-400 font-mono" x-text="successRate + '%'"></p>
                    <p class="text-xs uppercase font-black text-gray-400 tracking-widest mt-2">{{ __('Success Rate') }}</p>
                </div>
            </div>
        </section>

        <!-- Academic Excellence -->
        <section id="academics" class="py-32 bg-[#F8FAFC] dark:bg-[#020617] transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-6 text-center mb-24">
                <h3 class="text-[10px] font-black uppercase text-upf-magenta dark:text-pink-500 tracking-[0.3em] mb-4 italic">{{ __('Our Pillars') }}</h3>
                <h2 class="text-5xl font-black text-upf-blue dark:text-white tracking-tighter italic transition-colors">{{ __('World-Class Academics') }}</h2>
            </div>
            
            <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-10">
                <!-- Program 1 -->
                <div class="bg-white dark:bg-slate-800 p-12 rounded-[2.5rem] shadow-xl hover:shadow-2xl dark:shadow-none transition-all border border-gray-100 dark:border-slate-700 group">
                    <div class="w-20 h-20 bg-indigo-50 dark:bg-slate-700 rounded-3xl flex items-center justify-center text-upf-blue dark:text-blue-400 mb-8 group-hover:bg-upf-blue group-hover:text-white dark:group-hover:bg-blue-500 transition-all">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                    </div>
                    <h4 class="text-2xl font-black text-gray-900 dark:text-white mb-4 transition-colors">{{ __('Engineering & IT') }}</h4>
                    <p class="text-gray-500 dark:text-gray-400 font-medium leading-relaxed transition-colors">{{ __('Leading the digital transformation with specialized labs and partnerships.') }}</p>
                </div>

                <!-- Program 2 -->
                <div class="bg-white dark:bg-slate-800 p-12 rounded-[2.5rem] shadow-xl hover:shadow-2xl dark:shadow-none transition-all border border-gray-100 dark:border-slate-700 group">
                    <div class="w-20 h-20 bg-rose-50 dark:bg-slate-700 rounded-3xl flex items-center justify-center text-upf-magenta dark:text-pink-400 mb-8 group-hover:bg-upf-magenta group-hover:text-white dark:group-hover:bg-pink-500 transition-all">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <h4 class="text-2xl font-black text-gray-900 dark:text-white mb-4 transition-colors">{{ __('Business & Management') }}</h4>
                    <p class="text-gray-500 dark:text-gray-400 font-medium leading-relaxed transition-colors">{{ __('Incubating future CEOs through international exchange and leadership.') }}</p>
                </div>

                <!-- Program 3 -->
                <div class="bg-white dark:bg-slate-800 p-12 rounded-[2.5rem] shadow-xl hover:shadow-2xl dark:shadow-none transition-all border border-gray-100 dark:border-slate-700 group">
                    <div class="w-20 h-20 bg-amber-50 dark:bg-slate-700 rounded-3xl flex items-center justify-center text-amber-600 dark:text-amber-400 mb-8 group-hover:bg-amber-600 group-hover:text-white dark:group-hover:bg-amber-500 transition-all">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <h4 class="text-2xl font-black text-gray-900 dark:text-white mb-4 transition-colors">{{ __('Post-Graduate Excellence') }}</h4>
                    <p class="text-gray-500 dark:text-gray-400 font-medium leading-relaxed transition-colors">{{ __('Advanced research and doctorate programs driving regional innovation.') }}</p>
                </div>
            </div>
        </section>

        <!-- Premium Footer -->
        <footer id="contact" class="bg-upf-navy dark:bg-slate-950 pt-24 pb-12 text-white transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-16 border-b border-white/10 pb-20">
                <div class="col-span-1 md:col-span-2 space-y-8">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-white dark:bg-slate-800 rounded-2xl flex items-center justify-center">
                            <span class="font-black text-xl text-upf-blue dark:text-white">U</span>
                        </div>
                        <h2 class="text-2xl font-black italic tracking-tighter">Université Privée de Fès</h2>
                    </div>
                    <p class="text-gray-400 max-w-sm leading-relaxed">
                        {{ __('A recognized center of higher education and professional training, dedicated to the development of human capital and regional progress.') }}
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 rounded-full border border-white/20 flex items-center justify-center hover:bg-upf-magenta transition-all">FB</a>
                        <a href="#" class="w-10 h-10 rounded-full border border-white/20 flex items-center justify-center hover:bg-upf-magenta transition-all">IN</a>
                        <a href="#" class="w-10 h-10 rounded-full border border-white/20 flex items-center justify-center hover:bg-upf-magenta transition-all">XT</a>
                    </div>
                </div>

                <div>
                    <h5 class="text-lg font-black mb-8 italic text-upf-magenta dark:text-pink-500">{{ __('Liaisons') }}</h5>
                    <ul class="space-y-4 text-gray-400 font-medium">
                        <li><a href="#" class="hover:text-white transition-all">{{ __('Program Overview') }}</a></li>
                        <li><a href="#" class="hover:text-white transition-all">{{ __('Online Library') }}</a></li>
                        <li><a href="#" class="hover:text-white transition-all">{{ __('Career Services') }}</a></li>
                        <li><a href="#" class="hover:text-white transition-all">{{ __('Apply Now') }}</a></li>
                    </ul>
                </div>

                <div>
                    <h5 class="text-lg font-black mb-8 italic text-upf-magenta dark:text-pink-500">{{ __('HQ Information') }}</h5>
                    <ul class="space-y-4 text-gray-400 font-medium">
                        <li>Lotissement Saada, Fès, Morocco</li>
                        <li>+212 (0) 535 60 80 80</li>
                        <li>contact@upf.ac.ma</li>
                    </ul>
                </div>
            </div>
            
            <div class="max-w-7xl mx-auto px-6 pt-12 flex flex-col md:flex-row justify-between items-center text-xs font-bold text-gray-500 uppercase tracking-widest">
                <p>&copy; 2024 UPF - Université Privée de Fès. {{ __('All Rights Reserved.') }}</p>
                <div class="flex space-x-8 mt-4 md:mt-0">
                    <a href="#" class="hover:text-white">{{ __('Privacy') }}</a>
                    <a href="#" class="hover:text-white">{{ __('Terms') }}</a>
                </div>
            </div>
        </footer>

        <style>
            @keyframes bounce-slow {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-15px); }
            }
            .animate-bounce-slow {
                animation: bounce-slow 4s ease-in-out infinite;
            }
            html {
                scroll-behavior: smooth;
            }
        </style>
    </body>
</html>
