<div x-data="aiChat()" class="fixed bottom-6 right-6 z-50 font-sans">
    
    <!-- Bouton d'ouverture du chat -->
    <button @click="toggleChat" 
            class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white rounded-full p-4 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center focus:outline-none focus:ring-4 focus:ring-indigo-300"
            :class="{'rotate-90 scale-0 opacity-0': isOpen, 'rotate-0 scale-100 opacity-100': !isOpen}">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
    </button>

    <!-- Fenêtre du chat -->
    <div x-show="isOpen" 
         @click.away="isOpen = false"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-10 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-10 scale-95"
         class="absolute bottom-16 right-0 w-80 sm:w-96 bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-100 dark:border-slate-800 flex flex-col overflow-hidden"
         style="display: none; height: 500px;">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-4 flex justify-between items-center text-white">
            <div class="flex items-center gap-3">
                <div class="bg-white/20 p-2 rounded-full">
                    <span class="text-xl">🤖</span>
                </div>
                <div>
                    <h3 class="font-black text-sm">Smart UPF Assistant</h3>
                    <p class="text-[10px] text-indigo-100">Propulsé par LLaMA 3.3 IA</p>
                </div>
            </div>
            <button @click="isOpen = false" class="text-white hover:text-indigo-200 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Chat Area -->
        <div id="ai-chat-messages" class="flex-1 p-4 overflow-y-auto bg-slate-50 dark:bg-slate-900/50 space-y-4 text-sm">
            
            <!-- Message de bienvenue -->
            <div class="flex items-start gap-2">
                <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-lg shrink-0">🤖</div>
                <div class="bg-white dark:bg-slate-800 p-3 rounded-2xl rounded-tl-none shadow-sm text-slate-700 dark:text-slate-300 border border-slate-100 dark:border-slate-700">
                    Bonjour ! Je suis Smart UPF, votre assistant virtuel intelligent. Je connais votre dossier académique. Que puis-je faire pour vous aujourd'hui ?
                </div>
            </div>

            <!-- Messages dynamiques -->
            <template x-for="(msg, index) in messages" :key="index">
                <div class="flex items-start gap-2" :class="msg.isUser ? 'flex-row-reverse' : ''">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-lg shrink-0"
                         :class="msg.isUser ? 'bg-purple-100 dark:bg-purple-900' : 'bg-indigo-100 dark:bg-indigo-900'">
                        <span x-text="msg.isUser ? '👤' : '🤖'"></span>
                    </div>
                    <div class="p-3 rounded-2xl shadow-sm max-w-[85%] border"
                         :class="msg.isUser ? 'bg-indigo-600 text-white rounded-tr-none border-indigo-700' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-tl-none border-slate-100 dark:border-slate-700'"
                         x-html="formatMessage(msg.text)">
                    </div>
                </div>
            </template>

            <!-- Loading Indicator -->
            <div x-show="isLoading" class="flex items-start gap-2">
                <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-lg shrink-0">🤖</div>
                <div class="bg-white dark:bg-slate-800 p-3 rounded-2xl rounded-tl-none shadow-sm flex gap-1 items-center h-10 border border-slate-100 dark:border-slate-700">
                    <span class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce"></span>
                    <span class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></span>
                    <span class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay: 0.4s"></span>
                </div>
            </div>
            
            <!-- Dummy element to scroll to bottom -->
            <div id="chat-bottom"></div>
        </div>

        <!-- Input Area -->
        <div class="p-3 bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800">
            <form @submit.prevent="sendMessage" class="flex gap-2">
                <input type="text" x-model="newMessage" placeholder="Posez votre question..." 
                       class="flex-1 bg-slate-50 dark:bg-slate-800 border-0 rounded-xl px-4 py-2 focus:ring-2 focus:ring-indigo-500 text-slate-700 dark:text-slate-200"
                       :disabled="isLoading" required>
                <button type="submit" :disabled="isLoading || newMessage.trim() === ''"
                        class="bg-indigo-600 text-white rounded-xl p-2 px-4 hover:bg-indigo-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-5 h-5 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('aiChat', () => ({
            isOpen: false,
            isLoading: false,
            newMessage: '',
            messages: [],
            
            toggleChat() {
                this.isOpen = !this.isOpen;
                if(this.isOpen) {
                    setTimeout(() => this.scrollToBottom(), 100);
                }
            },
            
            async sendMessage() {
                if (this.newMessage.trim() === '') return;
                
                const userMsg = this.newMessage;
                this.messages.push({ text: userMsg, isUser: true });
                this.newMessage = '';
                this.isLoading = true;
                this.scrollToBottom();

                try {
                    const response = await fetch('{{ route("ai.chat") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ message: userMsg })
                    });
                    
                    const data = await response.json();
                    
                    this.messages.push({ 
                        text: data.reply || "Erreur de connexion à l'IA.", 
                        isUser: false 
                    });
                } catch (error) {
                    this.messages.push({ text: "Désolé, une erreur réseau s'est produite.", isUser: false });
                } finally {
                    this.isLoading = false;
                    this.scrollToBottom();
                }
            },
            
            scrollToBottom() {
                setTimeout(() => {
                    const chat = document.getElementById('ai-chat-messages');
                    if(chat) chat.scrollTop = chat.scrollHeight;
                }, 50);
            },
            
            formatMessage(text) {
                // Remplacer les retours à la ligne par des <br>
                return text.replace(/\n/g, '<br>');
            }
        }));
    });
</script>
