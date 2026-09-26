@extends('layouts.app')

@section('title', 'Notifications – TenantCloud')

@section('content')

    <style>
        .notif-header { display:flex; justify-content:space-between; align-items:flex-start; gap:16px; flex-wrap:wrap; margin-bottom:22px; }
        .notif-header h1 { font-size:24px; font-weight:700; margin-bottom:4px; }
        .notif-header p  { color:#6b7280; font-size:14px; }

        .notif-actions { display:flex; gap:10px; flex-wrap:wrap; }
        .notif-actions button {
            padding:9px 16px; border-radius:10px; font-size:13px; font-weight:600;
            border:1px solid #e5e7eb; background:#fff; color:#374151; cursor:pointer;
            transition:background .15s, color .15s;
        }
        .notif-actions button:hover { background:#f0fdf4; color:#16a34a; border-color:#bbf7d0; }
        .notif-actions .danger:hover { background:#fef2f2; color:#dc2626; border-color:#fecaca; }

        .notif-tabs { display:flex; gap:6px; margin-bottom:18px; border-bottom:1px solid #e5e7eb; }
        .notif-tab {
            padding:10px 16px; font-size:14px; font-weight:600;
            color:#6b7280; text-decoration:none;
            border-bottom:2px solid transparent; margin-bottom:-1px;
            display:flex; align-items:center; gap:8px;
        }
        .notif-tab:hover { color:#16a34a; }
        .notif-tab.active { color:#16a34a; border-color:#22c55e; }
        .notif-tab .badge {
            font-size:11px; padding:2px 8px; border-radius:20px;
            background:#f3f4f6; color:#6b7280; font-weight:700;
        }
        .notif-tab.active .badge { background:#dcfce7; color:#16a34a; }

        .notif-filters {
            display:flex; gap:10px; flex-wrap:wrap; align-items:center;
            margin-bottom:20px; padding:14px 16px;
            background:#fff; border:1px solid #e5e7eb; border-radius:12px;
        }
        .notif-filters input[type="text"] {
            padding:9px 12px; border:1px solid #e5e7eb; border-radius:8px;
            font-size:13px; outline:none; font-family:inherit; color:#111827;
        }
        .notif-filters input[type="text"]:focus { border-color:#22c55e; }
        .notif-filters .filter-search { flex:1; min-width:200px; }

        .notif-filters .date-field {
            display:inline-flex; align-items:center; gap:8px;
            padding:0 12px; border:1px solid #e5e7eb; border-radius:8px;
            background:#fff;
        }
        .notif-filters .date-field span {
            font-size:12px; font-weight:600; color:#6b7280;
            text-transform:uppercase; letter-spacing:.4px;
        }
        .notif-filters .date-field input[type="date"] {
            border:none; padding:9px 0; font-size:13px;
            outline:none; font-family:inherit; color:#111827;
            background:transparent; cursor:pointer;
        }

        .notif-filters button {
            padding:9px 18px; background:#22c55e; color:#fff; border:none;
            border-radius:8px; font-size:13px; font-weight:600; cursor:pointer;
        }
        .notif-filters button:hover { background:#16a34a; }
        .notif-filters a.clear {
            padding:9px 14px; font-size:13px; color:#6b7280; text-decoration:none;
            display:flex; align-items:center;
        }
        .notif-filters a.clear:hover { color:#dc2626; }

        .notif-list { display:flex; flex-direction:column; gap:10px; }
        .notif-item {
            display:flex; gap:16px; align-items:flex-start;
            background:#fff; border:1px solid #e5e7eb; border-radius:12px;
            padding:16px 18px; position:relative;
        }
        .notif-item.unread { background:#f0fdf4; border-color:#bbf7d0; }
        .notif-item.unread::before {
            content:''; position:absolute; left:0; top:0; bottom:0;
            width:4px; background:#22c55e; border-radius:12px 0 0 12px;
        }
        .notif-icon {
            width:40px; height:40px; border-radius:10px; flex-shrink:0;
            display:flex; align-items:center; justify-content:center;
            font-size:18px;
        }
        .notif-icon.system  { background:#e0e7ff; }
        .notif-icon.payment { background:#dcfce7; }
        .notif-icon.request { background:#fef3c7; }
        .notif-icon.lease   { background:#dbeafe; }

        .notif-content { flex:1; min-width:0; }
        .notif-title { font-size:15px; font-weight:600; color:#111827; margin-bottom:4px; }
        .notif-body  { font-size:13px; color:#6b7280; line-height:1.5; word-wrap:break-word; }
        .notif-meta  { font-size:12px; color:#9ca3af; margin-top:6px; }

        .notif-buttons { display:flex; gap:6px; flex-shrink:0; }
        .notif-buttons form { display:inline; }
        .notif-buttons button {
            width:32px; height:32px; border-radius:8px; border:1px solid #e5e7eb;
            background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center;
            color:#6b7280; font-size:14px;
        }
        .notif-buttons button:hover { background:#f0fdf4; color:#16a34a; border-color:#bbf7d0; }
        .notif-buttons button.danger:hover { background:#fef2f2; color:#dc2626; border-color:#fecaca; }

        .notif-empty {
            text-align:center; padding:60px 20px; color:#9ca3af; font-size:14px;
            background:#fff; border:1px dashed #e5e7eb; border-radius:16px;
        }
        .notif-empty svg { color:#d1d5db; margin-bottom:12px; }
    </style>

    <div class="notif-header">
        <div>
            <h1>Notifications</h1>
            <p>{{ $counts['unread'] }} unread · {{ $counts['all'] }} total</p>
        </div>

        <div class="notif-actions">
            <form method="POST" action="{{ route('tenant.notifications.markAllRead') }}">
                @csrf
                <button type="submit">✓ Mark all read</button>
            </form>
            <form method="POST" action="{{ route('tenant.notifications.markAllUnread') }}">
                @csrf
                <button type="submit">○ Mark all unread</button>
            </form>
            <form method="POST" action="{{ route('tenant.notifications.destroyAll') }}"
                  onsubmit="return confirm('Delete ALL notifications? This cannot be undone.');">
                @csrf
                <button type="submit" class="danger">🗑 Delete all</button>
            </form>
        </div>
    </div>

    @if (session('status'))
        <div style="background:#dcfce7; color:#16a34a; padding:12px 16px; border-radius:10px; margin-bottom:16px; font-size:14px;">
            {{ session('status') }}
        </div>
    @endif

    <div class="notif-tabs">
        <a href="{{ route('tenant.notifications', array_merge(request()->except('tab', 'page'), ['tab' => 'all'])) }}"
           class="notif-tab {{ $tab === 'all' ? 'active' : '' }}">
            All <span class="badge">{{ $counts['all'] }}</span>
        </a>
        <a href="{{ route('tenant.notifications', array_merge(request()->except('tab', 'page'), ['tab' => 'unread'])) }}"
           class="notif-tab {{ $tab === 'unread' ? 'active' : '' }}">
            Unread <span class="badge">{{ $counts['unread'] }}</span>
        </a>
        <a href="{{ route('tenant.notifications', array_merge(request()->except('tab', 'page'), ['tab' => 'read'])) }}"
           class="notif-tab {{ $tab === 'read' ? 'active' : '' }}">
            Read <span class="badge">{{ $counts['read'] }}</span>
        </a>
    </div>

    <form method="GET" action="{{ route('tenant.notifications') }}" class="notif-filters">
        <input type="hidden" name="tab" value="{{ $tab }}">
        <input type="text" name="search" class="filter-search" value="{{ $search }}" placeholder="Search notifications…">
        <label class="date-field"><span>From</span><input type="date" name="from" value="{{ $from }}"></label>
        <label class="date-field"><span>To</span><input type="date" name="to" value="{{ $to }}"></label>
        <button type="submit">Filter</button>
        @if ($search || $from || $to)
            <a href="{{ route('tenant.notifications', ['tab' => $tab]) }}" class="clear">✕ Clear</a>
        @endif
    </form>

    @if ($notifications->isEmpty())
        <div class="notif-empty">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
            </svg>
            <div>
                @if ($search || $from || $to)
                    No notifications match your filters.
                @elseif ($tab === 'unread')
                    You're all caught up — no unread notifications.
                @elseif ($tab === 'read')
                    You haven't read any notifications yet.
                @else
                    You don't have any notifications yet.
                @endif
            </div>
        </div>
    @else
        <div class="notif-list">
            @foreach ($notifications as $n)
                <div class="notif-item {{ $n->isUnread() ? 'unread' : '' }}">
                    <div class="notif-icon {{ $n->icon }}">
                        @if ($n->icon === 'payment') 💳
                        @elseif ($n->icon === 'request') 🛠
                        @elseif ($n->icon === 'lease') 📄
                        @else 🔔
                        @endif
                    </div>

                    <div class="notif-content">
                        <div class="notif-title">{{ $n->title }}</div>
                        @if ($n->body)
                            <div class="notif-body">{{ $n->body }}</div>
                        @endif
                        <div class="notif-meta">
                            {{ $n->created_at->diffForHumans() }}
                            @if ($n->isUnread())
                                · <strong style="color:#16a34a;">Unread</strong>
                            @else
                                · Read {{ $n->read_at->diffForHumans() }}
                            @endif
                        </div>
                    </div>

                    <div class="notif-buttons">
                        @if ($n->isUnread())
                            <form method="POST" action="{{ route('tenant.notifications.read', $n) }}">
                                @csrf
                                <button type="submit" title="Mark as read">✓</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('tenant.notifications.unread', $n) }}">
                                @csrf
                                <button type="submit" title="Mark as unread">○</button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('tenant.notifications.destroy', $n) }}"
                              onsubmit="return confirm('Delete this notification?');">
                            @csrf
                            <button type="submit" class="danger" title="Delete">✕</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div style="margin-top:22px;">
            {{ $notifications->links() }}
        </div>
    @endif

@endsection