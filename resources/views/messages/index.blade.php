<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Messagerie Interne') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden flex flex-col md:flex-row h-[700px]">
                
                <!-- Sidebar: Conversations List -->
                <div class="w-full md:w-1/3 bg-gray-50 border-r border-gray-100 flex flex-col h-full">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="font-black text-xl text-gray-900 italic mb-4">Boîte de réception</h3>
                        <!-- Optional Search could go here -->
                    </div>
                    <div class="flex-1 overflow-y-auto">
                        @forelse($conversations as $conv)
                            @php
                                $otherUser = $conv->user_one_id === Auth::id() ? $conv->userTwo : $conv->userOne;
                                $lastMessage = $conv->messages->first();
                                $hasUnread = $conv->messages()->where('user_id', '!=', Auth::id())->whereNull('read_at')->exists();
                            @endphp
                            <a href="{{ route('chat.show', $conv) }}" class="block p-5 border-b border-gray-100 hover:bg-white transition-colors {{ $hasUnread ? 'bg-indigo-50/50' : '' }}">
                                <div class="flex items-center gap-4">
                                    <div class="relative">
                                        <div class="w-12 h-12 rounded-2xl bg-upf-blue/10 text-upf-blue flex items-center justify-center font-black text-lg shadow-sm">
                                            {{ strtoupper(substr($otherUser->name, 0, 1)) }}
                                        </div>
                                        @if($hasUnread)
                                        <span class="absolute -top-1 -right-1 w-3.5 h-3.5 bg-upf-magenta border-2 border-white rounded-full"></span>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex justify-between items-center mb-1">
                                            <h4 class="font-black text-gray-900 text-sm truncate {{ $hasUnread ? 'text-upf-blue' : '' }}">{{ $otherUser->name }}</h4>
                                            @if($lastMessage)
                                            <span class="text-[9px] font-bold text-gray-400 uppercase">{{ $lastMessage->created_at->shortAbsoluteDiffForHumans() }}</span>
                                            @endif
                                        </div>
                                        @if($lastMessage)
                                            <p class="text-xs truncate {{ $hasUnread ? 'font-bold text-gray-800' : 'font-medium text-gray-500' }}">
                                                {{ $lastMessage->user_id === Auth::id() ? 'Vous: ' : '' }}{{ $lastMessage->content }}
                                            </p>
                                        @else
                                            <p class="text-xs italic text-gray-400">Nouvelle conversation</p>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="p-8 text-center text-gray-400">
                                <div class="text-4xl mb-4">📭</div>
                                <p class="text-xs font-bold uppercase tracking-widest italic">Aucune conversation</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Main area: Empty state -->
                <div class="hidden md:flex flex-1 flex-col items-center justify-center bg-white p-8 text-center">
                    <div class="w-32 h-32 bg-indigo-50 rounded-full flex items-center justify-center mb-6">
                        <span class="text-6xl">💬</span>
                    </div>
                    <h2 class="text-2xl font-black text-gray-900 italic mb-2">Sélectionnez une conversation</h2>
                    <p class="text-gray-500 text-sm max-w-sm">Choisissez une conversation dans la liste de gauche pour afficher les messages ou en envoyer un nouveau.</p>
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>
