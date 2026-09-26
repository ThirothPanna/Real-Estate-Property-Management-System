@extends('layouts.app')

@section('title', 'Dashboard – NEKJOUL IMANAGE')

@section('content')

    <style>
        /* ---- Status badges ---- */
        .status-badge { padding:4px 10px; border-radius:20px; font-size:12px; font-weight:600; }
        .status-resolved      { background:#dcfce7; color:#16a34a; }
        .status-in-progress   { background:#dbeafe; color:#2563eb; }
        .status-pending,
        .status-default       { background:#fef3c7; color:#d97706; }
        .status-completed     { background:#dcfce7; color:#16a34a; }
        .status-failed        { background:#fee2e2; color:#dc2626; }

        /* ---- Success popup ---- */
        .success-overlay {
            position:fixed; inset:0; background:rgba(0,0,0,.45);
            display:none; align-items:center; justify-content:center; z-index:900;
        }
        .success-overlay.show { display:flex; animation:fadeIn .3s ease; }
        @keyframes fadeIn { from { opacity:0; } to { opacity:1; } }

        .success-card {
            background:#fff; border-radius:20px; padding:44px 48px;
            text-align:center; max-width:420px; width:90%;
            box-shadow:0 30px 80px rgba(0,0,0,.25);
            animation:popIn .45s cubic-bezier(.34,1.56,.64,1);
        }
        @keyframes popIn {
            from { transform:scale(.7); opacity:0; }
            to   { transform:scale(1);  opacity:1; }
        }
        .success-ring {
            width:96px; height:96px; border-radius:50%; background:#dcfce7;
            display:flex; align-items:center; justify-content:center; margin:0 auto 20px;
        }
        .success-ring svg { width:56px; height:56px; }
        .success-ring path {
            stroke:#16a34a; stroke-width:4; fill:none;
            stroke-linecap:round; stroke-linejoin:round;
            stroke-dasharray:60; stroke-dashoffset:60;
            animation:draw .5s .15s ease forwards;
        }
        @keyframes draw { to { stroke-dashoffset:0; } }
        .success-title { font-size:22px; font-weight:700; color:#111827; margin-bottom:6px; }
        .success-text { font-size:14px; color:#6b7280; margin-bottom:26px; }
        .success-btn {
            background:#22c55e; color:#fff; border:none;
            padding:12px 28px; border-radius:10px; font-weight:700; font-size:15px; cursor:pointer;
        }
        .success-btn:hover { background:#16a34a; }
    </style>

    {{-- ============ SUCCESS POPUP (after payment) ============ --}}
    @if (session('payment_success'))
        <div class="success-overlay show" id="successOverlayDash">
            <div class="success-card">
                <div class="success-ring">
                    <svg viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                </div>
                <div class="success-title">Payment Successful</div>
                <div class="success-text">
                    ${{ number_format(session('payment_success'), 2) }} has been recorded.
                </div>
                <button type="button" class="success-btn"
                        onclick="document.getElementById('successOverlayDash').style.display='none'">
                    Done
                </button>
            </div>
        </div>
    @endif

    @if (session('status'))
        <div style="background:#dcfce7; color:#16a34a; padding:12px 16px; border-radius:10px; margin-bottom:16px; font-size:14px;">
            {{ session('status') }}
        </div>
    @endif

    <div class="welcome">
        <h1>Welcome back, {{ auth()->user()->name }} 👋</h1>
        <p>Set up your tenancy to get started.</p>
    </div>

    <div class="stats">
        <div class="stat">
            <div class="label">Next Rent Due</div>
            <div class="value">—</div>
            <div class="trend">Not set</div>
        </div>
        <div class="stat">
            <div class="label">Lease Status</div>
            <div class="value">—</div>
            <div class="trend">No lease</div>
        </div>
        <div class="stat">
            <div class="label">Open Requests</div>
            <div class="value">{{ $requests->count() }}</div>
            <div class="trend">{{ $requests->where('status', 'pending')->count() }} pending</div>
        </div>
        <div class="stat">
            <div class="label">Total Paid</div>
            <div class="value">${{ number_format($payments->sum('amount'), 2) }}</div>
            <div class="trend">{{ $payments->count() }} payments</div>
        </div>
    </div>

    <div class="quick-actions">
        <a href="{{ route('tenant.pay') }}" class="btn btn-primary">💳 Pay Rent</a>
        <button class="btn btn-outline" onclick="openRequestModal()">🛠 Submit Request</button>
        <button class="btn btn-outline">📄 View Lease</button>
        <button class="btn btn-outline">📊 Report Payment</button>
    </div>

    <div class="panel">
        <h3>Lease</h3>
        <div class="empty">No lease yet. Your landlord hasn't shared a lease with you.</div>
    </div>

    <div class="panel">
        <h3>Recent Transactions</h3>
        <table>
            <thead>
                <tr><th>Status</th><th>Date</th><th>Category</th><th>Amount</th><th>Method</th></tr>
            </thead>
            <tbody>
                @if ($payments->isEmpty())
                    <tr><td colspan="5" style="text-align:center; padding:32px 0;">No transactions yet.</td></tr>
                @else
                    @foreach ($payments as $pay)
                        <tr>
                            <td>
                                <span class="status-badge status-{{ $pay->status }}">
                                    {{ ucfirst($pay->status) }}
                                </span>
                            </td>
                            <td style="color:#111827;">{{ $pay->paid_on->format('M d, Y') }}</td>
                            <td style="color:#111827;">{{ $pay->category }}</td>
                            <td style="color:#111827; font-weight:600;">${{ number_format($pay->amount, 2) }}</td>
                            <td style="color:#6b7280;">{{ ucfirst(str_replace('_', ' ', $pay->method)) }}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

    <div class="panel">
        <h3>Maintenance Requests</h3>

        @if ($requests->isEmpty())
            <div class="empty">You haven't submitted any requests yet.</div>
        @else
            <div style="display:flex; flex-direction:column; gap:12px;">
                @foreach ($requests as $req)
                    <div style="display:flex; justify-content:space-between; align-items:center; padding:14px; border:1px solid #f3f4f6; border-radius:12px;">
                        <div>
                            <div style="font-size:14px; font-weight:600; color:#111827;">{{ $req->title }}</div>
                            <div style="font-size:12px; color:#6b7280; margin-top:2px;">
                                {{ $req->created_at->diffForHumans() }} · Priority: {{ ucfirst($req->priority) }}
                            </div>
                        </div>
                        @php
                            $statusClass = match ($req->status) {
                                'resolved'    => 'status-resolved',
                                'in_progress' => 'status-in-progress',
                                default       => 'status-default',
                            };
                        @endphp
                        <span class="status-badge {{ $statusClass }}">
                            {{ ucfirst(str_replace('_', ' ', $req->status)) }}
                        </span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="panel" style="display:flex; align-items:center; justify-content:space-between; gap:20px; flex-wrap:wrap;">
        <div>
            <h3 style="margin-bottom:6px;">Rent Reporting &amp; Credit Boost</h3>
            <p style="color:#6b7280; font-size:14px;">Report rent payments you're already making to major credit bureaus.</p>
        </div>
        <button class="btn btn-primary">Enroll</button>
    </div>

    {{-- ============ LEASE DOCUMENTS (with uploader) ============ --}}
    <div class="panel">
        <h3>Lease Documents</h3>

        {{-- Hidden file input + form --}}
        <form method="POST" action="{{ route('tenant.documents.store') }}" enctype="multipart/form-data" id="uploadForm">
            @csrf
            <input type="file" name="document" id="documentInput" style="display:none;"
                   accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
        </form>

        {{-- Click-to-upload box --}}
        <div class="lease-upload" onclick="document.getElementById('documentInput').click()">
            📎 Click to upload lease documents
            <div style="font-size:12px; color:#cbd5e1; margin-top:6px;">
                PDF, JPG, PNG, DOC, DOCX · Max 10 MB
            </div>
        </div>

        @error('document')
            <div style="color:#ef4444; font-size:13px; margin-top:8px;">{{ $message }}</div>
        @enderror

        {{-- Uploaded documents list --}}
        @if ($documents->isNotEmpty())
            <div style="margin-top:20px; display:flex; flex-direction:column; gap:10px;">
                @foreach ($documents as $doc)
                    <div style="display:flex; align-items:center; gap:14px; padding:14px; border:1px solid #f3f4f6; border-radius:12px;">

                        {{-- Icon --}}
                        <div style="width:40px; height:40px; border-radius:10px; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:18px;
                            @if($doc->icon === 'pdf')   background:#fee2e2;
                            @elseif($doc->icon === 'image') background:#dbeafe;
                            @elseif($doc->icon === 'doc')   background:#e0e7ff;
                            @else background:#f3f4f6;
                            @endif">
                            @if ($doc->icon === 'pdf')   📄
                            @elseif ($doc->icon === 'image') 🖼️
                            @elseif ($doc->icon === 'doc')   📝
                            @else 📎
                            @endif
                        </div>

                        {{-- Info --}}
                        <div style="flex:1; min-width:0;">
                            <div style="font-size:14px; font-weight:600; color:#111827; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                {{ $doc->original_name }}
                            </div>
                            <div style="font-size:12px; color:#6b7280; margin-top:2px;">
                                {{ $doc->readable_size }} · Uploaded {{ $doc->created_at->diffForHumans() }}
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div style="display:flex; gap:6px; flex-shrink:0;">
                            <a href="{{ route('tenant.documents.download', $doc) }}"
                               style="width:32px; height:32px; border-radius:8px; border:1px solid #e5e7eb; display:flex; align-items:center; justify-content:center; color:#6b7280; text-decoration:none;"
                               title="Download">⬇</a>

                            <form method="POST" action="{{ route('tenant.documents.destroy', $doc) }}"
                                  onsubmit="return confirm('Delete this document?');" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        style="width:32px; height:32px; border-radius:8px; border:1px solid #e5e7eb; background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; color:#6b7280;"
                                        title="Delete">✕</button>
                            </form>
                        </div>

                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ============ REQUEST MODAL ============ --}}
    <div id="requestModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:500; align-items:center; justify-content:center;">
        <div style="background:#fff; border-radius:16px; width:100%; max-width:520px; padding:28px; box-shadow:0 20px 60px rgba(0,0,0,.25);">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <h3 style="font-size:18px; margin:0;">Submit Maintenance Request</h3>
                <button onclick="closeRequestModal()" style="background:none; border:none; cursor:pointer; font-size:22px; color:#6b7280; line-height:1;">&times;</button>
            </div>

            <form method="POST" action="{{ route('tenant.requests.store') }}">
                @csrf

                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:12px; color:#6b7280; font-weight:600; text-transform:uppercase; margin-bottom:6px;">Title</label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           placeholder="e.g. Leaking kitchen faucet"
                           style="width:100%; padding:10px 12px; border:1px solid #e5e7eb; border-radius:10px; font-size:14px; outline:none;">
                    @error('title') <span style="color:#ef4444; font-size:12px;">{{ $message }}</span> @enderror
                </div>

                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:12px; color:#6b7280; font-weight:600; text-transform:uppercase; margin-bottom:6px;">Description</label>
                    <textarea name="description" rows="4" required
                              placeholder="Describe the problem in detail"
                              style="width:100%; padding:10px 12px; border:1px solid #e5e7eb; border-radius:10px; font-size:14px; outline:none; resize:vertical; font-family:inherit;">{{ old('description') }}</textarea>
                    @error('description') <span style="color:#ef4444; font-size:12px;">{{ $message }}</span> @enderror
                </div>

                <div style="margin-bottom:20px;">
                    <label style="display:block; font-size:12px; color:#6b7280; font-weight:600; text-transform:uppercase; margin-bottom:6px;">Priority</label>
                    <select name="priority" required
                            style="width:100%; padding:10px 12px; border:1px solid #e5e7eb; border-radius:10px; font-size:14px; outline:none; background:#fff;">
                        <option value="low"    {{ old('priority') === 'low'    ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high"   {{ old('priority') === 'high'   ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                    @error('priority') <span style="color:#ef4444; font-size:12px;">{{ $message }}</span> @enderror
                </div>

                <div style="display:flex; gap:10px; justify-content:flex-end;">
                    <button type="button" onclick="closeRequestModal()" class="btn btn-outline">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ============ MODAL SCRIPTS ============ --}}
    <script>
        function openRequestModal()  { document.getElementById('requestModal').style.display = 'flex'; }
        function closeRequestModal() { document.getElementById('requestModal').style.display = 'none'; }

        document.addEventListener('DOMContentLoaded', function () {
            var m = document.getElementById('requestModal');
            if (!m) return;
            m.addEventListener('click', function (e) {
                if (e.target === m) m.style.display = 'none';
            });

            // Auto-submit the upload form when a file is selected
            var input = document.getElementById('documentInput');
            if (input) {
                input.addEventListener('change', function () {
                    if (this.files.length > 0) {
                        document.getElementById('uploadForm').submit();
                    }
                });
            }
        });
    </script>

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                openRequestModal();
            });
        </script>
    @endif

@endsection