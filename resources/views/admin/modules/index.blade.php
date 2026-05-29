<x-app-layout>
    <x-slot name="header">
        <x-page-header 
            title="{{ __('Catalogue des Modules') }}" 
            subtitle="{{ __('Gestion des modules académiques, des coefficients et des charges de cours de l\'UPF.') }}"
            icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>'
        >
            <x-slot name="actions">
                <a href="{{ route('admin.modules.import.form') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-xl font-bold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none transition shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    {{ __('Importer CSV/Excel') }}
                </a>
                <x-primary-button tag="a" href="{{ route('admin.modules.create') }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"></path></svg>
                    {{ __('Ajouter Module') }}
                </x-primary-button>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-6 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <x-alert-messages />

            <x-card>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-100">
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ __('Code Module') }}</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ __('Désignation') }}</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ __('Coefficient') }}</th>
                                <th class="px-6 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 font-bold text-gray-700">
                            @foreach($modules as $module)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <x-badge type="neutral">{{ $module->code }}</x-badge>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-extrabold text-gray-900 leading-tight">{{ $module->name }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="px-3 py-1 rounded-lg bg-emerald-50 border border-emerald-100 text-emerald-700 font-black text-xs">
                                            x{{ number_format($module->coefficient, 2) }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end items-center gap-1">
                                        <a href="{{ route('admin.modules.edit', $module->id) }}" class="p-2 text-amber-500 hover:bg-amber-50 rounded-xl transition-all" title="{{ __('Modifier') }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>

                                        <form action="{{ route('admin.modules.destroy', $module) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-rose-500 hover:bg-rose-50 rounded-xl transition-all" onclick="return confirm('{{ __('Voulez-vous vraiment supprimer ce module ? Cela peut impacter les notes historiques.') }}')" title="{{ __('Supprimer') }}">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($modules->isEmpty())
                <div class="p-16 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <p class="text-gray-500 font-bold">{{ __('Aucun module n\'a encore été configuré.') }}</p>
                </div>
                @endif
            </x-card>
        </div>
    </div>
</x-app-layout>
