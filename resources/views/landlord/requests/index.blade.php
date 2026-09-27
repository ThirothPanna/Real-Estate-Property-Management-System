@extends('layouts.app')

@section('title', 'Requests – NEKJOUL IMANAGE')

@section('content')

    <style>
        .req-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 22px;
        }
        .req-header h1 { font-size: 24px; font-weight: 700; margin-bottom: 4px; }
        .req-header p  { color: #6b7280; font-size: 14px; }

        .req-tabs {
            display: flex;
            gap: 6px;
            margin-bottom: 16px;
            border-bottom: 1px solid #e5e7eb;
        }
        .req-tab {
            padding: 10px 16px;
            font-size: 14px;
            font-weight: 600;
            color: #6b7280;
            text-decoration: none;
            border-bottom: 2px solid transparent;
            margin-bottom: -1px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .req-tab:hover { color: #16a34a; }
        .req-tab.active { color: #16a34a; border-color: #22c55e; }
        .req-tab .badge {
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 20px;
            background: #f3f4f6;
            color: #6b7280;
            font-weight: 700;
        }
        .req-tab.active .badge { background: #dcfce7; color: #16a34a; }

        .req-filters {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
            margin-bottom: 20px;
            padding: 14px 16px;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
        }
        .req-filters input[type="text"] {
            flex: 1;
            min-width: 220px;
            padding: 9px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 13px;
            outline: none;
        }
        .req-filters input[type="text"]:focus { border-color: #22c55e; }
        .req-filters button {
            padding: 9px 18px;
            background: #22c55e;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }
        .req-filters button:hover { background: #16a34a; }
        .req-filters a.clear {
            padding: 9px 14px;
            font-size: 13px;
            color: #6b7280;
            text-decoration: none;
        }

        .req-table-wrap {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,.05);
            overflow: hidden;
        }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        thead th {
            text-align: left;
            color: #6b7280;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .4px;
            padding: 14px 18px;
            border-bottom: 1px solid #e5e7eb;
            background: #f9fafb;
        }
        tbody td {
            padding: 16px 18px;
            border-bottom: 1px solid #f3f4f6;
            color: #111827;
            vertical-align: top;
        }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover { background: #fafafa; }

        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        .badge.pending     { background: #fef3c7; color: #d97706; }
        .badge.in_progress { background: #dbeafe; color: #2563eb; }
        .badge.resolved    { background: #dcfce7; color: #16a34a; }

        .priority { font-weight: 700; font-size: 12px; text-transform: uppercase; letter-spacing: .3px; }
        .priority.low    { color: #16a34a; }
        .priority.medium { color: #d97706; }
        .priority.high   { color: #dc2626; }
        .priority.urgent { color: #b91c1c; }

        .btn-update {
            padding: 6px 14px;
            font-size: 12px;
            font-weight: 600;
            color: #16a34a;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-update:hover { background: #dcfce7; }

        .empty {
            text-align: center;
            color: #9ca3af;
            padding: 60px 20px;
            font-size: 14px;
        }
        .empty-icon { font-size: 48px; margin-bottom: 12px; }

        .modal-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,.45);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 500;
        }
        .modal-overlay.open { display: flex; }
        .modal-box {
            background: #fff;
            border-radius: 16px;
            width: 100%;
            max-width: 480px;
            padding: 28px;
            box-shadow: 0 20px 60px rgba(0,0,0,.25);
        }
        .modal-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 6px;
        }
        .modal-sub {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 20px;
        }
        .modal-field {
            margin-bottom: 16px;
        }
        .modal-field label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .4px;
            margin-bottom: 6px;
        }
        .modal-field select,
        .modal-field textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            outline: none;
            font-family: inherit;
        }
        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }
    </style>

    @if (session('status'))
        <div style="background:#dcfce7; color:#16a34a; padding:12px 16px; border-radius:10px; margin-bottom:16px; font-size:14px;">
            {{ session('status') }}
        </div>
    @endif

    <div class="req-header">
        <div>
            <h1>Maintenance Requests</h1>
            <p>{{ $counts['all'] }} total · {{ $counts['pending'] }} pending · {{ $counts['in_progress'] }} in progress · {{ $counts['resolved'] }} resolved</p>
        </div>
    </div>

    <div class="req-tabs">
        <a href="{{ route('landlord.requests.index', array_merge(request()->except('tab','page'), ['tab' => 'all'])) }}"
           class="req-tab {{ $tab === 'all' ? 'active' : '' }}">
            All <span class="badge">{{ $counts['all'] }}</span>
        </a>
        <a href="{{ route('landlord.requests.index', array_merge(request()->except('tab','page'), ['tab' => 'pending'])) }}"
           class="req-tab {{ $tab === 'pending' ? 'active' : '' }}">
            Pending <span class="badge">{{ $counts['pending'] }}</span>
        </a>
        <a href="{{ route('landlord.requests.index', array_merge(request()->except('tab','page'), ['tab' => 'in_progress'])) }}"
           class="req-tab {{ $tab === 'in_progress' ? 'active' : '' }}">
            In Progress <span class="badge">{{ $counts['in_progress'] }}</span>
        </a>
        <a href="{{ route('landlord.requests.index', array_merge(request()->except('tab','page'), ['tab' => 'resolved'])) }}"
           class="req-tab {{ $tab === 'resolved' ? 'active' : '' }}">
            Resolved <span class="badge">{{ $counts['resolved'] }}</span>
        </a>
    </div>

    <form method="GET" action="{{ route('landlord.requests.index') }}" class="req-filters">
        <input type="hidden" name="tab" value="{{ $tab }}">
        <input type="text" name="search" value="{{ $search }}" placeholder="Search by tenant, title, or description…">
        <button type="submit">Search</button>
        @if ($search)
            <a href="{{ route('landlord.requests.index', ['tab' => $tab]) }}" class="clear">✕ Clear</a>
        @endif
    </form>

    @if ($requests->isEmpty())
        <div class="req-table-wrap">
            <div class="empty">
                <div class="empty-icon">🛠</div>
                <div>No requests found.</div>
            </div>
        </div>
    @else
        <div class="req-table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Tenant</th>
                        <th>Request</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th style="text-align:right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($requests as $r)
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <img src="{{ $r->user->avatar_url }}" alt=""
                                         style="width:36px; height:36px; border-radius:50%; object-fit:cover;">
                                    <div>
                                        <div style="font-weight:600;">{{ $r->user->name }}</div>
                                        <div style="font-size:12px; color:#6b7280;">{{ $r->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight:600; color:#111827;">{{ $r->title }}</div>
                                <div style="font-size:12px; color:#6b7280; max-width:320px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                    {{ $r->description }}
                                </div>
                            </td>
                            <td>
                                <span class="priority {{ $r->priority }}">{{ $r->priority }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $r->status }}">
                                    {{ ucfirst(str_replace('_', ' ', $r->status)) }}
                                </span>
                            </td>
                            <td style="font-size:13px; color:#6b7280;">
                                {{ $r->created_at->diffForHumans() }}
                            </td>
                            <td style="text-align:right;">
                                <button class="btn-update"
                                        onclick="openUpdateModal({{ $r->id }}, '{{ addslashes($r->title) }}', '{{ $r->status }}')">
                                    Update
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top:20px;">
            {{ $requests->links() }}
        </div>
    @endif

    <div class="modal-overlay" id="updateModal">
        <div class="modal-box">
            <div class="modal-title">Update Request</div>
            <div class="modal-sub" id="modalSub">Change the status of this request.</div>

            <form method="POST" id="updateForm" action="">
                @csrf
                @method('PATCH')

                <div class="modal-field">
                    <label>Status</label>
                    <select name="status" id="modalStatus" required>
                        <option value="pending">Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="resolved">Resolved</option>
                    </select>
                </div>

                <div class="modal-field">
                    <label>Note (optional)</label>
                    <textarea name="note" rows="3" placeholder="Add a note for the tenant…"></textarea>
                </div>

                <div class="modal-actions">
                    <button type="button" onclick="closeUpdateModal()" class="btn btn-outline">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openUpdateModal(id, title, currentStatus) {
            document.getElementById('modalSub').textContent = title;
            document.getElementById('modalStatus').value = currentStatus;
            document.getElementById('updateForm').action = '/landlord/requests/' + id;
            document.getElementById('updateModal').classList.add('open');
        }
        function closeUpdateModal() {
            document.getElementById('updateModal').classList.remove('open');
        }
        document.getElementById('updateModal').addEventListener('click', function (e) {
            if (e.target === this) closeUpdateModal();
        });
    </script>

@endsection