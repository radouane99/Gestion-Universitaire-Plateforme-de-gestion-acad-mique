<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex justify-between items-center">
            <span>{{ __('My Academic Grades') }}</span>
        </h2>
    </x-slot>

    <div class="py-12 bg-[#F8FAFC]" x-data="gradeSimulator()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Dashboard Card -->
            <div class="bg-gradient-to-br from-emerald-600 to-teal-800 rounded-3xl p-10 text-white shadow-2xl relative overflow-hidden flex flex-col md:flex-row justify-between items-center gap-6 transition-all duration-500"
                 :class="{'from-indigo-600 to-purple-800': isSimulation}">
                <div class="relative z-10">
                    <h2 class="text-3xl font-black mb-2 italic" x-text="isSimulation ? 'Mode Simulation' : 'Academic Performance'">Academic Performance</h2>
                    <p class="text-emerald-100 opacity-80" :class="{'text-indigo-100': isSimulation}" x-text="isSimulation ? 'Saisissez des notes fictives pour estimer votre moyenne globale.' : 'Track your verified marks and final results by semester.'">Track your verified marks and final results by semester.</p>
                    
                    <button @click="toggleSimulation" class="mt-4 px-6 py-2 rounded-full font-black text-sm uppercase tracking-widest transition-all shadow-lg"
                            :class="isSimulation ? 'bg-white text-indigo-700 hover:bg-gray-100' : 'bg-white text-emerald-700 hover:bg-gray-100'">
                        <span x-text="isSimulation ? 'Quitter la Simulation' : 'Activer le Simulateur'">Activer le Simulateur</span>
                    </button>
                </div>
                <div class="relative z-10 text-right">
                    <div class="text-sm font-bold text-emerald-200 uppercase tracking-widest" :class="{'text-indigo-200': isSimulation}">Moyenne Annuelle <span x-show="isSimulation">(Estimée)</span></div>
                    <div class="text-5xl font-black" :class="parseFloat(yearlyGpa).toFixed(2) >= 10 ? 'text-white' : 'text-rose-300'">
                        <span x-text="parseFloat(yearlyGpa).toFixed(2)"></span> <span class="text-xl text-opacity-80">/ 20</span>
                    </div>
                    
                    <template x-if="parseFloat(yearlyGpa) >= 10">
                        <div class="mt-2 inline-block px-4 py-1 bg-white text-emerald-800 rounded-full text-xs font-black uppercase tracking-widest" :class="{'text-indigo-800': isSimulation}">Année Validée ✅</div>
                    </template>
                    <template x-if="parseFloat(yearlyGpa) < 10 && parseFloat(yearlyGpa) > 0">
                        <div class="mt-2 inline-block px-4 py-1 bg-rose-500 text-white rounded-full text-xs font-black uppercase tracking-widest">Ajourné(e) ⚠️</div>
                    </template>
                </div>
                <div class="absolute -top-24 -right-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            </div>

            <!-- Semesters List -->
            @forelse($gradesBySemester as $semesterName => $grades)
            @php
                $semIndex = $loop->index;
            @endphp
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden mb-8 semester-block" data-semester-idx="{{ $semIndex }}">
                <div class="bg-gray-50/80 px-8 py-5 border-b border-gray-100 flex justify-between items-center transition-colors duration-500" :class="{'bg-indigo-50/50': isSimulation}">
                    <h3 class="text-xl font-black text-upf-blue">{{ $semesterName }}</h3>
                    <div class="text-sm font-bold text-gray-500">
                        Moyenne Semestre : 
                        <span class="text-lg font-black" :class="parseFloat(semGpas[{{ $semIndex }}]).toFixed(2) >= 10 ? 'text-emerald-500' : 'text-rose-500'" x-text="parseFloat(semGpas[{{ $semIndex }}]).toFixed(2)"></span>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-white border-b border-gray-50">
                                <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Module</th>
                                <th class="px-8 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">CC1 (40%)</th>
                                <th class="px-8 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">CC2 (40%)</th>
                                <th class="px-8 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Final Exam</th>
                                <th class="px-8 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Weighted Final</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($grades as $grade)
                            @php
                                $modIdx = $grade->id;
                            @endphp
                            <tr class="hover:bg-emerald-50/30 transition-colors duration-200 module-row" :class="{'hover:bg-indigo-50/30': isSimulation}" data-mod-idx="{{ $modIdx }}" data-sem-idx="{{ $semIndex }}">
                                <td class="px-8 py-6">
                                    <p class="font-extrabold text-gray-900 leading-none">{{ $grade->module->name }}</p>
                                    <p class="text-[10px] text-gray-400 mt-1 uppercase font-bold tracking-tighter">{{ $grade->module->code ?? 'UPF-MOD' }}</p>
                                </td>
                                
                                <td class="px-8 py-6 text-center">
                                    <div x-show="!isSimulation || moduleData[{{ $modIdx }}].real_cc1 !== null">
                                        <span class="font-bold text-gray-600" x-text="moduleData[{{ $modIdx }}].cc1 !== null ? moduleData[{{ $modIdx }}].cc1 : '--'"></span>
                                    </div>
                                    <div x-show="isSimulation && moduleData[{{ $modIdx }}].real_cc1 === null">
                                        <input type="number" min="0" max="20" step="0.25" x-model.number="moduleData[{{ $modIdx }}].sim_cc1" @input="recalculate()" class="w-16 px-2 py-1 text-xs border border-indigo-200 rounded focus:ring-indigo-500 focus:border-indigo-500 bg-indigo-50 font-bold text-indigo-700 text-center shadow-inner">
                                    </div>
                                </td>

                                <td class="px-8 py-6 text-center">
                                    <div x-show="!isSimulation || moduleData[{{ $modIdx }}].real_cc2 !== null">
                                        <span class="font-bold text-gray-600" x-text="moduleData[{{ $modIdx }}].cc2 !== null ? moduleData[{{ $modIdx }}].cc2 : '--'"></span>
                                    </div>
                                    <div x-show="isSimulation && moduleData[{{ $modIdx }}].real_cc2 === null">
                                        <input type="number" min="0" max="20" step="0.25" x-model.number="moduleData[{{ $modIdx }}].sim_cc2" @input="recalculate()" class="w-16 px-2 py-1 text-xs border border-indigo-200 rounded focus:ring-indigo-500 focus:border-indigo-500 bg-indigo-50 font-bold text-indigo-700 text-center shadow-inner">
                                    </div>
                                </td>

                                <td class="px-8 py-6 text-center">
                                    <div x-show="!isSimulation || moduleData[{{ $modIdx }}].real_exam !== null">
                                        <span class="font-black text-upf-blue" x-text="moduleData[{{ $modIdx }}].exam !== null ? moduleData[{{ $modIdx }}].exam : '--'"></span>
                                    </div>
                                    <div x-show="isSimulation && moduleData[{{ $modIdx }}].real_exam === null">
                                        <input type="number" min="0" max="20" step="0.25" x-model.number="moduleData[{{ $modIdx }}].sim_exam" @input="recalculate()" class="w-16 px-2 py-1 text-xs border border-indigo-300 rounded focus:ring-indigo-500 focus:border-indigo-500 bg-indigo-100 font-black text-indigo-900 text-center shadow-inner">
                                    </div>
                                </td>

                                <td class="px-8 py-6 text-right">
                                    <div class="inline-flex flex-col items-end">
                                        <span class="px-4 py-1 rounded-full font-black text-sm transition-colors duration-300"
                                              :class="{
                                                'bg-emerald-100 text-emerald-700': getFinalGrade({{ $modIdx }}) >= 10,
                                                'bg-rose-100 text-rose-700': getFinalGrade({{ $modIdx }}) < 10 && getFinalGrade({{ $modIdx }}) !== null,
                                                'bg-gray-100 text-gray-500': getFinalGrade({{ $modIdx }}) === null
                                              }">
                                            <span x-text="getFinalGrade({{ $modIdx }}) !== null ? parseFloat(getFinalGrade({{ $modIdx }})).toFixed(2) : 'PENDING'"></span>
                                        </span>
                                        
                                        <template x-if="getFinalGrade({{ $modIdx }}) >= 10">
                                            <span class="text-[8px] font-black text-emerald-500 uppercase mt-1">Validated ✅</span>
                                        </template>
                                        <template x-if="getFinalGrade({{ $modIdx }}) < 10 && getFinalGrade({{ $modIdx }}) !== null">
                                            <span class="text-[8px] font-black text-rose-500 uppercase mt-1">Rattrapage ⚠️</span>
                                        </template>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="p-24 text-center">
                    <div class="text-4xl mb-4">📑</div>
                    <p class="text-gray-400 italic font-bold">Aucune note ou module n'a été enregistré pour cette année.</p>
                </div>
            </div>
            @endforelse

        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('gradeSimulator', () => ({
                isSimulation: false,
                yearlyGpa: {{ $yearlyGPA ?? 0 }},
                semGpas: {},
                moduleData: {},
                semesters: [], // array of objects mapping semIdx to array of modIdxs

                init() {
                    // Populate initial data from PHP to JS
                    @php
                        $semIndex = 0;
                    @endphp
                    @foreach($gradesBySemester as $semesterName => $grades)
                        this.semGpas[{{ $semIndex }}] = 0;
                        let modsInSem = [];
                        @foreach($grades as $grade)
                            modsInSem.push({{ $grade->id }});
                            this.moduleData[{{ $grade->id }}] = {
                                semIdx: {{ $semIndex }},
                                real_cc1: {{ $grade->cc1 !== null ? $grade->cc1 : 'null' }},
                                real_cc2: {{ $grade->cc2 !== null ? $grade->cc2 : 'null' }},
                                real_exam: {{ $grade->exam !== null ? $grade->exam : 'null' }},
                                real_final: {{ $grade->final_grade !== null ? $grade->final_grade : 'null' }},
                                
                                cc1: {{ $grade->cc1 !== null ? $grade->cc1 : 'null' }},
                                cc2: {{ $grade->cc2 !== null ? $grade->cc2 : 'null' }},
                                exam: {{ $grade->exam !== null ? $grade->exam : 'null' }},
                                
                                sim_cc1: '',
                                sim_cc2: '',
                                sim_exam: '',
                            };
                        @endforeach
                        this.semesters[{{ $semIndex }}] = modsInSem;
                        @php $semIndex++; @endphp
                    @endforeach
                    
                    this.recalculate();
                },

                toggleSimulation() {
                    this.isSimulation = !this.isSimulation;
                    this.recalculate();
                },

                getFinalGrade(modIdx) {
                    let m = this.moduleData[modIdx];
                    if (!this.isSimulation) {
                        return m.real_final;
                    }

                    // Calculate simulated grade
                    let cc1 = m.real_cc1 !== null ? m.real_cc1 : (m.sim_cc1 !== '' ? parseFloat(m.sim_cc1) : null);
                    let cc2 = m.real_cc2 !== null ? m.real_cc2 : (m.sim_cc2 !== '' ? parseFloat(m.sim_cc2) : null);
                    let exam = m.real_exam !== null ? m.real_exam : (m.sim_exam !== '' ? parseFloat(m.sim_exam) : null);

                    // Note: calculation logic might differ per university. 
                    // Usually: (CC1 + CC2) / 2 = CC. Then (CC * 0.4 + Exam * 0.6) or similar.
                    // If CC1 and CC2 aren't both present, maybe just use Exam. 
                    // To keep it simple: Let's assume average of available marks.
                    let sum = 0;
                    let count = 0;
                    if (cc1 !== null) { sum += cc1; count++; }
                    if (cc2 !== null) { sum += cc2; count++; }
                    if (exam !== null) { sum += exam; count++; }

                    if (count === 0) return null;
                    return sum / count;
                },

                recalculate() {
                    let totalGPA = 0;
                    let validSemestersCount = 0;

                    for (let semIdx = 0; semIdx < this.semesters.length; semIdx++) {
                        let semTotal = 0;
                        let validModsCount = 0;

                        this.semesters[semIdx].forEach(modIdx => {
                            let final = this.getFinalGrade(modIdx);
                            if (final !== null && !isNaN(final)) {
                                semTotal += parseFloat(final);
                                validModsCount++;
                            }
                        });

                        let semGpa = validModsCount > 0 ? (semTotal / validModsCount) : 0;
                        this.semGpas[semIdx] = semGpa;

                        if (validModsCount > 0) {
                            totalGPA += semGpa;
                            validSemestersCount++;
                        }
                    }

                    this.yearlyGpa = validSemestersCount > 0 ? (totalGPA / validSemestersCount) : 0;
                }
            }));
        });
    </script>
</x-app-layout>
