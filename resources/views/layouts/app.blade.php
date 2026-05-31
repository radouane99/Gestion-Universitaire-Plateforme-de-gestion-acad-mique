<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'UPF Portal') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        
        <!-- FullCalendar -->
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

        <!-- PWA Meta Tags -->
        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#1f2937">
        <meta name="description" content="Application de gestion universitaire intégrée pour l'Université Privée de Fès">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="UPF Portail">
        <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
        <link rel="icon" type="image/png" href="/icons/icon-192x192.png">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script>
            // Dark mode support
            if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }

            // Accent color theme customizer
            window.applyThemeAccent = function (accent) {
                accent = accent || localStorage.getItem('themeAccent') || 'blue';
                const colors = {
                    'blue': { primary: '#003893', hover: '#002a6f', text: 'text-upf-blue', bg: 'bg-upf-blue', glow: 'rgba(0, 56, 147, 0.15)' },
                    'magenta': { primary: '#E6007E', hover: '#c10067', text: 'text-upf-magenta', bg: 'bg-upf-magenta', glow: 'rgba(230, 0, 126, 0.15)' },
                    'indigo': { primary: '#6366F1', hover: '#4f46e5', text: 'text-indigo-600', bg: 'bg-indigo-600', glow: 'rgba(99, 102, 241, 0.15)' },
                    'emerald': { primary: '#10B981', hover: '#059669', text: 'text-emerald-600', bg: 'bg-emerald-600', glow: 'rgba(16, 185, 129, 0.15)' }
                };
                const theme = colors[accent] || colors['blue'];
                let styleTag = document.getElementById('theme-accent-inline');
                if (!styleTag) {
                    styleTag = document.createElement('style');
                    styleTag.id = 'theme-accent-inline';
                    document.head.appendChild(styleTag);
                }
                styleTag.innerHTML = `
                    :root {
                        --upf-primary: ${theme.primary};
                        --upf-primary-hover: ${theme.hover};
                        --upf-primary-glow: ${theme.glow};
                    }
                    .text-upf-blue, .text-upf-magenta, .text-indigo-600, .text-emerald-600,
                    .group:hover .group-hover\\:text-upf-blue, .group:hover .group-hover\\:text-upf-magenta {
                        color: var(--upf-primary) !important;
                    }
                    .bg-upf-blue, .bg-upf-magenta, .bg-indigo-600, .bg-emerald-600,
                    .group:hover .group-hover\\:bg-upf-blue, .group:hover .group-hover\\:bg-upf-magenta {
                        background-color: var(--upf-primary) !important;
                    }
                    .hover\\:bg-upf-blue:hover, .hover\\:bg-upf-magenta:hover, .hover\\:bg-indigo-650:hover, .hover\\:bg-emerald-650:hover {
                        background-color: var(--upf-primary-hover) !important;
                    }
                    .border-upf-blue, .border-upf-magenta, .border-indigo-600, .border-emerald-600 {
                        border-color: var(--upf-primary) !important;
                    }
                `;
                localStorage.setItem('themeAccent', accent);
                window.dispatchEvent(new CustomEvent('accent-changed', { detail: accent }));
            }
            window.applyThemeAccent();
        </script>
        
        <!-- Service Worker Registration -->
        <script>
            // Unregister service worker in development to prevent aggressive caching issues
            if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
                if ('serviceWorker' in navigator) {
                    navigator.serviceWorker.getRegistrations().then(registrations => {
                        for (let registration of registrations) {
                            registration.unregister().then(() => {
                                console.log('[PWA] Unregistered service worker in development');
                            });
                        }
                    });
                }
                // Clear any stored Cache Storage caches to bust cache instantly
                if ('caches' in window) {
                    caches.keys().then(names => {
                        for (let name of names) {
                            caches.delete(name).then(() => {
                                console.log('[PWA] Cleared cache storage:', name);
                            });
                        }
                    });
                }
            } else if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js')
                        .then(registration => {
                            console.log('[PWA] Service Worker registered successfully:', registration.scope);
                            
                            // Check for updates periodically (every hour)
                            setInterval(() => {
                                registration.update();
                            }, 60 * 60 * 1000);
                            
                            // Handle update available
                            registration.addEventListener('updatefound', () => {
                                const newWorker = registration.installing;
                                newWorker.addEventListener('statechange', () => {
                                    if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                        // New service worker ready, notify user
                                        console.log('[PWA] New version available. Refresh to update.');
                                        // You can dispatch custom event here for UI notification
                                        window.dispatchEvent(new CustomEvent('swUpdated', { detail: registration }));
                                    }
                                });
                            });
                        })
                        .catch(err => {
                            console.error('[PWA] Service Worker registration failed:', err);
                        });
                    
                    // Handle successful service worker activation
                    let refreshing;
                    navigator.serviceWorker.addEventListener('controllerchange', () => {
                        if (refreshing) return;
                        refreshing = true;
                        window.location.reload();
                    });
                });
            } else {
                console.log('[PWA] Service Workers not supported in this browser');
            }

            // Detect install prompt
            let deferredPrompt;
            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                deferredPrompt = e;
                // Signal app that install button should be shown
                window.dispatchEvent(new CustomEvent('pwaMountPointReady', { detail: { deferredPrompt } }));
            });

            // Handle app installed
            window.addEventListener('appinstalled', () => {
                console.log('[PWA] App successfully installed');
                deferredPrompt = null;
            });
        </script>
    </head>
    <body class="font-sans antialiased bg-[#F8FAFC] dark:bg-[#020617] transition-colors duration-300">
        <div class="min-h-screen">
            @include('layouts.navigation')

            <!-- Main Content Area (offset by sidebar + top bar) -->
            <div class="{{ app()->getLocale() == 'ar' ? 'lg:mr-[280px]' : 'lg:ml-[280px]' }} pt-16 pb-16 lg:pb-0 min-h-screen transition-all duration-300">
                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white dark:bg-slate-900 border-b border-gray-100 dark:border-slate-800 transition-colors duration-300">
                        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main>
                    {{ $slot }}
                </main>
            </div>

            <!-- AI Chat Widget (Llama) for All Users -->
            @if(Auth::check())
                <x-ai-chat-widget />
            @endif
        </div>
    </body>
</html>
