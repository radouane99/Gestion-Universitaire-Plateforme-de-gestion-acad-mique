<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tighter italic">
                {{ __('Catalogue des Modules') }}
            </h2>
            <div class="flex gap-3">
                <a href="{{ route('admin.modules.import.form') }}" class="px-4 py-2.5 border border-gray-300 text-gray-700 bg-white rounded-2xl hover:bg-gray-50 flex items-center gap-2 text-xs font-black uppercase tracking-wider shadow-sm transition-all duration-200">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Importer Excel/CSV
                </a>
                <a href="{{ route('admin.modules.create') }}" class="px-4 py-2.5 bg-upf-blue text-white rounded-2xl hover:bg-upf-navy flex items-center gap-2 text-xs font-black uppercase tracking-wider shadow-md hover:scale-[1.02] transform transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"></path></svg>
                    Ajouter Module
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            @if(session('success'))
                <div class="p-5 text-sm text-emerald-800 rounded-2xl bg-emerald-50 border border-emerald-100 font-bold shadow-sm animate-pulse">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('warning'))
                <div class="p-5 text-sm text-amber-800 rounded-2xl bg-amber-50 border border-amber-100 font-bold shadow-sm">
                    {{ session('warning') }}
                </div>
            @endif

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-black text-gray-900 italic">Catalogue des Enseignements</h3>
                        <p class="text-gray-500 text-sm">Gestion des modules académiques, des coefficients et des charges de cours de l'UPF.</p>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-100">
                                <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Code Module</th>
                                <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Désignation</th>
                                <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Coefficient (Poids)</th>
                                <th class="px-8 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Management</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 font-bold text-gray-700">
                            @foreach($modules as $module)
                            <tr class="hover:bg-emerald-50/10 transition-colors duration-200">
                                <td class="px-8 py-6">
                                    <span class="px-3.5 py-1.5 bg-gray-100 text-gray-900 rounded-xl font-black text-xs border border-gray-200">
                                        {{ $module->code }}
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    <p class="font-extrabold text-gray-900 text-base leading-tight">{{ $module->name }}</p>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-700 flex items-center justify-center font-black text-xs mr-3">
                                            x{{ number_format($module->coefficient, 2) }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex justify-end items-center gap-2">
                                        <!-- Éditer -->
                                        <a href="{{ route('admin.modules.edit', $module->id) }}" class="p-2.5 text-amber-500 hover:bg-amber-50 rounded-xl transition-all" title="Modifier le module">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>

                                        <!-- Supprimer -->
                                        <form action="{{ route('admin.modules.destroy', $module) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2.5 text-rose-500 hover:bg-rose-50 rounded-xl transition-all duration-300" onclick="return confirm('Voulez-vous vraiment supprimer ce module ? Cela peut impacter les notes historiques.')" title="Supprimer">
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
                <div class="p-24 text-center">
                    <p class="text-gray-400 italic">Aucun module n'a encore été configuré dans le catalogue.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
