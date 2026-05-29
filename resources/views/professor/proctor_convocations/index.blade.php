<x-app-layout>
    <x-slot name="header">
        <x-page-header 
            title="{{ __('Mes Convocations de Surveillance') }}" 
            subtitle="{{ __('Espace Professeur — Surveillance d\'Examens') }}"
            icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v7l-9-5V9l9 5z"></path></svg>'
        >
        </x-page-header>
    </x-slot>

    <div class="py-6 bg-[#F8FAFC]">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-alert-messages />

            {{-- Stats Banner --}}
            <div class="bg-gradient-to-br from-indigo-700 via-purple-700 to-blue-800 rounded-[2.5rem] p-10 text-white shadow-sm relative overflow-hidden">
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div>
                        <p class="text-[11px] font-black uppercase tracking-[0.3em] text-purple-300 mb-2">{{ __('Espace Professeur') }}</p>
                        <h2 class="text-3xl font-black tracking-tighter">{{ __('Surveillance d\'Examens') }}</h2>
                        <p class="text-blue-200 text-xs mt-2 opacity-80">{{ __('Vos affectations de surveillance officielles. Téléchargez le PDF et confirmez réception.') }}</p>
                    </div>
                    <div class="flex gap-4">
                        <div class="bg-white/15 backdrop-blur border border-white/20 px-6 py-4 rounded-2xl text-center shadow-inner">
                            <p class="text-2xl font-black">{{ $totalAssigned }}</p>
                            <p class="text-[9px] uppercase font-black text-blue-200 tracking-widest">{{ __('Total') }}</p>
                        </div>
                        <div class="bg-white/15 backdrop-blur border border-white/20 px-6 py-4 rounded-2xl text-center shadow-inner">
                            <p class="text-2xl font-black">{{ $upcoming->count() }}</p>
                            <p class="text-[9px] uppercase font-black text-blue-200 tracking-widest">{{ __('À venir') }}</p>
                        </div>
                        <div class="bg-white/15 backdrop-blur border border-white/20 px-6 py-4 rounded-2xl text-center shadow-inner">
                            <p class="text-2xl font-black">{{ $confirmed }}</p>
                            <p class="text-[9px] uppercase font-black text-green-300 tracking-widest">{{ __('Confirmées') }}</p>
                        </div>
                    </div>
                </div>
                <div class="absolute -bottom-16 -right-16 w-56 h-56 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>
            </div>

            {{-- UPCOMING SURVEILLANCES --}}
            <x-card class="p-0">
                <div class="px-8 py-5 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-sm">📅</span>
                    <h3 class="font-black text-gray-900">{{ __('Surveillances à venir') }}</h3>
                    <span class="bg-indigo-600 text-white text-[10px] font-black px-2 py-0.5 rounded-full">{{ $upcoming->count() }}</span>
                </div>

                @forelse($upcoming as $conv)
                    @php
                        $exam = $conv->exam;
                        $daysUntil = now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($exam->date)->startOfDay(), false);
                        $urgencyClass = $daysUntil <= 3
                            ? 'border-red-200 bg-red-50/20'
                            : ($daysUntil <= 7 ? 'border-amber-200 bg-amber-50/20' : 'border-gray-50');
                    @endphp
                    <div class="px-8 py-5 border-b {{ $urgencyClass }} hover:bg-gray-50/30 transition-colors">
                        <div class="flex items-center gap-6">

                            {{-- Date Block --}}
                            <div class="text-center min-w-[64px]">
                                <div class="w-14 h-14 rounded-2xl {{ $daysUntil <= 3 ? 'bg-red-500' : ($daysUntil <= 7 ? 'bg-amber-500' : 'bg-indigo-600') }} text-white flex flex-col items-center justify-center shadow-md">
                                    <span class="text-[9px] uppercase font-black opacity-80">{{ \Carbon\Carbon::parse($exam->date)->isoFormat('MMM') }}</span>
                                    <span class="text-xl font-black leading-none">{{ \Carbon\Carbon::parse($exam->date)->format('d') }}</span>
                                </div>
                            </div>

                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1 flex-wrap">
                                    <h4 class="font-black text-gray-900">{{ $exam->module->name ?? '—' }}</h4>
                                    <span class="bg-indigo-100 text-indigo-700 text-[10px] font-black px-2 py-0.5 rounded-full uppercase">{{ $exam->type }}</span>
                                    <span class="{{ $conv->role === 'principal' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-600' }} text-[10px] font-black px-2 py-0.5 rounded-full uppercase">
                                        {{ $conv->role === 'principal' ? '⭐ ' . __('Principal') : __('Assistant') }}
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black {{ $conv->status_color }}">
                                        {{ $conv->status_label }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-4 text-xs text-gray-500 font-bold flex-wrap">
                                    <span>🕐 {{ date('H:i', strtotime($exam->start_time)) }} → {{ $exam->end_time }}</span>
                                    <span>⏱ {{ $exam->duration }} {{ __('min') }}</span>
                                    <span>📍 {{ $exam->room->name ?? __('Salle à confirmer') }}</span>
                                    <span>👥 {{ $exam->group->name ?? '—' }}</span>
                                </div>
                                <p class="text-[10px] text-gray-400 mt-1 font-bold uppercase tracking-widest">{{ __('Réf') }}: {{ $conv->reference }}</p>
                            </div>

                            {{-- Countdown --}}
                            <div class="text-center min-w-[80px]">
                                @if($daysUntil === 0)
                                    <span class="text-red-600 font-black text-sm">{{ __('Aujourd\'hui !') }}</span>
                                @elseif($daysUntil === 1)
                                    <span class="text-red-500 font-black text-sm">{{ __('Demain') }}</span>
                                @else
                                    <span class="text-gray-600 font-black text-lg">{{ $daysUntil }}</span>
                                    <p class="text-[9px] text-gray-400 font-black uppercase">{{ __('jours') }}</p>
                                @endif
                            </div>

                            {{-- Actions --}}
                            <div class="flex flex-col gap-2 min-w-[120px]">
                                <a href="{{ route('professor.proctor_convocations.download', $conv) }}"
                                   class="flex items-center justify-center gap-1 bg-gray-900 hover:bg-black text-white font-black py-2 px-4 rounded-xl shadow-sm transition-all hover:scale-105 text-xs uppercase tracking-widest">
                                    📄 {{ __('PDF') }}
                                </a>
                                @if($conv->status !== 'confirmed')
                                    <form action="{{ route('professor.proctor_convocations.confirm', $conv) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="w-full flex items-center justify-center gap-1 bg-emerald-600 hover:bg-emerald-700 text-white font-black py-2 px-4 rounded-xl shadow-sm transition-all hover:scale-105 text-xs uppercase tracking-widest">
                                            ✅ {{ __('Confirmer') }}
                                        </button>
                                    </form>
                                @else
                                    <div class="text-center py-1">
                                        <span class="text-[10px] text-emerald-650 font-black px-2.5 py-1 rounded-full bg-emerald-50 border border-emerald-200">✅ {{ __('Confirmé') }}</span>
                                    </div>
                                    @if($exam->pvExamen)
                                        <a href="{{ route('professor.exams.pv.create', $exam) }}"
                                           class="flex items-center justify-center gap-1 bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 font-bold py-1.5 px-3 rounded-xl transition-all text-[10px] uppercase tracking-wider">
                                            ✏️ {{ __('Modifier PV') }}
                                        </a>
                                        <a href="{{ route('professor.exams.pv.pdf', $exam) }}"
                                           class="flex items-center justify-center gap-1 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 font-bold py-1.5 px-3 rounded-xl transition-all text-[10px] uppercase tracking-wider">
                                            📥 {{ __('Télécharger PV') }}
                                        </a>
                                    @else
                                        <a href="{{ route('professor.exams.pv.create', $exam) }}"
                                           class="flex items-center justify-center gap-1 bg-purple-600 hover:bg-purple-750 text-white font-bold py-1.5 px-3 rounded-xl shadow-sm transition-all hover:scale-105 text-[10px] uppercase tracking-wider">
                                            ✍️ {{ __('Rédiger PV') }}
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <div class="text-5xl mb-4">🎉</div>
                        <p class="font-black text-gray-400">{{ __('Aucune surveillance programmée.') }}</p>
                        <p class="text-gray-300 text-sm mt-2">{{ __('Soumettez vos disponibilités pour être affecté.') }}</p>
                        <a href="{{ route('professor.availability.index') }}"
                            class="mt-4 inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-black py-2.5 px-5 rounded-xl text-xs uppercase tracking-widest transition-all">
                            📅 {{ __('Mes disponibilités') }}
                        </a>
                    </div>
                @endforelse
            </x-card>

            {{-- PAST SURVEILLANCES --}}
            @if($past->isNotEmpty())
            <x-card class="p-0 opacity-75">
                <div class="px-8 py-5 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-xl bg-gray-400 text-white flex items-center justify-center text-sm">📁</span>
                    <h3 class="font-black text-gray-500">{{ __('Surveillances passées') }}</h3>
                    <span class="bg-gray-200 text-gray-600 text-[10px] font-black px-2 py-0.5 rounded-full">{{ $past->count() }}</span>
                </div>

                @foreach($past as $conv)
                    @php $exam = $conv->exam; @endphp
                    <div class="flex items-center gap-6 px-8 py-4 border-b border-gray-50 hover:bg-gray-50/30 transition-colors">
                        <div class="text-center min-w-[64px]">
                            <div class="w-14 h-14 rounded-2xl bg-gray-300 text-white flex flex-col items-center justify-center">
                                <span class="text-[9px] uppercase font-black opacity-80">{{ \Carbon\Carbon::parse($exam->date)->isoFormat('MMM') }}</span>
                                <span class="text-xl font-black leading-none">{{ \Carbon\Carbon::parse($exam->date)->format('d') }}</span>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <h4 class="font-black text-gray-500">{{ $exam->module->name ?? '—' }}</h4>
                                <span class="bg-gray-100 text-gray-400 text-[10px] font-black px-2 py-0.5 rounded-full uppercase">{{ $exam->type }}</span>
                                <span class="{{ $conv->role === 'principal' ? 'bg-indigo-50 text-indigo-400' : 'bg-gray-50 text-gray-400' }} text-[10px] font-black px-2 py-0.5 rounded-full uppercase">
                                    {{ $conv->role === 'principal' ? __('Principal') : __('Assistant') }}
                                </span>
                            </div>
                            <div class="flex items-center gap-4 text-xs text-gray-300 font-bold">
                                <span>📍 {{ $exam->room->name ?? '—' }}</span>
                                <span>{{ \Carbon\Carbon::parse($exam->date)->format('d/m/Y') }}</span>
                                <span>{{ __('Réf') }}: {{ $conv->reference }}</span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2 min-w-[120px]">
                            <a href="{{ route('professor.proctor_convocations.download', $conv) }}"
                               class="flex items-center justify-center gap-1 bg-gray-100 hover:bg-gray-205 text-gray-600 font-bold py-2 px-4 rounded-xl text-xs transition-colors uppercase tracking-wider text-center">
                                📄 {{ __('Archive') }}
                            </a>
                            @if($exam->pvExamen)
                                <a href="{{ route('professor.exams.pv.create', $exam) }}"
                                   class="flex items-center justify-center gap-1 bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 font-bold py-1.5 px-3 rounded-xl transition-all text-[10px] uppercase tracking-wider text-center">
                                    ✏️ {{ __('Modifier PV') }}
                                </a>
                                <a href="{{ route('professor.exams.pv.pdf', $exam) }}"
                                   class="flex items-center justify-center gap-1 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 font-bold py-1.5 px-3 rounded-xl transition-all text-[10px] uppercase tracking-wider text-center">
                                    📥 {{ __('Télécharger PV') }}
                                </a>
                            @else
                                <a href="{{ route('professor.exams.pv.create', $exam) }}"
                                   class="flex items-center justify-center gap-1 bg-purple-600 hover:bg-purple-750 text-white font-bold py-1.5 px-3 rounded-xl shadow-sm transition-all hover:scale-105 text-[10px] uppercase tracking-wider text-center">
                                    ✍️ {{ __('Rédiger PV') }}
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </x-card>
            @endif

        </div>
    </div>
</x-app-layout>
