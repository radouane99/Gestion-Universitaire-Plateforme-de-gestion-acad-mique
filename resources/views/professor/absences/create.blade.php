<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mark Attendance for') }} {{ $session->group->name }} - {{ $session->module->name }}
        </h2>
    </x-slot>

    <div class="py-12 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <div class="bg-gradient-to-r from-upf-magenta to-[#8A0A4A] rounded-3xl p-10 text-white shadow-2xl relative overflow-hidden mb-8">
                <div class="relative z-10">
                    <h2 class="text-3xl font-black mb-2 italic">Class Attendance</h2>
                    <p class="text-blue-100 opacity-80">{{ $session->module->name }} &mdash; Group {{ $session->group->name }}</p>
                </div>
                <!-- Abstract branding element -->
                <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
            </div>

            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                <form action="{{ route('professor.absences.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="schedule_id" value="{{ $session->id }}">
                    
                    <div class="p-10 border-b border-gray-100 bg-gray-50/50">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <x-input-label for="date" :value="__('Session Date')" class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2" />
                                <x-text-input type="date" name="date" class="block w-full border-gray-200 rounded-2xl bg-white shadow-sm focus:ring-upf-magenta focus:border-upf-magenta font-bold p-4" value="{{ date('Y-m-d') }}" required />
                            </div>
                            <div>
                                <x-input-label for="session_type" :value="__('Pedagogical Format')" class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2" />
                                <select name="session_type" class="block w-full border-gray-200 rounded-2xl bg-white shadow-sm focus:ring-upf-magenta focus:border-upf-magenta font-black p-4">
                                    <option value="Lecture">Théorique (Lecture)</option>
                                    <option value="TP">Pratique (TP)</option>
                                    <option value="TD">Dirigé (TD)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-gray-50/80">
                                    <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Student Credentials</th>
                                    <th class="px-8 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Attendance Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($session->group->students as $student)
                                <tr class="hover:bg-rose-50/30 transition-colors duration-200">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 rounded-full bg-upf-magenta/10 text-upf-magenta flex items-center justify-center font-black text-sm mr-3">
                                                {{ substr($student->user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-extrabold text-gray-900">{{ $student->user->name }}</p>
                                                @if($student->absence_score >= 120)
                                                    <span class="inline-block bg-red-100 text-red-700 px-2 py-0.5 rounded-md text-[10px] font-black uppercase mt-1 border border-red-200">Score: {{ $student->absence_score }}h (Alerte)</span>
                                                @else
                                                    <span class="inline-block bg-gray-100 text-gray-600 px-2 py-0.5 rounded-md text-[10px] font-black uppercase mt-1">Score: {{ $student->absence_score ?? 0 }}h</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex justify-center items-center space-x-6">
                                            <label class="group relative flex items-center cursor-pointer">
                                                <input type="radio" name="absences[{{ $student->id }}]" value="1" checked class="sr-only peer">
                                                <div class="w-12 h-12 flex items-center justify-center rounded-2xl bg-gray-100 text-gray-400 peer-checked:bg-emerald-500 peer-checked:text-white transition-all shadow-sm">
                                                    <svg class="w-6 h-6 font-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                </div>
                                                <span class="ml-3 text-[10px] font-black uppercase text-gray-400 peer-checked:text-emerald-600">Present</span>
                                            </label>
                                            <label class="group relative flex items-center cursor-pointer">
                                                <input type="radio" name="absences[{{ $student->id }}]" value="0" class="sr-only peer">
                                                <div class="w-12 h-12 flex items-center justify-center rounded-2xl bg-gray-100 text-gray-400 peer-checked:bg-rose-500 peer-checked:text-white transition-all shadow-sm">
                                                    <svg class="w-6 h-6 font-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </div>
                                                <span class="ml-3 text-[10px] font-black uppercase text-gray-400 peer-checked:text-rose-600">Absent</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="p-8 border-t border-gray-100 bg-gray-50/30 flex justify-end">
                        <button type="submit" class="px-10 py-4 bg-upf-magenta text-white rounded-2xl font-black shadow-xl hover:bg-[#8A0A4A] hover:scale-105 transform transition-all duration-300">
                            Commit Attendance Log
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
