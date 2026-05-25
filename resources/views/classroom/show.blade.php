<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('classroom.index') }}" class="text-xs font-black uppercase text-upf-blue hover:text-upf-navy flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Classroom
            </a>
            <span class="text-gray-300">/</span>
            <h2 class="font-black text-xl text-gray-800 leading-tight tracking-tight">
                {{ $module->name }}
                <span class="text-gray-300 font-normal">·</span>
                <span class="text-upf-magenta">{{ $group->name }}</span>
            </h2>
        </div>
    </x-slot>

    <div class="py-10 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            @if(session('success'))
                <div class="p-4 text-sm text-emerald-800 rounded-2xl bg-emerald-50 border border-emerald-100 font-bold flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            {{-- ===== HERO BANNER ===== --}}
            <div class="bg-gradient-to-br from-indigo-900 via-upf-blue to-upf-magenta rounded-[2.5rem] p-10 text-white shadow-2xl relative overflow-hidden">
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-[0.3em] bg-white/20 px-3 py-1 rounded-full border border-white/10">
                            {{ $group->name }}
                            @if($group->filiere) · {{ $group->filiere->name }} @endif
                        </span>
                        <h2 class="text-3xl font-black mt-3 mb-2 italic tracking-tight">📚 {{ $module->name }}</h2>
                        <p class="text-blue-100 opacity-90 text-sm max-w-xl">Consultez les annonces, téléchargez les supports de cours et posez vos questions directement à l'enseignant.</p>
                    </div>
                    <div class="flex gap-4">
                        <div class="bg-white/15 backdrop-blur border border-white/20 px-6 py-4 rounded-2xl text-center">
                            <p class="text-2xl font-black">{{ $posts->count() }}</p>
                            <p class="text-[9px] uppercase font-black text-blue-200 tracking-widest">Publications</p>
                        </div>
                        <div class="bg-white/15 backdrop-blur border border-white/20 px-6 py-4 rounded-2xl text-center">
                            <p class="text-2xl font-black">{{ $posts->whereNotNull('file_path')->count() }}</p>
                            <p class="text-[9px] uppercase font-black text-blue-200 tracking-widest">Supports</p>
                        </div>
                    </div>
                </div>
                <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- ===== MAIN FEED ===== --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Publisher Form (Professor / Admin only) --}}
                    @if(Auth::user()->isProfessor() || Auth::user()->isAdmin())
                    <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm" x-data="{ fileName: null, type: 'annonce' }">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 rounded-xl bg-upf-blue/10 text-upf-blue flex items-center justify-center text-xl">📣</div>
                            <div>
                                <h3 class="text-lg font-black text-gray-900 italic">Publier dans le Classroom</h3>
                                <p class="text-xs text-gray-400 font-bold">Annonce ou support pour les étudiants de {{ $group->name }}</p>
                            </div>
                        </div>

                        {{-- Type Selector --}}
                        <div class="flex gap-3 mb-5">
                            <button type="button" @click="type = 'annonce'"
                                :class="type === 'annonce' ? 'bg-upf-blue text-white shadow-md' : 'bg-gray-50 text-gray-500 border border-gray-100'"
                                class="flex-1 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all">
                                📣 Annonce
                            </button>
                            <button type="button" @click="type = 'support'"
                                :class="type === 'support' ? 'bg-upf-magenta text-white shadow-md' : 'bg-gray-50 text-gray-500 border border-gray-100'"
                                class="flex-1 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all">
                                📎 Support de Cours
                            </button>
                        </div>

                        <form action="{{ route('classroom.post', [$group->id, $module->id]) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <textarea name="content" rows="3" required
                                :placeholder="type === 'annonce' ? 'Rédigez votre annonce ou message pour les étudiants...' : 'Décrivez le support de cours (titre du chapitre, contenu, objectifs...)'"
                                class="w-full border-gray-100 rounded-2xl bg-gray-50 p-4 font-medium text-gray-900 focus:ring-upf-blue text-sm resize-none"></textarea>

                            {{-- File upload --}}
                            <label class="cursor-pointer block mb-4"
                                :class="type === 'support' ? 'opacity-100' : 'opacity-50'">
                                <div class="flex items-center gap-3 px-5 py-4 border-2 border-dashed rounded-2xl transition-all"
                                    :class="fileName ? 'border-emerald-300 bg-emerald-50' : 'border-gray-200 bg-gray-50 hover:border-upf-blue hover:bg-indigo-50/30'">
                                    <div class="text-2xl" x-text="fileName ? '✅' : '📁'"></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-black text-gray-700" x-text="fileName ? fileName : 'Cliquez pour déposer un fichier'"></p>
                                        <p class="text-[10px] text-gray-400 font-bold">PDF, PowerPoint, Word, ZIP — max 20 Mo</p>
                                    </div>
                                    <input type="file" name="file" class="hidden"
                                        @change="fileName = $event.target.files[0] ? $event.target.files[0].name : null"
                                        :required="type === 'support'">
                                </div>
                            </label>

                            <div class="flex items-center justify-between mt-4">
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="checkbox" name="notify_students" class="w-4 h-4 rounded border-gray-300 text-upf-blue focus:ring-upf-blue cursor-pointer">
                                    <span class="text-xs font-bold text-gray-500 group-hover:text-gray-700 transition-colors">Notifier les étudiants de ce groupe</span>
                                </label>
                                <button type="submit"
                                    :class="type === 'annonce' ? 'bg-upf-blue hover:bg-upf-navy' : 'bg-upf-magenta hover:bg-pink-700'"
                                    class="px-8 py-3 text-white rounded-xl font-black shadow-lg transition-all transform hover:-translate-y-0.5 text-xs uppercase tracking-wider"
                                    x-text="type === 'annonce' ? 'Publier l\'annonce' : 'Déposer le support'">
                                </button>
                            </div>
                        </form>
                    </div>
                    @endif

                    {{-- Feed Stream --}}
                    @forelse($posts as $post)
                    <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden hover:shadow-xl transition-all duration-300">

                        {{-- Post type stripe --}}
                        <div class="h-1 {{ $post->file_path ? 'bg-upf-magenta' : 'bg-upf-blue' }}"></div>

                        <div class="p-8">
                            {{-- Post Header --}}
                            <div class="flex items-center justify-between mb-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center font-black text-sm shadow-inner
                                        {{ $post->user->isProfessor() ? 'bg-amber-50 text-amber-600' : 'bg-upf-blue/10 text-upf-blue' }}">
                                        {{ strtoupper(substr($post->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <p class="font-black text-gray-900 text-sm">{{ $post->user->name }}</p>
                                            @if($post->user->isProfessor())
                                                <span class="text-[8px] font-black uppercase bg-amber-50 text-amber-600 px-2 py-0.5 rounded border border-amber-100">Enseignant</span>
                                            @elseif($post->user->isAdmin())
                                                <span class="text-[8px] font-black uppercase bg-blue-50 text-upf-blue px-2 py-0.5 rounded border border-blue-100">Administration</span>
                                            @endif
                                            {{-- Type tag --}}
                                            @if($post->file_path)
                                                <span class="text-[8px] font-black uppercase bg-pink-50 text-upf-magenta px-2 py-0.5 rounded border border-pink-100">📎 Support</span>
                                            @else
                                                <span class="text-[8px] font-black uppercase bg-indigo-50 text-upf-blue px-2 py-0.5 rounded border border-indigo-100">📣 Annonce</span>
                                            @endif
                                        </div>
                                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">{{ $post->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>

                                {{-- File Download Button --}}
                                @if($post->file_path)
                                <a href="{{ route('classroom.download_file', $post) }}" target="_blank"
                                   class="inline-flex items-center gap-2 text-xs font-black text-upf-magenta bg-pink-50 border border-pink-100 px-4 py-2.5 rounded-xl hover:bg-upf-magenta hover:text-white transition-all uppercase tracking-widest shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    Télécharger
                                </a>
                                @endif
                            </div>

                            {{-- Post Content --}}
                            <div class="text-gray-700 font-semibold leading-relaxed mb-6 text-sm bg-gray-50/50 rounded-2xl p-4 border border-gray-100">
                                {!! nl2br(e($post->content)) !!}
                            </div>

                            {{-- Comments Section --}}
                            <div class="pt-5 border-t border-gray-100">
                                <div class="flex items-center gap-2 mb-4">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                    <h5 class="text-[10px] uppercase font-black tracking-widest text-gray-400">
                                        {{ $post->comments->count() }} Commentaire(s)
                                    </h5>
                                </div>

                                @foreach($post->comments as $comment)
                                <div class="flex gap-3 mb-3">
                                    <div class="w-8 h-8 rounded-xl flex items-center justify-center text-[10px] font-black shadow-sm flex-shrink-0
                                        {{ $comment->user->isProfessor() ? 'bg-amber-50 text-amber-600' : 'bg-gray-100 text-gray-500' }}">
                                        {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                    </div>
                                    <div class="flex-1 bg-gray-50 border border-gray-100 rounded-2xl p-3.5">
                                        <div class="flex justify-between items-center mb-1.5">
                                            <span class="text-[10px] font-black text-gray-900">
                                                {{ $comment->user->name }}
                                                @if($comment->user->isProfessor())
                                                    <span class="text-amber-500 ml-1">★</span>
                                                @endif
                                            </span>
                                            <span class="text-[8px] font-bold text-gray-400 uppercase">{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-xs text-gray-700 font-semibold leading-relaxed">{{ $comment->content }}</p>
                                    </div>
                                </div>
                                @endforeach

                                {{-- Comment Form --}}
                                <form action="{{ route('classroom.comment', $post) }}" method="POST" class="flex items-center gap-2 mt-3">
                                    @csrf
                                    <div class="w-8 h-8 rounded-xl bg-upf-blue flex items-center justify-center text-white text-[10px] font-black flex-shrink-0">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                    <input type="text" name="content" required
                                        class="flex-1 border-gray-100 rounded-xl bg-gray-50 px-4 py-3 text-xs font-medium focus:ring-upf-blue text-gray-900"
                                        placeholder="Écrire une question ou un commentaire...">
                                    <button type="submit"
                                        class="w-10 h-10 bg-upf-blue text-white rounded-xl hover:bg-upf-navy hover:scale-105 transition-all shadow-md flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="bg-white p-20 rounded-[2.5rem] text-center border border-gray-100 shadow-sm border-dashed">
                        <div class="text-6xl mb-4">💬</div>
                        <h3 class="text-lg font-black text-gray-400 mb-2">Aucune publication pour le moment</h3>
                        <p class="text-sm text-gray-300 font-medium">
                            @if(Auth::user()->isProfessor())
                                Utilisez le formulaire ci-dessus pour publier votre première annonce ou déposer un support de cours.
                            @else
                                L'espace d'échange de ce module est vide. L'enseignant n'a pas encore publié de contenu.
                            @endif
                        </p>
                    </div>
                    @endforelse
                </div>

                {{-- ===== SIDEBAR ===== --}}
                <div class="space-y-6">

                    {{-- Course Materials Library --}}
                    <div class="bg-white rounded-[2.5rem] p-7 border border-gray-100 shadow-sm">
                        <div class="flex items-center gap-2 border-b border-gray-100 pb-4 mb-5">
                            <span class="text-xl">📂</span>
                            <h4 class="text-sm font-black text-gray-900 italic">Supports de Cours</h4>
                            @php $materials = $posts->filter(fn($p) => !empty($p->file_path)); @endphp
                            <span class="ml-auto text-xs font-black text-upf-magenta bg-pink-50 px-2 py-0.5 rounded-full">{{ $materials->count() }}</span>
                        </div>

                        <div class="space-y-3">
                            @forelse($materials as $material)
                            <div class="flex items-center gap-3 p-3 bg-gray-50/50 border border-gray-100 rounded-2xl hover:bg-white hover:shadow-md transition-all group">
                                @php
                                    $ext = strtolower(pathinfo($material->file_path, PATHINFO_EXTENSION));
                                    $iconColor = match($ext) {
                                        'pdf' => 'bg-red-50 text-red-500',
                                        'pptx', 'ppt' => 'bg-orange-50 text-orange-500',
                                        'docx', 'doc' => 'bg-blue-50 text-blue-500',
                                        'zip', 'rar' => 'bg-purple-50 text-purple-500',
                                        default => 'bg-gray-50 text-gray-500',
                                    };
                                    $iconEmoji = match($ext) {
                                        'pdf' => '📕',
                                        'pptx', 'ppt' => '📊',
                                        'docx', 'doc' => '📝',
                                        'zip', 'rar' => '🗜️',
                                        default => '📄',
                                    };
                                @endphp
                                <div class="w-9 h-9 rounded-xl {{ $iconColor }} flex items-center justify-center text-sm flex-shrink-0">{{ $iconEmoji }}</div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-black text-gray-900 truncate">{{ basename($material->file_path) }}</p>
                                    <p class="text-[9px] text-gray-400 font-bold uppercase">{{ strtoupper($ext) }} · {{ $material->created_at->format('d/m/Y') }}</p>
                                </div>
                                <a href="{{ route('classroom.download_file', $material) }}" target="_blank"
                                   class="w-8 h-8 bg-upf-blue/10 text-upf-blue rounded-lg hover:bg-upf-blue hover:text-white transition-all flex items-center justify-center flex-shrink-0 shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3"></path></svg>
                                </a>
                            </div>
                            @empty
                            <div class="text-center py-8">
                                <div class="text-3xl mb-2">📭</div>
                                <p class="text-[10px] font-black text-gray-300 uppercase tracking-widest italic">Aucun support déposé</p>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Module Info Card --}}
                    <div class="bg-white rounded-[2.5rem] p-7 border border-gray-100 shadow-sm space-y-4">
                        <h4 class="text-sm font-black text-gray-900 italic border-b border-gray-100 pb-3">ℹ️ Infos du Module</h4>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Code</span>
                                <span class="text-xs font-black text-upf-blue">{{ $module->code ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Coefficient</span>
                                <span class="text-xs font-black text-gray-900">{{ $module->coefficient ?? '—' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Groupe</span>
                                <span class="text-xs font-black text-gray-900">{{ $group->name }}</span>
                            </div>
                            @if($group->filiere)
                            <div class="flex justify-between">
                                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Filière</span>
                                <span class="text-xs font-black text-upf-magenta">{{ $group->filiere->name }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Rules Card --}}
                    <div class="bg-gradient-to-br from-upf-navy to-upf-blue rounded-[2.5rem] p-7 text-white shadow-xl">
                        <h4 class="text-sm font-black mb-4 italic">📜 Règles du Classroom</h4>
                        <ul class="space-y-2.5 text-xs font-bold opacity-90 leading-relaxed">
                            <li class="flex gap-2"><span>✅</span> Respectez la courtoisie universitaire</li>
                            <li class="flex gap-2"><span>✅</span> Langage académique et professionnel requis</li>
                            <li class="flex gap-2"><span>✅</span> Questions uniquement relatives au module</li>
                            <li class="flex gap-2"><span>📎</span> Les supports sont la propriété de l'UPF</li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
