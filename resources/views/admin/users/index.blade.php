<x-app-layout>
    <x-slot name="header">
        <x-page-header 
            title="{{ __('Staff & Professeurs') }}" 
            subtitle="{{ __('Gestion complète des comptes administratifs et enseignants de l\'UPF.') }}"
            icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>'
        >
            <x-slot name="actions">
                <a href="{{ route('admin.users.import.form') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-xl font-bold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none transition shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    {{ __('Importer CSV/Excel') }}
                </a>
                <x-primary-button tag="a" href="{{ route('admin.users.create') }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"></path></svg>
                    {{ __('Ajouter Utilisateur') }}
                </x-primary-button>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-6 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6" x-data="{ selectedRole: 'all' }">
            
            <x-alert-messages />

            <!-- Filtres de Rôles -->
            <div class="flex flex-wrap gap-2 bg-white p-2 rounded-2xl shadow-sm border border-gray-100">
                <button @click="selectedRole = 'all'" :class="selectedRole === 'all' ? 'bg-upf-blue text-white shadow-md' : 'text-gray-600 hover:bg-gray-50'" class="px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all">
                    {{ __('Tous') }}
                </button>
                <button @click="selectedRole = 'admin'" :class="selectedRole === 'admin' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-50'" class="px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all">
                    {{ __('Administrateurs') }}
                </button>
                <button @click="selectedRole = 'professor'" :class="selectedRole === 'professor' ? 'bg-emerald-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-50'" class="px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all">
                    {{ __('Professeurs') }}
                </button>
            </div>

            <x-card>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-100">
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ __('Détails Utilisateur') }}</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ __('Type de Compte') }}</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ __('Infos Spécifiques') }}</th>
                                <th class="px-6 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 font-bold text-gray-700">
                            @foreach($users as $user)
                            <tr class="hover:bg-gray-50/50 transition-colors" x-show="selectedRole === 'all' || selectedRole === '{{ $user->role->name }}'" x-transition>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-upf-blue to-upf-navy text-white flex items-center justify-center font-black text-sm shadow-sm mr-4">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-extrabold text-gray-900 leading-tight text-sm">{{ $user->name }}</p>
                                            <p class="text-[11px] text-gray-400 mt-0.5">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->role->name === 'admin')
                                        <x-badge type="primary">{{ __('Admin') }}</x-badge>
                                    @elseif($user->role->name === 'professor')
                                        <x-badge type="success">{{ __('Professeur') }}</x-badge>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->role->name === 'professor')
                                        <span class="text-sm text-gray-800 font-bold">{{ __('Dép') }}: {{ $user->professor->department ?? '-' }}</span>
                                    @else
                                        <span class="text-[11px] text-gray-400 italic font-black uppercase">{{ __('Accès Total') }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end items-center gap-1">
                                        <a href="{{ route('admin.users.show', $user->id) }}" class="p-2 text-upf-blue hover:bg-blue-50 rounded-xl transition-all" title="{{ __('Consulter') }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>

                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="p-2 text-amber-500 hover:bg-amber-50 rounded-xl transition-all" title="{{ __('Modifier') }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>

                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-rose-500 hover:bg-rose-50 rounded-xl transition-all" onclick="return confirm('{{ __('Êtes-vous sûr de vouloir supprimer définitivement ce compte ?') }}')" title="{{ __('Supprimer') }}">
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

                @if($users->isEmpty())
                <div class="p-16 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <p class="text-gray-500 font-bold">{{ __('Aucun utilisateur enregistré pour le moment.') }}</p>
                </div>
                @endif
            </x-card>
        </div>
    </div>
</x-app-layout>
