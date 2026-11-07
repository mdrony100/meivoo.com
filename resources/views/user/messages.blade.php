@extends('layouts.user')
@section('title', 'Messages - Family Feed')
@section('body_class', 'messages-page')
@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.2/css/lightgallery.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.2/css/lg-thumbnail.min.css">
    <style>
        #messages-page {
            display: flex;
            height: calc(100vh - 60px);
            position: relative;
        }

        #conv-sidebar {
            width: 320px;
            flex-shrink: 0;
            border-right: 1px solid #eee;
            overflow-y: auto;
        }

        #chat-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .chat-back-btn {
            background: none;
            border: none;
            font-size: 1.1rem;
            margin-right: 10px;
            cursor: pointer;
        }

        .chat-header {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 15px;
        }

        /* ---------- MOBILE ---------- */
        @media (max-width:768px) {

            #conv-sidebar {
                display: block;
            }

            #chat-area {
                display: none;
            }

            #messages-page #conv-sidebar {
                display: block !important;
            }

            #messages-page.chat-open #conv-sidebar {
                display: none !important;
            }

            #messages-page.chat-open #chat-area {
                display: flex;
            }
        }

        .gallery-grid {
            display: grid;
            gap: 8px;
            margin-top: 10px;
        }

        .gallery-grid.count-1 {
            grid-template-columns: 1fr;
        }

        .gallery-grid.count-2 {
            grid-template-columns: 1fr 1fr;
        }

        .gallery-grid.count-3 {
            grid-template-columns: 1fr 1fr;
        }

        .gallery-grid.count-3 a:first-child {
            grid-column: span 2;
        }

        .gallery-grid.count-4-plus {
            grid-template-columns: repeat(2, 1fr);
        }

        .gallery-item {
            position: relative;
            height: 150px;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
        }

        .more-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
            z-index: 1;
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
@endpush

