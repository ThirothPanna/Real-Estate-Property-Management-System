@extends('layouts.app')

@section('title', 'Requests – NEKJOUL IMANAGE')

@section('content')

    <style>
        .page-h1 { font-size:24px; font-weight:700; margin-bottom:4px; }
        .page-sub { color:#6b7280; font-size:14px; margin-bottom:24px; }

        .panel { background:#fff; border-radius:16px; padding:24px; box-shadow:0 1px 3px rgba(0,0,0,.05); }
        .panel h3 { font-size:16px; font-weight:700; margin-bottom:16px; }

        .req-item {
            display:flex; justify-content:space-between; align-items:center;
            padding:16px 18px; border:1px solid #f3f4f6; border-radius:12px;
            margin-bottom:10px;
        }
        .req-title { font-size:15px; font-weight:600; color:#111827; margin-bottom:4px; }
        .req-meta { font-size:12px; color:#6b7280; }
        .badge { padding:4px 10px; border-radius:20px; font-size:12px; font-weight:600; }
        .badge.pending     { background:#fef3c7; color:#d97706; }
        .badge.in_progress { background:#dbeafe; color:#2563eb; }
        .badge.resolved    { background:#dcfce7; color:#16a34a; }

        .priority { font-weight:600; }
        .priority.low    { color:#16a34a; }
        .priority.medium { color:#d97706; }
        .priority.high   { color:#dc2626; }
        .priority.urgent { color:#b91c1c; }

        .empty { text-align:center; color:#9ca3af; padding:40px 0; font-size:14px; }
    </style>

    <div class="page-h1">Maintenance Requests</div>
    <div class="page-sub">All requests you've submitted.</div>

    <div class="panel">
        <h3>All Requests ({{ $requests->total() }})</h3>

        @if ($requests->isEmpty())
            <div class="empty">You haven't submitted any requests yet.</div>
        @else
            @foreach ($requests as $r)
                <div class="req-item">
                    <div>
                        <div class="req-title">{{ $r->title }}</div>
                        <div class="req-meta">
                            {{ $r->created_at->format('M d, Y · H:i') }} ·
                            Priority: <span class="priority {{ $r->priority }}">{{ ucfirst($r->priority) }}</span>
                        </div>
                    </div>
                    <span class="badge {{ $r->status }}">{{ ucfirst(str_replace('_', ' ', $r->status)) }}</span>
                </div>
            @endforeach

            <div style="margin-top:20px;">
                {{ $requests->links() }}
            </div>
        @endif
    </div>

@endsection