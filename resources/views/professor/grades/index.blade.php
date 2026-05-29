<x-app-layout>
    <x-slot name="header">
        <x-page-header 
            title="{{ __('Mes Modules & Saisie des Notes') }}" 
            subtitle="{{ __('Sélectionnez un module ci-dessous pour gérer les notes de vos étudiants.') }}"
            icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
        >
        </x-page-header>
    </x-slot>

    <div class="py-6 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($taught as $session)
                <div class="group bg-white rounded-3xl shadow-sm border border-gray-100 p-8 transition-all duration-500 hover:shadow-xl hover:border-emerald-500 transform hover:-translate-y-2 relative overflow-hidden">
                    <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </div>
                    
                    <h4 class="text-xl font-black text-gray-900 mb-2 leading-tight uppercase tracking-tight">{{ $session->module->name }}</h4>
                    <div class="flex items-center space-x-2 text-sm text-gray-500 mb-6">
                        <span class="bg-gray-100 px-3 py-1 rounded-xl font-bold">{{ __('Groupe') }} : {{ $session->group->name }}</span>
                    </div>

                    <a href="{{ route('professor.grades.edit', [$session->group_id, $session->module_id]) }}" class="inline-flex items-center font-black text-emerald-600 group-hover:text-emerald-700">
                        {{ __('Saisir les Notes') }}
                        <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>
                @endforeach
            </div>

            @if($taught->isEmpty())
                <div class="text-center py-24 bg-white rounded-3xl border border-dashed border-gray-200 shadow-sm">
                    <p class="text-gray-400 italic font-bold">{{ __('Aucun enseignement assigné pour le moment.') }}</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
