<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-upf-navy dark:text-white leading-tight tracking-tight">
                {{ __('Tableau de bord') }}
            </h2>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400 bg-white dark:bg-slate-800 px-4 py-2 rounded-full shadow-sm border border-gray-100 dark:border-slate-700">
                {{ now()->translatedFormat('l j F Y') }}
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Welcome Banner -->
            <div class="bg-gradient-to-r from-upf-blue to-upf-navy rounded-2xl p-8 shadow-lg text-white relative overflow-hidden">
                <div class="relative z-10">
                    <h3 class="text-3xl font-black mb-2">{{ __('Bienvenue') }}, {{ Auth::user()->name }}! 👋</h3>
                    <p class="text-blue-100 max-w-xl">{{ __('Voici un aperçu de votre espace de gestion. Utilisez les raccourcis ci-dessous pour accéder rapidement aux fonctionnalités principales.') }}</p>
                </div>
                <!-- Decorative circles -->
                <div class="absolute top-0 right-0 -mt-16 -mr-16 w-64 h-64 bg-white opacity-10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 right-32 -mb-16 w-48 h-48 bg-upf-magenta opacity-20 rounded-full blur-2xl"></div>
            </div>

            <!-- KPIs Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- KPI 1 -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-slate-700 transition-all hover:shadow-md hover:border-upf-blue/30 group">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('Total Utilisateurs') }}</p>
                            <h4 class="text-3xl font-black text-upf-navy dark:text-white group-hover:text-upf-blue transition-colors">{{ $totalUsers }}</h4>
                        </div>
                        <div class="p-3 bg-blue-50 dark:bg-slate-700 text-upf-blue dark:text-blue-400 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- KPI 2 -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-slate-700 transition-all hover:shadow-md hover:border-upf-magenta/30 group">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('Modules Actifs') }}</p>
                            <h4 class="text-3xl font-black text-upf-navy dark:text-white group-hover:text-upf-magenta transition-colors">{{ $activeModules }}</h4>
                        </div>
                        <div class="p-3 bg-pink-50 dark:bg-slate-700 text-upf-magenta dark:text-pink-400 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- KPI 3 -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-slate-700 transition-all hover:shadow-md hover:border-emerald-500/30 group">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('Salles Disponibles') }}</p>
                            <h4 class="text-3xl font-black text-upf-navy dark:text-white group-hover:text-emerald-500 transition-colors">{{ $availableRooms }}</h4>
                        </div>
                        <div class="p-3 bg-emerald-50 dark:bg-slate-700 text-emerald-500 dark:text-emerald-400 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-8 shadow-sm border border-gray-100 dark:border-slate-700">
                <h3 class="text-lg font-bold text-upf-navy dark:text-white mb-6">{{ __('Actions Rapides') }}</h3>
                <div class="flex flex-wrap gap-4">
                    <button class="inline-flex items-center px-4 py-2 bg-upf-blue text-white rounded-xl font-bold text-sm hover:bg-upf-navy transition-colors focus:ring-2 focus:ring-offset-2 focus:ring-upf-blue">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        {{ __('Nouvel Utilisateur') }}
                    </button>
                    <button class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-700 text-gray-700 dark:text-white border border-gray-200 dark:border-slate-600 rounded-xl font-bold text-sm hover:bg-gray-50 dark:hover:bg-slate-600 transition-colors focus:ring-2 focus:ring-offset-2 focus:ring-gray-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        {{ __('Voir l\'Emploi du Temps') }}
                    </button>
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>
