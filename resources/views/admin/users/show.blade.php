<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tighter italic">
                {{ __("Fiche Profil de ") }} {{ $user->name }}
            </h2>
            <a href="{{ route('admin.users.index') }}" class="px-5 py-3 border border-gray-200 text-gray-700 hover:bg-gray-50 rounded-2xl font-black text-xs uppercase tracking-widest transition-all">
                Retour à la Liste
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-[#F8FAFC]">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Profil Header Banner -->
            <div class="bg-gradient-to-r from-upf-blue to-upf-navy rounded-3xl p-10 text-white shadow-2xl relative overflow-hidden flex flex-col md:flex-row items-center gap-6">
                <div class="w-24 h-24 rounded-3xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center font-black text-4xl shadow-inner">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <div class="text-center md:text-left space-y-2">
                    <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-wider bg-upf-magenta text-white shadow-sm">{{ $user->role->name }}</span>
                    <h3 class="text-3xl font-black italic">{{ $user->name }}</h3>
                    <p class="text-sm text-blue-100/80 font-semibold">{{ $user->email }}</p>
                </div>
            </div>

            <!-- Fiche d'information -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-100 bg-gray-50/50">
                    <h4 class="text-lg font-black text-gray-900 italic">Informations Académiques & Profil</h4>
                </div>

                <div class="p-8 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 font-bold text-gray-700">
                        <div class="space-y-1">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block">Nom Complet</span>
                            <span class="text-gray-900 text-base font-extrabold">{{ $user->name }}</span>
                        </div>

                        <div class="space-y-1">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block">Adresse Email</span>
                            <span class="text-gray-900 text-base font-extrabold">{{ $user->email }}</span>
                        </div>

                        <div class="space-y-1">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block">Rôle Système</span>
                            <span class="text-gray-900 text-base font-extrabold uppercase">{{ $user->role->name }}</span>
                        </div>

                        <div class="space-y-1">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block">Date d'Inscription</span>
                            <span class="text-gray-900 text-base font-extrabold">{{ $user->created_at->format('d/m/Y à H:i') }}</span>
                        </div>

                        <!-- Role specific data -->
                        @if($user->role->name === 'student')
                            <div class="space-y-1">
                                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block">Matricule Étudiant</span>
                                <span class="text-upf-magenta text-base font-black">{{ $user->student->student_number ?? '-' }}</span>
                            </div>

                            <div class="space-y-1">
                                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block font-black">Groupe Académique</span>
                                <span class="text-upf-blue text-base font-black">{{ $user->student->group->name ?? 'Aucun' }}</span>
                            </div>

                            <div class="space-y-1 md:col-span-2">
                                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block">Niveau d'Études</span>
                                <span class="text-gray-900 text-base font-extrabold">{{ $user->student->group->level ?? 'N/A' }}</span>
                            </div>
                        @elseif($user->role->name === 'professor')
                            <div class="space-y-1">
                                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block">Département d'Enseignement</span>
                                <span class="text-upf-blue text-base font-black">{{ $user->professor->department ?? '-' }}</span>
                            </div>

                            <div class="space-y-1">
                                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block">Type de Contrat</span>
                                <span class="text-gray-900 text-base font-extrabold capitalize">
                                    {{ $user->professor->status ?? 'permanent' }}
                                </span>
                            </div>

                            @if($user->professor && $user->professor->status === 'vacataire')
                                <div class="space-y-1 md:col-span-2">
                                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block">Date de Fin de Contrat</span>
                                    <span class="text-gray-900 text-base font-extrabold">
                                        {{ $user->professor->contract_end_date ? $user->professor->contract_end_date->format('d/m/Y') : 'Non renseignée' }}
                                        @if(!$user->professor->isContractActive())
                                            <span class="ml-2 px-2 py-0.5 rounded bg-rose-100 text-rose-800 text-[10px] font-black uppercase tracking-widest">Expiré ⚠️</span>
                                        @else
                                            <span class="ml-2 px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-black uppercase tracking-widest">Actif ✅</span>
                                        @endif
                                    </span>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                <div class="p-8 border-t border-gray-100 bg-gray-50/50 flex justify-between gap-4">
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="flex-1 py-4 bg-amber-500 hover:bg-amber-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest text-center shadow-md">
                        Modifier le Profil
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
