<script type="module">
    import { EmojiButton } from 'https://cdn.jsdelivr.net/npm/@joeattardi/emoji-button@4.6.4/dist/index.min.js';
    window.EmojiButton = EmojiButton;
</script>
<div x-data="{
    isOpen: false,
    isLoading: false,
    currentView: 'users',
    users: [],
    activeUser: null,
    messages: [],
    newMessage: '',
    currentUserId: '{{ auth()->id() }}',
    refreshInterval: null,
    errorMessage: null,
    routes: {
        users: '{{ route('chat.users') }}',
        send: '{{ route('chat.send') }}',
        messages: '{{ route('chat.messages', ['user' => '__USER_ID__']) }}' 
    },
    csrf: '{{ csrf_token() }}',

    get totalUnread() {
        return this.users.reduce((sum, user) => sum + (user.unread_count || 0), 0);
    },

    picker: null,
    
    init() {
        console.log('Inline Chat Widget Initialized');
        // Initial fetch
        this.fetchUsers();
        
        // Global Polling Service (runs everywhere)
        // Polls users list (stats) or messages depending on view
        this.startPolling();
        
        window.addEventListener('beforeunload', () => {
            this.stopPolling();
        });
    },

    togglePicker() {
        // Lazy initialize if not ready yet (handles async script loading)
        if (!this.picker && typeof window.EmojiButton !== 'undefined') {
             this.picker = new window.EmojiButton({
                position: 'top-start',
                theme: 'auto',
                autoHide: false,
                zIndex: 10000
            });

            this.picker.on('emoji', selection => {
                this.newMessage += selection.emoji;
                this.$refs.chatInput.focus();
            });
        }

        if (this.picker) {
            this.picker.togglePicker(document.querySelector('#emoji-trigger'));
        } else {
            console.warn('EmojiButton library not loaded yet.');
        }
    },

    startPolling() {
        this.stopPolling();
        this.refreshInterval = setInterval(() => {
            if (this.isOpen && this.currentView === 'chat') {
                this.fetchMessages(true); // Poll messages if chat is open
            } else {
                this.fetchUsers(true); // Poll user list (for unread counts) otherwise
            }
        }, 4000);
    },

    stopPolling() {
        if(this.refreshInterval) clearInterval(this.refreshInterval);
    },

    toggleChat() {
        this.isOpen = !this.isOpen;
        if (this.isOpen) {
            this.fetchUsers();
            this.$nextTick(() => {
                this.scrollToBottom();
            });
        }
    },

    fetchUsers(silent = false) {
        if(!silent) {
            this.isLoading = true;
            this.errorMessage = null; 
        }
        
        fetch(this.routes.users, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
            .then(res => {
                if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
                return res.json();
            })
            .then(data => {
                this.users = data;
                this.isLoading = false;
                this.errorMessage = null;
            })
            .catch(err => {
                console.error('Error fetching users:', err);
                this.isLoading = false;
                if(!silent) this.errorMessage = 'Failed to load users.';
            });
    },

    openChat(user) {
        this.activeUser = user;
        this.currentView = 'chat';
        this.messages = [];
        this.fetchMessages(); // Immediate fetch
        this.scrollToBottom();
        // Polling will automatically switch to messages in next tick
    },

    showUserList() {
        this.currentView = 'users';
        this.activeUser = null;
        this.fetchUsers(); // Refresh counts immediately
    },

    fetchMessages(silent = false) {
        if(!this.activeUser) return;
        
        // Replace placeholder with actual ID
        const url = this.routes.messages.replace('__USER_ID__', this.activeUser.id);
        
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
            .then(res => {
                if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
                return res.json();
            })
            .then(data => {
                const shouldScroll = (data.length > this.messages.length) || (this.messages.length === 0);
                this.messages = data;
                
                // If new message came in while reading, only scroll if we were arguably at bottom
                // For simplicity, we scroll if new message count changed significantly
                if(shouldScroll) {
                    this.$nextTick(() => this.scrollToBottom());
                }
            })
            .catch(err => {
                console.error('Error fetching messages:', err);
            });
    },

    sendMessage() {
        if(!this.newMessage.trim()) return;

        const payload = {
            receiver_id: this.activeUser.id,
            message: this.newMessage
        };

        // Optimistic UI
        const tempMsg = {
            id: 'temp-' + Date.now(),
            sender_id: this.currentUserId,
            message: this.newMessage,
            created_at: new Date().toISOString(),
            is_sending: true
        };
        this.messages.push(tempMsg);
        this.scrollToBottom();
        const msgText = this.newMessage;
        this.newMessage = '';

        fetch(this.routes.send, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.csrf,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            // Replace temp message or just re-fetch
            const idx = this.messages.findIndex(m => m.id === tempMsg.id);
            if(idx !== -1) {
                this.messages[idx] = data; // Update with real data
            } else {
                 this.fetchMessages(true);
            }
        })
        .catch(err => {
            console.error('Error sending message:', err);
            // Revert on error (optional) or show alert
            alert('Failed to send message');
            this.newMessage = msgText; // Restore text
        });
    },

    scrollToBottom() {
        const container = document.getElementById('chat-messages');
        if(container) {
            container.scrollTop = container.scrollHeight;
        }
    },

    formatTime(isoString) {
        if(!isoString) return '';
        const date = new Date(isoString);
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }
}" 
class="fixed bottom-4 right-4 z-[9999]" 
style="position: fixed; bottom: 20px; right: 20px; z-index: 9999; display: block;">

    <!-- Chat Toggle Button -->
    <button @click="toggleChat()" 
            style="color: white; border-radius: 9999px; width: 60px; height: 60px; padding: 0; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: transform 0.2s;"
            class="bg-blue-600 hover:bg-blue-700 text-white rounded-full p-4 shadow-lg flex items-center justify-center hover:scale-110 relative">
        
        <!-- Unread Badge (Global) -->
        <span x-show="totalUnread > 0" 
              x-text="totalUnread"
              class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-6 w-6 flex items-center justify-center border-2 border-white"
              style="position: absolute; top: -5px; right: -5px; background-color: #ef4444; color: white; font-size: 0.75rem; font-weight: bold; border-radius: 9999px; height: 1.5rem; width: 1.5rem; display: flex; align-items: center; justify-content: center; border: 2px solid white;">
        </span>

        <svg x-show="!isOpen" class="w-6 h-6" style="width: 28px; height: 28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
        <svg x-show="isOpen" class="w-6 h-6" style="width: 28px; height: 28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
    </button>

    <!-- Chat Panel -->
    <div x-show="isOpen" 
         style="position: absolute; bottom: 70px; right: 0; width: 350px; height: 500px; border-radius: 0.5rem; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); overflow: hidden; display: flex; flex-direction: column;"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-20 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-20 scale-95"
         class="absolute bottom-16 right-0 w-80 md:w-96 h-[500px] bg-white dark:bg-gray-800 rounded-lg shadow-2xl overflow-hidden border border-gray-200 dark:border-gray-700 flex flex-col">
        
        <!-- Header -->
        <div class="bg-blue-600 p-4 text-white flex justify-between items-center shadow" style="padding: 1rem; color: white; display: flex; justify-content: space-between; align-items: center;">
            <h3 class="font-bold flex items-center" style="font-weight: 700; display: flex; align-items: center;">
                <button x-show="currentView === 'chat'" @click="showUserList()" class="mr-2 hover:bg-blue-700 rounded-full p-1" style="margin-right: 0.5rem; background: none; border: none; cursor: pointer; color: white;">
                    <svg class="w-5 h-5" style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <span x-text="currentView === 'users' ? 'Team Chat' : activeUser.name"></span>
            </h3>
        </div>

        <!-- User List View -->
        <div x-show="currentView === 'users'" class="flex-1 overflow-y-auto p-2 space-y-2" style="flex: 1; overflow-y: auto; padding: 0.5rem;">
            <template x-if="isLoading && users.length === 0">
                 <div class="flex justify-center p-4 text-gray-500 dark:text-gray-400" style="text-align: center; color: #6b7280; padding: 1rem;">Loading users...</div>
            </template>
            
            <template x-if="errorMessage">
                <div class="flex flex-col items-center justify-center p-4 text-red-500">
                    <span x-text="errorMessage"></span>
                    <button @click="fetchUsers()" class="mt-2 text-sm text-blue-600 hover:underline">Retry</button>
                </div>
            </template>

            <template x-for="user in users" :key="user.id">
                <div @click="openChat(user)" class="p-3 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-lg cursor-pointer flex items-center transition justify-between" style="padding: 0.75rem; margin-bottom: 0.5rem; border-radius: 0.5rem; cursor: pointer; display: flex; align-items: center; justify-content: space-between;">
                     <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 font-bold mr-3 relative" style="width: 2.5rem; height: 2.5rem; border-radius: 9999px; background-color: #d1d5db; display: flex; align-items: center; justify-content: center; margin-right: 0.75rem;">
                            <span x-text="user.name.charAt(0).toUpperCase()"></span>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-800 dark:text-gray-200" x-text="user.name"></div>
                            <div class="text-xs text-gray-500 dark:text-gray-400" x-text="user.email"></div>
                        </div>
                     </div>
                     <!-- Unread Badge (Per User) -->
                     <div x-show="user.unread_count > 0" 
                          x-text="user.unread_count"
                          class="bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center"
                          style="background-color: #ef4444; color: white; font-size: 0.75rem; font-weight: bold; border-radius: 9999px; height: 1.25rem; width: 1.25rem; display: flex; align-items: center; justify-content: center;">
                     </div>
                </div>
            </template>
            <div x-show="users.length === 0 && !isLoading && !errorMessage" class="text-center text-gray-500 dark:text-gray-400 mt-4" style="text-align: center; color: #6b7280; margin-top: 1rem;">No other users found.</div>
        </div>

        <!-- Chat View -->
        <div x-show="currentView === 'chat'" class="flex-1 flex flex-col min-h-0 overflow-hidden" style="flex: 1; display: flex; flex-direction: column; min-height: 0; overflow: hidden;">
            <!-- Messages Area -->
            <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50 dark:bg-gray-900" style="flex: 1; overflow-y: auto; padding: 1rem;">
                <template x-for="msg in messages" :key="msg.id || msg.temp_id">
                    <div class="flex w-full" 
                         :class="msg.sender_id == currentUserId ? 'justify-end' : 'justify-start'">
                        
                        <div class="flex flex-col max-w-[75%]" 
                             :class="msg.sender_id == currentUserId ? 'items-end' : 'items-start'">
                            
                            <div class="px-4 py-2 rounded-2xl shadow-sm text-sm break-words relative group"
                                 :class="{
                                    'bg-blue-600 text-white rounded-br-none': msg.sender_id == currentUserId, 
                                    'bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 border border-gray-200 dark:border-gray-600 rounded-bl-none': msg.sender_id != currentUserId,
                                    'opacity-70': msg.is_sending
                                 }">
                                <p x-text="msg.message" class="leading-relaxed"></p>
                            </div>
                            
                            <span class="text-[10px] text-gray-400 mt-1 px-1" x-text="formatTime(msg.created_at)"></span>
                        </div>
                    </div>
                </template>
                
                <div x-show="messages.length === 0" class="flex flex-col items-center justify-center h-full text-gray-400">
                    <svg class="w-12 h-12 mb-2 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    <span class="text-sm">No messages yet</span>
                    <span class="text-xs">Say hello! 👋</span>
                </div>
            </div>

            <!-- Input Area -->
            <div class="p-3 bg-white dark:bg-gray-800 border-t dark:border-gray-700 shrink-0 z-10" style="padding: 0.75rem; position: relative; z-index: 10;">
                <form @submit.prevent="sendMessage" class="flex gap-2 items-center" style="display: flex; gap: 0.5rem; align-items: center;">
                    <!-- Emoji Trigger -->
                    <button type="button" @click="togglePicker()" id="emoji-trigger" 
                            class="text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 p-1 transition"
                            style="background: none; border: none; cursor: pointer; color: #6b7280;">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </button>
                    
                    <input type="text" x-model="newMessage" x-ref="chatInput" placeholder="Type a message..." 
                           class="flex-1 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-full focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm py-2 px-4 shadow-sm outline-none" 
                           style="flex: 1; border: 1px solid #d1d5db; border-radius: 9999px; font-size: 0.875rem; padding: 0.5rem 1rem; outline: none;"
                           required>
                    <button type="submit" 
                            class="bg-blue-600 text-white rounded-full p-2 hover:bg-blue-700 shadow-md transition disabled:opacity-50 hover:scale-110 active:scale-95" 
                            style="background-color: #2563eb; color: white; border-radius: 9999px; padding: 0.5rem; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;"
                            :disabled="!newMessage.trim()">
                        <svg class="w-5 h-5 transform rotate-90" style="width: 20px; height: 20px; transform: rotate(90deg);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
