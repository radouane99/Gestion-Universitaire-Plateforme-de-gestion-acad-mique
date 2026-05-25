<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tight italic">
                {{ __('Gestion des Filières') }}
            </h2>
            <a href="{{ route('admin.filieres.create') }}"
               class="inline-flex items-center px-6 py-3 bg-upf-magenta rounded-full font-black text-xs text-white uppercase tracking-widest hover:bg-pink-700 transition-all duration-200 shadow-lg hover:-translate-y-1 transform">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Nouvelle Filière
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Hero Banner --}}
            <div class="bg-gradient-to-br from-upf-navy via-upf-blue to-blue-700 rounded-[2.5rem] p-10 text-white shadow-2xl relative overflow-hidden">
                <div class="relative z-10 flex justify-between items-center">
                    <div>
                        <p class="text-[11px] font-black uppercase tracking-[0.3em] text-upf-magenta mb-2">Architecture Académique</p>
                        <h2 class="text-3xl font-black tracking-tighter">Programmes d'Études 🎓</h2>
                        <p class="text-blue-200 opacity-80 mt-2 text-sm max-w-lg">Gérez les filières qui structurent vos groupes et vos modules de cours. Chaque filière représente un programme d'ingénierie complet.</p>
                    </div>
                    <div class="text-right hidden md:block">
                        <p class="text-6xl font-black">{{ $filieres->count() }}</p>
                        <p class="text-[10px] uppercase tracking-widest text-blue-200 opacity-80">Filières Actives</p>
                        <p class="text-lg font-black text-upf-magenta mt-1">{{ $filieres->sum('groups_count') }} groupes · {{ $filieres->sum('modules_count') }} modules</p>
                    </div>
                </div>
                <div class="absolute -bottom-20 -right-20 w-64 h-64 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -top-16 left-32 w-48 h-48 bg-upf-magenta/10 rounded-full blur-2xl pointer-events-none"></div>
            </div>

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl font-bold flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl font-bold flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                    {{ session('error') }}
                </div>
            @endif

            {{-- Filières Cards Grid --}}
            @if($filieres->isEmpty())
                <div class="bg-white rounded-[2rem] border border-dashed border-gray-200 p-20 text-center shadow-sm">
                    <div class="text-6xl mb-4">🏛️</div>
                    <h3 class="text-xl font-black text-gray-900 mb-2">Aucune filière créée</h3>
                    <p class="text-gray-400 font-medium mb-8">Commencez par créer votre première filière académique.</p>
                    <a href="{{ route('admin.filieres.create') }}" class="inline-flex items-center px-8 py-4 bg-upf-blue text-white rounded-2xl font-black uppercase tracking-widest hover:bg-upf-navy transition-all shadow-xl hover:-translate-y-1 transform">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Créer la première filière
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($filieres as $filiere)
                    <div class="group bg-white dark:bg-slate-900 rounded-[2rem] border border-gray-100 dark:border-slate-800 shadow-sm hover:shadow-2xl hover:-translate-y-1 transform transition-all duration-300 overflow-hidden">

                        {{-- Card Header --}}
                        <div class="bg-gradient-to-br from-upf-blue to-upf-navy p-7 text-white relative overflow-hidden">
                            <div class="relative z-10 flex justify-between items-start">
                                <div>
                                    <span class="inline-block bg-upf-magenta text-white text-xs font-black uppercase tracking-widest px-3 py-1 rounded-full mb-3">
                                        {{ $filiere->code }}
                                    </span>
                                    <h3 class="text-xl font-black tracking-tight leading-tight">{{ $filiere->name }}</h3>
                                </div>
                                <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center flex-shrink-0 ml-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                </div>
                            </div>
                            @if($filiere->description)
                                <p class="text-blue-200 text-xs mt-3 opacity-80 leading-relaxed relative z-10">{{ Str::limit($filiere->description, 80) }}</p>
                            @endif
                            <div class="absolute -bottom-10 -right-10 w-32 h-32 bg-white/5 rounded-full blur-2xl pointer-events-none group-hover:bg-white/10 transition-all"></div>
                        </div>

                        {{-- Card Stats --}}
                        <div class="grid grid-cols-2 divide-x divide-gray-100 dark:divide-slate-800 border-b border-gray-100 dark:border-slate-800">
                            <div class="px-6 py-4 text-center">
                                <p class="text-3xl font-black text-upf-blue">{{ $filiere->groups_count }}</p>
                                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-1">Groupes</p>
                            </div>
                            <div class="px-6 py-4 text-center">
                                <p class="text-3xl font-black text-upf-magenta">{{ $filiere->modules_count }}</p>
                                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-1">Modules</p>
                            </div>
                        </div>

                        {{-- Card Actions --}}
                        <div class="px-6 py-4 flex items-center justify-between">
                            <a href="{{ route('admin.filieres.edit', $filiere) }}"
                               class="inline-flex items-center gap-2 px-4 py-2 bg-upf-blue/10 text-upf-blue hover:bg-upf-blue hover:text-white rounded-xl text-xs font-black uppercase tracking-widest transition-all duration-200">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                Modifier
                            </a>

                            <form action="{{ route('admin.filieres.destroy', $filiere) }}" method="POST"
                                  onsubmit="return confirm('Supprimer la filière « {{ $filiere->name }} » ? Cette action est irréversible.');">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white rounded-xl text-xs font-black uppercase tracking-widest transition-all duration-200 {{ $filiere->groups_count > 0 || $filiere->modules_count > 0 ? 'opacity-40 cursor-not-allowed' : '' }}"
                                        {{ $filiere->groups_count > 0 || $filiere->modules_count > 0 ? 'disabled title=Contient des groupes ou modules' : '' }}>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
