@extends('common.layout')

@section('title', 'Chat')
@section('page-title', 'Chat')

@section('styles')
<style>

    /* =========================================
    CHAT PAGE
    ========================================= */

    .chat-page {
        padding: 28px 30px 35px;
        background: #f8f9f7;
        min-height: calc(100vh - 80px);
    }

    .chat-page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 22px;
    }

    .chat-page-header h1 {
        margin: 0;
        color: #172b3d;
        font-size: 28px;
        font-weight: 500;
    }

    .chat-page-header p {
        margin: 6px 0 0;
        color: #89949a;
        font-size: 13px;
    }


    /* =========================================
    NEW CHAT BUTTON
    ========================================= */

    .new-chat-btn {
        border: 1px solid #b2b94b;
        background: white;
        color: #273d50;
        height: 42px;
        padding: 0 18px;
        border-radius: 7px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: .2s;
    }

    .new-chat-btn span {
        color: #a9b23f;
        font-size: 20px;
        margin-right: 6px;
    }

    .new-chat-btn:hover {
        background: #f4f6df;
    }


    /* =========================================
    MAIN CHAT CONTAINER
    ========================================= */

    .chat-container {
        height: calc(100vh - 190px);
        min-height: 600px;

        display: flex;

        background: #fff;

        border-radius: 12px;

        box-shadow:
            0 2px 12px rgba(40, 55, 65, .07);

        overflow: hidden;
    }


    /* =========================================
    CONVERSATION PANEL
    ========================================= */

    .conversation-panel {
        width: 355px;
        min-width: 355px;

        border-right: 1px solid #e8ecec;

        background: #fff;

        display: flex;
        flex-direction: column;
    }

    .conversation-header {
        padding: 22px 22px 12px;
    }

    .conversation-header h3 {
        margin: 0;

        color: #273d50;

        font-size: 17px;
        font-weight: 600;
    }

    .conversation-count {
        display: block;

        margin-top: 4px;

        color: #9ba4a8;

        font-size: 12px;
    }


    /* =========================================
    SEARCH
    ========================================= */

    .chat-search {
        margin: 8px 18px 12px;

        height: 40px;

        border: 1px solid #e2e6e7;

        background: #fafbfb;

        border-radius: 7px;

        display: flex;
        align-items: center;

        padding: 0 12px;
    }

    .search-icon {
        color: #87949a;
        font-size: 19px;
        margin-right: 7px;
    }

    .chat-search input {
        border: none;
        outline: none;

        width: 100%;

        background: transparent;

        font-size: 13px;

        color: #273d50;
    }

    .chat-search input::placeholder {
        color: #aab2b5;
    }


    /* =========================================
    FILTERS
    ========================================= */

    .conversation-filters {
        display: flex;
        gap: 7px;

        padding: 0 18px 13px;

        border-bottom: 1px solid #edf0f0;
    }

    .filter-btn {
        border: none;

        background: transparent;

        color: #8b969b;

        font-size: 12px;

        padding: 6px 12px;

        border-radius: 5px;

        cursor: pointer;
    }

    .filter-btn.active {
        background: #eef1d7;
        color: #4e5d29;
        font-weight: 600;
    }


    /* =========================================
    CONVERSATION ITEMS
    ========================================= */

    .conversation-list {
        overflow-y: auto;
        flex: 1;
    }

    .conversation-item {
        display: flex;

        padding: 16px 18px;

        cursor: pointer;

        border-bottom: 1px solid #f0f2f2;

        transition: .15s;
    }

    .conversation-item:hover {
        background: #f8faf8;
    }

    .conversation-item.active {
        background: #f1f3df;

        border-left: 3px solid #b1b84c;

        padding-left: 15px;
    }


    /* =========================================
    AVATAR
    ========================================= */

    .avatar {
        width: 43px;
        height: 43px;

        min-width: 43px;

        border-radius: 50%;

        display: flex;
        align-items: center;
        justify-content: center;

        position: relative;

        color: white;

        font-size: 12px;
        font-weight: 600;
    }

    .avatar-blue {
        background: #30485c;
    }

    .avatar-green {
        background: #859747;
    }

    .avatar-purple {
        background: #746986;
    }

    .avatar-navy {
        background: #3b5367;
    }

    .online-dot {
        position: absolute;

        right: 0;
        bottom: 1px;

        width: 10px;
        height: 10px;

        background: #aeb94b;

        border: 2px solid white;

        border-radius: 50%;
    }


    /* =========================================
    CONVERSATION INFO
    ========================================= */

    .conversation-info {
        min-width: 0;
        flex: 1;
        margin-left: 12px;
    }

    .conversation-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .conversation-top strong {
        color: #273d50;
        font-size: 13px;
    }

    .message-time {
        color: #a2aaad;
        font-size: 10px;
    }

    .conversation-bottom {
        display: flex;
        justify-content: space-between;
        align-items: center;

        margin-top: 6px;
    }

    .last-message {
        color: #899398;

        font-size: 12px;

        overflow: hidden;

        text-overflow: ellipsis;

        white-space: nowrap;
    }

    .unread-count {
        background: #b1b84b;

        color: white;

        min-width: 19px;
        height: 19px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 50%;

        font-size: 10px;
        font-weight: 600;

        margin-left: 8px;
    }


    /* =========================================
    CHAT WINDOW
    ========================================= */

    .chat-window {
        flex: 1;

        min-width: 0;

        display: flex;

        flex-direction: column;

        background: #fcfdfc;
    }


    /* =========================================
    CHAT HEADER
    ========================================= */

    .chat-header {
        height: 76px;

        padding: 0 24px;

        border-bottom: 1px solid #e8ecec;

        background: white;

        display: flex;

        align-items: center;

        justify-content: space-between;
    }

    .chat-user {
        display: flex;
        align-items: center;
    }

    .large-avatar {
        width: 43px;
        height: 43px;
    }

    .chat-user h3 {
        margin: 0 0 4px 12px;

        color: #273d50;

        font-size: 15px;
    }

    .chat-status {
        margin-left: 12px;

        color: #89969a;

        font-size: 11px;
    }

    .status-dot {
        width: 7px;
        height: 7px;

        display: inline-block;

        border-radius: 50%;

        background: #aeb84a;

        margin-right: 4px;
    }

    .chat-header-actions {
        display: flex;
        gap: 8px;
    }

    .chat-header-actions button {
        width: 35px;
        height: 35px;

        border: 1px solid #e4e8e8;

        background: white;

        color: #63737c;

        border-radius: 6px;

        cursor: pointer;

        font-size: 17px;
    }

    .chat-header-actions button:hover {
        background: #f2f4e3;
    }


    /* =========================================
    MESSAGES
    ========================================= */

    .messages-area {
        flex: 1;

        overflow-y: auto;

        padding: 25px 30px;
    }

    .date-divider {
        display: flex;

        align-items: center;

        gap: 15px;

        color: #a0a8ab;

        font-size: 10px;

        margin: 4px 0 25px;
    }

    .date-divider::before,
    .date-divider::after {
        content: "";

        height: 1px;

        background: #e9eded;

        flex: 1;
    }

    .date-divider span {
        white-space: nowrap;
    }


    /* =========================================
    MESSAGE ROW
    ========================================= */

    .message-row {
        display: flex;

        margin-bottom: 19px;

        max-width: 75%;
    }

    .message-row.received {
        align-items: flex-end;
    }

    .message-row.sent {
        margin-left: auto;

        justify-content: flex-end;
    }

    .message-avatar {
        width: 31px;
        height: 31px;

        min-width: 31px;

        font-size: 9px;

        margin-right: 9px;
    }

    .message-content {
        display: flex;
        flex-direction: column;
    }

    .message-bubble {
        padding: 11px 15px;

        border-radius: 9px;

        font-size: 13px;

        line-height: 1.5;

        max-width: 500px;
    }

    .received .message-bubble {
        background: white;

        border: 1px solid #e5e9e8;

        color: #43535d;

        border-bottom-left-radius: 3px;
    }

    .sent .message-bubble {
        background: #30485c;

        color: white;

        border-bottom-right-radius: 3px;
    }

    .message-meta {
        font-size: 9px;

        color: #a3abad;

        margin-top: 5px;
    }

    .sent .message-meta {
        text-align: right;
    }

    .message-check {
        color: #aeb84b;

        margin-left: 3px;
    }


    /* =========================================
    TYPING
    ========================================= */

    .typing-indicator {
        display: none;

        padding: 0 30px 8px;

        color: #9ba4a7;

        font-size: 10px;
    }


    /* =========================================
    COMPOSER
    ========================================= */

    .message-composer {
        display: flex;

        align-items: center;

        gap: 10px;

        padding: 15px 20px;

        background: white;

        border-top: 1px solid #e8ecec;
    }


    .message-input-wrapper {
        flex: 1;

        display: flex;

        align-items: center;

        border: 1px solid #dfe5e5;

        background: #fafbfb;

        border-radius: 8px;

        padding: 0 10px;
    }

    .message-input-wrapper:focus-within {
        border-color: #b3bb55;
    }

    .message-input-wrapper textarea {
        width: 100%;

        border: none;

        outline: none;

        resize: none;

        background: transparent;

        padding: 11px 5px;

        color: #344954;

        font-family: inherit;

        font-size: 13px;

        max-height: 90px;
    }

    .send-btn {
        width: 42px;
        height: 42px;

        border: none;

        background: #30485c;

        color: white;

        border-radius: 7px;

        cursor: pointer;

        font-size: 16px;

        transition: .2s;
    }

    .send-btn:hover {
        background: #22394b;

        transform: translateY(-1px);
    }


    /* =========================================
    MODAL
    ========================================= */

    .chat-modal-overlay {
        display: none;

        position: fixed;

        inset: 0;

        background: rgba(30, 44, 55, .35);

        z-index: 1000;

        align-items: center;

        justify-content: center;
    }

    .chat-modal-overlay.show {
        display: flex;
    }

    .chat-modal {
        width: 450px;

        max-width: calc(100% - 30px);

        background: white;

        border-radius: 10px;

        box-shadow: 0 15px 50px rgba(0,0,0,.15);

        overflow: hidden;
    }

    .modal-header {
        padding: 20px;

        display: flex;

        justify-content: space-between;

        border-bottom: 1px solid #edf0f0;
    }

    .modal-header h3 {
        margin: 0;

        color: #273d50;

        font-size: 17px;
    }

    .modal-header p {
        margin: 5px 0 0;

        color: #929da0;

        font-size: 11px;
    }

    .modal-header button {
        border: none;

        background: transparent;

        color: #68777e;

        font-size: 23px;

        cursor: pointer;
    }

    .modal-search {
        padding: 15px 18px;
    }

    .modal-search input {
        width: 100%;

        box-sizing: border-box;

        height: 40px;

        border: 1px solid #e1e6e6;

        border-radius: 6px;

        padding: 0 12px;

        outline: none;

        font-size: 12px;
    }

    .driver-selection {
        padding-bottom: 10px;
    }

    .driver-option {
        display: flex;

        align-items: center;

        gap: 12px;

        padding: 12px 20px;

        cursor: pointer;
    }

    .driver-option:hover {
        background: #f5f7ee;
    }

    .driver-option > div:nth-child(2) {
        flex: 1;
    }

    .driver-option strong {
        display: block;

        color: #304657;

        font-size: 13px;
    }

    .driver-option span {
        color: #929c9f;

        font-size: 10px;
    }


    /* =========================================
    SCROLLBAR
    ========================================= */

    .conversation-list::-webkit-scrollbar,
    .messages-area::-webkit-scrollbar {
        width: 5px;
    }

    .conversation-list::-webkit-scrollbar-thumb,
    .messages-area::-webkit-scrollbar-thumb {
        background: #d6dcdc;

        border-radius: 10px;
    }


    /* =========================================
    RESPONSIVE
    ========================================= */

    @media (max-width: 1000px) {

        .conversation-panel {
            width: 300px;
            min-width: 300px;
        }

        .message-row {
            max-width: 85%;
        }

    }

    @media (max-width: 750px) {

        .chat-page {
            padding: 20px 15px;
        }

        .chat-page-header {
            margin-bottom: 15px;
        }

        .chat-page-header h1 {
            font-size: 23px;
        }

        .chat-page-header p {
            display: none;
        }

        .new-chat-btn {
            height: 38px;
        }

        .chat-container {
            height: calc(100vh - 150px);
            min-height: 500px;
        }

        .conversation-panel {
            width: 100%;

            min-width: 100%;
        }

        .chat-window {
            display: none;
        }

        .conversation-panel.mobile-hidden {
            display: none;
        }

        .chat-window.mobile-show {
            display: flex;
            width: 100%;
        }

        .message-row {
            max-width: 90%;
        }

        .messages-area {
            padding: 20px 15px;
        }

        .chat-header {
            padding: 0 15px;
        }

    }

