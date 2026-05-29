<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tighter italic">
            {{ __('Ajouter un Étudiant') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-[#F8FAFC]">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-xl font-black text-gray-900 italic">Nouveau Profil Étudiant</h3>
                    <p class="text-gray-500 text-sm mt-1">Saisissez les informations pour inscrire un nouvel étudiant.</p>
                </div>

                <div class="p-8">
                    <form action="{{ route('admin.students.store') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <div>
                            <x-input-label for="name" :value="__('Nom Complet')" class="font-black uppercase text-xs tracking-wider text-gray-600 mb-2"/>
                            <x-text-input id="name" name="name" type="text" class="block w-full rounded-2xl border-gray-200 focus:border-upf-blue focus:ring focus:ring-upf-blue/20 transition-all bg-gray-50/50 font-bold text-gray-800" :value="old('name')" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Adresse Email')" class="font-black uppercase text-xs tracking-wider text-gray-600 mb-2"/>
                            <x-text-input id="email" name="email" type="email" class="block w-full rounded-2xl border-gray-200 focus:border-upf-blue focus:ring focus:ring-upf-blue/20 transition-all bg-gray-50/50 font-bold text-gray-800" :value="old('email')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        <div>
                            <x-input-label for="password" :value="__('Mot de passe')" class="font-black uppercase text-xs tracking-wider text-gray-600 mb-2"/>
                            <x-text-input id="password" name="password" type="password" class="block w-full rounded-2xl border-gray-200 focus:border-upf-blue focus:ring focus:ring-upf-blue/20 transition-all bg-gray-50/50 font-bold text-gray-800" required />
                            <x-input-error class="mt-2" :messages="$errors->get('password')" />
                        </div>

                        <div>
                            <x-input-label for="password_confirmation" :value="__('Confirmer le mot de passe')" class="font-black uppercase text-xs tracking-wider text-gray-600 mb-2"/>
                            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="block w-full rounded-2xl border-gray-200 focus:border-upf-blue focus:ring focus:ring-upf-blue/20 transition-all bg-gray-50/50 font-bold text-gray-800" required />
                        </div>

                        <div class="border-t border-gray-100 pt-6">
                            <h4 class="font-black text-gray-900 mb-4 uppercase text-xs tracking-wider">Informations Pédagogiques</h4>

                            <div class="space-y-4">
                                <div>
                                    <x-input-label for="group_id" :value="__('Classe / Groupe')" class="font-black uppercase text-xs tracking-wider text-gray-600 mb-2"/>
                                    <select name="group_id" id="group_id" class="block w-full rounded-2xl border-gray-200 focus:border-upf-blue focus:ring focus:ring-upf-blue/20 transition-all bg-gray-50/50 font-bold text-gray-800">
                                        <option value="">-- Sélectionnez une classe --</option>
                                        @foreach($groups as $group)
                                            <option value="{{ $group->id }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>
                                                {{ $group->name }} ({{ $group->level }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('group_id')" />
                                </div>
                                <div>
                                    <x-input-label for="student_number" :value="__('Matricule Étudiant')" class="font-black uppercase text-xs tracking-wider text-gray-600 mb-2"/>
                                    <x-text-input id="student_number" name="student_number" type="text" class="block w-full rounded-2xl border-gray-200 focus:border-upf-blue focus:ring focus:ring-upf-blue/20 transition-all bg-gray-50/50 font-bold text-gray-800" :value="old('student_number')" placeholder="Ex: EST2026..." />
                                    <x-input-error class="mt-2" :messages="$errors->get('student_number')" />
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-100">
                            <a href="{{ route('admin.students.index') }}" class="px-5 py-3 text-sm font-black text-gray-600 hover:text-gray-900 uppercase tracking-widest transition-colors">
                                Annuler
                            </a>
                            <button type="submit" class="px-8 py-3 bg-upf-blue text-white rounded-2xl hover:bg-upf-navy text-sm font-black uppercase tracking-wider shadow-md hover:scale-[1.02] transform transition-all duration-200">
                                Inscrire l'étudiant
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
