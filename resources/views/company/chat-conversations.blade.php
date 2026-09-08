@if($conversations)
    @foreach($conversations as $conversation)
        <div
            class="conversation-item {{ $conversation->unread_count > 0 ? 'active' : '' }}"
            data-name="{{ $conversation->driver->name }}"
            data-type="drivers"
            data-unread="false"
            onclick="openConversation('{{ $conversation->driver->name }}')"
        >
            <div class="avatar avatar-blue">
                {{ strtoupper(substr($conversation->driver->name, 0, 2)) }}
                @if($conversation->driver->is_online)
                    <span class="online-dot"></span>
                @endif
            </div>

            <div class="conversation-info">
                <div class="conversation-top">
                    <strong>{{ $conversation->driver->name }}</strong>
                    <span class="message-time">{{ $conversation->latestMessage->created_at }}</span>
                </div>

                <div class="conversation-bottom">
                    <span class="last-message">
                        {{ $conversation->latestMessage->message }}
                    </span>

                    @if($conversation->unread_count > 0)
                        <span class="unread-count">{{ $conversation->unread_count }}</span>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
@endif
