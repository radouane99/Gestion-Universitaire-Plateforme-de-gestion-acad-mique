<x-app-layout>
    <x-slot name="header">
        <x-page-header 
            title="{{ __('Registre des Étudiants') }}" 
            subtitle="{{ __('Gestion complète des profils étudiants, matricules et classes.') }}"
            icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>'
        >
            <x-slot name="actions">
                <a href="{{ route('admin.students.import.form') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-xl font-bold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none transition shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    {{ __('Importer CSV/Excel') }}
                </a>
                <x-primary-button tag="a" href="{{ route('admin.students.create') }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"></path></svg>
                    {{ __('Ajouter Étudiant') }}
                </x-primary-button>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-6 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <x-alert-messages />

            <x-card class="mb-2">
                <form method="GET" action="{{ route('admin.students.index') }}" class="flex flex-col md:flex-row gap-4 items-end p-4">
                    <div class="w-full md:w-1/3">
                        <label for="filiere_id" class="block font-medium text-sm text-gray-700">{{ __('Filtrer par Filière') }}</label>
                        <select name="filiere_id" id="filiere_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-sm" onchange="this.form.submit()">
                            <option value="">{{ __('Toutes les filières') }}</option>
                            @foreach($filieres as $filiere)
                                <option value="{{ $filiere->id }}" {{ request('filiere_id') == $filiere->id ? 'selected' : '' }}>{{ $filiere->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-full md:w-1/3">
                        <label for="group_id" class="block font-medium text-sm text-gray-700">{{ __('Filtrer par Groupe') }}</label>
                        <select name="group_id" id="group_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-sm" onchange="this.form.submit()">
                            <option value="">{{ __('Tous les groupes') }}</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if(request()->hasAny(['filiere_id', 'group_id']) && (request('filiere_id') != '' || request('group_id') != ''))
                    <div>
                        <a href="{{ route('admin.students.index') }}" class="inline-flex items-center px-4 py-2 bg-rose-50 border border-rose-200 rounded-xl font-bold text-xs text-rose-600 uppercase tracking-widest hover:bg-rose-100 transition shadow-sm h-[38px]">
                            {{ __('Effacer les filtres') }}
                        </a>
                    </div>
                    @endif
                </form>
            </x-card>

            <x-card>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-100">
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ __('Détails Étudiant') }}</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ __('Matricule & Classe') }}</th>
                                <th class="px-6 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 font-bold text-gray-700">
                            @foreach($students as $student)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-amber-400 to-amber-600 text-white flex items-center justify-center font-black text-sm shadow-sm mr-4">
                                            {{ substr($student->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-extrabold text-gray-900 leading-tight text-sm">{{ $student->name }}</p>
                                            <p class="text-[11px] text-gray-400 mt-0.5">{{ $student->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-800 font-bold">{{ $student->student->group->name ?? __('Non assigné') }}</div>
                                    <div class="text-[10px] text-gray-400 font-black mt-0.5 uppercase">{{ __('Matricule') }}: {{ $student->student->student_number ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end items-center gap-1">
                                        <a href="{{ route('admin.students.show', $student->id) }}" class="p-2 text-upf-blue hover:bg-blue-50 rounded-xl transition-all" title="{{ __('Consulter') }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>

                                        <a href="{{ route('admin.students.edit', $student->id) }}" class="p-2 text-amber-500 hover:bg-amber-50 rounded-xl transition-all" title="{{ __('Modifier') }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>

                                        <form action="{{ route('admin.students.destroy', $student) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-rose-500 hover:bg-rose-50 rounded-xl transition-all" onclick="return confirm('{{ __('Êtes-vous sûr de vouloir supprimer définitivement cet étudiant ?') }}')" title="{{ __('Supprimer') }}">
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

                @if($students->isEmpty())
                <div class="p-16 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <p class="text-gray-500 font-bold">{{ __('Aucun étudiant enregistré pour le moment.') }}</p>
                </div>
                @endif
            </x-card>
        </div>
    </div>
</x-app-layout>
