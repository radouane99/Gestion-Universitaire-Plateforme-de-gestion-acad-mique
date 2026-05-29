<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tighter italic">
                ⚙️ Simulation du Planning - Session {{ $session->name }}
            </h2>
            <a href="{{ route('admin.exams.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition-all shadow-sm flex items-center gap-2">
                <span>🔙</span> Retour aux examens
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-[#F8FAFC] min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Status Badge -->
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 flex justify-between items-center">
                <div>
                    <p class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-1">Statut actuel du planning</p>
                    <div class="flex items-center gap-3">
                        @if($session->status === 'draft')
                            <span class="px-4 py-1.5 bg-gray-100 text-gray-700 font-black rounded-full text-sm">Brouillon</span>
                        @elseif($session->status === 'simulated')
                            <span class="px-4 py-1.5 bg-blue-100 text-blue-700 font-black rounded-full text-sm">Simulé (Non Validé)</span>
                        @elseif($session->status === 'validated')
                            <span class="px-4 py-1.5 bg-green-100 text-green-700 font-black rounded-full text-sm">Validé</span>
                        @elseif($session->status === 'published')
                            <span class="px-4 py-1.5 bg-purple-100 text-purple-700 font-black rounded-full text-sm">Publié</span>
                        @endif
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    @if(in_array($session->status, ['draft', 'simulated']))
                        <form action="{{ route('admin.exams.planning.generate', $session) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-5 py-2.5 bg-upf-blue hover:bg-blue-800 text-white font-black rounded-xl transition-all shadow-lg flex items-center gap-2">
                                <span>🤖</span> Lancer la Simulation
                            </button>
                        </form>
                    @endif

                    @if($session->status === 'simulated' && $stats['total_exams'] > 0)
                        <form action="{{ route('admin.exams.planning.validate', $session) }}" method="POST" onsubmit="return confirm('Attention: Valider le planning figera les dates et heures. Voulez-vous continuer ?');">
                            @csrf
                            <button type="submit" class="px-5 py-2.5 bg-green-500 hover:bg-green-600 text-white font-black rounded-xl transition-all shadow-lg flex items-center gap-2">
                                <span>✅</span> Valider le Planning
                            </button>
                        </form>
                    @endif

                    @if($session->status === 'validated')
                        <form action="{{ route('admin.exams.planning.convocations', $session) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-black rounded-xl transition-all shadow-lg flex items-center gap-2">
                                <span>🎫</span> Générer les Convocations
                            </button>
                        </form>
                    @endif

                    @if($session->status === 'validated' && $stats['total_convocations'] > 0)
                        <form action="{{ route('admin.exams.planning.publish', $session) }}" method="POST" onsubmit="return confirm('Attention: La publication rendra le planning et les convocations visibles pour les étudiants et professeurs. Continuer ?');">
                            @csrf
                            <button type="submit" class="px-5 py-2.5 bg-purple-500 hover:bg-purple-600 text-white font-black rounded-xl transition-all shadow-lg flex items-center gap-2">
                                <span>📢</span> Publier le Planning
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Simulation Results Alert -->
            @if(session('results'))
                @php $res = session('results'); @endphp
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-green-50 border border-green-200 rounded-2xl p-6">
                        <div class="text-green-500 text-3xl mb-2">✅</div>
                        <h3 class="font-black text-green-800 text-lg">Placés avec succès</h3>
                        <p class="text-green-600 text-3xl font-black">{{ $res['planned'] }}</p>
                    </div>
                    <div class="bg-red-50 border border-red-200 rounded-2xl p-6">
                        <div class="text-red-500 text-3xl mb-2">❌</div>
                        <h3 class="font-black text-red-800 text-lg">Non placés</h3>
                        <p class="text-red-600 text-3xl font-black">{{ count($res['unplanned']) }}</p>
                    </div>
                    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6">
                        <div class="text-amber-500 text-3xl mb-2">⚠️</div>
                        <h3 class="font-black text-amber-800 text-lg">Conflits/Avertissements</h3>
                        <p class="text-amber-600 text-3xl font-black">{{ count($res['conflicts']) }}</p>
                    </div>
                </div>

                @if(count($res['unplanned']) > 0 || count($res['conflicts']) > 0)
                    <div class="bg-white rounded-[2rem] shadow-sm border border-red-100 p-8">
                        <h3 class="font-black text-xl text-red-600 mb-6 flex items-center gap-3">
                            <span>🚨</span> Rapport des problèmes
                        </h3>
                        
                        @if(count($res['unplanned']) > 0)
                            <div class="mb-6">
                                <h4 class="font-bold text-red-800 mb-3">Modules non planifiés (Manque de salle ou créneau saturé) :</h4>
                                <ul class="list-disc pl-5 space-y-1 text-red-600 text-sm font-medium">
                                    @foreach($res['unplanned'] as $unplanned)
                                        <li>{{ $unplanned }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(count($res['conflicts']) > 0)
                            <div>
                                <h4 class="font-bold text-amber-800 mb-3">Avertissements (ex: manque de surveillants) :</h4>
                                <ul class="list-disc pl-5 space-y-1 text-amber-600 text-sm font-medium">
                                    @foreach($res['conflicts'] as $conflict)
                                        <li>{{ $conflict }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @endif
            @endif

            <!-- Exams List -->
            @if($stats['total_exams'] > 0)
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <div>
                        <h3 class="text-xl font-black text-upf-blue italic tracking-tight">Examens Planifiés ({{ $stats['total_exams'] }})</h3>
                        <p class="text-sm text-gray-500 font-medium">Convocations générées : {{ $stats['total_convocations'] }}</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white border-b border-gray-100">
                                <th class="py-4 px-6 text-xs font-black text-gray-400 uppercase tracking-widest">Date & Heure</th>
                                <th class="py-4 px-6 text-xs font-black text-gray-400 uppercase tracking-widest">Module & Groupe</th>
                                <th class="py-4 px-6 text-xs font-black text-gray-400 uppercase tracking-widest">Salle</th>
                                <th class="py-4 px-6 text-xs font-black text-gray-400 uppercase tracking-widest">Surveillants</th>
                                <th class="py-4 px-6 text-xs font-black text-gray-400 uppercase tracking-widest text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($session->exams->sortBy('date') as $exam)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="py-4 px-6">
                                        <p class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($exam->date)->format('d/m/Y') }}</p>
                                        <p class="text-sm font-medium text-upf-blue">{{ $exam->start_time }} ({{ $exam->duration }} min)</p>
                                    </td>
                                    <td class="py-4 px-6">
                                        <p class="font-bold text-gray-900">{{ $exam->module->name }}</p>
                                        <p class="text-xs font-bold text-gray-500 bg-gray-100 px-2 py-1 rounded inline-block mt-1">{{ $exam->group->name }}</p>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="font-bold text-upf-magenta">{{ $exam->room->name }}</span>
                                    </td>
                                    <td class="py-4 px-6">
                                        @if($exam->proctors->count() > 0)
                                            <div class="flex -space-x-2">
                                                @foreach($exam->proctors as $proctor)
                                                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs border-2 border-white" title="{{ $proctor->user->name }}">
                                                        {{ substr($proctor->user->name, 0, 1) }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-xs font-bold text-red-500 bg-red-50 px-2 py-1 rounded">Aucun</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <a href="{{ route('admin.exams.display_list.show', $exam) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-xl transition-colors inline-block" title="Liste d'affichage">
                                            📋
                                        </a>
                                        @if(in_array($session->status, ['draft', 'simulated']))
                                            <a href="{{ route('admin.exams.edit', $exam) }}" class="p-2 text-amber-600 hover:bg-amber-50 rounded-xl transition-colors inline-block" title="Modifier">
                                                ✏️
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
