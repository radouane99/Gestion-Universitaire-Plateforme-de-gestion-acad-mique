<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="group">
                        <x-application-logo class="block h-10 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-4 sm:-my-px sm:ms-10 sm:flex items-center">
                    @if(Auth::user()->isAdmin())
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            {{ __('Tableau de bord') }}
                        </x-nav-link>
                        <!-- Dropdown Scolarité -->
                        @php
                            $isScolariteActive = request()->routeIs('admin.academic.*') || request()->routeIs('admin.users.*') || request()->routeIs('admin.groups.*') || request()->routeIs('admin.schedules.*') || request()->routeIs('admin.reservations.*') || request()->routeIs('admin.exams.*');
                        @endphp
                        <div class="inline-flex items-center h-full">
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center h-20 px-1 pt-1 border-b-2 {{ $isScolariteActive ? 'border-upf-magenta text-upf-blue font-black' : 'border-transparent text-gray-500 hover:text-upf-blue hover:border-upf-blue/30' }} text-[11px] font-bold uppercase tracking-widest transition duration-200 ease-in-out focus:outline-none whitespace-nowrap">
                                        <span>{{ __('Scolarité') }}</span>
                                        <svg class="fill-current h-4 w-4 ms-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('admin.academic.index')">
                                        {{ __('Année & Affectations') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.users.index')">
                                        {{ __('Utilisateurs') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.filieres.index')">
                                        {{ __('Filières') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.groups.index')">
                                        {{ __('Groupes') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.schedules.index')">
                                        {{ __('Emploi du temps') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.reservations.index')">
                                        {{ __('Réservations') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.exams.index')">
                                        {{ __('Examens') }}
                                    </x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        </div>
                        
                        <!-- Dropdown Gestion -->
                        @php
                            $isGestionActive = request()->routeIs('admin.modules.*') || request()->routeIs('admin.rooms.*') || request()->routeIs('admin.messages.*') || request()->routeIs('admin.activity-logs.*') || request()->routeIs('admin.textbooks.*') || request()->routeIs('admin.absences.*') || request()->routeIs('admin.requests.*');
                        @endphp
                        <div class="inline-flex items-center h-full">
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center h-20 px-1 pt-1 border-b-2 {{ $isGestionActive ? 'border-upf-magenta text-upf-blue font-black' : 'border-transparent text-gray-500 hover:text-upf-blue hover:border-upf-blue/30' }} text-[11px] font-bold uppercase tracking-widest transition duration-200 ease-in-out focus:outline-none whitespace-nowrap">
                                        <span>{{ __('Gestion') }}</span>
                                        <svg class="fill-current h-4 w-4 ms-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('admin.modules.index')">
                                        {{ __('Modules') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.rooms.index')">
                                        {{ __('Salles') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.messages.index')">
                                        {{ __('Messages') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.activity-logs.index')">
                                        {{ __('Journal d\'Activité') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.grades.index')">
                                        {{ __('Gestion des Notes') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.textbooks.index')">
                                        {{ __('Cahiers de Textes') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.absences.index')">
                                        {{ __('Absences & Justificatifs') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.requests.index')">
                                        {{ __('Demandes Administratives') }}
                                    </x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @endif

                    @if(Auth::user()->isProfessor())
                        <x-nav-link :href="route('professor.dashboard')" :active="request()->routeIs('professor.dashboard')">
                            {{ __('Tableau de bord') }}
                        </x-nav-link>

                        <!-- Dropdown Pédagogie -->
                        @php
                            $isPedagogieActive = request()->routeIs('professor.grades.*') || request()->routeIs('professor.absences.*') || request()->routeIs('professor.textbook.*');
                        @endphp
                        <div class="inline-flex items-center h-full">
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center h-20 px-1 pt-1 border-b-2 {{ $isPedagogieActive ? 'border-upf-magenta text-upf-blue font-black' : 'border-transparent text-gray-500 hover:text-upf-blue hover:border-upf-blue/30' }} text-[11px] font-bold uppercase tracking-widest transition duration-200 ease-in-out focus:outline-none whitespace-nowrap">
                                        <span>{{ __('Pédagogie') }}</span>
                                        <svg class="fill-current h-4 w-4 ms-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('professor.grades.index')">
                                        {{ __('Gestion des Notes') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('professor.absences.index')">
                                        {{ __('Absences') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('professor.textbook.index')">
                                        {{ __('Cahier de Textes') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('professor.availability.index')">
                                        {{ __('Disponibilités Examens') }}
                                    </x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        </div>

                        <!-- Dropdown Services -->
                        @php
                            $isServicesActive = request()->routeIs('professor.requests.*') || request()->routeIs('professor.reservations.*');
                        @endphp
                        <div class="inline-flex items-center h-full">
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center h-20 px-1 pt-1 border-b-2 {{ $isServicesActive ? 'border-upf-magenta text-upf-blue font-black' : 'border-transparent text-gray-500 hover:text-upf-blue hover:border-upf-blue/30' }} text-[11px] font-bold uppercase tracking-widest transition duration-200 ease-in-out focus:outline-none whitespace-nowrap">
                                        <span>{{ __('Services') }}</span>
                                        <svg class="fill-current h-4 w-4 ms-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('professor.requests.create')">
                                        {{ __('Demandes Administratives') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('professor.reservations.index')">
                                        {{ __('Réservations Salles') }}
                                    </x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @endif

                    @if(Auth::user()->isStudent())
                        <x-nav-link :href="route('student.dashboard')" :active="request()->routeIs('student.dashboard')">
                            {{ __('Mon Espace') }}
                        </x-nav-link>
                        <x-nav-link :href="route('student.grades')" :active="request()->routeIs('student.grades')">
                            {{ __('Mes Notes') }}
                        </x-nav-link>
                        <x-nav-link :href="route('student.absences')" :active="request()->routeIs('student.absences')">
                            {{ __('Mes Absences') }}
                        </x-nav-link>
                        <x-nav-link :href="route('student.convocations.index')" :active="request()->routeIs('student.convocations.*')">
                            {{ __('Convocations') }}
                        </x-nav-link>
                    @endif

                    <!-- Classroom -->
                    <x-nav-link :href="route('classroom.index')" :active="request()->routeIs('classroom.*')">
                        {{ __('Classroom') }}
                    </x-nav-link>

                    <!-- Messagerie -->
                    <x-nav-link :href="route('chat.index')" :active="request()->routeIs('chat.*')">
                        {{ __('Messagerie') }}
                    </x-nav-link>

                    <!-- Calendrier Académique -->
                    <x-nav-link :href="route('calendar')" :active="request()->routeIs('calendar')">
                        {{ __('Calendrier') }}
                    </x-nav-link>

                    <!-- FAQ -->
                    <x-nav-link :href="route('faq')" :active="request()->routeIs('faq')">
                        {{ __('FAQ') }}
                    </x-nav-link>
            <!-- Dark Mode Toggle -->
            <div class="hidden sm:flex sm:items-center sm:ms-4" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val)); if(darkMode) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark');">
                <button @click="darkMode = !darkMode; if(darkMode) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark');" 
                        class="p-2 text-gray-400 hover:text-upf-blue dark:hover:text-amber-400 transition-colors focus:outline-none">
                    <template x-if="!darkMode">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    </template>
                    <template x-if="darkMode">
                        <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </template>
                </button>
            </div>

            <!-- Notifications Bell -->
            <div class="hidden sm:flex sm:items-center sm:ms-4">
                <x-dropdown align="right" width="80">
                    <x-slot name="trigger">
                        <button class="relative p-2 text-gray-400 hover:text-upf-blue transition-colors focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            @php $unreadCount = Auth::user()->unreadNotifications->count(); @endphp
                            @if($unreadCount > 0)
                                <span class="absolute top-0 right-0 w-5 h-5 bg-upf-magenta text-white text-[9px] font-black rounded-full flex items-center justify-center border-2 border-white animate-pulse">
                                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                </span>
                            @endif
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="w-80">
                            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                                <h4 class="text-xs font-black uppercase tracking-widest text-gray-700">🔔 Notifications</h4>
                                @if($unreadCount > 0)
                                    <form action="{{ route('notifications.markAllRead') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-[10px] font-black text-upf-blue hover:text-upf-magenta transition-colors uppercase tracking-wide">
                                            Tout lire
                                        </button>
                                    </form>
                                @endif
                            </div>
                            <div class="max-h-80 overflow-y-auto divide-y divide-gray-50">
                                @forelse(Auth::user()->notifications->take(8) as $notification)
                                    @php
                                        $data = $notification->data;
                                        $isUnread = is_null($notification->read_at);
                                        $colorClass = match($data['color'] ?? 'blue') {
                                            'amber' => 'border-amber-400 bg-amber-50',
                                            'green' => 'border-emerald-400 bg-emerald-50',
                                            'red'   => 'border-red-400 bg-red-50',
                                            default => 'border-upf-blue bg-blue-50',
                                        };
                                    @endphp
                                    <a href="{{ $data['url'] ?? '#' }}" 
                                       onclick="fetch('/notifications/{{ $notification->id }}/read', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}})"
                                       class="flex items-start gap-3 p-4 hover:bg-gray-50 transition-all {{ $isUnread ? 'bg-blue-50/40' : '' }}">
                                        <div class="text-xl flex-shrink-0 mt-0.5">{{ $data['icon'] ?? '🔔' }}</div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-black text-gray-900 leading-tight {{ $isUnread ? 'text-upf-blue' : '' }} truncate">
                                                {{ $data['title'] ?? 'Notification' }}
                                            </p>
                                            <p class="text-[11px] text-gray-500 mt-0.5 leading-snug line-clamp-2">{{ $data['body'] ?? '' }}</p>
                                            <p class="text-[10px] text-gray-400 mt-1 font-bold">{{ $notification->created_at->diffForHumans() }}</p>
                                        </div>
                                        @if($isUnread)
                                            <div class="w-2 h-2 rounded-full bg-upf-blue flex-shrink-0 mt-2"></div>
                                        @endif
                                    </a>
                                @empty
                                    <div class="text-center py-10 px-4">
                                        <div class="text-4xl mb-3">🔕</div>
                                        <p class="text-sm font-black text-gray-400">Tout est calme ici !</p>
                                        <p class="text-xs text-gray-300 mt-1">Vous n'avez aucune notification.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>


            <!-- Language Switcher -->
            <div class="hidden sm:flex sm:items-center sm:ms-2">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-xs leading-4 font-black rounded-xl text-upf-magenta bg-pink-50 hover:bg-pink-100 focus:outline-none transition ease-in-out duration-150 uppercase tracking-widest">
                            {{ strtoupper(App::getLocale()) }}
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('lang.switch', 'en')" class="flex items-center justify-between">
                            <span>English</span>
                            <span class="text-[10px] font-black text-gray-300">USA</span>
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('lang.switch', 'fr')" class="flex items-center justify-between">
                            <span>Français</span>
                            <span class="text-[10px] font-black text-gray-300">FRA</span>
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('lang.switch', 'ar')" class="flex items-center justify-between text-right">
                            <span dir="rtl">العربية</span>
                            <span class="text-[10px] font-black text-gray-300">MAR</span>
                        </x-dropdown-link>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm leading-4 font-bold rounded-xl text-upf-blue bg-indigo-50 hover:bg-indigo-100 focus:outline-none transition ease-in-out duration-150">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-upf-blue flex items-center justify-center text-white mr-2 text-xs uppercase">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                                {{ Auth::user()->name }}
                            </div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Mon Profil') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Déconnexion') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Tableau de bord') }}
            </x-responsive-nav-link>
            
            @if(Auth::user()->isStudent())
                <x-responsive-nav-link :href="route('student.grades')" :active="request()->routeIs('student.grades')">
                    {{ __('Mes Notes') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('student.absences')" :active="request()->routeIs('student.absences')">
                    {{ __('Mes Absences') }}
                </x-responsive-nav-link>
            @endif

            @if(Auth::user()->isProfessor())
                <x-responsive-nav-link :href="route('professor.grades.index')" :active="request()->routeIs('professor.grades.*')">
                    {{ __('Gestion des Notes') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('professor.absences.index')" :active="request()->routeIs('professor.absences.*')">
                    {{ __('Absences') }}
                </x-responsive-nav-link>
            @endif

            <x-responsive-nav-link :href="route('classroom.index')" :active="request()->routeIs('classroom.*')">
                {{ __('Classroom') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Mon Profil') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Déconnexion') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