@section('content')
    <main class="main-content p-0">
        <div id="messages-page" class="{{ Request::get('user') ? 'chat-open' : '' }}">
            <div id="conv-sidebar">
                <div class="conv-header">
                    <h2 style="font-family: 'Poppins', sans-serif;">Family Members</h2>
                    <div class="search-wrap">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input class="conv-search" type="search" placeholder="Search users..." id="conversationSearch">
                    </div>
                </div>

                <div class="conv-list" id="conversationList">
                    @forelse ($contacts as $contact)
                        @php
                            $contactInitials = collect(preg_split('/\s+/', trim($contact->name)) ?: [])
                                ->filter()
                                ->take(2)
                                ->map(fn($part) => strtoupper(substr($part, 0, 1)))
                                ->implode('');
                        @endphp
                        <a class="conv-item {{ $activeContact && $activeContact->id === $contact->id ? 'active' : '' }}"
                            href="{{ route('user.messages', ['user' => $contact->id]) }}" data-conv-item
                            data-search="{{ strtolower($contact->name . ' ' . ($contact->preview_text ?? '')) }}">
                            <div class="conv-avatar">
                                <div class="avatar-circle">
                                    @if ($contact->image)
                                        <img src="{{ asset($contact->image) }}" width="40" height="40"
                                            class="rounded-pill" alt="">
                                    @else
                                        {{ $contactInitials }}
                                    @endif
                                </div>
                            </div>
                            <div class="conv-meta">
                                <div class="conv-row">
                                    <p class="conv-name">{{ $contact->name }}</p>
                                    <span
                                        class="conv-time">{{ $contact->last_message_at?->diffForHumans() ?? 'Now' }}</span>
                                </div>
                                <p class="conv-preview">{{ $contact->preview_text ?? 'No messages yet' }}</p>
                            </div>
                            @if (($contact->unread_count ?? 0) > 0 && !($activeContact && $activeContact->id === $contact->id))
                                <span class="unread-badge">{{ $contact->unread_count }}</span>
                            @endif
                        </a>
                    @empty
                        <div class="p-3">
                            <div class="rounded-4 border bg-white p-3">
                                <p class="mb-1 fw-semibold small">No family member found</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            @if (Request::get('user'))
                <div id="chat-area">
                    <div class="chat-header">
                        <button class="chat-back-btn d-block d-lg-none" type="button" onclick="goBackToList()"
                            title="Back to users">
                            <i class="fa-solid fa-arrow-left"></i>
                        </button>

                        <div class="avatar-circle" id="chatAvatar">
                            @if ($contact->image)
                                <img src="{{ asset($contact->image) }}" width="40" height="40" class="rounded-pill"
                                    alt="">
                            @else
                                {{ $contactInitials }}
                            @endif
                        </div>
                        <div class="chat-header-info">
                            <p class="chat-header-name" id="chatName">
                                {{ $activeContact ? $activeContact->name : 'Messages' }}
                            </p>
                        </div>
                    </div>

                    @if ($activeContact)
                        <div class="chat-messages" id="chatMessages"
                            data-conversation-id="{{ $activeConversation?->id ?? '' }}">
                            @forelse ($activeMessages as $message)
                                @php
                                    $isMine = (int) $message->sender_user_id === (int) auth()->id();
                                @endphp

                                <div class="msg-row {{ $isMine ? 'sent' : 'received' }}"
                                    data-message-id="{{ $message->id }}">

                                    @unless ($isMine)
                                        <div class="avatar-circle sm flex-shrink-0">
                                            {{ substr($message->sender->name ?? 'U', 0, 1) }}
                                        </div>
                                    @endunless

                                    <div class="msg-bubble-wrap">
                                        <div class="msg-bubble">
                                            @if ($message->body)
                                                <p class="mb-0">{{ $message->body }}</p>
                                            @endif

                                            @if ($message->attachments->isNotEmpty())
                                                @php
                                                    $images = $message->attachments->where('type', 'image');
                                                    $others = $message->attachments->whereNotIn('type', ['image']);
                                                    $count = $images->count();
                                                    $gridClass =
                                                        $count == 1
                                                            ? 'count-1'
                                                            : ($count == 2
                                                                ? 'count-2'
                                                                : ($count == 3
                                                                    ? 'count-3'
                                                                    : 'count-4-plus'));
                                                @endphp

                                                @if ($images->isNotEmpty())
                                                    <div class="gallery-grid {{ $gridClass }} mt-0"
                                                        id="lightgallery-{{ $message->id }}">
                                                        @foreach ($images as $index => $attachment)
                                                            <a href="{{ Storage::url($attachment->path) }}"
                                                                class="{{ $index < 4 ? 'gallery-item' : 'd-none' }}">

                                                                <img src="{{ Storage::url($attachment->path) }}"
                                                                    alt="image">

                                                                @if ($images->count() > 4 && $index == 3)
                                                                    <div class="more-overlay">+{{ $images->count() - 4 }}
                                                                    </div>
                                                                @endif
                                                            </a>
                                                        @endforeach
                                                    </div>

                                                    <script>
                                                        document.addEventListener("DOMContentLoaded", function() {
                                                            const el = document.getElementById('lightgallery-{{ $message->id }}');
                                                            if (el && !el.classList.contains('lg-initialized')) {
                                                                lightGallery(el, {
                                                                    selector: 'a',
                                                                    plugins: [lgThumbnail],
                                                                    thumbnail: true
                                                                });
                                                                el.classList.add('lg-initialized');
                                                            }
                                                        });
                                                    </script>
                                                @endif

                                                @foreach ($others as $attachment)
                                                    @if ($attachment->type == 'audio')
                                                        <div
                                                            class="audio-player-wrapper d-flex align-items-center justify-content-center">
                                                            <audio controls preload="metadata">
                                                                <source src="{{ Storage::url($attachment->path) }}"
                                                                    type="audio/webm">
                                                            </audio>
                                                        </div>
                                                    @else
                                                        <a href="{{ Storage::url($attachment->path) }}" target="_blank"
                                                            class="text-decoration-none d-flex align-items-center text-white mb-1">
                                                            <i class="fa-solid fa-file-download me-2"></i> Download File
                                                        </a>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>

                                        <span class="msg-time small text-muted mt-1 d-block">
                                            {{ $message->created_at->format('h:i A') }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <div class="d-flex flex-column align-items-center justify-content-center h-100">
                                    <div class="text-center">
                                        <i class="fa-solid fa-comments fa-2x mb-2 opacity-50"></i>
                                        <p>No messages yet</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>

                        <div class="chat-input-bar">
                            <form id="messageForm" class="w-100" action="{{ route('user.messages.store') }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                @if ($activeConversation)
                                    <input type="hidden" name="conversation_id" value="{{ $activeConversation->id }}">
                                @endif
                                <input type="hidden" name="recipient_user_id" value="{{ $activeContact->id }}">

                                <div class="input-wrap">
                                    <div id="normalMode" style="display:flex; width:100%; align-items:center;">
                                        <input type="file" name="attachment[]" id="attachmentInput"
                                            style="display: none;" multiple>
                                        <button type="button" class="btn"
                                            onclick="document.getElementById('attachmentInput').click()">
                                            <i class="fa-solid fa-paperclip"></i>
                                            <span id="fileCountLabel"
                                                style="font-size: 12px; margin-left: 5px; color: #7c3aed; font-weight: bold;"></span>
                                        </button>
                                        <input class="chat-input" type="text" name="message" id="msgInput"
                                            placeholder="Type a message...">
                                        <button type="button" id="recordBtn" class="btn me-2">
                                            <i class="fa-solid fa-microphone"></i>
                                        </button>
                                        <button class="send-btn" type="submit" id="mainSendBtn"><i
                                                class="fa-solid fa-paper-plane"></i></button>
                                    </div>

                                    <div id="recordingMode"
                                        style="display:none; width:100%; align-items:center; gap:10px;">
                                        <button type="button" class="btn text-danger" id="cancelRecordBtn">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>

                                        <div style="flex-grow:1; display:flex; align-items:center; gap:10px;">
                                            <div id="timer">00:00</div>
                                            <div style="flex-grow:1; height:6px; background:#eee; border-radius:3px;">
                                                <div id="progressFill"
                                                    style="width:0%; height:100%; background:#7c3aed; border-radius:3px;">
                                                </div>
                                            </div>
                                        </div>

                                        <button class="send-btn" type="button" id="recordSendBtn">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="chat-messages d-flex align-items-center justify-content-center" id="chatMessages">
                            <div class="text-center px-4">
                                <div class="avatar-circle mx-auto mb-3">ME</div>
                                <h3 class="h5 fw-semibold mb-2">Pick a user</h3>
                                <p class="mb-0 text-muted">Choose a user from the left to view and send messages.</p>
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <div class="chat-messages d-flex align-items-center justify-content-center" id="chatMessages">
                    <div class="text-center px-4">
                        <h3 class="h5 fw-semibold mb-2">Pick a user</h3>
                        <p class="mb-0 text-muted">Choose a user from the left to view and send messages.</p>
                    </div>
                </div>
            @endif

        </div>
    </main>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.2/lightgallery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.2/plugins/thumbnail/lg-thumbnail.min.js"></script>

    <script>
        let mediaRecorder;
        let audioChunks = [];
        let timerInterval, startTime;

        const normalMode = document.getElementById('normalMode');
        const recordingMode = document.getElementById('recordingMode');
        const recordBtn = document.getElementById('recordBtn');
        const cancelRecordBtn = document.getElementById('cancelRecordBtn');
        const recordSendBtn = document.getElementById('recordSendBtn');
        const timerDisplay = document.getElementById('timer');
        const progressFill = document.getElementById('progressFill');

        document.getElementById('attachmentInput').addEventListener('change', function() {
            const fileCountLabel = document.getElementById('fileCountLabel');
            if (fileCountLabel) {
                fileCountLabel.textContent = this.files.length > 0 ? this.files.length + " files" : "";
            }
        });

        recordBtn.addEventListener('click', async () => {
            const stream = await navigator.mediaDevices.getUserMedia({
                audio: true
            });
            mediaRecorder = new MediaRecorder(stream);
            audioChunks = [];

            mediaRecorder.addEventListener('dataavailable', event => {
                audioChunks.push(event.data);
            });

            mediaRecorder.start();
            normalMode.style.display = 'none';
            recordingMode.style.display = 'flex';

            startTime = Date.now();
            timerInterval = setInterval(() => {
                const elapsed = Math.floor((Date.now() - startTime) / 1000);
                timerDisplay.textContent =
                    `${Math.floor(elapsed / 60).toString().padStart(2, '0')}:${(elapsed % 60).toString().padStart(2, '0')}`;
                progressFill.style.width = Math.min(elapsed * 2, 100) + '%';
            }, 1000);
        });

        function stopRecording(shouldSend) {
            if (!mediaRecorder) return;

            mediaRecorder.onstop = () => {
                if (shouldSend && audioChunks.length > 0) {
                    const audioBlob = new Blob(audioChunks, {
                        type: 'audio/webm'
                    });
                    const file = new File([audioBlob], 'voice.webm', {
                        type: 'audio/webm'
                    });

                    const formData = new FormData(document.getElementById('messageForm'));
                    formData.append('attachment[]', file);
                    formData.set('message_type', 'audio');

                    fetch("{{ route('user.messages.store') }}", {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(response => {
                            if (response.ok) location.reload();
                            else alert('Upload failed!');
                        });
                }
                audioChunks = [];
            };

            mediaRecorder.stop();
            mediaRecorder.stream.getTracks().forEach(track => track.stop());
            clearInterval(timerInterval);

            normalMode.style.display = 'flex';
            recordingMode.style.display = 'none';
        }

        cancelRecordBtn.addEventListener('click', () => stopRecording(false));
        recordSendBtn.addEventListener('click', () => stopRecording(true));
    </script>

    <script>
        (function() {
            const pollUrl = "{{ route('user.messages.poll') }}";
            const searchInput = document.getElementById('conversationSearch');
            const items = Array.from(document.querySelectorAll('[data-conv-item]'));
            const chatMessages = document.getElementById('chatMessages');
            const readCountValue = document.getElementById('readCountValue');
            const unreadCountValue = document.getElementById('unreadCountValue');
            let isPolling = false;

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const query = this.value.trim().toLowerCase();
                    items.forEach(function(item) {
                        const text = (item.dataset.search || '').toLowerCase();
                        item.style.display = text.includes(query) ? '' : 'none';
                    });
                });
            }

            // user list item click -> mobile e chat area open
            // items.forEach(function(item) {
            //     item.addEventListener('click', function() {
            //         const messagesPage = document.getElementById('messages-page');
            //         if (messagesPage) {
            //             messagesPage.classList.add('chat-open');
            //         }
            //     });
            // });

            if (chatMessages) {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            function escapeHtml(value) {
                return String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#39;');
            }

            function renderAttachment(message) {
                if (!message.attachment_url) {
                    return '';
                }

                const url = escapeHtml(message.attachment_url);

                if (message.message_type === 'image') {
                    return `<a href="${url}" target="_blank" rel="noopener"><img src="${url}" alt="Message attachment" style="max-width:100%;border-radius:12px;"></a>`;
                }

                if (message.message_type === 'voice') {
                    return `<audio controls style="width:100%;"><source src="${url}"></audio>`;
                }

                return `<a href="${url}" target="_blank" rel="noopener"><i class="fa-solid fa-paperclip me-1"></i> Download attachment</a>`;
            }

            function renderMessage(message) {
                const rowClass = message.is_mine ? 'sent' : 'received';
                const avatar = message.is_mine ?
                    '' :
                    `<div class="avatar-circle sm flex-shrink-0" style="align-self:flex-start;margin-top:4px;">${escapeHtml(message.sender_initials || 'ME')}</div>`;
                const sender = message.is_mine ?
                    '' :
                    `<p class="msg-sender-name">${escapeHtml(message.sender_name || 'User')}</p>`;
                const body = message.body ?
                    `<p class="mb-2">${escapeHtml(message.body)}</p>` :
                    '';

                return `
                                                                                        <div class="msg-row ${rowClass}" data-message-id="${message.id}">
                                                                                            ${avatar}
                                                                                            <div class="msg-bubble-wrap">
                                                                                                ${sender}
                                                                                                <div class="msg-bubble">
                                                                                                    ${body}
                                                                                                    ${renderAttachment(message)}
                                                                                                </div>
                                                                                                <span class="msg-time">${escapeHtml(message.time || '')}</span>
                                                                                            </div>
                                                                                        </div>
                                                                                    `;
            }

            async function pollNewMessages() {
                if (!chatMessages || isPolling) {
                    return;
                }

                const userId = chatMessages.dataset.userId;

                if (!userId) {
                    return;
                }

                isPolling = true;

                try {
                    const sinceId = Number(chatMessages.dataset.lastMessageId || '0');
                    const url =
                        `${pollUrl}?user_id=${encodeURIComponent(userId)}&since_id=${encodeURIComponent(sinceId)}`;
                    const response = await fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    if (!response.ok) {
                        return;
                    }

                    const payload = await response.json();
                    const messages = Array.isArray(payload.messages) ? payload.messages : [];

                    if (!messages.length) {
                        return;
                    }

                    const emptyState = document.getElementById('emptyConversationState');

                    if (emptyState) {
                        emptyState.remove();
                    }

                    messages.forEach(function(message) {
                        chatMessages.insertAdjacentHTML('beforeend', renderMessage(message));
                    });

                    chatMessages.dataset.lastMessageId = String(payload.latest_id || messages[messages.length - 1]
                        .id || sinceId);

                    if (payload.conversation_id) {
                        chatMessages.dataset.conversationId = String(payload.conversation_id);
                    }

                    chatMessages.scrollTop = chatMessages.scrollHeight;

                    if (readCountValue && typeof payload.read_count !== 'undefined') {
                        readCountValue.textContent = String(payload.read_count);
                    }

                    if (unreadCountValue && typeof payload.unread_count !== 'undefined') {
                        unreadCountValue.textContent = String(payload.unread_count);
                    }
                } catch (error) {
                    // Polling is best-effort; ignore transient fetch issues.
                } finally {
                    isPolling = false;
                }
            }

            if (chatMessages && chatMessages.dataset.userId) {
                setInterval(pollNewMessages, 3000);
            }
        })();

        function goBackToList() {
            $('#messages-page').removeClass('chat-open');

            // optional
            history.pushState({}, '', '{{ route('user.messages') }}');
        }
    </script>
@endpush
