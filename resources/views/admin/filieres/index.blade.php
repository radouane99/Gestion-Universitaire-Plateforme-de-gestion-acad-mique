<x-app-layout>
    <x-slot name="header">
        <x-page-header 
            title="{{ __('Programmes & Filières') }}" 
            subtitle="{{ __('Gérez les filières qui structurent vos groupes et vos modules de cours.') }}"
            icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>'
        >
            <x-slot name="actions">
                <div class="px-4 py-2 bg-white rounded-xl shadow-sm border border-gray-100 flex items-center gap-3 mr-2">
                    <span class="text-xs font-bold text-gray-500">{{ __('Filières Actives') }}:</span>
                    <span class="text-sm font-black text-upf-blue">{{ $filieres->count() }}</span>
                </div>
                <x-primary-button tag="a" href="{{ route('admin.filieres.create') }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"></path></svg>
                    {{ __('Nouvelle Filière') }}
                </x-primary-button>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-6 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-alert-messages />

            @if($filieres->isEmpty())
                <div class="bg-white rounded-[2rem] border border-dashed border-gray-200 p-20 text-center shadow-sm">
                    <div class="text-6xl mb-4">🏛️</div>
                    <h3 class="text-xl font-black text-gray-900 mb-2">{{ __('Aucune filière créée') }}</h3>
                    <p class="text-gray-400 font-medium mb-8">{{ __('Commencez par créer votre première filière académique.') }}</p>
                    <x-primary-button tag="a" href="{{ route('admin.filieres.create') }}">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        {{ __('Créer la première filière') }}
                    </x-primary-button>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($filieres as $filiere)
                    <div class="group bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transform transition-all duration-300 overflow-hidden flex flex-col">
                        <div class="bg-gradient-to-br from-upf-blue to-upf-navy p-6 text-white relative overflow-hidden">
                            <div class="relative z-10 flex justify-between items-start">
                                <div>
                                    <span class="inline-block bg-white/20 text-white text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full mb-3 backdrop-blur-sm">
                                        {{ $filiere->code }}
                                    </span>
                                    <h3 class="text-lg font-black tracking-tight leading-tight">{{ $filiere->name }}</h3>
                                </div>
                                <div class="w-10 h-10 bg-white/10 rounded-2xl flex items-center justify-center flex-shrink-0 ml-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                </div>
                            </div>
                            @if($filiere->description)
                                <p class="text-blue-100 text-xs mt-3 opacity-90 leading-relaxed relative z-10">{{ Str::limit($filiere->description, 80) }}</p>
                            @endif
                            <div class="absolute -bottom-10 -right-10 w-32 h-32 bg-white/5 rounded-full blur-2xl pointer-events-none group-hover:bg-white/10 transition-all"></div>
                        </div>

                        <div class="grid grid-cols-2 divide-x divide-gray-100 border-b border-gray-100 bg-gray-50/50">
                            <div class="px-6 py-4 text-center">
                                <p class="text-2xl font-black text-gray-800">{{ $filiere->groups_count }}</p>
                                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-1">{{ __('Groupes') }}</p>
                            </div>
                            <div class="px-6 py-4 text-center">
                                <p class="text-2xl font-black text-gray-800">{{ $filiere->modules_count }}</p>
                                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-1">{{ __('Modules') }}</p>
                            </div>
                        </div>

                        <div class="px-6 py-4 flex items-center justify-between mt-auto bg-white">
                            <a href="{{ route('admin.filieres.edit', $filiere) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-50 text-gray-700 hover:bg-upf-blue hover:text-white rounded-xl text-xs font-black uppercase tracking-widest transition-all duration-200">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                {{ __('Modifier') }}
                            </a>

                            <form action="{{ route('admin.filieres.destroy', $filiere) }}" method="POST" onsubmit="return confirm('{{ __('Supprimer la filière « :name » ? Cette action est irréversible.', ['name' => $filiere->name]) }}');">
                                @csrf @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white rounded-xl text-xs font-black uppercase tracking-widest transition-all duration-200 {{ $filiere->groups_count > 0 || $filiere->modules_count > 0 ? 'opacity-40 cursor-not-allowed' : '' }}" {{ $filiere->groups_count > 0 || $filiere->modules_count > 0 ? 'disabled title="Contient des groupes ou modules"' : '' }}>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    {{ __('Supprimer') }}
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
