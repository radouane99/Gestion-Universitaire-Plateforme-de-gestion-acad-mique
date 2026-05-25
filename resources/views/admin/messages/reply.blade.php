<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Répondre au Message') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900">
                    
                    <div class="mb-6">
                        <a href="{{ route('admin.messages.index') }}" class="inline-flex items-center text-sm font-bold text-gray-500 hover:text-upf-blue transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            Retour à la boîte de réception
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="mb-6 bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-xl flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-6 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Original Message Box -->
                    <div class="mb-8 p-6 bg-slate-50 rounded-2xl border border-gray-100">
                        <h3 class="text-sm font-black uppercase tracking-widest text-gray-400 mb-4">Message Original</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm mb-4">
                            <div>
                                <span class="font-bold text-gray-700">De :</span> {{ $message->name }} ({{ $message->email }})
                            </div>
                            <div>
                                <span class="font-bold text-gray-700">Reçu le :</span> {{ $message->created_at->format('d/m/Y H:i') }}
                            </div>
                            <div class="col-span-2">
                                <span class="font-bold text-gray-700">Sujet :</span> {{ $message->subject }}
                            </div>
                        </div>
                        <div class="p-4 bg-white rounded-xl border border-gray-100 text-sm text-gray-600 italic">
                            {{ $message->message }}
                        </div>
                    </div>

                    <!-- Reply Form -->
                    <form action="{{ route('admin.messages.send-reply', $message) }}" method="POST" class="space-y-6">
                        @csrf
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Votre Réponse</label>
                            <textarea name="reply_text" rows="8" required class="w-full px-5 py-4 bg-slate-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-upf-magenta focus:border-upf-magenta transition-colors resize-none" placeholder="Écrivez votre réponse ici..."></textarea>
                            @error('reply_text') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit" class="inline-flex items-center px-8 py-4 bg-upf-blue text-white rounded-2xl font-black shadow-xl hover:bg-upf-navy transition-all transform hover:-translate-y-1">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                            Envoyer la réponse par Email
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
