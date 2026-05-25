<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight text-center">
            {{ __('Venue Reservations') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <div class="bg-gradient-to-br from-amber-500 to-amber-700 rounded-3xl p-10 text-white shadow-2xl relative overflow-hidden">
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-center">
                    <div>
                        <h2 class="text-3xl font-black mb-2 italic">Infrastructure Access</h2>
                        <p class="text-amber-100 opacity-80">Reserved halls and laboratories for specialized academic sessions.</p>
                    </div>
                    <a href="{{ route('professor.reservations.create') }}" class="mt-6 md:mt-0 px-8 py-4 bg-white text-amber-600 font-black rounded-2xl hover:bg-upf-magenta hover:text-white transition-all shadow-xl">
                        New Reservation
                    </a>
                </div>
                <div class="absolute -top-12 -left-12 w-48 h-48 bg-white/10 rounded-full blur-2xl"></div>
            </div>

            @if(session('success'))
                <div class="p-4 text-sm text-emerald-800 rounded-2xl bg-emerald-50 border border-emerald-100 font-bold">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-50/80">
                                <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Reserved Venue</th>
                                <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Timing</th>
                                <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Security Hash</th>
                                <th class="px-8 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Access Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($reservations as $reservation)
                            <tr class="hover:bg-amber-50/30 transition-colors duration-200">
                                <td class="px-8 py-6">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center font-black mr-3 shadow-inner">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        </div>
                                        <div>
                                            <p class="font-extrabold text-gray-900 leading-none">{{ $reservation->room->name }}</p>
                                            <p class="text-[10px] text-gray-400 font-bold mt-1 uppercase tracking-tighter">{{ $reservation->purpose }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 font-bold text-gray-700">
                                    <p class="text-xs font-black text-gray-700 mb-1 italic">{{ date('d M Y', strtotime($reservation->start_time)) }}</p>
                                    <p class="text-[10px] font-bold text-gray-400">{{ date('H:i', strtotime($reservation->start_time)) }} - {{ date('H:i', strtotime($reservation->end_time)) }}</p>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <span class="text-[10px] font-mono font-bold text-gray-400 opacity-50 uppercase">UPF-{{ strtoupper(hexdec(substr($reservation->id, 0, 4))) }}</span>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <span class="px-4 py-1 {{ $reservation->status == 'approved' ? 'bg-emerald-100 text-emerald-700' : ($reservation->status == 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }} rounded-full font-black text-[10px] uppercase">
                                        {{ $reservation->status }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($reservations->isEmpty())
                <div class="p-24 text-center">
                    <div class="text-4xl mb-4">🏛️</div>
                    <p class="text-gray-400 italic">No room reservations found. Scale your impact by reserving a hall.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
