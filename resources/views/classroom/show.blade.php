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

    <div class="py-10 bg-[#F8FAFC] dark:bg-[#020617] transition-colors duration-300" x-data="{ tab: 'feed' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            @if(session('success'))
                <div class="p-6 text-sm text-emerald-800 bg-emerald-50 border border-emerald-100 rounded-3xl font-black flex items-center gap-3 shadow-sm">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-6 text-sm text-rose-800 bg-rose-50 border border-rose-100 rounded-3xl font-black flex items-center gap-3 shadow-sm">
                    <span class="text-xl">⚠️</span>
                    {{ session('error') }}
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
                        <p class="text-blue-105 opacity-90 text-sm max-w-xl">
                            Espace d'échange et d'évaluation académique de votre classe.
                        </p>
                    </div>
                    <div class="flex gap-4">
                        <div class="bg-white/15 backdrop-blur border border-white/20 px-6 py-4 rounded-2xl text-center shadow-inner">
                            <p class="text-2xl font-black">{{ $posts->count() }}</p>
                            <p class="text-[9px] uppercase font-black text-blue-200 tracking-widest">Publications</p>
                        </div>
                        <div class="bg-white/15 backdrop-blur border border-white/20 px-6 py-4 rounded-2xl text-center shadow-inner">
                            <p class="text-2xl font-black">{{ $homeworks->count() }}</p>
                            <p class="text-[9px] uppercase font-black text-blue-200 tracking-widest">Devoirs</p>
                        </div>
                    </div>
                </div>
                <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>
            </div>

            {{-- ===== PREMIUM NAVIGATION TABS ===== --}}
            <div class="flex flex-wrap p-1.5 bg-slate-100 dark:bg-slate-900 rounded-3xl w-fit gap-1 shadow-sm border border-slate-200/50 dark:border-slate-800">
                <button @click="tab = 'feed'"
                    :class="tab === 'feed' ? 'bg-white dark:bg-slate-800 text-upf-blue dark:text-blue-400 shadow-md font-black' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 font-bold'"
                    class="px-8 py-3 rounded-2xl text-xs uppercase tracking-widest transition-all duration-200">
                    📣 Annonces & Cours
                </button>
                <button @click="tab = 'homework'"
                    :class="tab === 'homework' ? 'bg-white dark:bg-slate-800 text-upf-blue dark:text-blue-400 shadow-md font-black' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 font-bold'"
                    class="px-8 py-3 rounded-2xl text-xs uppercase tracking-widest transition-all duration-200">
                    📚 Devoirs & Soumissions
                </button>
                <button @click="tab = 'chat'"
                    :class="tab === 'chat' ? 'bg-white dark:bg-slate-800 text-upf-blue dark:text-blue-400 shadow-md font-black' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 font-bold'"
                    class="px-8 py-3 rounded-2xl text-xs uppercase tracking-widest transition-all duration-200">
                    💬 Salon de Discussion
                </button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- ===== LEFT COLUMN (DYNAMIC CONTENT TAB) ===== --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- ==================== TAB 1 : FEED STREAM ==================== --}}
                    <div x-show="tab === 'feed'" class="space-y-6" x-transition:enter="transition ease-out duration-300">
                        {{-- Publisher Form (Professor / Admin only) --}}
                        @if(Auth::user()->isProfessor() || Auth::user()->isAdmin())
                        <div class="bg-white dark:bg-slate-900 p-8 rounded-[2.5rem] border border-gray-100 dark:border-slate-800 shadow-sm" x-data="{ fileName: null, type: 'annonce' }">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 rounded-xl bg-upf-blue/10 text-upf-blue flex items-center justify-center text-xl shadow-inner">📣</div>
                                <div>
                                    <h3 class="text-lg font-black text-slate-900 dark:text-white italic">Publier dans le Classroom</h3>
                                    <p class="text-xs text-slate-400 font-bold">Partagez des cours ou annonces pour {{ $group->name }}</p>
                                </div>
                            </div>

                            <div class="flex gap-3 mb-5">
                                <button type="button" @click="type = 'annonce'"
                                    :class="type === 'annonce' ? 'bg-upf-blue text-white shadow-md' : 'bg-gray-50 dark:bg-slate-800 text-gray-500 dark:text-slate-400 border border-gray-100 dark:border-slate-800'"
                                    class="flex-1 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all">
                                    📣 Annonce
                                </button>
                                <button type="button" @click="type = 'support'"
                                    :class="type === 'support' ? 'bg-upf-magenta text-white shadow-md' : 'bg-gray-50 dark:bg-slate-800 text-gray-500 dark:text-slate-400 border border-gray-100 dark:border-slate-800'"
                                    class="flex-1 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all">
                                    📎 Support de Cours
                                </button>
                            </div>

                            <form action="{{ route('classroom.post', [$group->id, $module->id]) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                <textarea name="content" rows="3" required
                                    :placeholder="type === 'annonce' ? 'Rédigez votre annonce ou message pour les étudiants...' : 'Décrivez le support de cours (titre du chapitre, contenu, objectifs...)'"
                                    class="w-full border-gray-100 dark:border-slate-800 rounded-2xl bg-gray-50 dark:bg-slate-950 p-4 font-medium text-slate-900 dark:text-slate-100 focus:ring-upf-blue text-sm resize-none"></textarea>

                                <label class="cursor-pointer block mb-4" :class="type === 'support' ? 'opacity-100' : 'opacity-50'">
                                    <div class="flex items-center gap-3 px-5 py-4 border-2 border-dashed rounded-2xl transition-all"
                                        :class="fileName ? 'border-emerald-300 bg-emerald-50 dark:bg-emerald-950/20' : 'border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 hover:border-upf-blue hover:bg-indigo-50/30 dark:hover:bg-indigo-950/20'">
                                        <div class="text-2xl" x-text="fileName ? '✅' : '📁'"></div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-black text-slate-700 dark:text-slate-350" x-text="fileName ? fileName : 'Cliquez pour déposer un fichier'"></p>
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
                        <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden hover:shadow-xl transition-all duration-300">
                            <div class="h-1.5 {{ $post->file_path ? 'bg-upf-magenta' : 'bg-upf-blue' }}"></div>
                            <div class="p-8">
                                <div class="flex items-center justify-between mb-5">
                                    <div class="flex items-center gap-4">
                                        <div class="w-11 h-11 rounded-2xl flex items-center justify-center font-black text-sm shadow-inner
                                            {{ $post->user->isProfessor() ? 'bg-amber-50 dark:bg-amber-950/20 text-amber-600' : 'bg-upf-blue/10 text-upf-blue' }}">
                                            {{ strtoupper(substr($post->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="flex flex-wrap items-center gap-2">
                                                <p class="font-black text-slate-900 dark:text-white text-sm">{{ $post->user->name }}</p>
                                                @if($post->user->isProfessor())
                                                    <span class="text-[8px] font-black uppercase bg-amber-50 dark:bg-amber-950/30 text-amber-600 px-2 py-0.5 rounded border border-amber-100 dark:border-amber-900/30">Enseignant</span>
                                                @elseif($post->user->isAdmin())
                                                    <span class="text-[8px] font-black uppercase bg-blue-50 dark:bg-blue-950/30 text-upf-blue px-2 py-0.5 rounded border border-blue-100 dark:border-blue-900/30">Administration</span>
                                                @endif
                                                @if($post->file_path)
                                                    <span class="text-[8px] font-black uppercase bg-pink-50 dark:bg-pink-950/30 text-upf-magenta px-2 py-0.5 rounded border border-pink-100 dark:border-pink-900/30">📎 Support</span>
                                                @else
                                                    <span class="text-[8px] font-black uppercase bg-indigo-50 dark:bg-indigo-950/30 text-upf-blue px-2 py-0.5 rounded border border-indigo-100 dark:border-indigo-900/30">📣 Annonce</span>
                                                @endif
                                            </div>
                                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">{{ $post->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>

                                    @if($post->file_path)
                                    <a href="{{ route('classroom.download_file', $post) }}" target="_blank"
                                       class="inline-flex items-center gap-2 text-xs font-black text-upf-magenta bg-pink-50 dark:bg-pink-950/20 border border-pink-100 dark:border-pink-900/30 px-4 py-2.5 rounded-xl hover:bg-upf-magenta hover:text-white transition-all uppercase tracking-widest shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        Télécharger
                                    </a>
                                    @endif
                                </div>

                                <div class="text-slate-700 dark:text-slate-350 font-semibold leading-relaxed mb-6 text-sm bg-gray-50/50 dark:bg-slate-950/30 rounded-2xl p-4 border border-gray-100 dark:border-slate-800">
                                    {!! nl2br(e($post->content)) !!}
                                </div>

                                {{-- Comments --}}
                                <div class="pt-5 border-t border-gray-100 dark:border-slate-800">
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
                                        <div class="flex-1 bg-gray-50 dark:bg-slate-950 border border-gray-100 dark:border-slate-850 rounded-2xl p-3.5">
                                            <div class="flex justify-between items-center mb-1.5">
                                                <span class="text-[10px] font-black text-slate-900 dark:text-white">
                                                    {{ $comment->user->name }}
                                                    @if($comment->user->isProfessor()) <span class="text-amber-500 ml-1">★</span> @endif
                                                </span>
                                                <span class="text-[8px] font-bold text-gray-450 uppercase">{{ $comment->created_at->diffForHumans() }}</span>
                                            </div>
                                            <p class="text-xs text-slate-700 dark:text-slate-350 font-semibold leading-relaxed">{{ $comment->content }}</p>
                                        </div>
                                    </div>
                                    @endforeach

                                    <form action="{{ route('classroom.comment', $post) }}" method="POST" class="flex items-center gap-2 mt-3">
                                        @csrf
                                        <div class="w-8 h-8 rounded-xl bg-upf-blue flex items-center justify-center text-white text-[10px] font-black flex-shrink-0">
                                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                        </div>
                                        <input type="text" name="content" required
                                            class="flex-1 border-gray-100 dark:border-slate-800 rounded-xl bg-gray-50 dark:bg-slate-950 px-4 py-3 text-xs font-medium focus:ring-upf-blue text-slate-900 dark:text-white"
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
                        <div class="bg-white dark:bg-slate-900 p-20 rounded-[2.5rem] text-center border border-gray-100 dark:border-slate-800 shadow-sm border-dashed">
                            <div class="text-6xl mb-4">📣</div>
                            <h3 class="text-lg font-black text-slate-400 mb-2">Aucune annonce pour le moment</h3>
                            <p class="text-sm text-slate-400 font-medium">L'enseignant n'a pas encore publié d'annonces ou de cours.</p>
                        </div>
                        @endforelse
                    </div>

                    {{-- ==================== TAB 2 : DEVOIRS & SOUMISSIONS ==================== --}}
                    <div x-show="tab === 'homework'" class="space-y-6" style="display: none;" x-transition:enter="transition ease-out duration-300">
                        
                        {{-- Create Homework (Professor Only) --}}
                        @if(Auth::user()->isProfessor() || Auth::user()->isAdmin())
                        <div class="bg-white dark:bg-slate-900 p-8 rounded-[2.5rem] border border-gray-100 dark:border-slate-800 shadow-sm">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 rounded-xl bg-upf-magenta/10 text-upf-magenta flex items-center justify-center text-xl shadow-inner">📅</div>
                                <div>
                                    <h3 class="text-lg font-black text-slate-900 dark:text-white italic">Publier un Nouveau Devoir</h3>
                                    <p class="text-xs text-slate-450 font-bold">Fixez une date limite et fournissez les consignes de travail</p>
                                </div>
                            </div>

                            <form action="{{ route('classroom.homework.store', [$group->id, $module->id]) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-1">
                                        <label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider">Titre du Devoir</label>
                                        <input type="text" name="title" required placeholder="Ex: Devoir de Programmation N°1"
                                            class="w-full border-gray-100 dark:border-slate-800 rounded-2xl bg-gray-50 dark:bg-slate-950 p-4 font-semibold text-slate-900 dark:text-white text-sm focus:ring-upf-magenta">
                                    </div>
                                    <div class="space-y-1">
                                        <label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider">Date & Heure Limite (Deadline)</label>
                                        <input type="datetime-local" name="due_date" required
                                            class="w-full border-gray-100 dark:border-slate-800 rounded-2xl bg-gray-50 dark:bg-slate-950 p-4 font-semibold text-slate-900 dark:text-white text-sm focus:ring-upf-magenta">
                                    </div>
                                </div>

                                <div class="space-y-1">
                                    <label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider">Instructions & Barème</label>
                                    <textarea name="description" rows="3" required placeholder="Détaillez le travail à accomplir..."
                                        class="w-full border-gray-100 dark:border-slate-800 rounded-2xl bg-gray-50 dark:bg-slate-950 p-4 font-semibold text-slate-900 dark:text-white text-sm focus:ring-upf-magenta resize-none"></textarea>
                                </div>

                                <div class="space-y-1">
                                    <label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider">Fichier de Consigne (Optionnel)</label>
                                    <input type="file" name="attachment"
                                        class="w-full border border-dashed border-gray-200 dark:border-slate-800 rounded-2xl bg-gray-50 dark:bg-slate-950 p-4 text-xs font-bold text-slate-500">
                                </div>

                                <div class="flex justify-end">
                                    <button type="submit" class="px-8 py-3.5 bg-upf-magenta hover:bg-pink-700 text-white rounded-xl font-black shadow-lg transition-all text-xs uppercase tracking-widest transform hover:-translate-y-0.5">
                                        Publier le Devoir
                                    </button>
                                </div>
                            </form>
                        </div>
                        @endif

                        {{-- Homework List --}}
                        <div class="space-y-6">
                            @forelse($homeworks as $hw)
                                @php 
                                    $hasSubmitted = false;
                                    $submission = null;
                                    if(Auth::user()->isStudent() && Auth::user()->student) {
                                        $submission = $hw->studentSubmission(Auth::user()->student->id);
                                        $hasSubmitted = !is_null($submission);
                                    }
                                    $isOverdue = now()->gt($hw->due_date);
                                @endphp
                                <div x-data="{ openSubmissions: false }" class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                                    <div class="p-8">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                                            <div class="flex items-center gap-3">
                                                <span class="text-3xl">📝</span>
                                                <div>
                                                    <h4 class="text-lg font-black text-slate-900 dark:text-white">{{ $hw->title }}</h4>
                                                    <p class="text-xs text-rose-500 font-bold mt-0.5 flex items-center gap-1">
                                                        <span>📅 Limite :</span>
                                                        <span>{{ $hw->due_date->translatedFormat('d F Y à H:i') }}</span>
                                                        @if($isOverdue) <span class="bg-rose-100 text-rose-700 px-2 py-0.5 text-[9px] uppercase rounded font-black ml-1">Dépassé</span> @endif
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Status Badges Student --}}
                                            @if(Auth::user()->isStudent())
                                                @if($hasSubmitted)
                                                    @if($submission->grade !== null)
                                                        <span class="px-4 py-2 bg-emerald-50 text-emerald-600 text-xs font-black uppercase tracking-wider rounded-full border border-emerald-100">
                                                            Noté : {{ $submission->grade }}/20
                                                        </span>
                                                    @else
                                                        <span class="px-4 py-2 bg-blue-50 text-blue-600 text-xs font-black uppercase tracking-wider rounded-full border border-blue-100">
                                                            Rendu
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="px-4 py-2 {{ $isOverdue ? 'bg-rose-50 text-rose-600 border-rose-100' : 'bg-amber-50 text-amber-600 border-amber-100' }} text-xs font-black uppercase tracking-wider rounded-full border">
                                                        {{ $isOverdue ? 'Non Rendu' : 'À rendre' }}
                                                    </span>
                                                @endif
                                            @elseif(Auth::user()->isProfessor() || Auth::user()->isAdmin())
                                                <button @click="openSubmissions = !openSubmissions" class="px-5 py-2.5 bg-blue-50 hover:bg-blue-100 text-upf-blue text-xs font-black uppercase tracking-widest rounded-xl transition-all border border-blue-100">
                                                    Soumissions ({{ $hw->submissions->count() }})
                                                </button>
                                            @endif
                                        </div>

                                        <p class="text-slate-700 dark:text-slate-350 text-sm font-semibold mb-5 leading-relaxed bg-slate-50 dark:bg-slate-950/30 p-4 border border-slate-100 dark:border-slate-850 rounded-2xl">
                                            {!! nl2br(e($hw->description)) !!}
                                        </p>

                                        {{-- Attachment download --}}
                                        @if($hw->attachment_path)
                                        <a href="{{ route('classroom.homework.download', $hw) }}"
                                            class="inline-flex items-center gap-2 text-xs font-black text-slate-650 bg-slate-50 border border-slate-200/50 hover:bg-slate-100 px-4 py-2.5 rounded-xl transition-all uppercase tracking-widest shadow-sm mb-5">
                                            📎 Télécharger la consigne ({{ strtoupper(pathinfo($hw->attachment_path, PATHINFO_EXTENSION)) }})
                                        </a>
                                        @endif

                                        {{-- Student upload area --}}
                                        @if(Auth::user()->isStudent() && (!$isOverdue || $hasSubmitted))
                                            <div class="pt-5 border-t border-slate-100 dark:border-slate-800">
                                                @if($hasSubmitted)
                                                    <div class="p-4 bg-emerald-500/5 border border-emerald-500/10 text-emerald-700 dark:text-emerald-450 rounded-2xl space-y-2">
                                                        <p class="text-xs font-black">✔ Devoir rendu le {{ $submission->submitted_at->format('d/m/Y à H:i') }}</p>
                                                        <a href="{{ route('classroom.submission.download', $submission) }}" class="text-[10px] uppercase font-black tracking-widest text-emerald-600 hover:text-emerald-700 block">💾 Revoir mon fichier rendu</a>

                                                        @if($submission->grade !== null)
                                                            <div class="mt-3 p-3 bg-white dark:bg-slate-900 border border-emerald-250/20 rounded-xl space-y-1">
                                                                <p class="text-xs font-black text-slate-900 dark:text-white">Note : <strong class="text-emerald-600">{{ $submission->grade }} / 20</strong></p>
                                                                @if($submission->professor_comment)
                                                                    <p class="text-[11px] text-slate-500 italic">"{{ $submission->professor_comment }}"</p>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif

                                                @if(!$submission && !$isOverdue)
                                                <form action="{{ route('classroom.submission.store', $hw) }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-end gap-3" x-data="{ fileUploaded: false }">
                                                    @csrf
                                                    <div class="flex-1 w-full space-y-1">
                                                        <label class="block text-[9px] font-black uppercase text-slate-400 tracking-wider">Votre travail (PDF / ZIP)</label>
                                                        <input type="file" name="submission_file" required @change="fileUploaded = true"
                                                            class="w-full border border-dashed border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 p-3 text-xs font-bold text-slate-500 rounded-xl">
                                                    </div>
                                                    <button type="submit" :disabled="!fileUploaded"
                                                        :class="fileUploaded ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-gray-300 cursor-not-allowed'"
                                                        class="px-6 py-3.5 text-white font-black text-xs uppercase tracking-widest rounded-xl transition-all shadow-md shrink-0 w-full sm:w-auto">
                                                        Rendre le Devoir
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Submissions List Drawer (Professor Only) --}}
                                    @if(Auth::user()->isProfessor() || Auth::user()->isAdmin())
                                    <div x-show="openSubmissions" class="border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/20 p-8 space-y-6" style="display: none;" x-transition:enter="transition ease-out duration-200">
                                        <h5 class="text-xs font-black uppercase tracking-widest text-slate-400">📄 Travaux rendus par les étudiants</h5>
                                        
                                        <div class="divide-y divide-slate-100 dark:divide-slate-850">
                                            @forelse($hw->submissions as $sub)
                                                <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-650 flex items-center justify-center text-xs font-bold">{{ strtoupper(substr($sub->student->user->name, 0, 1)) }}</div>
                                                        <div>
                                                            <p class="text-xs font-black text-slate-900 dark:text-white">{{ $sub->student->user->name }}</p>
                                                            <p class="text-[9px] text-slate-400 font-bold uppercase mt-0.5">Rendu le {{ $sub->submitted_at->format('d/m/Y H:i') }}</p>
                                                        </div>
                                                    </div>

                                                    <div class="flex flex-wrap items-center gap-3">
                                                        <a href="{{ route('classroom.submission.download', $sub) }}" class="px-3.5 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-[10px] font-black uppercase rounded-lg transition-all shadow-sm">
                                                            💾 Télécharger le fichier
                                                        </a>

                                                        @if($sub->grade !== null)
                                                            <span class="px-3.5 py-2 bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase rounded-lg border border-emerald-100">
                                                                Noté : {{ $sub->grade }}/20
                                                            </span>
                                                        @else
                                                            {{-- Grade Form --}}
                                                            <form action="{{ route('classroom.submission.grade', $sub) }}" method="POST" class="flex items-center gap-2" x-data="{ isGrading: false }">
                                                                @csrf
                                                                <button type="button" @click="isGrading = true" x-show="!isGrading" class="px-3.5 py-2 bg-upf-magenta hover:bg-pink-700 text-white text-[10px] font-black uppercase rounded-lg transition-all shadow-sm">
                                                                    💯 Noter
                                                                </button>
                                                                <div class="flex items-center gap-2" x-show="isGrading" style="display: none;">
                                                                    <input type="number" step="0.25" min="0" max="20" name="grade" required placeholder="Note"
                                                                        class="w-16 border-gray-250 dark:border-slate-800 rounded-lg p-1.5 text-xs font-bold text-slate-900 dark:bg-slate-950">
                                                                    <input type="text" name="professor_comment" placeholder="Commentaire..."
                                                                        class="border-gray-250 dark:border-slate-800 rounded-lg p-1.5 text-xs font-bold text-slate-900 dark:bg-slate-950 w-24">
                                                                    <button type="submit" class="p-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></button>
                                                                    <button type="button" @click="isGrading = false" class="p-1.5 bg-gray-200 text-gray-500 rounded-lg"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                                                                </div>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            @empty
                                                <p class="text-center py-6 text-slate-400 font-bold italic text-xs">Aucun travail rendu pour le moment.</p>
                                            @endforelse
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            @empty
                                <div class="bg-white dark:bg-slate-900 p-20 rounded-[2.5rem] text-center border border-gray-100 dark:border-slate-800 shadow-sm border-dashed">
                                    <div class="text-6xl mb-4">📚</div>
                                    <h3 class="text-lg font-black text-slate-400 mb-2">Aucun devoir publié</h3>
                                    <p class="text-sm text-slate-400 font-medium">Les travaux et devoirs s'afficheront ici au fur et à mesure.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- ==================== TAB 3 : SALON DE DISCUSSION ==================== --}}
                    <div 
                        x-show="tab === 'chat'" 
                        class="space-y-6" 
                        style="display: none;"
                        x-data="{
                            messages: [],
                            newMessage: '',
                            fileName: null,
                            loading: false,

                            async fetchMessages() {
                                const res = await fetch('{{ route('classroom.chat.messages', [$group->id, $module->id]) }}');
                                const data = await res.json();
                                // Only update if messages length or contents changed to avoid redraw flicker
                                if(JSON.stringify(this.messages) !== JSON.stringify(data)) {
                                    this.messages = data;
                                    this.scrollChat();
                                }
                            },

                            async sendMessage() {
                                if(!this.newMessage.trim() && !this.$refs.chatFile.files[0]) return;

                                const formData = new FormData();
                                formData.append('message', this.newMessage);
                                if(this.$refs.chatFile.files[0]) {
                                    formData.append('chat_file', this.$refs.chatFile.files[0]);
                                }

                                this.newMessage = '';
                                this.fileName = null;
                                this.$refs.chatFile.value = '';

                                await fetch('{{ route('classroom.chat.post', [$group->id, $module->id]) }}', {
                                    method: 'POST',
                                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                    body: formData
                                });
                                this.fetchMessages();
                            },

                            scrollChat() {
                                this.$nextTick(() => {
                                    const chatContainer = this.$refs.chatBox;
                                    if(chatContainer) {
                                        chatContainer.scrollTop = chatContainer.scrollHeight;
                                    }
                                });
                            },

                            init() {
                                this.fetchMessages();
                                // Poll every 3 seconds for instant real-time feeling
                                setInterval(() => { this.fetchMessages() }, 3000);
                            }
                        }"
                        x-init="init()"
                        x-transition:enter="transition ease-out duration-300"
                    >
                        <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-[2.5rem] overflow-hidden flex flex-col shadow-sm" style="height: 580px;">
                            <!-- Chat Header -->
                            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/20 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <h4 class="font-black text-slate-850 dark:text-white text-sm">Salon de Discussion Interactif</h4>
                                </div>
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $group->name }}</span>
                            </div>

                            <!-- Messages Container -->
                            <div x-ref="chatBox" class="flex-1 p-6 overflow-y-auto space-y-4 bg-slate-50/20 dark:bg-slate-950/10">
                                <template x-for="m in messages" :key="m.id">
                                    <div :class="m.user_id == {{ Auth::id() }} ? 'justify-end' : 'justify-start'" class="flex items-start gap-3">
                                        
                                        <!-- Avatar other -->
                                        <div x-show="m.user_id != {{ Auth::id() }}" class="w-8 h-8 rounded-xl bg-indigo-50 text-upf-blue flex items-center justify-center text-[10px] font-black flex-shrink-0 shadow-sm">
                                            <span x-text="m.user_name.substring(0, 1).toUpperCase()"></span>
                                        </div>

                                        <!-- Message Bubble -->
                                        <div :class="m.user_id == {{ Auth::id() }} ? 'bg-upf-blue text-white rounded-br-none' : 'bg-white dark:bg-slate-950 border border-slate-100 dark:border-slate-850 text-slate-800 dark:text-slate-200 rounded-bl-none'"
                                            class="p-4 rounded-2xl max-w-[80%] shadow-sm">
                                            
                                            <!-- User name if other -->
                                            <p x-show="m.user_id != {{ Auth::id() }}" class="text-[9px] font-black uppercase text-upf-magenta tracking-wide mb-1" x-text="m.user_name"></p>
                                            
                                            <!-- Content message -->
                                            <p class="text-xs font-semibold leading-relaxed break-words" x-text="m.message"></p>

                                            <!-- Shared file attachment if exists -->
                                            <div x-show="m.file_name" class="mt-2.5">
                                                <a :href="m.file_url" target="_blank"
                                                    :class="m.user_id == {{ Auth::id() }} ? 'bg-white/20 text-white hover:bg-white/30' : 'bg-slate-50 dark:bg-slate-850 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'"
                                                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all shadow-sm">
                                                    📎 <span x-text="m.file_name"></span>
                                                </a>
                                            </div>

                                            <span :class="m.user_id == {{ Auth::id() }} ? 'text-white/60' : 'text-slate-400'"
                                                class="block text-[8px] text-right font-black uppercase tracking-tighter mt-1.5" x-text="m.time"></span>
                                        </div>

                                        <!-- Avatar self -->
                                        <div x-show="m.user_id == {{ Auth::id() }}" class="w-8 h-8 rounded-xl bg-upf-magenta text-white flex items-center justify-center text-[10px] font-black flex-shrink-0 shadow-sm">
                                            <span x-text="'{{ Auth::user()->name }}'.substring(0, 1).toUpperCase()"></span>
                                        </div>

                                    </div>
                                </template>
                            </div>

                            <!-- Selected File Banner if active -->
                            <div x-show="fileName" class="px-6 py-2 bg-pink-50 dark:bg-pink-950/20 text-upf-magenta text-[10px] font-black uppercase border-t border-slate-100 dark:border-slate-850 flex items-center justify-between" style="display: none;">
                                <span x-text="'📎 Fichier sélectionné : ' + fileName"></span>
                                <button type="button" @click="fileName = null; $refs.chatFile.value = ''" class="text-rose-500 font-bold hover:underline">Supprimer</button>
                            </div>

                            <!-- Input Form -->
                            <form @submit.prevent="sendMessage()" class="p-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/30 flex items-center gap-2">
                                <label class="cursor-pointer">
                                    <div class="w-10 h-10 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400 rounded-xl flex items-center justify-center shadow-sm">
                                        📎
                                    </div>
                                    <input x-ref="chatFile" type="file" class="hidden"
                                        @change="fileName = $event.target.files[0] ? $event.target.files[0].name : null">
                                </label>
                                <input type="text" x-model="newMessage" placeholder="Écrire un message en temps réel..." required
                                    class="flex-1 border-gray-150 dark:border-slate-800 rounded-xl bg-white dark:bg-slate-950 px-4 py-3 text-xs font-semibold focus:ring-upf-blue text-slate-900 dark:text-white">
                                <button type="submit" class="w-10 h-10 bg-upf-blue hover:bg-upf-navy text-white rounded-xl hover:scale-105 transition-all shadow-md flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path></svg>
                                </button>
                            </form>
                        </div>
                    </div>

                </div>

                {{-- ===== RIGHT COLUMN (SIDEBAR) ===== --}}
                <div class="space-y-6">

                    {{-- Course Materials Library --}}
                    <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-7 border border-gray-100 dark:border-slate-800 shadow-sm">
                        <div class="flex items-center gap-2 border-b border-gray-100 dark:border-slate-800 pb-4 mb-5">
                            <span class="text-xl">📂</span>
                            <h4 class="text-sm font-black text-slate-900 dark:text-white italic">Supports de Cours</h4>
                            @php $materials = $posts->filter(fn($p) => !empty($p->file_path)); @endphp
                            <span class="ml-auto text-xs font-black text-upf-magenta bg-pink-50 dark:bg-pink-950/20 px-2 py-0.5 rounded-full">{{ $materials->count() }}</span>
                        </div>

                        <div class="space-y-3">
                            @forelse($materials as $material)
                            <div class="flex items-center gap-3 p-3 bg-gray-50/50 dark:bg-slate-950/30 border border-gray-100 dark:border-slate-850 rounded-2xl hover:bg-white dark:hover:bg-slate-900 hover:shadow-md transition-all group">
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
                                    <p class="text-xs font-black text-slate-900 dark:text-slate-200 truncate">{{ basename($material->file_path) }}</p>
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
                                <p class="text-[10px] font-black text-gray-350 uppercase tracking-widest italic">Aucun support déposé</p>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Module Info Card --}}
                    <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-7 border border-gray-100 dark:border-slate-800 shadow-sm space-y-4">
                        <h4 class="text-sm font-black text-slate-900 dark:text-white italic border-b border-gray-100 dark:border-slate-800 pb-3">ℹ️ Infos du Module</h4>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Code</span>
                                <span class="text-xs font-black text-upf-blue">{{ $module->code ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Coefficient</span>
                                <span class="text-xs font-black text-slate-900 dark:text-slate-200">{{ $module->coefficient ?? '—' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Groupe</span>
                                <span class="text-xs font-black text-slate-900 dark:text-slate-200">{{ $group->name }}</span>
                            </div>
                            @if($group->filiere)
                            <div class="flex justify-between">
                                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Filière</span>
                                <span class="text-xs font-black text-upf-magenta">{{ $group->filiere->name }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
