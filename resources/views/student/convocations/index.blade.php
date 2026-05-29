<x-app-layout>
    <x-slot name="header">
        <x-page-header 
            title="{{ __('Mes Convocations d\'Examen') }}" 
            subtitle="{{ __('Espace Étudiant') }}"
            icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>'
        >
        </x-page-header>
    </x-slot>

    <div class="py-6 bg-[#F8FAFC]">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-alert-messages />

            {{-- Stats Banner --}}
            <div class="bg-gradient-to-br from-upf-blue via-upf-navy to-black rounded-[2.5rem] p-10 text-white shadow-sm relative overflow-hidden">
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div>
                        <p class="text-[11px] font-black uppercase tracking-[0.3em] text-upf-magenta mb-2">{{ __('Espace Étudiant') }}</p>
                        <h2 class="text-3xl font-black tracking-tighter">{{ __('Mes Convocations') }}</h2>
                        <p class="text-blue-300 text-xs mt-2 opacity-80">{{ __('Téléchargez chaque convocation en PDF ou attendez l\'email de notification.') }}</p>
                    </div>
                    <div class="flex gap-4">
                        <div class="bg-white/15 backdrop-blur border border-white/20 px-6 py-4 rounded-2xl text-center shadow-inner">
                            <p class="text-2xl font-black">{{ $upcoming->count() }}</p>
                            <p class="text-[9px] uppercase font-black text-blue-200 tracking-widest">{{ __('À venir') }}</p>
                        </div>
                        <div class="bg-white/15 backdrop-blur border border-white/20 px-6 py-4 rounded-2xl text-center shadow-inner">
                            <p class="text-2xl font-black">{{ $past->count() }}</p>
                            <p class="text-[9px] uppercase font-black text-blue-200 tracking-widest">{{ __('Passées') }}</p>
                        </div>
                    </div>
                </div>
                <div class="absolute -bottom-16 -right-16 w-56 h-56 bg-upf-magenta/10 rounded-full blur-3xl pointer-events-none"></div>
            </div>

            {{-- UPCOMING CONVOCATIONS --}}
            <x-card class="p-0">
                <div class="px-8 py-5 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-xl bg-upf-blue text-white flex items-center justify-center text-sm shadow-inner">📅</span>
                    <h3 class="font-black text-gray-900">{{ __('Convocations à venir') }}</h3>
                    <span class="bg-upf-blue text-white text-[10px] font-black px-2 py-0.5 rounded-full shadow-sm">{{ $upcoming->count() }}</span>
                </div>

                @forelse($upcoming as $conv)
                    @php
                        $exam = $conv->exam;
                        $daysUntil = now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($exam->date)->startOfDay(), false);
                        $urgencyClass = $daysUntil <= 3 ? 'border-red-200 bg-red-50/20' : ($daysUntil <= 7 ? 'border-amber-200 bg-amber-50/20' : 'border-gray-50');
                    @endphp
                    <div class="flex flex-col md:flex-row md:items-center gap-6 px-8 py-6 border-b {{ $urgencyClass }} hover:bg-gray-50/50 transition-colors">
                        
                        {{-- Date Block --}}
                        <div class="text-center min-w-[64px] flex-shrink-0">
                            <div class="w-14 h-14 rounded-2xl {{ $daysUntil <= 3 ? 'bg-red-500' : ($daysUntil <= 7 ? 'bg-amber-500' : 'bg-upf-blue') }} text-white flex flex-col items-center justify-center shadow-md">
                                <span class="text-[9px] uppercase font-black opacity-80">{{ \Carbon\Carbon::parse($exam->date)->isoFormat('MMM') }}</span>
                                <span class="text-xl font-black leading-none">{{ \Carbon\Carbon::parse($exam->date)->format('d') }}</span>
                            </div>
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                <h4 class="font-black text-gray-900 truncate">{{ $exam->module->name }}</h4>
                                <span class="bg-upf-blue/10 text-upf-blue text-[10px] font-black px-2 py-0.5 rounded-full uppercase tracking-widest">{{ $exam->type }}</span>
                                @if($conv->status === 'sent')
                                    <span class="bg-emerald-100 text-emerald-700 border border-emerald-200 text-[10px] font-black px-2 py-0.5 rounded-full">✉️ {{ __('Email envoyé') }}</span>
                                @elseif($conv->status === 'downloaded')
                                    <span class="bg-purple-100 text-purple-700 border border-purple-200 text-[10px] font-black px-2 py-0.5 rounded-full">✅ {{ __('Téléchargé') }}</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-4 text-xs text-gray-500 font-bold flex-wrap">
                                <span>🕐 {{ date('H:i', strtotime($exam->start_time)) }} → {{ $exam->end_time }}</span>
                                <span>⏱ {{ $exam->duration }} {{ __('min') }}</span>
                                <span>📍 {{ $exam->room->name ?? __('Salle à confirmer') }}</span>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-2 font-bold uppercase tracking-widest">{{ __('Réf') }}: {{ $conv->reference }}</p>
                        </div>

                        {{-- Countdown --}}
                        <div class="text-center min-w-[80px] flex-shrink-0">
                            @if($daysUntil === 0)
                                <span class="text-red-600 font-black text-sm">{{ __('Aujourd\'hui !') }}</span>
                            @elseif($daysUntil === 1)
                                <span class="text-red-500 font-black text-sm">{{ __('Demain') }}</span>
                            @else
                                <span class="text-gray-600 font-black text-lg">{{ $daysUntil }}</span>
                                <p class="text-[9px] text-gray-400 font-black uppercase">{{ __('jours') }}</p>
                            @endif
                        </div>

                        {{-- Download Button --}}
                        <div class="flex-shrink-0">
                            <a href="{{ route('student.convocations.download', $conv) }}"
                               class="flex items-center justify-center gap-2 bg-gray-900 hover:bg-black text-white font-black py-2.5 px-5 rounded-2xl shadow-sm transition-all hover:scale-105 text-xs uppercase tracking-widest whitespace-nowrap">
                                📄 {{ __('PDF') }}
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4 shadow-inner">
                            <span class="text-2xl">🎉</span>
                        </div>
                        <p class="font-black text-gray-400">{{ __('Aucun examen à venir pour le moment.') }}</p>
                    </div>
                @endforelse
            </x-card>

            {{-- PAST CONVOCATIONS --}}
            @if($past->isNotEmpty())
            <x-card class="p-0 opacity-80">
                <div class="px-8 py-5 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-xl bg-gray-400 text-white flex items-center justify-center text-sm shadow-inner">📁</span>
                    <h3 class="font-black text-gray-500">{{ __('Examens passés') }}</h3>
                    <span class="bg-gray-200 text-gray-600 text-[10px] font-black px-2 py-0.5 rounded-full shadow-sm">{{ $past->count() }}</span>
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
                                <span class="bg-gray-100 border border-gray-200 text-gray-500 text-[10px] font-black px-2 py-0.5 rounded-full uppercase">{{ $exam->type }}</span>
                            </div>
                            <div class="flex items-center gap-4 text-xs text-gray-400 font-bold">
                                <span>📍 {{ $exam->room->name ?? '—' }}</span>
                                <span>{{ __('Réf') }}: {{ $conv->reference }}</span>
                            </div>
                        </div>
                        <a href="{{ route('student.convocations.download', $conv) }}"
                           class="flex items-center justify-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-black py-2 px-4 rounded-xl transition-colors shadow-sm text-xs whitespace-nowrap">
                            📄 {{ __('Archiver') }}
                        </a>
                    </div>
                @endforeach
            </x-card>
            @endif

        </div>
    </div>
</x-app-layout>
