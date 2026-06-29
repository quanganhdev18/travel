@auth
<div id="live-chatbox" class="live-chatbox shadow-lg rounded-top-3">
    <div class="chatbox-header bg-primary text-white p-3 rounded-top-3 d-flex justify-content-between align-items-center" style="cursor: pointer;" onclick="toggleChatbox()">
        <h6 class="mb-0"><i class="bi bi-chat-dots me-2"></i>Hỗ trợ trực tuyến</h6>
        <i class="bi bi-chevron-up" id="chatbox-toggle-icon"></i>
    </div>
    
    <div class="chatbox-body bg-white" id="chatbox-body" style="display: none; height: 350px; flex-direction: column;">
        <div class="chat-messages p-3" id="chat-messages" style="flex: 1; overflow-y: auto; background-color: #f8f9fa;">
            <!-- Messages will be injected here -->
            <div class="text-center text-muted small my-2">Bắt đầu cuộc trò chuyện với CSKH</div>
        </div>
        
        <div class="chat-input p-2 border-top bg-white">
            <div class="d-flex align-items-center mb-2">
                <a href="{{ asset('downloads/danh_sach_hanh_khach_template.csv') }}" class="btn btn-sm btn-outline-success w-100" download>
                    <i class="bi bi-file-earmark-excel"></i> Tải template hành khách
                </a>
            </div>
            <form id="chat-form" onsubmit="sendChatMessage(event)" class="d-flex align-items-center gap-2">
                <input type="file" id="chat-attachment" class="d-none" accept=".xlsx,.xls,.csv" onchange="updateFileName()">
                <button type="button" class="btn btn-light border" onclick="document.getElementById('chat-attachment').click()">
                    <i class="bi bi-paperclip text-secondary"></i>
                </button>
                <input type="text" id="chat-message-input" class="form-control" placeholder="Nhập tin nhắn..." autocomplete="off">
                <button type="submit" class="btn btn-primary" id="btn-send-msg">
                    <i class="bi bi-send"></i>
                </button>
            </form>
            <div id="file-name-display" class="small text-muted mt-1" style="display: none;"></div>
        </div>
    </div>
</div>

<style>
.live-chatbox {
    position: fixed;
    bottom: 0;
    right: 20px;
    width: 320px;
    z-index: 1050;
    transition: all 0.3s ease;
}
.chat-msg {
    margin-bottom: 10px;
    max-width: 85%;
    clear: both;
}
.chat-msg.sent {
    float: right;
}
.chat-msg.received {
    float: left;
}
.msg-bubble {
    padding: 8px 12px;
    border-radius: 15px;
    font-size: 0.9rem;
    word-wrap: break-word;
}
.chat-msg.sent .msg-bubble {
    background-color: var(--primary-color);
    color: white;
    border-bottom-right-radius: 0;
}
.chat-msg.received .msg-bubble {
    background-color: #e9ecef;
    color: #212529;
    border-bottom-left-radius: 0;
}
.msg-time {
    font-size: 0.7rem;
    color: #6c757d;
    margin-top: 2px;
}
.chat-msg.sent .msg-time {
    text-align: right;
}
</style>

<script>
    let chatConversationId = null;

    function toggleChatbox() {
        const body = document.getElementById('chatbox-body');
        const icon = document.getElementById('chatbox-toggle-icon');
        if (body.style.display === 'none') {
            body.style.display = 'flex';
            icon.classList.remove('bi-chevron-up');
            icon.classList.add('bi-chevron-down');
            
            if(!chatConversationId) {
                initConversation();
            } else {
                scrollToBottom();
            }
        } else {
            body.style.display = 'none';
            icon.classList.remove('bi-chevron-down');
            icon.classList.add('bi-chevron-up');
        }
    }

    function updateFileName() {
        const fileInput = document.getElementById('chat-attachment');
        const display = document.getElementById('file-name-display');
        if(fileInput.files.length > 0) {
            display.textContent = 'Đính kèm: ' + fileInput.files[0].name;
            display.style.display = 'block';
        } else {
            display.style.display = 'none';
        }
    }

    function scrollToBottom() {
        const box = document.getElementById('chat-messages');
        box.scrollTop = box.scrollHeight;
    }

    function initConversation() {
        fetch('/chat/start', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            chatConversationId = data.id;
            loadMessages();
            listenToChat();
        });
    }

    function loadMessages() {
        fetch('/chat/'+chatConversationId+'/messages')
        .then(res => res.json())
        .then(messages => {
            const container = document.getElementById('chat-messages');
            container.innerHTML = '';
            messages.forEach(msg => appendMessage(msg));
            scrollToBottom();
        });
    }

    function appendMessage(msg) {
        const container = document.getElementById('chat-messages');
        const isSent = msg.sender_id == {{ auth()->id() ?? 'null' }};
        
        const div = document.createElement('div');
        div.className = "chat-msg " + (isSent ? "sent" : "received");
        
        let contentHtml = msg.message ? "<div>"+msg.message+"</div>" : '';
        if (msg.attachment_path) {
            contentHtml += '<div class="mt-1"><a href="'+msg.attachment_path+'" target="_blank" class="'+(isSent ? 'text-white text-decoration-underline' : 'text-primary')+'"><i class="bi bi-file-earmark"></i> '+msg.attachment_name+'</a></div>';
        }

        const date = new Date(msg.created_at);
        const timeStr = date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});

        div.innerHTML = '<div class="msg-bubble shadow-sm">' + contentHtml + '</div><div class="msg-time">' + timeStr + '</div>';
        container.appendChild(div);
        
        // Clear float
        const clear = document.createElement('div');
        clear.style.clear = 'both';
        container.appendChild(clear);
    }

    function sendChatMessage(e) {
        e.preventDefault();
        const input = document.getElementById('chat-message-input');
        const fileInput = document.getElementById('chat-attachment');
        
        const text = input.value.trim();
        const file = fileInput.files.length > 0 ? fileInput.files[0] : null;

        if (!text && !file) return;

        const formData = new FormData();
        if(text) formData.append('message', text);
        if(file) formData.append('attachment', file);

        document.getElementById('btn-send-msg').disabled = true;

        fetch('/chat/'+chatConversationId+'/send', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(res => res.json())
        .then(msg => {
            input.value = '';
            fileInput.value = '';
            updateFileName();
            appendMessage(msg);
            scrollToBottom();
        })
        .finally(() => {
            document.getElementById('btn-send-msg').disabled = false;
        });
    }

    function listenToChat() {
        if(window.Echo) {
            window.Echo.private('conversation.'+chatConversationId)
                .listen('MessageSent', (e) => {
                    // if message is not from me
                    if(e.message.sender_id != {{ auth()->id() ?? 'null' }}) {
                        appendMessage(e.message);
                        scrollToBottom();
                        // Optional: auto show chatbox if hidden
                        const body = document.getElementById('chatbox-body');
                        if (body.style.display === 'none') {
                            toggleChatbox();
                        }
                    }
                });
        }
    }
</script>
@endauth