</style>
@endsection

@section('content')
<div class="chat-page">

    {{-- Page Header --}}
    <div class="chat-page-header">
        <div>
            <p>Communicate with drivers and your team</p>
        </div>

        <button class="new-chat-btn" onclick="openNewChat()">
            <span>+</span>
            New Chat
        </button>
    </div>


    {{-- Chat Container --}}
    <div class="chat-container">

        {{-- =========================================
             LEFT: CONVERSATIONS
        ========================================== --}}
        <aside class="conversation-panel">

            <div class="conversation-header">
                <div>
                    <h3>Messages</h3>
                    <span class="conversation-count">4 conversations</span>
                </div>
            </div>


            {{-- Search --}}
            <div class="chat-search">
                <span class="search-icon">⌕</span>
                <input
                    type="text"
                    id="conversationSearch"
                    placeholder="Search conversations..."
                    onkeyup="searchConversations()"
                >
            </div>


            {{-- Filters --}}
            <div class="conversation-filters">
                <button class="filter-btn active" onclick="filterChats('all', this)">
                    All
                </button>

                <button class="filter-btn" onclick="filterChats('unread', this)">
                    Unread
                </button>

                <button class="filter-btn" onclick="filterChats('drivers', this)">
                    Drivers
                </button>
            </div>


            {{-- Conversation List --}}
            <div class="conversation-list" id="conversationList">

                {{-- Conversation --}}
                <div
                    class="conversation-item active"
                    data-name="John Smith"
                    data-type="drivers"
                    data-unread="true"
                    onclick="openConversation('John Smith')"
                >
                    <div class="avatar avatar-blue">
                        JS
                        <span class="online-dot"></span>
                    </div>

                    <div class="conversation-info">
                        <div class="conversation-top">
                            <strong>John Smith</strong>
                            <span class="message-time">10:42 AM</span>
                        </div>

                        <div class="conversation-bottom">
                            <span class="last-message">
                                The delivery has been picked up.
                            </span>

                            <span class="unread-count">2</span>
                        </div>
                    </div>
                </div>


                {{-- Conversation --}}
                <div
                    class="conversation-item"
                    data-name="Michael Johnson"
                    data-type="drivers"
                    data-unread="false"
                    onclick="openConversation('Michael Johnson')"
                >
                    <div class="avatar avatar-green">
                        MJ
                    </div>

                    <div class="conversation-info">
                        <div class="conversation-top">
                            <strong>Michael Johnson</strong>
                            <span class="message-time">Yesterday</span>
                        </div>

                        <div class="conversation-bottom">
                            <span class="last-message">
                                Thanks, received the instructions.
                            </span>
                        </div>
                    </div>
                </div>


                {{-- Conversation --}}
                <div
                    class="conversation-item"
                    data-name="Sarah Williams"
                    data-type="drivers"
                    data-unread="true"
                    onclick="openConversation('Sarah Williams')"
                >
                    <div class="avatar avatar-purple">
                        SW
                        <span class="online-dot"></span>
                    </div>

                    <div class="conversation-info">
                        <div class="conversation-top">
                            <strong>Sarah Williams</strong>
                            <span class="message-time">Yesterday</span>
                        </div>

                        <div class="conversation-bottom">
                            <span class="last-message">
                                Is the pickup address correct?
                            </span>

                            <span class="unread-count">1</span>
                        </div>
                    </div>
                </div>


                {{-- Conversation --}}
                <div
                    class="conversation-item"
                    data-name="Operations Team"
                    data-type="team"
                    data-unread="false"
                    onclick="openConversation('Operations Team')"
                >
                    <div class="avatar avatar-navy">
                        OT
                    </div>

                    <div class="conversation-info">
                        <div class="conversation-top">
                            <strong>Operations Team</strong>
                            <span class="message-time">Mon</span>
                        </div>

                        <div class="conversation-bottom">
                            <span class="last-message">
                                New delivery assigned.
                            </span>
                        </div>
                    </div>
                </div>

            </div>
        </aside>


        {{-- =========================================
             RIGHT: CHAT WINDOW
        ========================================== --}}
        <section class="chat-window">

            {{-- Chat Header --}}
            <div class="chat-header">

                <div class="chat-user">

                    <div class="avatar avatar-blue large-avatar">
                        JS
                        <span class="online-dot"></span>
                    </div>

                    <div>
                        <h3 id="chatUserName">John Smith</h3>

                        <div class="chat-status">
                            <span class="status-dot"></span>
                            Online
                        </div>
                    </div>

                </div>


                <div class="chat-header-actions">

                    <button title="Search">
                        ⌕
                    </button>

                    <button title="More">
                        ⋮
                    </button>

                </div>

            </div>


            {{-- Messages --}}
            <div class="messages-area" id="messagesArea">

                <div class="date-divider">
                    <span>Today</span>
                </div>


                {{-- Received --}}
                <div class="message-row received">

                    <div class="avatar avatar-blue message-avatar">
                        JS
                    </div>

                    <div class="message-content">

                        <div class="message-bubble">
                            Hi, I have picked up the delivery.
                        </div>

                        <span class="message-meta">
                            10:38 AM
                        </span>

                    </div>

                </div>


                {{-- Sent --}}
                <div class="message-row sent">

                    <div class="message-content">

                        <div class="message-bubble">
                            Great. Please proceed to the delivery address.
                        </div>

                        <span class="message-meta">
                            10:39 AM
                            <span class="message-check">✓✓</span>
                        </span>

                    </div>

                </div>


                {{-- Received --}}
                <div class="message-row received">

                    <div class="avatar avatar-blue message-avatar">
                        JS
                    </div>

                    <div class="message-content">

                        <div class="message-bubble">
                            Sure. ETA is approximately 25 minutes.
                        </div>

                        <span class="message-meta">
                            10:41 AM
                        </span>

                    </div>

                </div>


                {{-- Sent --}}
                <div class="message-row sent">

                    <div class="message-content">

                        <div class="message-bubble">
                            Perfect. Please update me once you arrive.
                        </div>

                        <span class="message-meta">
                            10:42 AM
                            <span class="message-check">✓✓</span>
                        </span>

                    </div>

                </div>


            </div>


            {{-- Typing --}}
            <div class="typing-indicator" id="typingIndicator">
                John is typing...
            </div>


            {{-- Message Composer --}}
            <div class="message-composer">

              

                <div class="message-input-wrapper">

                    <textarea
                        id="messageInput"
                        rows="1"
                        placeholder="Type a message..."
                        onkeydown="handleMessageKey(event)"
                    ></textarea>

                   

                </div>

                <button class="send-btn" onclick="sendMessage()">
                    <span>➤</span>
                </button>

            </div>

        </section>

    </div>

