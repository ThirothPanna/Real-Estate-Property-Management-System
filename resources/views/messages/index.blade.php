@extends('layouts.app')

@section('title', 'Messages – NEKJOUL IMANAGE')

@section('content')
    <style>
        .messages-page { max-width:900px; margin:0 auto; }
        .messages-heading { margin-bottom:20px; padding:20px 22px; border:1px solid #e4e6eb; border-radius:14px; background:#fff; }
        .messages-heading h1 { margin:0; color:#050505; font-size:24px; font-weight:700; }
        .messages-heading p { margin:6px 0 0; color:#65676b; font-size:14px; }
        .conversation-list { display:grid; gap:12px; }
        .conversation-card { display:flex; align-items:center; justify-content:space-between; gap:16px; padding:16px 18px; border:1px solid #e4e6eb; border-radius:14px; background:#fff; transition:background .15s,border-color .15s; }
        .conversation-card:hover { border-color:#b6d5ff; background:#f7faff; }
        .conversation-contact { display:flex; min-width:0; align-items:center; gap:12px; color:inherit; text-decoration:none; }
        .conversation-contact:hover .conversation-person { color:#1877f2; }
        .conversation-avatar { width:46px; height:46px; flex:none; border-radius:50%; object-fit:cover; background:#f3f4f6; }
        .conversation-person { color:#050505; font-weight:700; }
        .conversation-property { margin-top:4px; color:#65676b; font-size:12px; }
        .conversation-preview { max-width:620px; margin-top:5px; color:#65676b; font-size:13px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        .conversation-action { flex:none; padding:8px 13px; border-radius:18px; background:#e7f3ff; color:#1877f2; font-size:12px; font-weight:700; text-decoration:none; }
        .conversation-action:hover { background:#d8ebff; }
        .messages-empty { padding:42px 20px; border:1px solid #e4e6eb; border-radius:14px; background:#fff; color:#65676b; text-align:center; }
    </style>

    <main class="messages-page">
        <header class="messages-heading">
            <h1>Messages</h1>
            <p>Private conversations with your landlord or tenant about a rental property.</p>
        </header>

        @if ($tenancies->isEmpty() && $inquiries->isEmpty())
            <div class="messages-empty">There are no tenant conversations linked to your account yet.</div>
        @else
            <div class="conversation-list">
                @foreach ($tenancies as $tenancy)
                    @php
                        $otherPerson = auth()->user()->isTenant() ? $tenancy->landlord : $tenancy->tenant;
                    @endphp
                    <div class="conversation-card">
                        <a class="conversation-contact" href="{{ route($routePrefix . '.messages.profile', $tenancy) }}">
                            <img class="conversation-avatar" src="{{ $otherPerson->avatar_url }}" alt="">
                            <div>
                            <div class="conversation-person">{{ $otherPerson->name }}</div>
                            <div class="conversation-property">{{ $tenancy->property->name }} · {{ ucfirst($tenancy->status) }} tenancy</div>
                            </div>
                        </a>
                        <a class="conversation-action" href="{{ route($routePrefix . '.messages.show', $tenancy) }}">Open chat&nbsp; →</a>
                    </div>
                @endforeach
                @foreach ($inquiries as $inquiry)
                    @php
                        $otherPerson = auth()->user()->isTenant() ? $inquiry->landlord : $inquiry->tenant;
                        $inquiryUrl = auth()->user()->isTenant()
                            ? route('tenant.inquiries.show', $inquiry->property)
                            : route('landlord.inquiries.show', [$inquiry->property, $inquiry->tenant]);
                    @endphp
                    <div class="conversation-card">
                        <a class="conversation-contact" href="{{ $inquiryUrl }}">
                            <img class="conversation-avatar" src="{{ $otherPerson->avatar_url }}" alt="">
                            <div>
                                <div class="conversation-person">{{ $otherPerson->name }}</div>
                                <div class="conversation-property">Property inquiry · {{ $inquiry->property->name }}</div>
                                <div class="conversation-preview">{{ $inquiry->latest->body }}</div>
                            </div>
                        </a>
                        <a class="conversation-action" href="{{ $inquiryUrl }}">Open chat&nbsp; →</a>
                    </div>
                @endforeach
            </div>
        @endif
    </main>
@endsection
