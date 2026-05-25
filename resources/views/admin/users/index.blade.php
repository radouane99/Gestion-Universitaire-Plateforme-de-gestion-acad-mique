<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tighter italic">
                {{ __('Registre des Utilisateurs') }}
            </h2>
            <div class="flex gap-3">
                <a href="{{ route('admin.users.import.form') }}" class="px-4 py-2.5 border border-gray-300 text-gray-700 bg-white rounded-2xl hover:bg-gray-50 flex items-center gap-2 text-xs font-black uppercase tracking-wider shadow-sm transition-all duration-200">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Importer Excel/CSV
                </a>
                <a href="{{ route('admin.users.create') }}" class="px-4 py-2.5 bg-upf-blue text-white rounded-2xl hover:bg-upf-navy flex items-center gap-2 text-xs font-black uppercase tracking-wider shadow-md hover:scale-[1.02] transform transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"></path></svg>
                    Ajouter Utilisateur
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8" x-data="{ selectedRole: 'all' }">
            
            @if(session('success'))
                <div class="p-5 text-sm text-emerald-800 rounded-2xl bg-emerald-50 border border-emerald-100 font-bold shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('warning'))
                <div class="p-5 text-sm text-amber-800 rounded-2xl bg-amber-50 border border-amber-100 font-bold shadow-sm">
                    {{ session('warning') }}
                </div>
            @endif

            <!-- Filtres de Rôles (Tabs Alpine.js) -->
            <div class="flex flex-wrap gap-3 bg-white p-3 rounded-2xl shadow-sm border border-gray-100">
                <button @click="selectedRole = 'all'" :class="selectedRole === 'all' ? 'bg-upf-blue text-white shadow-md' : 'text-gray-600 hover:bg-gray-50'" class="px-5 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all">
                    Tous
                </button>
                <button @click="selectedRole = 'admin'" :class="selectedRole === 'admin' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-50'" class="px-5 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all">
                    Administrateurs
                </button>
                <button @click="selectedRole = 'professor'" :class="selectedRole === 'professor' ? 'bg-emerald-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-50'" class="px-5 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all">
                    Professeurs
                </button>
                <button @click="selectedRole = 'student'" :class="selectedRole === 'student' ? 'bg-amber-500 text-white shadow-md' : 'text-gray-600 hover:bg-gray-50'" class="px-5 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all">
                    Étudiants
                </button>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-black text-gray-900 italic">Registre Académique</h3>
                        <p class="text-gray-500 text-sm">Gestion complète des comptes administratifs, enseignants et étudiants de l'UPF.</p>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-100">
                                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Détails Utilisateur</th>
                                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Type de Compte</th>
                                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Infos Spécifiques</th>
                                <th class="px-8 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 font-bold text-gray-700">
                            @foreach($users as $user)
                            <tr class="hover:bg-gray-50/50 transition-colors" x-show="selectedRole === 'all' || selectedRole === '{{ $user->role->name }}'" x-transition>
                                <td class="px-8 py-6">
                                    <div class="flex items-center">
                                        <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-upf-blue to-upf-navy text-white flex items-center justify-center font-black text-base shadow-md mr-4">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-extrabold text-gray-900 leading-tight text-sm">{{ $user->name }}</p>
                                            <p class="text-xs text-gray-400 mt-1">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    @if($user->role->name === 'admin')
                                        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-wider bg-indigo-50 border border-indigo-100 text-indigo-600">Admin</span>
                                    @elseif($user->role->name === 'professor')
                                        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-wider bg-emerald-50 border border-emerald-100 text-emerald-600">Professeur</span>
                                    @else
                                        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-wider bg-amber-50 border border-amber-100 text-amber-600">Étudiant</span>
                                    @endif
                                </td>
                                <td class="px-8 py-6 text-sm text-gray-500 font-semibold">
                                    @if($user->role->name === 'student')
                                        <div class="text-gray-800 font-bold">Classe: {{ $user->student->group->name ?? 'Non assigné' }}</div>
                                        <div class="text-[10px] text-gray-400 font-black mt-0.5 uppercase">Matricule: {{ $user->student->student_number ?? '-' }}</div>
                                    @elseif($user->role->name === 'professor')
                                        <span class="text-gray-800 font-bold">Dép: {{ $user->professor->department ?? '-' }}</span>
                                    @else
                                        <span class="text-gray-400 italic">Accès Total</span>
                                    @endif
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex justify-end items-center gap-2">
                                        <!-- Voir Profil -->
                                        <a href="{{ route('admin.users.show', $user->id) }}" class="p-2.5 text-upf-blue hover:bg-blue-50 rounded-xl transition-all" title="Consulter la fiche complète">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>

                                        <!-- Éditer -->
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="p-2.5 text-amber-500 hover:bg-amber-50 rounded-xl transition-all" title="Modifier l'utilisateur">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>

                                        <!-- Archiver / Supprimer -->
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2.5 text-rose-500 hover:bg-rose-50 rounded-xl transition-all" onclick="return confirm('Êtes-vous sûr de vouloir supprimer définitivement ce compte ?')" title="Archiver / Supprimer">
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
                <div class="p-24 text-center">
                    <p class="text-gray-400 italic">Aucun utilisateur enregistré pour le moment.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