</div>


{{-- =========================================
     NEW CHAT MODAL
========================================== --}}
<div class="chat-modal-overlay" id="newChatModal">

    <div class="chat-modal">

        <div class="modal-header">
            <div>
                <h3>New Conversation</h3>
                <p>Select a driver or team member</p>
            </div>

            <button onclick="closeNewChat()">×</button>
        </div>

        <div class="modal-search">
            <input
                type="text"
                placeholder="Search drivers..."
            >
        </div>

        <div class="driver-selection">

            <div class="driver-option" onclick="selectDriver('John Smith')">
                <div class="avatar avatar-blue">
                    JS
                </div>

                <div>
                    <strong>John Smith</strong>
                    <span>Driver</span>
                </div>

                <span class="status-dot"></span>
            </div>

            <div class="driver-option" onclick="selectDriver('Michael Johnson')">
                <div class="avatar avatar-green">
                    MJ
                </div>

                <div>
                    <strong>Michael Johnson</strong>
                    <span>Driver</span>
                </div>
            </div>

            <div class="driver-option" onclick="selectDriver('Sarah Williams')">
                <div class="avatar avatar-purple">
                    SW
                </div>

                <div>
                    <strong>Sarah Williams</strong>
                    <span>Driver</span>
                </div>

                <span class="status-dot"></span>
            </div>

        </div>

    </div>

