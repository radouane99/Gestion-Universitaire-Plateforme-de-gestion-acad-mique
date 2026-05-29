<x-app-layout>
    <x-slot name="header">
        <x-page-header 
            title="{{ __('Appel : :module', ['module' => $session->module->name]) }}" 
            subtitle="{{ __('Groupe :group', ['group' => $session->group->name]) }}"
            icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>'
        >
            <x-slot name="actions">
                <a href="{{ route('professor.absences.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-xl font-bold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 transition shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    {{ __('Retour aux sessions') }}
                </a>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-6 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <x-card class="p-0">
                <form action="{{ route('professor.absences.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="schedule_id" value="{{ $session->id }}">
                    
                    <div class="p-8 border-b border-gray-100 bg-gray-50/50">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <x-input-label for="date" :value="__('Date de la séance')" class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2" />
                                <x-text-input type="date" name="date" class="block w-full border-gray-200 rounded-2xl bg-white shadow-sm focus:ring-emerald-500 focus:border-emerald-500 font-bold p-3" value="{{ date('Y-m-d') }}" required />
                            </div>
                            <div>
                                <x-input-label for="session_type" :value="__('Type de séance')" class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2" />
                                <select name="session_type" class="block w-full border-gray-200 rounded-2xl bg-white shadow-sm focus:ring-emerald-500 focus:border-emerald-500 font-bold p-3 text-sm text-gray-700">
                                    <option value="Lecture">{{ __('Théorique (Cours)') }}</option>
                                    <option value="TP">{{ __('Pratique (TP)') }}</option>
                                    <option value="TD">{{ __('Dirigé (TD)') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-gray-50/80 border-b border-gray-100">
                                    <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ __('Étudiant') }}</th>
                                    <th class="px-8 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ __('Statut') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($session->group->students as $student)
                                <tr class="hover:bg-gray-50/30 transition-colors duration-200">
                                    <td class="px-8 py-4">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 rounded-xl bg-upf-blue/10 text-upf-blue flex items-center justify-center font-black text-sm mr-4 border border-blue-100">
                                                {{ substr($student->user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-extrabold text-gray-900 leading-tight">{{ $student->user->name }}</p>
                                                @if($student->absence_score >= 120)
                                                    <span class="inline-block bg-rose-50 text-rose-600 px-2 py-0.5 rounded-lg text-[10px] font-black uppercase mt-1 border border-rose-100">{{ __('Score') }} : {{ $student->absence_score }}h ({{ __('Alerte') }})</span>
                                                @else
                                                    <span class="inline-block bg-gray-100 text-gray-600 px-2 py-0.5 rounded-lg text-[10px] font-black uppercase mt-1">{{ __('Score') }} : {{ $student->absence_score ?? 0 }}h</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-4">
                                        <div class="flex justify-center items-center space-x-6">
                                            <label class="group relative flex items-center cursor-pointer">
                                                <input type="radio" name="absences[{{ $student->id }}]" value="1" checked class="sr-only peer">
                                                <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-gray-50 text-gray-400 peer-checked:bg-emerald-500 peer-checked:text-white transition-all shadow-sm border border-gray-200 peer-checked:border-emerald-500">
                                                    <svg class="w-5 h-5 font-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                </div>
                                                <span class="ml-3 text-[10px] font-black uppercase text-gray-400 peer-checked:text-emerald-600">{{ __('Présent') }}</span>
                                            </label>
                                            <label class="group relative flex items-center cursor-pointer">
                                                <input type="radio" name="absences[{{ $student->id }}]" value="0" class="sr-only peer">
                                                <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-gray-50 text-gray-400 peer-checked:bg-rose-500 peer-checked:text-white transition-all shadow-sm border border-gray-200 peer-checked:border-rose-500">
                                                    <svg class="w-5 h-5 font-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </div>
                                                <span class="ml-3 text-[10px] font-black uppercase text-gray-400 peer-checked:text-rose-600">{{ __('Absent') }}</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="p-6 border-t border-gray-100 bg-gray-50/50 flex justify-end">
                        <button type="submit" class="px-8 py-3 bg-emerald-600 text-white rounded-xl font-black shadow-lg hover:bg-emerald-700 hover:scale-105 transform transition-all duration-300">
                            {{ __('Enregistrer la présence') }}
                        </button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
