<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tight italic">
                {{ __('Édition des Notes') }}
            </h2>
            <a href="{{ route('admin.grades.index') }}" class="text-sm font-bold text-gray-500 hover:text-upf-magenta transition-colors">
                ← Retour au sélecteur
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Context Banner -->
            <div class="bg-gradient-to-br from-upf-navy to-black rounded-[2rem] p-8 text-white shadow-xl relative overflow-hidden flex flex-col md:flex-row items-center justify-between">
                <div class="relative z-10">
                    <p class="text-[10px] uppercase font-black tracking-[0.2em] text-upf-magenta mb-2">{{ __('Groupe Académique') }}</p>
                    <h2 class="text-3xl font-black mb-1">{{ $group->name }}</h2>
                    <p class="text-blue-200 font-medium">{{ $module->name }} ({{ $module->code }})</p>
                </div>
                <div class="relative z-10 mt-6 md:mt-0 text-center bg-white/10 backdrop-blur-md px-8 py-4 rounded-2xl border border-white/20">
                    <p class="text-4xl font-black">{{ $group->students->count() }}</p>
                    <p class="text-[10px] uppercase font-bold tracking-widest text-blue-200 mt-1">Étudiants Inscrits</p>
                </div>
                <div class="absolute top-0 right-0 w-64 h-64 bg-upf-magenta/20 rounded-full blur-3xl transform translate-x-1/2 -translate-y-1/2"></div>
            </div>

            <!-- Grades Form -->
            <form method="POST" action="{{ route('admin.grades.store') }}" class="bg-white dark:bg-slate-900 overflow-hidden shadow-xl sm:rounded-[2rem] border border-gray-100 dark:border-slate-800">
                @csrf
                <input type="hidden" name="group_id" value="{{ $group->id }}">
                <input type="hidden" name="module_id" value="{{ $module->id }}">

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-800">
                        <thead class="bg-gray-50/50 dark:bg-slate-800/50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">
                                    Étudiant
                                </th>
                                <th scope="col" class="px-6 py-4 text-center text-[10px] font-black text-upf-blue dark:text-blue-400 uppercase tracking-widest">
                                    CC 1 (20%)
                                </th>
                                <th scope="col" class="px-6 py-4 text-center text-[10px] font-black text-upf-blue dark:text-blue-400 uppercase tracking-widest">
                                    CC 2 (20%)
                                </th>
                                <th scope="col" class="px-6 py-4 text-center text-[10px] font-black text-upf-magenta uppercase tracking-widest">
                                    Examen (60%)
                                </th>
                                <th scope="col" class="px-6 py-4 text-center text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">
                                    Note Finale Actuelle
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-slate-900 divide-y divide-gray-100 dark:divide-slate-800">
                            @foreach($group->students as $student)
                                @php
                                    $grade = $grades->get($student->id);
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 rounded-xl bg-indigo-50 dark:bg-slate-800 flex items-center justify-center text-upf-blue dark:text-blue-400 font-black text-xs">
                                                {{ substr($student->user->name, 0, 2) }}
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $student->user->name }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400 font-mono">{{ $student->student_number }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <input type="hidden" name="grades[{{ $student->id }}][student_id]" value="{{ $student->id }}">
                                        <input type="number" step="0.25" min="0" max="20" 
                                               name="grades[{{ $student->id }}][cc1]" 
                                               value="{{ old('grades.'.$student->id.'.cc1', $grade->cc1 ?? '') }}"
                                               class="w-24 text-center rounded-xl border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white shadow-sm focus:border-upf-blue focus:ring focus:ring-upf-blue focus:ring-opacity-50 transition-all font-mono text-sm"
                                               placeholder="--">
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <input type="number" step="0.25" min="0" max="20" 
                                               name="grades[{{ $student->id }}][cc2]" 
                                               value="{{ old('grades.'.$student->id.'.cc2', $grade->cc2 ?? '') }}"
                                               class="w-24 text-center rounded-xl border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white shadow-sm focus:border-upf-blue focus:ring focus:ring-upf-blue focus:ring-opacity-50 transition-all font-mono text-sm"
                                               placeholder="--">
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <input type="number" step="0.25" min="0" max="20" 
                                               name="grades[{{ $student->id }}][exam]" 
                                               value="{{ old('grades.'.$student->id.'.exam', $grade->exam ?? '') }}"
                                               class="w-24 text-center rounded-xl border-upf-magenta/30 dark:border-pink-900/50 dark:bg-slate-800 dark:text-white shadow-sm focus:border-upf-magenta focus:ring focus:ring-upf-magenta focus:ring-opacity-50 transition-all font-mono text-sm bg-pink-50/30"
                                               placeholder="--">
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($grade && $grade->final_grade !== null)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-black {{ $grade->final_grade >= 10 ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400' }}">
                                                {{ number_format($grade->final_grade, 2) }}
                                            </span>
                                        @else
                                            <span class="text-gray-400 italic text-sm">--</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-6 bg-gray-50/50 dark:bg-slate-800/50 border-t border-gray-100 dark:border-slate-800 flex items-center justify-between">
                    <p class="text-xs text-gray-500 font-medium">
                        <svg class="w-4 h-4 inline-block mr-1 text-upf-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Le calcul de la note finale est automatique : (CC1+CC2)/2 × 40% + Examen × 60%
                    </p>
                    <button type="submit" class="inline-flex items-center px-8 py-3 bg-upf-blue border border-transparent rounded-xl font-black text-xs text-white uppercase tracking-widest hover:bg-upf-navy active:bg-upf-navy focus:outline-none focus:ring-2 focus:ring-upf-magenta focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg hover:-translate-y-0.5">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                        Enregistrer les Notes
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
