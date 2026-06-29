@extends('layouts.admin')
@section('title', 'Quản lý Live Chat')

@section('content')
<div class="container-fluid py-4" x-data="chatAdmin()">
    <h3 class="mb-4">Hỗ trợ khách hàng trực tuyến</h3>
    
    <div class="row">
        <!-- Danh sách hội thoại -->
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Danh sách Chat</h5>
                </div>
                <div class="card-body p-0" style="height: 600px; overflow-y: auto;">
                    <div class="list-group list-group-flush">
                        <template x-for="conv in conversations" :key="conv.id">
                            <button class="list-group-item list-group-item-action p-3" 
                                    :class="{'active': currentConv && currentConv.id == conv.id}"
                                    @click="selectConversation(conv)">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong x-text="conv.user ? conv.user.name : 'Khách'"></strong>
                                    <small x-text="formatTime(conv.updated_at)"></small>
                                </div>
                                <div class="small text-muted text-truncate" x-text="conv.messages.length > 0 ? conv.messages[0].message : 'Chưa có tin nhắn'"></div>
                            </button>
                        </template>
                        <div x-show="conversations.length === 0" class="p-4 text-center text-muted">
                            Không có cuộc trò chuyện nào.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Khung chat chi tiết -->
        <div class="col-md-8">
            <div class="card shadow-sm h-100" x-show="currentConv">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        Chat với <span x-text="currentConv ? currentConv.user.name : ''" class="text-primary"></span>
                        <template x-if="currentConv && currentConv.booking_id">
                            <span class="badge bg-info ms-2">Booking #<span x-text="currentConv.booking_id"></span></span>
                        </template>
                    </h5>
                </div>
                <div class="card-body p-4 d-flex flex-column" style="height: 600px; background-color: #f8f9fa;">
                    <!-- Messages Container -->
                    <div class="chat-messages flex-grow-1" id="admin-chat-messages" style="overflow-y: auto; margin-bottom: 20px;">
                        <template x-for="msg in messages" :key="msg.id">
                            <div class="mb-3 d-flex" :class="msg.sender_id == adminId ? 'justify-content-end' : ''">
                                <div style="max-width: 75%;">
                                    <div class="p-3 rounded-3 shadow-sm" 
                                         :class="msg.sender_id == adminId ? 'bg-primary text-white' : 'bg-white border'">
                                        <div x-text="msg.message" x-show="msg.message"></div>
                                        <template x-if="msg.attachment_path">
                                            <div class="mt-2">
                                                <a :href="msg.attachment_path" target="_blank" 
                                                   class="btn btn-sm"
                                                   :class="msg.sender_id == adminId ? 'btn-light text-primary' : 'btn-outline-primary'">
                                                    <i class="bi bi-file-earmark-excel"></i> <span x-text="msg.attachment_name"></span>
                                                </a>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="small mt-1 text-muted" :class="msg.sender_id == adminId ? 'text-end' : ''" x-text="formatTime(msg.created_at)"></div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Input Form -->
                    <form @submit.prevent="sendMessage" class="mt-auto bg-white p-3 border rounded">
                        <div class="d-flex align-items-center gap-2">
                            <input type="text" x-model="newMessage" class="form-control" placeholder="Nhập câu trả lời..." autocomplete="off">
                            <button type="submit" class="btn btn-primary px-4" :disabled="sending">
                                <i class="bi bi-send"></i> Gửi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div x-show="!currentConv" class="h-100">
                <div class="card shadow-sm h-100 d-flex justify-content-center align-items-center bg-light">
                    <div class="text-center text-muted">
                        <i class="bi bi-chat-dots" style="font-size: 3rem;"></i>
                        <p class="mt-3">Chọn một đoạn chat để bắt đầu</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function initChatAdmin() {
        if (typeof Alpine !== 'undefined' && !Alpine.data('chatAdmin')) {
            Alpine.data('chatAdmin', () => ({
                conversations: [],
                messages: [],
                currentConv: null,
                newMessage: '',
                sending: false,
                adminId: {{ auth()->id() }},
                
                init() {
                    this.loadConversations();
                    setInterval(() => {
                        if(!this.currentConv) {
                            this.loadConversations(); // Polling fallback for list
                        }
                    }, 10000);
                },

                loadConversations() {
                    fetch('/chat/conversations')
                        .then(r => r.json())
                        .then(data => {
                            this.conversations = data;
                        });
                },

                selectConversation(conv) {
                    this.currentConv = conv;
                    this.loadMessages(conv.id);
                    this.listenToConversation(conv.id);
                },

                loadMessages(id) {
                    fetch('/chat/' + id + '/messages')
                        .then(r => r.json())
                        .then(data => {
                            this.messages = data;
                            this.scrollToBottom();
                        });
                },

                sendMessage() {
                    if (!this.newMessage.trim() || !this.currentConv) return;
                    
                    this.sending = true;
                    const formData = new FormData();
                    formData.append('message', this.newMessage);
                    
                    fetch('/chat/' + this.currentConv.id + '/send', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: formData
                    })
                    .then(r => r.json())
                    .then(msg => {
                        this.messages.push(msg);
                        this.newMessage = '';
                        this.scrollToBottom();
                        this.loadConversations(); // refresh list to update latest msg preview
                    })
                    .finally(() => {
                        this.sending = false;
                    });
                },

                listenToConversation(id) {
                    if(window.Echo) {
                        // Leave old channels
                        window.Echo.leaveAllChannels();
                        
                        window.Echo.private('conversation.' + id)
                            .listen('MessageSent', (e) => {
                                if(e.message.sender_id != this.adminId) {
                                    this.messages.push(e.message);
                                    this.scrollToBottom();
                                    this.loadConversations();
                                }
                            });
                    }
                },

                scrollToBottom() {
                    setTimeout(() => {
                        const box = document.getElementById('admin-chat-messages');
                        if(box) box.scrollTop = box.scrollHeight;
                    }, 100);
                },

                formatTime(dateString) {
                    if(!dateString) return '';
                    const date = new Date(dateString);
                    return date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) + ' ' + date.toLocaleDateString();
                }
            }));
        }
    }

    if (typeof Alpine !== 'undefined') {
        initChatAdmin();
    } else {
        document.addEventListener('alpine:init', initChatAdmin);
    }
</script>
@endpush
@endsection
