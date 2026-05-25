<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('chat.index') }}" class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm hover:bg-gray-50 transition-colors text-gray-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $otherUser->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 bg-[#F8FAFC]">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 flex flex-col h-[700px] overflow-hidden">
                
                <!-- Chat Header -->
                <div class="px-8 py-5 border-b border-gray-100 flex justify-between items-center bg-white z-10 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-upf-blue/10 text-upf-blue flex items-center justify-center font-black text-lg">
                            {{ strtoupper(substr($otherUser->name, 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="font-black text-gray-900 text-lg">{{ $otherUser->name }}</h3>
                            <p class="text-[10px] uppercase font-bold tracking-widest text-gray-400">
                                {{ $otherUser->isProfessor() ? 'Enseignant' : ($otherUser->isAdmin() ? 'Administration' : 'Étudiant') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Messages Area -->
                <div class="flex-1 overflow-y-auto p-8 space-y-6 bg-gray-50/50" id="messages-container">
                    @forelse($conversation->messages as $msg)
                        @if($msg->user_id === Auth::id())
                            <!-- My Message (Right) -->
                            <div class="flex justify-end">
                                <div class="max-w-[75%]">
                                    <div class="bg-upf-blue text-white rounded-[2rem] rounded-tr-none px-6 py-4 shadow-md">
                                        <p class="text-sm font-medium leading-relaxed">{!! nl2br(e($msg->content)) !!}</p>
                                    </div>
                                    <div class="flex items-center justify-end gap-1 mt-1">
                                        <span class="text-[10px] font-bold text-gray-400">{{ $msg->created_at->format('H:i') }}</span>
                                        @if($msg->read_at)
                                            <span class="text-[10px] text-upf-blue">✓✓</span>
                                        @else
                                            <span class="text-[10px] text-gray-400">✓</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Their Message (Left) -->
                            <div class="flex justify-start">
                                <div class="max-w-[75%]">
                                    <div class="bg-white border border-gray-100 text-gray-800 rounded-[2rem] rounded-tl-none px-6 py-4 shadow-sm">
                                        <p class="text-sm font-medium leading-relaxed">{!! nl2br(e($msg->content)) !!}</p>
                                    </div>
                                    <div class="flex items-center justify-start gap-1 mt-1">
                                        <span class="text-[10px] font-bold text-gray-400">{{ $msg->created_at->format('H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="text-center py-20">
                            <span class="text-5xl mb-4 block">👋</span>
                            <p class="text-gray-400 font-bold text-sm">Dites bonjour à {{ $otherUser->name }} !</p>
                        </div>
                    @endforelse
                </div>

                <!-- Message Input -->
                <div class="p-6 bg-white border-t border-gray-100">
                    <form action="{{ route('chat.store', $conversation) }}" method="POST" class="flex gap-4 items-end">
                        @csrf
                        <div class="flex-1 bg-gray-50 border border-gray-200 rounded-[1.5rem] p-2 focus-within:ring-2 focus-within:ring-upf-blue focus-within:border-upf-blue transition-all">
                            <textarea name="content" rows="1" placeholder="Écrivez votre message..." required autofocus
                                class="w-full bg-transparent border-none focus:ring-0 text-sm font-medium text-gray-800 resize-none max-h-32 py-3 px-4 custom-scrollbar" 
                                oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"></textarea>
                        </div>
                        <button type="submit" class="w-14 h-14 rounded-full bg-upf-blue text-white flex items-center justify-center hover:bg-upf-navy hover:scale-105 transition-all shadow-lg flex-shrink-0">
                            <svg class="w-6 h-6 ml-1" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #CBD5E1;
            border-radius: 20px;
        }
    </style>

    <script>
        // Scroll to bottom on load
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('messages-container');
            container.scrollTop = container.scrollHeight;
        });
    </script>
</x-app-layout>
