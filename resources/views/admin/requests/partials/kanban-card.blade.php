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
            <a href="{{ route('admin.requests.show', $req) }}" target="_blank" class="text-upf-blue hover:text-indigo-700 flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                PDF
            </a>
        @elseif($req->status === 'rejected')
            <span class="text-rose-500 line-clamp-1" title="{{ $req->reason }}">{{ Str::limit($req->reason, 20) }}</span>
        @endif
    </div>
</div>
