@extends('layouts.app')

@section('title', 'Announcements – NEKJOUL IMANAGE')

@section('content')

    <style>
        .ann-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }
        .ann-header h1 { font-size: 24px; font-weight: 700; margin-bottom: 4px; }
        .ann-header p  { color: #6b7280; font-size: 14px; }

        .btn-new {
            background: #22c55e;
            color: #fff;
            border: none;
            padding: 11px 22px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-new:hover { background: #16a34a; }

        /* Announcement cards */
        .ann-list { display: flex; flex-direction: column; gap: 14px; }
        .ann-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 20px 22px;
            transition: border-color .15s, box-shadow .15s;
        }
        .ann-card:hover {
            border-color: #bbf7d0;
            box-shadow: 0 6px 18px rgba(0,0,0,.06);
        }
        .ann-card-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 10px;
        }
        .ann-title {
            font-size: 16px;
            font-weight: 700;
            color: #111827;
        }
        .ann-meta {
            font-size: 12px;
            color: #6b7280;
            margin-top: 4px;
        }
        .ann-body {
            font-size: 14px;
            color: #374151;
            line-height: 1.55;
            margin-bottom: 14px;
            white-space: pre-wrap;
        }
        .ann-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 12px;
            border-top: 1px solid #f3f4f6;
            font-size: 12px;
            color: #9ca3af;
        }

        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .3px;
        }
        .badge.all        { background: #dcfce7; color: #16a34a; }
        .badge.property   { background: #dbeafe; color: #2563eb; }
        .badge.individual { background: #fef3c7; color: #d97706; }

        .btn-delete {
            background: #fff;
            color: #dc2626;
            border: 1px solid #fecaca;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-delete:hover { background: #fef2f2; }

        .empty {
            text-align: center;
            color: #9ca3af;
            padding: 60px 20px;
            font-size: 14px;
            background: #fff;
            border: 1px dashed #e5e7eb;
            border-radius: 16px;
        }
        .empty-icon { font-size: 48px; margin-bottom: 12px; }

        /* Modal */
        .modal-overlay {
            position: fixed; inset: 0;
            background: rgba(15,23,42,.55);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 500;
            padding: 20px;
        }
        .modal-overlay.open { display: flex; }
        .modal-box {
            background: #fff;
            border-radius: 18px;
            width: 100%;
            max-width: 560px;
            max-height: 90vh;
            overflow-y: auto;
            padding: 28px 32px;
            box-shadow: 0 30px 80px rgba(0,0,0,.30);
        }
        .modal-title { font-size: 20px; font-weight: 700; margin-bottom: 6px; }
        .modal-sub   { font-size: 13px; color: #6b7280; margin-bottom: 22px; }

        .modal-field { margin-bottom: 18px; }
        .modal-field label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .4px;
            margin-bottom: 6px;
        }
        .modal-field input,
        .modal-field textarea,
        .modal-field select {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            outline: none;
            font-family: inherit;
            background: #fff;
        }
        .modal-field input:focus,
        .modal-field textarea:focus,
        .modal-field select:focus { border-color: #22c55e; }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 24px;
        }
        .btn-cancel {
            padding: 10px 20px;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #374151;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
        }
        .btn-send {
            padding: 10px 22px;
            border-radius: 10px;
            border: none;
            background: #22c55e;
            color: #fff;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
        }
        .btn-send:hover { background: #16a34a; }

        /* Radio cards */
        .audience-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }
        .audience-card {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 14px;
            text-align: center;
            cursor: pointer;
            transition: border-color .15s, background .15s;
            position: relative;
        }
        .audience-card:hover { border-color: #bbf7d0; }
        .audience-card input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }
        .audience-card.selected {
            border-color: #22c55e;
            background: #f0fdf4;
        }
        .audience-icon { font-size: 22px; margin-bottom: 4px; }
        .audience-label { font-size: 13px; font-weight: 600; color: #111827; }
    </style>

    @if (session('status'))
        <div style="background:#dcfce7; color:#16a34a; padding:12px 16px; border-radius:10px; margin-bottom:16px; font-size:14px;">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div style="background:#fef2f2; color:#dc2626; padding:12px 16px; border-radius:10px; margin-bottom:16px; font-size:14px;">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- Header --}}
    <div class="ann-header">
        <div>
            <h1>Announcements</h1>
            <p>Send messages to your tenants. They'll receive a notification instantly.</p>
        </div>

        <button class="btn-new" onclick="openComposeModal()">
            New Announcement
        </button>
    </div>

    {{-- List --}}
    @if ($announcements->isEmpty())
        <div class="empty">
            <div class="empty-icon">◉</div>
            <div>You haven't sent any announcements yet.</div>
            <div style="margin-top:16px;">
                <button class="btn-new" onclick="openComposeModal()">
                    Send your first announcement
                </button>
            </div>
        </div>
    @else
        <div class="ann-list">
            @foreach ($announcements as $a)
                <div class="ann-card">
                    <div class="ann-card-head">
                        <div>
                            <div class="ann-title">{{ $a->title }}</div>
                            <div class="ann-meta">
                                Sent {{ $a->created_at->diffForHumans() }}
                                @if ($a->property)
                                    · to <strong>{{ $a->property->name }}</strong>
                                @endif
                            </div>
                        </div>

                        <span class="badge {{ $a->audience }}">
                            @if ($a->audience === 'all') All tenants
                            @elseif ($a->audience === 'property') By property
                            @else Individual
                            @endif
                        </span>
                    </div>

                    <div class="ann-body">{{ $a->body }}</div>

                    <div class="ann-footer">
                        <span>{{ $a->recipient_count }} recipient(s)</span>

                        <form method="POST" action="{{ route('landlord.announcements.destroy', $a) }}"
                              onsubmit="return confirm('Delete this announcement?');" style="margin:0;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-delete">Delete</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div style="margin-top:20px;">
            {{ $announcements->links() }}
        </div>
    @endif

    {{-- Compose Modal --}}
    <div class="modal-overlay" id="composeModal">
        <div class="modal-box">
            <div class="modal-title">New Announcement</div>
            <div class="modal-sub">This will send a notification to each recipient.</div>

            <form method="POST" action="{{ route('landlord.announcements.store') }}">
                @csrf

                <div class="modal-field">
                    <label>Title</label>
                    <input type="text" name="title" required maxlength="255"
                           value="{{ old('title') }}"
                           placeholder="e.g. Water maintenance on Tuesday">
                </div>

                <div class="modal-field">
                    <label>Message</label>
                    <textarea name="body" rows="5" required maxlength="2000"
                              placeholder="Write your message here…">{{ old('body') }}</textarea>
                </div>

                <div class="modal-field">
                    <label>Send to</label>

                    <div class="audience-grid">
                        <label class="audience-card selected" id="card-all" onclick="selectAudience('all')">
                            <input type="radio" name="audience" value="all" checked>
                            <div class="audience-icon">◉</div>
                            <div class="audience-label">All Tenants</div>
                        </label>

                        <label class="audience-card" id="card-property" onclick="selectAudience('property')">
                            <input type="radio" name="audience" value="property">
                            <div class="audience-icon">▣</div>
                            <div class="audience-label">By Property</div>
                        </label>

                        <label class="audience-card" id="card-individual" onclick="selectAudience('individual')">
                            <input type="radio" name="audience" value="individual">
                            <div class="audience-icon">◎</div>
                            <div class="audience-label">Individual</div>
                        </label>
                    </div>
                </div>

                {{-- Property selector --}}
                <div class="modal-field" id="propertyField" style="display:none;">
                    <label>Property</label>
                    <select name="property_id">
                        <option value="">Select a property…</option>
                        @foreach ($properties as $p)
                            <option value="{{ $p->id }}" {{ old('property_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tenant selector --}}
                <div class="modal-field" id="userField" style="display:none;">
                    <label>Tenant</label>
                    <select name="user_id">
                        <option value="">Select a tenant…</option>
                        @foreach ($tenants as $t)
                            <option value="{{ $t->id }}" {{ old('user_id') == $t->id ? 'selected' : '' }}>
                                {{ $t->name }} ({{ $t->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeComposeModal()">Cancel</button>
                    <button type="submit" class="btn-send">Send Announcement</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openComposeModal() {
            document.getElementById('composeModal').classList.add('open');
        }
        function closeComposeModal() {
            document.getElementById('composeModal').classList.remove('open');
        }
        document.getElementById('composeModal').addEventListener('click', function (e) {
            if (e.target === this) closeComposeModal();
        });

        function selectAudience(type) {
            // Update card styles
            document.getElementById('card-all').classList.toggle('selected', type === 'all');
            document.getElementById('card-property').classList.toggle('selected', type === 'property');
            document.getElementById('card-individual').classList.toggle('selected', type === 'individual');

            // Show/hide the relevant field
            document.getElementById('propertyField').style.display = (type === 'property') ? 'block' : 'none';
            document.getElementById('userField').style.display     = (type === 'individual') ? 'block' : 'none';
        }

        // Auto-open modal if there were validation errors
        @if ($errors->any())
            openComposeModal();
        @endif
    </script>

@endsection