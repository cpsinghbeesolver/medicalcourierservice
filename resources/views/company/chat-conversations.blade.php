@if($conversations)
    @foreach($conversations as $conversation)
        <div
            class="conversation-item"
            data-name="{{ $conversation->driver->name }}"
            data-id="{{ $conversation->id }}"
            data-deliveryId="{{ $conversation->delivery_id }}"
            data-type="drivers"
            data-unread="false"
            onclick="openConversation(this)"
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
                    <span class="message-time">
                        @if($conversation->latestMessage)
                            @if($conversation->latestMessage->created_at->isToday())
                                {{ $conversation->latestMessage->created_at->format('h:i A') }}
                            @else       
                                {{ $conversation->latestMessage->created_at->format('Y-m-d h:i A') }}
                            @endif
                        @endif
                    </span>
                </div>

                
                <div class="conversation-bottom">
                    @if($conversation->latestMessage)
                        <span class="last-message">
                            {{ $conversation->latestMessage->message }}
                        </span>
                    @endif
                    @if($conversation->delivery?->delivery_number)
                        <span class="delivery_number">
                            {{ $conversation->delivery->delivery_number }}
                        </span>
                    @endif
                    <!-- @if($conversation->unread_count > 0)
                        <span class="unread-count">{{ $conversation->unread_count }}</span>
                    @endif -->
                </div>
                
            </div>
        </div>
    @endforeach
@endif
