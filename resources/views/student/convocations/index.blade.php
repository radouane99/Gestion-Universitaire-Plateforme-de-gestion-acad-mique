<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tight italic">
            📋 Mes Convocations d'Examen
        </h2>
    </x-slot>

    <div class="py-10 bg-[#F8FAFC]">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <x-alert-messages />

            {{-- Stats Banner --}}
            <div class="bg-gradient-to-br from-upf-blue via-upf-navy to-black rounded-[2.5rem] p-10 text-white shadow-2xl relative overflow-hidden">
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div>
                        <p class="text-[11px] font-black uppercase tracking-[0.3em] text-upf-magenta mb-2">Espace Étudiant</p>
                        <h2 class="text-3xl font-black tracking-tighter">📋 Mes Convocations</h2>
                        <p class="text-blue-300 text-xs mt-2 opacity-80">Téléchargez chaque convocation en PDF ou attendez l'email de notification.</p>
                    </div>
                    <div class="flex gap-4">
                        <div class="bg-white/15 backdrop-blur border border-white/20 px-6 py-4 rounded-2xl text-center">
                            <p class="text-2xl font-black">{{ $upcoming->count() }}</p>
                            <p class="text-[9px] uppercase font-black text-blue-200 tracking-widest">À venir</p>
                        </div>
                        <div class="bg-white/15 backdrop-blur border border-white/20 px-6 py-4 rounded-2xl text-center">
                            <p class="text-2xl font-black">{{ $past->count() }}</p>
                            <p class="text-[9px] uppercase font-black text-blue-200 tracking-widest">Passées</p>
                        </div>
                    </div>
                </div>
                <div class="absolute -bottom-16 -right-16 w-56 h-56 bg-upf-magenta/10 rounded-full blur-3xl pointer-events-none"></div>
            </div>

            {{-- UPCOMING CONVOCATIONS --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 py-5 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-xl bg-upf-blue text-white flex items-center justify-center text-sm">📅</span>
                    <h3 class="font-black text-gray-900">Convocations à venir</h3>
                    <span class="bg-upf-blue text-white text-[10px] font-black px-2 py-0.5 rounded-full">{{ $upcoming->count() }}</span>
                </div>

                @forelse($upcoming as $conv)
                    @php
                        $exam = $conv->exam;
                        $daysUntil = now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($exam->date)->startOfDay(), false);
                        $urgencyClass = $daysUntil <= 3 ? 'border-red-200 bg-red-50/20' : ($daysUntil <= 7 ? 'border-amber-200 bg-amber-50/20' : 'border-gray-100');
                    @endphp
                    <div class="flex items-center gap-6 px-8 py-5 border-b {{ $urgencyClass }} hover:bg-gray-50/50 transition-colors">
                        
                        {{-- Date Block --}}
                        <div class="text-center min-w-[64px]">
                            <div class="w-14 h-14 rounded-2xl {{ $daysUntil <= 3 ? 'bg-red-500' : ($daysUntil <= 7 ? 'bg-amber-500' : 'bg-upf-blue') }} text-white flex flex-col items-center justify-center shadow-lg">
                                <span class="text-[9px] uppercase font-black opacity-80">{{ \Carbon\Carbon::parse($exam->date)->isoFormat('MMM') }}</span>
                                <span class="text-xl font-black leading-none">{{ \Carbon\Carbon::parse($exam->date)->format('d') }}</span>
                            </div>
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <h4 class="font-black text-gray-900 truncate">{{ $exam->module->name }}</h4>
                                <span class="bg-upf-blue/10 text-upf-blue text-[10px] font-black px-2 py-0.5 rounded-full uppercase tracking-widest">{{ $exam->type }}</span>
                                @if($conv->status === 'sent')
                                    <span class="bg-emerald-100 text-emerald-700 text-[10px] font-black px-2 py-0.5 rounded-full">✉️ Email envoyé</span>
                                @elseif($conv->status === 'downloaded')
                                    <span class="bg-purple-100 text-purple-700 text-[10px] font-black px-2 py-0.5 rounded-full">✅ Téléchargé</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-4 text-xs text-gray-500 font-bold">
                                <span>🕐 {{ date('H:i', strtotime($exam->start_time)) }} → {{ $exam->end_time }}</span>
                                <span>⏱ {{ $exam->duration }} min</span>
                                <span>📍 {{ $exam->room->name ?? 'Salle à confirmer' }}</span>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-1 font-bold uppercase tracking-widest">Réf: {{ $conv->reference }}</p>
                        </div>

                        {{-- Countdown --}}
                        <div class="text-center min-w-[80px]">
                            @if($daysUntil === 0)
                                <span class="text-red-600 font-black text-sm">Aujourd'hui !</span>
                            @elseif($daysUntil === 1)
                                <span class="text-red-500 font-black text-sm">Demain</span>
                            @else
                                <span class="text-gray-600 font-black text-lg">{{ $daysUntil }}</span>
                                <p class="text-[9px] text-gray-400 font-black uppercase">jours</p>
                            @endif
                        </div>

                        {{-- Download Button --}}
                        <a href="{{ route('student.convocations.download', $conv) }}"
                           class="flex items-center gap-2 bg-gray-900 hover:bg-black text-white font-black py-2.5 px-5 rounded-2xl shadow-md transition-all hover:scale-105 text-xs uppercase tracking-widest whitespace-nowrap">
                            📄 PDF
                        </a>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <div class="text-5xl mb-4">🎉</div>
                        <p class="font-black text-gray-400">Aucun examen à venir pour le moment.</p>
                    </div>
                @endforelse
            </div>

            {{-- PAST CONVOCATIONS --}}
            @if($past->isNotEmpty())
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden opacity-80">
                <div class="px-8 py-5 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-xl bg-gray-400 text-white flex items-center justify-center text-sm">📁</span>
                    <h3 class="font-black text-gray-500">Examens passés</h3>
                    <span class="bg-gray-200 text-gray-600 text-[10px] font-black px-2 py-0.5 rounded-full">{{ $past->count() }}</span>
                </div>

                @foreach($past as $conv)
                    @php $exam = $conv->exam; @endphp
                    <div class="flex items-center gap-6 px-8 py-4 border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                        <div class="text-center min-w-[64px]">
                            <div class="w-14 h-14 rounded-2xl bg-gray-300 text-white flex flex-col items-center justify-center">
                                <span class="text-[9px] uppercase font-black opacity-80">{{ \Carbon\Carbon::parse($exam->date)->isoFormat('MMM') }}</span>
                                <span class="text-xl font-black leading-none">{{ \Carbon\Carbon::parse($exam->date)->format('d') }}</span>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <h4 class="font-black text-gray-600 truncate">{{ $exam->module->name }}</h4>
                                <span class="bg-gray-100 text-gray-500 text-[10px] font-black px-2 py-0.5 rounded-full uppercase">{{ $exam->type }}</span>
                            </div>
                            <div class="flex items-center gap-4 text-xs text-gray-400 font-bold">
                                <span>📍 {{ $exam->room->name ?? '—' }}</span>
                                <span>Réf: {{ $conv->reference }}</span>
                            </div>
                        </div>
                        <a href="{{ route('student.convocations.download', $conv) }}"
                           class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-black py-2 px-4 rounded-xl transition-colors text-xs">
                            📄 Archiver
                        </a>
                    </div>
                @endforeach
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
