@extends('layouts.app')

@section('title', 'Chat – ' . $tenancy->property->name)

@section('content')
    <style>
        .chat-page { max-width:1000px; height:min(78vh, 850px); min-height:520px; margin:0 auto; }
        .chat-back { display:inline-block; margin-bottom:12px; color:#1877f2; font-size:14px; font-weight:600; text-decoration:none; }
        .chat-card { display:grid; height:calc(100% - 30px); min-height:0; grid-template-rows:auto minmax(0, 1fr) auto; overflow:hidden; border:1px solid #e4e6eb; border-radius:16px; background:#fff; box-shadow:0 8px 28px rgba(17,24,39,.08); }
        .chat-header { display:flex; align-items:center; gap:12px; padding:15px 20px; border-bottom:1px solid #e4e6eb; background:#fff; }
        .chat-avatar { width:46px; height:46px; flex:none; border-radius:50%; object-fit:cover; background:#f0f2f5; }
        .chat-contact { min-width:0; }
        .chat-header h1 { margin:0; color:#050505; font-size:16px; font-weight:700; }
        .chat-header h1 a { color:inherit; text-decoration:none; }
        .chat-header h1 a:hover { color:#1877f2; }
        .chat-header p { margin:4px 0 0; color:#65676b; font-size:12px; }
        .chat-messages { display:flex; min-height:0; flex-direction:column; gap:10px; overflow-y:auto; padding:22px; background:#f0f2f5; }
        .chat-empty { margin:auto; color:#6b7280; font-size:14px; text-align:center; }
        .chat-message { max-width:min(76%, 620px); padding:10px 14px; border-radius:18px 18px 18px 5px; background:#fff; box-shadow:0 1px 2px rgba(0,0,0,.07); overflow-wrap:anywhere; }
        .chat-message.mine { align-self:flex-end; border-radius:18px 18px 5px 18px; background:#0084ff; }
        .chat-sender { margin-bottom:4px; color:#65676b; font-size:11px; font-weight:700; }
        .chat-message.mine .chat-sender { color:rgba(255,255,255,.8); }
        .chat-body { color:#1c1e21; font-size:14px; line-height:1.5; white-space:pre-wrap; }
        .chat-message.mine .chat-body { color:#fff; }
        .chat-attachment { display:block; max-width:min(100%, 420px); max-height:360px; margin-top:8px; border-radius:9px; object-fit:contain; }
        .chat-attachment-link { display:inline-block; margin-top:8px; color:#15803d; font-size:13px; }
        .chat-time { margin-top:6px; color:#65676b; font-size:10px; }
        .chat-message.mine .chat-time { color:rgba(255,255,255,.8); }
        .chat-form { padding:12px 16px 14px; border-top:1px solid #e4e6eb; background:#fff; }
        .chat-form textarea { display:block; width:100%; min-height:58px; max-height:140px; resize:vertical; padding:12px 15px; border:1px solid #e4e6eb; border-radius:18px; background:#f0f2f5; font:inherit; font-size:14px; }
        .chat-form textarea:focus { outline:2px solid #b6d5ff; border-color:#1877f2; }
        .chat-form-footer { display:flex; justify-content:space-between; align-items:center; gap:12px; margin-top:10px; }
        .chat-hint { color:#65676b; font-size:11px; }
        .chat-send { padding:10px 18px; border:0; border-radius:20px; background:#0084ff; color:#fff; font:inherit; font-size:14px; font-weight:700; cursor:pointer; }
        .chat-send:hover { background:#006edb; }
        .chat-error { margin-top:7px; color:#dc2626; font-size:13px; }
        .chat-tools { display:flex; flex-wrap:wrap; align-items:center; gap:10px; margin-top:10px; }
        .chat-upload-label, .chat-record { padding:8px 12px; border:1px solid #e4e6eb; border-radius:20px; background:#fff; color:#3a3b3c; font:inherit; font-size:12px; font-weight:600; cursor:pointer; }
        .chat-upload-label:hover, .chat-record:hover { background:#f0f2f5; }
        .chat-upload-input { position:absolute; width:1px; height:1px; overflow:hidden; clip:rect(0,0,0,0); }
        .chat-attachment-status { color:#6b7280; font-size:12px; }
        @media (max-width:600px) { .chat-page { height:calc(100dvh - 170px); min-height:460px; } .chat-card { height:calc(100% - 30px); } .chat-messages { padding:14px; } .chat-message { max-width:90%; } .chat-form-footer { align-items:flex-end; } }
    </style>

    <main class="chat-page">
        <a class="chat-back" href="{{ route($routePrefix . '.messages.index') }}">← All messages</a>
        <section class="chat-card" aria-label="Conversation">
            <header class="chat-header">
                @php $chatContact = auth()->user()->isTenant() ? $tenancy->landlord : $tenancy->tenant; @endphp
                <img class="chat-avatar" src="{{ $chatContact->avatar_url }}" alt="">
                <div class="chat-contact">
                    <h1><a href="{{ route($routePrefix . '.messages.profile', $tenancy) }}">{{ $chatContact->name }}</a></h1>
                    <p>{{ $tenancy->property->name }} · {{ $tenancy->property->full_address }}</p>
                </div>
            </header>

            <div class="chat-messages" id="chat-messages" aria-live="polite">
                @forelse ($messages as $message)
                    <article class="chat-message {{ $message->sender_id === auth()->id() ? 'mine' : '' }}" @if ($loop->last) id="latest-message" @endif>
                        <div class="chat-sender">{{ $message->sender_id === auth()->id() ? 'You' : $message->sender->name }}</div>
                        @if ($message->body !== '') <div class="chat-body">{{ $message->body }}</div> @endif
                        @if ($message->attachment_path)
                            @if (str_starts_with($message->attachment_mime, 'image/'))
                                <a href="{{ route('messages.attachments.show', $message) }}" target="_blank" rel="noopener noreferrer">
                                    <img class="chat-attachment" src="{{ route('messages.attachments.show', $message) }}" alt="{{ $message->attachment_name ?: 'Photo attachment' }}" loading="lazy">
                                </a>
                            @else
                                <audio class="chat-attachment" controls preload="metadata" src="{{ route('messages.attachments.show', $message) }}">Your browser cannot play this voice message.</audio>
                            @endif
                        @endif
                        <div class="chat-time">{{ $message->created_at->format('M j, Y · g:i A') }}</div>
                    </article>
                @empty
                    <p class="chat-empty">No messages yet. Send a message to start the conversation.</p>
                @endforelse
            </div>

            @php($sendUrl = route($routePrefix . '.messages.store', $tenancy))
            @include('messages.composer', ['sendUrl' => $sendUrl])
        </section>
    </main>
@endsection
