<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Contact | UPF - Université Privée de Fès</title>
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
    <body class="antialiased selection:bg-upf-magenta selection:text-white bg-slate-50 dark:bg-slate-900 transition-colors duration-300">

        <!-- Premium Header (Identical to Welcome) -->
        <header class="fixed w-full z-50 bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border-b border-gray-100 dark:border-slate-800 transition-all duration-500" id="main-header">
            <nav class="max-w-7xl mx-auto px-6 h-24 flex justify-between items-center">
                <a href="{{ url('/') }}" class="flex items-center gap-4 group">
                    <div class="w-12 h-12 bg-upf-blue rounded-2xl flex items-center justify-center text-white shadow-xl group-hover:rotate-6 transition-transform">
                        <span class="font-black text-xl">U</span>
                    </div>
                    <div>
                        <h1 class="text-xl font-black text-upf-blue dark:text-white leading-tight tracking-tighter transition-colors">UPF</h1>
                        <p class="text-[10px] uppercase font-bold text-upf-magenta tracking-widest mt-0.5">Excellence Hub</p>
                    </div>
                </a>

                <div class="hidden md:flex items-center space-x-6 text-sm font-bold text-gray-500 dark:text-gray-400">
                    <a href="{{ url('/#about') }}" class="hover:text-upf-blue dark:hover:text-white transition-colors uppercase tracking-widest">{{ __('About') }}</a>
                    <a href="{{ url('/#academics') }}" class="hover:text-upf-blue dark:hover:text-white transition-colors uppercase tracking-widest">{{ __('Academics') }}</a>
                    <a href="{{ route('contact') }}" class="text-[#B00D5D] dark:text-pink-400 transition-colors uppercase tracking-widest">{{ __('Contact') }}</a>
                    
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

        <!-- Main Content -->
        <main class="pt-32 pb-24">
            <div class="max-w-7xl mx-auto px-6">
                <!-- Header Section -->
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <h2 class="text-4xl md:text-5xl font-black text-upf-blue dark:text-white tracking-tighter italic mb-6">
                        {{ __('Get in Touch') }}
                    </h2>
                    <p class="text-lg text-gray-500 dark:text-gray-400 leading-relaxed">
                        {{ __('Have questions about our programs or admissions? Our dedicated team is here to assist you with any inquiries you may have.') }}
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                    <!-- Contact Info Cards -->
                    <div class="lg:col-span-1 space-y-6">
                        <div class="bg-white dark:bg-slate-800 p-8 rounded-[2rem] shadow-xl border border-gray-100 dark:border-slate-700 transition-colors duration-300">
                            <div class="w-14 h-14 bg-indigo-50 dark:bg-slate-700 rounded-2xl flex items-center justify-center text-upf-blue dark:text-blue-400 mb-6">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ __('Campus Location') }}</h4>
                            <p class="text-gray-500 dark:text-gray-400 leading-relaxed">
                                Lotissement Saada, Fès<br>Morocco
                            </p>
                        </div>

                        <div class="bg-white dark:bg-slate-800 p-8 rounded-[2rem] shadow-xl border border-gray-100 dark:border-slate-700 transition-colors duration-300">
                            <div class="w-14 h-14 bg-rose-50 dark:bg-slate-700 rounded-2xl flex items-center justify-center text-upf-magenta dark:text-pink-400 mb-6">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ __('Email Address') }}</h4>
                            <p class="text-gray-500 dark:text-gray-400 leading-relaxed">
                                contact@upf.ac.ma<br>admissions@upf.ac.ma
                            </p>
                        </div>

                        <div class="bg-white dark:bg-slate-800 p-8 rounded-[2rem] shadow-xl border border-gray-100 dark:border-slate-700 transition-colors duration-300">
                            <div class="w-14 h-14 bg-amber-50 dark:bg-slate-700 rounded-2xl flex items-center justify-center text-amber-600 dark:text-amber-400 mb-6">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            </div>
                            <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ __('Phone Number') }}</h4>
                            <p class="text-gray-500 dark:text-gray-400 leading-relaxed">
                                +212 (0) 535 60 80 80<br>+212 (0) 535 60 80 81
                            </p>
                        </div>
                    </div>

                    <!-- Contact Form -->
                    <div class="lg:col-span-2">
                        <div class="bg-white dark:bg-slate-800 p-10 md:p-14 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-slate-700 transition-colors duration-300">
                            <h3 class="text-3xl font-black text-gray-900 dark:text-white mb-8">{{ __('Send us a message') }}</h3>
                            
                            @if(session('success'))
                                <div class="mb-8 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-600 dark:text-green-400 px-6 py-4 rounded-2xl flex items-center gap-4">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span class="font-bold">{{ session('success') }}</span>
                                </div>
                            @endif

                            <form action="{{ route('contact.store') }}" method="POST" class="space-y-6">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">{{ __('Full Name') }}</label>
                                        <input type="text" name="name" required class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-upf-magenta focus:border-upf-magenta dark:text-white transition-colors" placeholder="{{ __('Your Name') }}">
                                        @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">{{ __('Email Address') }}</label>
                                        <input type="email" name="email" required class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-upf-magenta focus:border-upf-magenta dark:text-white transition-colors" placeholder="email@example.com">
                                        @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">{{ __('Subject') }}</label>
                                    <input type="text" name="subject" required class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-upf-magenta focus:border-upf-magenta dark:text-white transition-colors" placeholder="{{ __('How can we help you?') }}">
                                    @error('subject') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">{{ __('Your Message') }}</label>
                                    <textarea name="message" rows="6" required class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-upf-magenta focus:border-upf-magenta dark:text-white transition-colors resize-none" placeholder="{{ __('Write your message here...') }}"></textarea>
                                    @error('message') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>

                                <button type="submit" class="w-full md:w-auto px-10 py-5 bg-upf-blue text-white rounded-2xl font-black shadow-xl hover:bg-upf-navy dark:hover:bg-blue-600 transition-all transform hover:-translate-y-1 text-center">
                                    {{ __('Send Message') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Premium Footer -->
        <footer class="bg-upf-navy dark:bg-slate-950 pt-24 pb-12 text-white transition-colors duration-300">
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
                </div>
                <div>
                    <h5 class="text-lg font-black mb-8 italic text-upf-magenta dark:text-pink-500">{{ __('Liaisons') }}</h5>
                    <ul class="space-y-4 text-gray-400 font-medium">
                        <li><a href="{{ url('/') }}" class="hover:text-white transition-all">{{ __('Program Overview') }}</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-white transition-all">{{ __('Contact') }}</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="max-w-7xl mx-auto px-6 pt-12 flex flex-col md:flex-row justify-between items-center text-xs font-bold text-gray-500 uppercase tracking-widest">
                <p>&copy; 2026 UPF - Université Privée de Fès. {{ __('All Rights Reserved.') }}</p>
            </div>
        </footer>

    </body>
</html>
