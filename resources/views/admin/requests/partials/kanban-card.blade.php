<div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-200 cursor-move hover:shadow-md hover:border-upf-blue transition-all" data-id="{{ $req->id }}">
    <div class="flex justify-between items-start mb-3">
        <span class="text-[10px] font-black uppercase tracking-widest text-white bg-upf-blue px-2 py-1 rounded-md">{{ $req->type }}</span>
        <div class="w-8 h-8 rounded-full bg-indigo-50 flex items-center justify-center text-upf-blue font-black text-[10px]">
            {{ substr($req->user->name, 0, 1) }}
        </div>
    </div>
    
    <h4 class="font-bold text-gray-900 text-sm mb-1">{{ $req->user->name }}</h4>
    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-3">
        {{ $req->user->isProfessor() ? 'Enseignant' : 'Étudiant' }}
    </p>

    <div class="text-xs text-gray-500 bg-gray-50 p-3 rounded-xl mb-4 border border-gray-100">
        @if($req->type === 'Ordre de Mission' && is_array($req->data))
            <p class="font-bold text-gray-800 mb-1">🌍 {{ $req->data['destination'] ?? 'N/A' }}</p>
            <p class="text-[10px]">{{ \Carbon\Carbon::parse($req->data['start_date'] ?? now())->format('d/m') }} - {{ \Carbon\Carbon::parse($req->data['end_date'] ?? now())->format('d/m') }}</p>
        @else
            <p class="italic line-clamp-2">{{ $req->reason ?: 'Aucun motif fourni' }}</p>
        @endif
    </div>

    <div class="flex items-center justify-between text-[10px] font-bold text-gray-400">
        <span>{{ $req->created_at->diffForHumans() }}</span>
        
        @if($req->status === 'approved')
            <div class="flex items-center gap-1.5">
                <a href="{{ route('documents.download', $req) }}?preview=1" target="_blank" class="text-emerald-600 hover:text-emerald-800 flex items-center gap-0.5" title="Aperçu avant téléchargement">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    Aperçu
                </a>
                <span class="text-gray-300">|</span>
                <a href="{{ route('documents.download', $req) }}" class="text-upf-blue hover:text-indigo-700 flex items-center gap-0.5" title="Télécharger">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Télécharger
                </a>
            </div>
        @elseif($req->status === 'rejected')
            <span class="text-rose-500 line-clamp-1" title="{{ $req->reason }}">{{ Str::limit($req->reason, 20) }}</span>
        @endif
    </div>
</div>
