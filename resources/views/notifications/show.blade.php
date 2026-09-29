@extends('layouts.app')

@section('title', $notification->title . ' – NEKJOUL IMANAGE')

@section('content')
    <style>
        .notification-detail { max-width:760px; margin:0 auto; }
        .notification-back { display:inline-block; margin-bottom:16px; color:#4b5563; font-size:14px; font-weight:600; text-decoration:none; }
        .notification-panel { padding:24px; border:1px solid #e5e7eb; border-radius:8px; background:#fff; }
        .notification-panel h1 { margin:0; color:#111827; font-size:22px; font-weight:700; }
        .notification-date { margin-top:7px; color:#6b7280; font-size:13px; }
        .notification-body { margin-top:20px; color:#374151; font-size:15px; line-height:1.7; white-space:pre-wrap; overflow-wrap:anywhere; }
        .notification-actions { display:flex; gap:10px; flex-wrap:wrap; margin-top:24px; padding-top:18px; border-top:1px solid #f3f4f6; }
        .notification-button { display:inline-flex; align-items:center; justify-content:center; padding:9px 13px; border:1px solid #d1d5db; border-radius:7px; color:#374151; font-size:13px; font-weight:600; text-decoration:none; }
        .notification-button.primary { border-color:#15803d; background:#15803d; color:#fff; }
    </style>

    <main class="notification-detail">
        <a class="notification-back" href="{{ $listUrl }}">← Back to notifications</a>
        <article class="notification-panel">
            <h1>{{ $notification->title }}</h1>
            <div class="notification-date">{{ $notification->created_at->format('M d, Y · g:i A') }}</div>
            <div class="notification-body">{{ $notification->body ?: 'No additional details.' }}</div>
            <div class="notification-actions">
                @if ($actionUrl)
                    <a class="notification-button primary" href="{{ $actionUrl }}">{{ $actionLabel }}</a>
                @endif
                <a class="notification-button" href="{{ $listUrl }}">All notifications</a>
            </div>
        </article>
    </main>
@endsection