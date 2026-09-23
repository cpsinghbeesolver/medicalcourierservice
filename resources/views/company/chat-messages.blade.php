    <!-- <div class="date-divider">
        <span>Today</span>
    </div> -->

    @foreach($messages as $message)
        @if($message->sender_id == auth()->user()->id)
            {{-- Sent --}}
            <div class="message-row sent">

                <div class="message-content">

                    <div class="message-bubble">
                        {{ $message->message }}
                    </div>

                    <span class="message-meta formatted-message-date" data-datetime="{{ $message->created_at ? $message->created_at->format('Y-m-d\TH:i:s') : ''; }}">
                        @if($message->created_at->isToday())
                            {{ $message->created_at->format('h:i A') }}
                        @else       
                            {{ $message->created_at->format('m-d-Y h:i A') }}
                        @endif
                        <span class="message-check">✓✓</span>
                    </span>

                </div>

            </div>
        @else
            {{-- Received --}}
            <div class="message-row received">

                <div class="avatar avatar-blue message-avatar">
                    {{ strtoupper(substr($message->sender->name, 0, 2)) }}
                </div>

                <div class="message-content">

                    <div class="message-bubble">
                        {{ $message->message }}
                    </div>

                   <span class="message-time formatted-message-date" data-datetime="{{ $message->created_at ? $message->created_at->format('Y-m-d\TH:i:s') : ''; }}">
                        @if($message->created_at->isToday())
                            {{ $message->created_at->format('h:i A') }}
                        @else       
                            {{ $message->created_at->format('m-d-Y h:i A') }}
                        @endif
                    </span>

                </div>

            </div>
        @endif
    @endforeach
