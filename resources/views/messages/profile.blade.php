@extends('layouts.app')

@section('title', $person->name . ' – Contact Profile')

@section('content')
    <style>
        .contact-profile { max-width:720px; margin:0 auto; }
        .contact-back { display:inline-block; margin-bottom:14px; color:#15803d; font-size:14px; font-weight:600; text-decoration:none; }
        .contact-card { padding:28px; border:1px solid #e5e7eb; border-radius:14px; background:#fff; text-align:center; }
        .contact-avatar { width:104px; height:104px; border-radius:50%; object-fit:cover; background:#f3f4f6; }
        .contact-name { margin:14px 0 4px; color:#111827; font-size:24px; font-weight:700; }
        .contact-role { margin:0; color:#6b7280; font-size:14px; }
        .contact-details { display:grid; gap:10px; margin:24px auto; max-width:460px; padding:18px; border-radius:10px; background:#f9fafb; text-align:left; }
        .contact-detail-label { color:#6b7280; font-size:12px; }
        .contact-detail-value { margin-top:2px; color:#111827; font-size:14px; overflow-wrap:anywhere; }
        .contact-property { color:#374151; font-size:13px; }
        .contact-message { display:inline-block; padding:11px 20px; border-radius:9px; background:#15803d; color:#fff; font-size:14px; font-weight:700; text-decoration:none; }
        .contact-message:hover { background:#166534; }
        @media (max-width:600px) { .contact-card { padding:22px 16px; } }
    </style>

    <main class="contact-profile">
        <a class="contact-back" href="{{ route($routePrefix . '.messages.index') }}">← All messages</a>
        <section class="contact-card">
            <img class="contact-avatar" src="{{ $person->avatar_url }}" alt="{{ $person->name }}">
            <h1 class="contact-name">{{ $person->name }}</h1>
            <p class="contact-role">{{ auth()->user()->isTenant() ? 'Your landlord' : 'Your tenant' }}</p>

            <div class="contact-details">
                <div>
                    <div class="contact-detail-label">Email</div>
                    <div class="contact-detail-value"><a href="mailto:{{ $person->email }}">{{ $person->email }}</a></div>
                </div>
                @if ($person->phone)
                    <div>
                        <div class="contact-detail-label">Phone</div>
                        <div class="contact-detail-value"><a href="tel:{{ $person->phone }}">{{ $person->phone }}</a></div>
                    </div>
                @endif
                <div>
                    <div class="contact-detail-label">Rental property</div>
                    <div class="contact-detail-value">{{ $tenancy->property->name }}</div>
                    <div class="contact-property">{{ $tenancy->property->full_address }}</div>
                </div>
            </div>

            <a class="contact-message" href="{{ route($routePrefix . '.messages.show', $tenancy) }}">Message {{ $person->name }}</a>
        </section>
    </main>
@endsection
