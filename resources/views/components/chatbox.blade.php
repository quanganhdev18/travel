@auth
<div id="live-chatbox" class="live-chatbox">
    <!-- Bubble Button -->
    <div id="chatbox-bubble" class="chatbox-bubble shadow" onclick="toggleChatbox()">
        <i class="bi bi-chat-dots-fill"></i>
    </div>
    
    <!-- Chat Panel -->
    <div id="chatbox-panel" class="chatbox-panel shadow-lg rounded-4" style="display: none;">
        <div class="chatbox-body bg-white position-relative rounded-4" id="chatbox-body" style="height: 350px; flex-direction: column; display: flex;">
            <!-- Close Button -->
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3 shadow-none z-3" aria-label="Close" onclick="toggleChatbox()" style="background-color: rgba(255,255,255,0.8); border-radius: 50%; padding: 0.5rem;"></button>
            
            <div class="chat-messages p-3 pt-5" id="chat-messages" style="flex: 1; overflow-y: auto; background-color: #f8f9fa;">
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
</div>

<style>
.live-chatbox {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1050;
}
.chatbox-bubble {
    width: 60px;
    height: 60px;
    background-color: var(--primary-color, #0d6efd);
    color: white;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 24px;
    cursor: pointer;
    transition: transform 0.3s ease;
}
.chatbox-bubble:hover {
    transform: scale(1.1);
}
.chatbox-panel {
    position: absolute;
    bottom: 70px;
    right: 0;
    width: 320px;
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.1);
    overflow: hidden;
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
    position: relative;
}
.chat-msg.sent .msg-bubble {
    background-color: var(--primary-color, #0d6efd);
    color: white;
    border-bottom-right-radius: 0;
}
.chat-msg.received .msg-bubble {
    background-color: #e9ecef;
    color: #212529;
    border-bottom-left-radius: 0;
}
.msg-star-btn {
    position: absolute;
    top: -8px;
    cursor: pointer;
    background: white;
    border-radius: 50%;
    padding: 1px 4px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    font-size: 0.8rem;
}
.chat-msg.sent .msg-star-btn {
    left: -10px;
}
.chat-msg.received .msg-star-btn {
    right: -10px;
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
        const panel = document.getElementById('chatbox-panel');
        if (panel.style.display === 'none') {
            panel.style.display = 'flex';
            
            if(!chatConversationId) {
                initConversation();
            } else {
                scrollToBottom();
            }
        } else {
            panel.style.display = 'none';
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
            let isImage = msg.attachment_path.match(/\.(jpeg|jpg|gif|png)$/i) != null;
            if(isImage) {
                contentHtml += '<div class="mt-1"><a href="'+msg.attachment_path+'" target="_blank"><img src="'+msg.attachment_path+'" class="img-fluid rounded" style="max-height:150px;"></a></div>';
            } else {
                contentHtml += '<div class="mt-1"><a href="'+msg.attachment_path+'" target="_blank" class="'+(isSent ? 'text-white text-decoration-underline' : 'text-primary')+'"><i class="bi bi-file-earmark-arrow-down"></i> '+msg.attachment_name+'</a></div>';
            }
        }

        const date = new Date(msg.created_at);
        const timeStr = date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        
        let starClass = msg.is_important ? 'bi-star-fill text-warning' : 'bi-star text-muted';
        let starHtml = '<div class="msg-star-btn" onclick="toggleImportantUser('+msg.id+', this)"><i class="bi '+starClass+'"></i></div>';

        div.innerHTML = '<div class="msg-bubble shadow-sm">' + starHtml + contentHtml + '</div><div class="msg-time">' + timeStr + '</div>';
        container.appendChild(div);
        
        // Clear float
        const clear = document.createElement('div');
        clear.style.clear = 'both';
        container.appendChild(clear);
    }

    function toggleImportantUser(msgId, btnElement) {
        let icon = btnElement.querySelector('i');
        let isCurrentlyImportant = icon.classList.contains('bi-star-fill');
        
        // Optimistic UI update
        if(isCurrentlyImportant) {
            icon.className = 'bi bi-star text-muted';
        } else {
            icon.className = 'bi bi-star-fill text-warning';
        }
        
        fetch('/chat/' + chatConversationId + '/messages/' + msgId + '/mark-important', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        }).then(r => r.json()).then(data => {
            if(data.is_important) {
                icon.className = 'bi bi-star-fill text-warning';
            } else {
                icon.className = 'bi bi-star text-muted';
            }
        }).catch(e => {
            // Revert on error
            if(isCurrentlyImportant) {
                icon.className = 'bi bi-star-fill text-warning';
            } else {
                icon.className = 'bi bi-star text-muted';
            }
        });
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
                        const panel = document.getElementById('chatbox-panel');
                        if (panel.style.display === 'none') {
                            toggleChatbox();
                        }
                    }
                });
        }
    }
</script>
@endauth
