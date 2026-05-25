<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Attendance Tracking') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <div class="bg-gradient-to-r from-upf-magenta to-[#8A0A4A] rounded-3xl p-10 text-white shadow-2xl relative overflow-hidden mb-8">
                <div class="relative z-10">
                    <h2 class="text-3xl font-black mb-2 italic">Attendance Record</h2>
                    <p class="text-blue-100 opacity-80">Track student presence across your scheduled academic sessions.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($taught as $session)
                <div class="group bg-white rounded-3xl shadow-sm border border-gray-100 p-8 transition-all duration-500 hover:shadow-2xl hover:border-upf-magenta transform hover:-translate-y-2 relative overflow-hidden">
                    <div class="w-14 h-14 bg-pink-50 text-upf-magenta rounded-2xl flex items-center justify-center mb-6 group-hover:bg-upf-magenta group-hover:text-white transition-all duration-300">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    
                    <h4 class="text-xl font-black text-gray-900 mb-2 leading-tight uppercase tracking-tight">{{ $session->module->name }}</h4>
                    <div class="space-y-2 mb-6">
                        <div class="flex items-center text-xs font-bold text-gray-500 uppercase">
                            <span class="bg-gray-100 px-3 py-1 rounded-full mr-2">Group: {{ $session->group->name }}</span>
                        </div>
                        <div class="text-xs font-medium text-gray-400 italic">
                            {{ date('H:i', strtotime($session->start_time)) }} - {{ date('H:i', strtotime($session->end_time)) }}
                        </div>
                    </div>

                    <a href="{{ route('professor.absences.create', $session->id) }}" class="inline-flex items-center font-black text-upf-magenta group-hover:text-[#8A0A4A]">
                        Log Presence
                        <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>
                @endforeach
            </div>

            @if($taught->isEmpty())
                <div class="text-center py-24 bg-white rounded-3xl border border-gray-100">
                    <p class="text-gray-400 italic">No assigned sessions available for attendance tracking.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
