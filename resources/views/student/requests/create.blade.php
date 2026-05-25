<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Administrative Request') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <div class="bg-gradient-to-br from-upf-navy to-upf-blue rounded-3xl p-10 text-white shadow-2xl relative overflow-hidden">
                <div class="relative z-10">
                    <h2 class="text-3xl font-black mb-2 italic">Digital Secretariat</h2>
                    <p class="text-blue-100 opacity-80">Request official documents, certificates, and academic transcripts in one click.</p>
                </div>
                <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-upf-magenta/10 rounded-full blur-3xl"></div>
            </div>

            @if(session('success'))
                <div class="p-4 text-sm text-emerald-800 rounded-2xl bg-emerald-50 border border-emerald-100 font-bold">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Submission Form -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden lg:col-span-1">
                    <div class="p-8 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-xl font-black text-gray-900 italic">Submission Form</h3>
                        <p class="text-gray-500 text-sm">Select the document type you wish to request.</p>
                    </div>

                    <form action="{{ route('student.requests.store') }}" method="POST" class="p-8 space-y-6">
                        @csrf
                        
                        <div class="space-y-4">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Document Category</label>
                            <div class="grid grid-cols-1 gap-3">
                                @foreach(['Attestation de Scolarité' => 'Attestation de Scolarité', 'Relevé de Notes' => 'Relevé de Notes', 'Convention de Stage' => 'Convention de Stage'] as $value => $label)
                                <label class="relative flex items-center p-4 border-2 border-gray-100 rounded-2xl cursor-pointer hover:border-upf-blue transition-all group">
                                    <input type="radio" name="type" value="{{ $value }}" class="sr-only peer" required>
                                    <div class="w-5 h-5 border-2 border-gray-200 rounded-full mr-3 flex items-center justify-center transition-all peer-checked:border-upf-blue peer-checked:bg-upf-blue">
                                        <div class="w-2 h-2 bg-white rounded-full"></div>
                                    </div>
                                    <div>
                                        <p class="font-extrabold text-xs text-gray-900 group-hover:text-upf-blue transition-colors">{{ $label }}</p>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="reason" class="text-[10px] font-black uppercase tracking-widest text-gray-400 block">Additional Notes (Optional)</label>
                            <textarea name="reason" id="reason" rows="4" 
                                class="w-full border-gray-200 rounded-2xl focus:ring-upf-blue focus:border-upf-blue p-4 font-bold text-gray-900 bg-gray-50 text-xs"
                                placeholder="Specify any specific academic year or details..."></textarea>
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="w-full py-4 bg-upf-blue text-white rounded-2xl font-black shadow-lg hover:bg-upf-navy transition-all flex items-center justify-center space-x-2 text-xs uppercase tracking-widest">
                                <span>Submit Request</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- History -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden lg:col-span-2">
                    <div class="p-8 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-xl font-black text-gray-900 italic">Mes Demandes Soumises</h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/70 border-b border-gray-100">
                                    <th class="p-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Date</th>
                                    <th class="p-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Document</th>
                                    <th class="p-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Statut</th>
                                    <th class="p-6 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 font-bold text-gray-700">
                                @forelse($requests as $req)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="p-6 text-sm text-gray-500">
                                            {{ $req->created_at->format('d/m/Y') }}
                                        </td>
                                        <td class="p-6">
                                            <div class="text-gray-900 text-sm font-black">{{ $req->type }}</div>
                                            @if($req->reason)
                                                <div class="text-[10px] text-gray-400 font-semibold mt-1">Note: {{ $req->reason }}</div>
                                            @endif
                                        </td>
                                        <td class="p-6">
                                            <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider
                                                {{ $req->status === 'approved' ? 'bg-emerald-50 text-emerald-600' : '' }}
                                                {{ $req->status === 'pending' ? 'bg-amber-50 text-amber-600' : '' }}
                                                {{ $req->status === 'rejected' ? 'bg-rose-50 text-rose-600' : '' }}
                                            ">
                                                {{ $req->status === 'approved' ? 'Approuvée' : ($req->status === 'pending' ? 'En Attente' : 'Refusée') }}
                                            </span>
                                            @if($req->status === 'rejected' && $req->reason)
                                                <div class="text-[10px] text-rose-400 font-bold mt-1">Motif: {{ $req->reason }}</div>
                                            @endif
                                        </td>
                                        <td class="p-6 text-right">
                                            @if($req->status === 'approved')
                                                <a href="{{ route('admin.requests.show', $req->id) }}" target="_blank" class="inline-flex items-center text-upf-blue hover:text-upf-navy font-black text-xs uppercase tracking-widest gap-1 bg-indigo-50 px-3 py-2 rounded-xl">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                    Imprimer PDF
                                                </a>
                                            @else
                                                <span class="text-xs text-gray-400 italic">Indisponible</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="p-16 text-center text-gray-400 italic">
                                            Aucune demande soumise pour le moment.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