</div>


@endsection

@section('scripts')
<script>

    /* =========================================
    SEARCH CONVERSATIONS
    ========================================= */

    function searchConversations() {

        const input = document
            .getElementById('conversationSearch')
            .value
            .toLowerCase();

        const conversations = document.querySelectorAll(
            '.conversation-item'
        );

        conversations.forEach(function(item) {

            const name = item
                .dataset
                .name
                .toLowerCase();

            item.style.display =
                name.includes(input)
                    ? 'flex'
                    : 'none';

        });
    }


    /* =========================================
    FILTER CONVERSATIONS
    ========================================= */

    function filterChats(type, button) {

        document
            .querySelectorAll('.filter-btn')
            .forEach(btn => btn.classList.remove('active'));

        button.classList.add('active');

        const conversations =
            document.querySelectorAll('.conversation-item');

        conversations.forEach(function(item) {

            if (type === 'all') {

                item.style.display = 'flex';

            } else if (type === 'unread') {

                item.style.display =
                    item.dataset.unread === 'true'
                        ? 'flex'
                        : 'none';

            } else if (type === 'drivers') {

                item.style.display =
                    item.dataset.type === 'drivers'
                        ? 'flex'
                        : 'none';
            }

        });
    }


    /* =========================================
    OPEN CONVERSATION
    ========================================= */

    function openConversation(name) {

        document
            .querySelectorAll('.conversation-item')
            .forEach(item => {

                item.classList.remove('active');

            });


        event.currentTarget.classList.add('active');


        document
            .getElementById('chatUserName')
            .textContent = name;


        // Remove unread badge
        const badge =
            event.currentTarget.querySelector('.unread-count');

        if (badge) {

            badge.remove();

            event.currentTarget.dataset.unread = 'false';

        }

    }


    /* =========================================
    SEND MESSAGE
    ========================================= */

    function sendMessage() {

        const input =
            document.getElementById('messageInput');

        const message =
            input.value.trim();

        if (!message) {
            return;
        }


        const messagesArea =
            document.getElementById('messagesArea');


        const messageRow =
            document.createElement('div');

        messageRow.className =
            'message-row sent';


        messageRow.innerHTML = `
            <div class="message-content">
                <div class="message-bubble">
                    ${escapeHtml(message)}
                </div>

                <span class="message-meta">
                    Just now
                    <span class="message-check">✓</span>
                </span>
            </div>
        `;


        messagesArea.appendChild(messageRow);


        input.value = '';

        input.style.height = 'auto';


        messagesArea.scrollTop =
            messagesArea.scrollHeight;


        /*
        * Laravel AJAX can be added here.
        *
        * Example:
        *
        * fetch('/chat/messages', {
        *     method: 'POST',
        *     headers: {
        *         'Content-Type': 'application/json',
        *         'X-CSRF-TOKEN':
        *             document.querySelector(
        *                 'meta[name="csrf-token"]'
        *             ).content
        *     },
        *     body: JSON.stringify({
        *         receiver_id: 5,
        *         message: message
        *     })
        * });
        */

    }


    /* =========================================
    ENTER TO SEND
    ========================================= */

    function handleMessageKey(event) {

        if (event.key === 'Enter' && !event.shiftKey) {

            event.preventDefault();

            sendMessage();

        }
    }


    /* =========================================
    ESCAPE HTML
    ========================================= */

    function escapeHtml(text) {

        const div = document.createElement('div');

        div.textContent = text;

        return div.innerHTML;
    }


    /* =========================================
    NEW CHAT MODAL
    ========================================= */

    function openNewChat() {

        document
            .getElementById('newChatModal')
            .classList.add('show');

    }

    function closeNewChat() {

        document
            .getElementById('newChatModal')
            .classList.remove('show');

    }

    function selectDriver(name) {

        document
            .getElementById('chatUserName')
            .textContent = name;

        closeNewChat();

    }


    /* =========================================
    AUTO SCROLL
    ========================================= */

    document.addEventListener('DOMContentLoaded', function() {

        const messagesArea =
            document.getElementById('messagesArea');

        messagesArea.scrollTop =
            messagesArea.scrollHeight;

    });


</script>
@endsection