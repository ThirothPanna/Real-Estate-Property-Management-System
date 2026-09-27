@extends('layouts.app')

@section('title', 'Leases – NEKJOUL IMANAGE')

@section('content')

    <style>
        .lease-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }
        .lease-header h1 { font-size: 24px; font-weight: 700; margin-bottom: 4px; }
        .lease-header p  { color: #6b7280; font-size: 14px; }

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

        .lease-list { display: flex; flex-direction: column; gap: 14px; }
        .lease-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 20px 22px;
            transition: border-color .15s, box-shadow .15s;
            display: flex;
            align-items: center;
            gap: 18px;
            flex-wrap: wrap;
        }
        .lease-card:hover {
            border-color: #bbf7d0;
            box-shadow: 0 6px 18px rgba(0,0,0,.06);
        }
        .lease-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: #fee2e2;
            color: #dc2626;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }
        .lease-info { flex: 1; min-width: 200px; }
        .lease-title {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 4px;
        }
        .lease-meta {
            font-size: 12px;
            color: #6b7280;
        }
        .lease-meta strong { color: #374151; }

        .lease-actions {
            display: flex;
            gap: 8px;
            flex-shrink: 0;
        }
        .lease-actions a,
        .lease-actions button {
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #374151;
            text-decoration: none;
            transition: background .15s, color .15s, border-color .15s;
        }
        .lease-actions a:hover { background: #f0fdf4; color: #16a34a; border-color: #bbf7d0; }
        .lease-actions button:hover { background: #fef2f2; color: #dc2626; border-color: #fecaca; }

        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .3px;
        }
        .badge.pending        { background: #fef3c7; color: #d97706; }
        .badge.acknowledged   { background: #dcfce7; color: #16a34a; }

        .empty {
            text-align: center;
            color: #9ca3af;
            padding: 60px 20px;
            font-size: 14px;
            background: #fff;
            border: 1px dashed #e5e7eb;
            border-radius: 16px;
        }
        .empty-icon { font-size: 52px; margin-bottom: 16px; }

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
            max-width: 520px;
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
        .modal-field input[type="text"],
        .modal-field input[type="file"],
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

    <div class="lease-header">
        <div>
            <h1>Leases</h1>
            <p>Create and share lease agreements with your tenants.</p>
        </div>

        <button class="btn-new" onclick="openLeaseModal()">
            📄 Create Lease
        </button>
    </div>

    @if ($leases->isEmpty())
        <div class="empty">
            <div class="empty-icon">📄</div>
            <div>No leases created yet.</div>
            <div style="margin-top:16px;">
                <button class="btn-new" onclick="openLeaseModal()">
                    📄 Create your first lease
                </button>
            </div>
        </div>
    @else
        <div class="lease-list">
            @foreach ($leases as $lease)
                <div class="lease-card">
                    <div class="lease-icon">📄</div>

                    <div class="lease-info">
                        <div class="lease-title">{{ $lease->title }}</div>
                        <div class="lease-meta">
                            <strong>{{ $lease->tenant->name }}</strong>
                            @if ($lease->tenancy && $lease->tenancy->property)
                                · {{ $lease->tenancy->property->name }}
                            @endif
                            · Shared {{ $lease->created_at->diffForHumans() }}
                        </div>
                    </div>

                    @if ($lease->isAcknowledged())
                        <span class="badge acknowledged">✓ Acknowledged</span>
                    @else
                        <span class="badge pending">⏳ Pending</span>
                    @endif

                    <div class="lease-actions">
                        <a href="{{ route('tenant.leases.download', $lease) }}" target="_blank">⬇ Download</a>

                        <form method="POST" action="{{ route('landlord.leases.destroy', $lease) }}"
                              onsubmit="return confirm('Delete this lease?');" style="margin:0;">
                            @csrf
                            @method('DELETE')
                            <button type="submit">✕ Delete</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div style="margin-top:20px;">
            {{ $leases->links() }}
        </div>
    @endif

    {{-- Modal --}}
    <div class="modal-overlay" id="leaseModal">
        <div class="modal-box">
            <div class="modal-title">Create Lease</div>
            <div class="modal-sub">Upload a lease PDF and share it with a tenant.</div>

            <form method="POST" action="{{ route('landlord.leases.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="modal-field">
                    <label>Tenancy</label>
                    <select name="tenancy_id" required>
                        <option value="">Select a tenancy…</option>
                        @foreach ($tenancies as $t)
                            <option value="{{ $t->id }}" {{ old('tenancy_id') == $t->id ? 'selected' : '' }}>
                                {{ $t->tenant->name }} — {{ $t->property->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="modal-field">
                    <label>Title</label>
                    <input type="text" name="title" required maxlength="255"
                           value="{{ old('title') }}"
                           placeholder="e.g. Lease Agreement 2026">
                </div>

                <div class="modal-field">
                    <label>Lease File</label>
                    <input type="file" name="lease_file" required
                           accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                    <p style="font-size:12px; color:#9ca3af; margin-top:6px;">
                        PDF, JPG, PNG, DOC, DOCX — max 10 MB
                    </p>
                </div>

                <div class="modal-field">
                    <label>Notes (optional)</label>
                    <textarea name="notes" rows="3"
                              placeholder="Any notes for the tenant…">{{ old('notes') }}</textarea>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeLeaseModal()">Cancel</button>
                    <button type="submit" class="btn-send">Share Lease</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openLeaseModal()  { document.getElementById('leaseModal').classList.add('open'); }
        function closeLeaseModal() { document.getElementById('leaseModal').classList.remove('open'); }
        document.getElementById('leaseModal').addEventListener('click', function (e) {
            if (e.target === this) closeLeaseModal();
        });

        @if ($errors->any())
            openLeaseModal();
        @endif
    </script>

@endsection