<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tighter italic">
                {{ __("Modifier l'Utilisateur : ") }} {{ $user->name }}
            </h2>
            <a href="{{ route('admin.users.index') }}" class="px-5 py-3 border border-gray-200 text-gray-700 hover:bg-gray-50 rounded-2xl font-black text-xs uppercase tracking-widest transition-all">
                Retour à la Liste
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-[#F8FAFC]">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <div class="bg-gradient-to-r from-amber-500 to-amber-700 rounded-3xl p-10 text-white shadow-2xl relative overflow-hidden">
                <div class="relative z-10">
                    <h2 class="text-3xl font-black mb-2 italic">Mise à jour du Compte</h2>
                    <p class="text-amber-100 opacity-80">Modifiez les informations personnelles ou mettez à jour le rôle et les attributs d'un utilisateur.</p>
                </div>
            </div>

            <!-- Formulaire d'édition -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden" 
                 x-data="{ 
                     roleName: '{{ $user->role->name }}',
                     rolesMapping: {
                         @foreach($roles as $rl)
                             '{{ $rl->id }}': '{{ $rl->name }}',
                         @endforeach
                     },
                     updateRole(el) {
                         this.roleName = this.rolesMapping[el.value] || 'admin';
                     }
                 }">
                <div class="p-8 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-xl font-black text-gray-900 italic">Modifier les Paramètres</h3>
                </div>

                <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="p-10 space-y-8">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nom Complet -->
                        <div class="space-y-2">
                            <label for="name" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Nom Complet</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="w-full border-gray-200 rounded-2xl focus:ring-amber-500 focus:border-amber-500 p-4 font-bold text-gray-900 bg-gray-50">
                        </div>

                        <!-- Adresse Email -->
                        <div class="space-y-2">
                            <label for="email" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Adresse Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="w-full border-gray-200 rounded-2xl focus:ring-amber-500 focus:border-amber-500 p-4 font-bold text-gray-900 bg-gray-50">
                        </div>

                        <!-- Mot de passe (Optionnel) -->
                        <div class="space-y-2">
                            <label for="password" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Nouveau Mot de Passe (Optionnel)</label>
                            <input type="password" name="password" id="password" placeholder="Laisser vide pour ne pas modifier" class="w-full border-gray-200 rounded-2xl focus:ring-amber-500 focus:border-amber-500 p-4 font-bold text-gray-900 bg-gray-50">
                        </div>

                        <!-- Confirmer Mot de passe -->
                        <div class="space-y-2">
                            <label for="password_confirmation" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Confirmer Nouveau Mot de Passe</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Laisser vide pour ne pas modifier" class="w-full border-gray-200 rounded-2xl focus:ring-amber-500 focus:border-amber-500 p-4 font-bold text-gray-900 bg-gray-50">
                        </div>

                        <!-- Rôle -->
                        <div class="space-y-2 md:col-span-2">
                            <label for="role_id" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Rôle de l'Utilisateur</label>
                            <select name="role_id" id="role_id" required @change="updateRole($el)" class="w-full border-gray-200 rounded-2xl focus:ring-amber-500 focus:border-amber-500 p-4 font-bold text-gray-900 bg-gray-50">
                                @foreach($roles as $r)
                                    <option value="{{ $r->id }}" {{ old('role_id', $user->role_id) == $r->id ? 'selected' : '' }}>{{ ucfirst($r->name) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Attributs Étudiants (Dynamique) -->
                        <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 bg-amber-50/30 p-6 rounded-2xl border border-amber-100/50" 
                             x-show="roleName === 'student'" x-transition>
                            <div class="space-y-2">
                                <label for="group_id" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Groupe Académique</label>
                                <select name="group_id" id="group_id" class="w-full border-gray-200 rounded-2xl focus:ring-amber-500 focus:border-amber-500 p-4 font-bold text-gray-900 bg-white">
                                    <option value="">Sélectionner un groupe</option>
                                    @foreach($groups as $grp)
                                        <option value="{{ $grp->id }}" {{ old('group_id', $user->student->group_id ?? '') == $grp->id ? 'selected' : '' }}>{{ $grp->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label for="student_number" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">N° d'Inscription (Matricule)</label>
                                <input type="text" name="student_number" id="student_number" value="{{ old('student_number', $user->student->student_number ?? '') }}" class="w-full border-gray-200 rounded-2xl focus:ring-amber-500 focus:border-amber-500 p-4 font-bold text-gray-900 bg-white">
                            </div>
                        </div>

                        <!-- Attributs Enseignants (Dynamique) -->
                        <div class="md:col-span-2 space-y-2 bg-emerald-50/20 p-6 rounded-2xl border border-emerald-100/30" 
                             x-show="roleName === 'professor'" x-transition>
                            <label for="department" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Département d'Enseignement</label>
                            <input type="text" name="department" id="department" value="{{ old('department', $user->professor->department ?? '') }}" class="w-full border-gray-200 rounded-2xl focus:ring-amber-500 focus:border-amber-500 p-4 font-bold text-gray-900 bg-white">
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="p-4 text-xs text-rose-800 rounded-2xl bg-rose-50 border border-rose-100 font-bold">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="pt-6">
                        <button type="submit" class="w-full py-5 bg-amber-500 text-white rounded-2xl font-black shadow-xl hover:bg-amber-600 hover:scale-[1.02] transform transition-all duration-300 flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                            <span>Sauvegarder les Modifications</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
