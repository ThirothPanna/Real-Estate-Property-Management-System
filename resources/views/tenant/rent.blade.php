@extends('layouts.app')

@section('title', 'Rent – NEKJOUL IMANAGE')

@section('content')

    <style>
        .page-header {
            display:flex; justify-content:space-between; align-items:flex-start;
            gap:16px; flex-wrap:wrap; margin-bottom:24px;
        }
        .page-h1 { font-size:24px; font-weight:700; margin-bottom:4px; }
        .page-sub { color:#6b7280; font-size:14px; }

        .btn-pay {
            background:#22c55e; color:#fff; border:none;
            padding:11px 22px; border-radius:10px; font-weight:600; font-size:14px;
            text-decoration:none; display:inline-flex; align-items:center; gap:8px;
            transition:transform .15s, opacity .15s;
        }
        .btn-pay:hover { transform:translateY(-2px); opacity:.94; }

        /* ---- Stat cards ---- */
        .stat-grid {
            display:grid;
            grid-template-columns:repeat(4, 1fr);
            gap:16px;
            margin-bottom:24px;
        }
        .stat-card {
            background:#fff; border-radius:14px; padding:20px;
            box-shadow:0 1px 3px rgba(0,0,0,.05);
            transition:transform .2s, box-shadow .2s;
        }
        .stat-card:hover {
            transform:translateY(-3px);
            box-shadow:0 8px 20px rgba(0,0,0,.08);
        }
        .stat-card .label {
            font-size:12px; color:#6b7280;
            text-transform:uppercase; letter-spacing:.5px;
        }
        .stat-card .value {
            font-size:24px; font-weight:700; margin-top:6px; color:#111827;
        }
        .stat-card .sub {
            font-size:12px; color:#9ca3af; margin-top:4px;
        }
        .stat-card.next-due .value { color:#16a34a; }
        .stat-card.next-due .sub   { color:#16a34a; }

        /* ---- Filter bar ---- */
        .filter-bar {
            display:flex; gap:10px; flex-wrap:wrap; align-items:center;
            margin-bottom:20px; padding:14px 18px;
            background:#fff; border:1px solid #e5e7eb; border-radius:12px;
        }
        .filter-bar label {
            font-size:13px; font-weight:600; color:#6b7280;
        }
        .filter-bar select {
            padding:8px 12px; border:1px solid #e5e7eb;
            border-radius:8px; font-size:13px; outline:none;
            font-family:inherit; color:#111827;
            background:#fff; cursor:pointer;
        }
        .filter-bar select:focus { border-color:#22c55e; }
        .filter-bar .btn-reset {
            padding:8px 14px; background:#fff; color:#6b7280;
            border:1px solid #e5e7eb; border-radius:8px;
            font-size:13px; font-weight:600; cursor:pointer;
            text-decoration:none;
        }
        .filter-bar .btn-reset:hover { background:#fef2f2; color:#dc2626; border-color:#fecaca; }

        /* ---- Panel + table ---- */
        .panel {
            background:#fff; border-radius:16px; padding:24px;
            box-shadow:0 1px 3px rgba(0,0,0,.05);
        }
        .panel h3 { font-size:16px; font-weight:700; margin-bottom:16px; }

        table { width:100%; border-collapse:collapse; font-size:14px; }
        th {
            text-align:left; color:#6b7280; font-weight:600;
            font-size:12px; text-transform:uppercase;
            letter-spacing:.4px; padding:10px 8px;
            border-bottom:1px solid #e5e7eb;
        }
        td {
            padding:14px 8px; border-bottom:1px solid #f3f4f6;
            color:#111827;
        }
        tr:last-child td { border-bottom:none; }
        tr:hover td { background:#f9fafb; }

        .badge {
            padding:4px 10px; border-radius:20px;
            font-size:12px; font-weight:600;
            display:inline-block;
        }
        .badge.completed { background:#dcfce7; color:#16a34a; }
        .badge.pending   { background:#fef3c7; color:#d97706; }
        .badge.failed    { background:#fee2e2; color:#dc2626; }

        .amount-cell {
            font-weight:700;
            color:#111827;
        }

        .btn-receipt {
            display:inline-flex; align-items:center; gap:6px;
            padding:6px 12px; border-radius:8px;
            border:1px solid #e5e7eb; background:#fff;
            font-size:12px; font-weight:600; color:#374151;
            text-decoration:none; cursor:pointer;
            transition:background .15s, color .15s, border-color .15s;
        }
        .btn-receipt:hover {
            background:#f0fdf4; color:#16a34a; border-color:#bbf7d0;
        }

        .empty {
            text-align:center; color:#9ca3af;
            padding:60px 20px; font-size:14px;
        }
        .empty-icon { font-size:48px; margin-bottom:12px; }

        @media (max-width: 900px) {
            .stat-grid { grid-template-columns:repeat(2, 1fr); }
        }
        @media (max-width: 500px) {
            .stat-grid { grid-template-columns:1fr; }
        }
    </style>

    @if (session('status'))
        <div style="background:#dcfce7; color:#16a34a; padding:12px 16px; border-radius:10px; margin-bottom:16px; font-size:14px;">
            {{ session('status') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="page-header">
        <div>
            <div class="page-h1">Rent</div>
            <div class="page-sub">Your rent payments and history.</div>
        </div>

        <a href="{{ route('tenant.pay') }}" class="btn-pay">
            💳 Pay Rent
        </a>
    </div>

    {{-- Stat cards --}}
    <div class="stat-grid">
        <div class="stat-card next-due">
            <div class="label">Next Rent Due</div>
            @if ($nextRentDue)
                <div class="value">{{ $nextRentDue->format('M d') }}</div>
                <div class="sub">{{ $nextRentDue->diffForHumans() }}</div>
            @else
                <div class="value">—</div>
                <div class="sub">No payments yet</div>
            @endif
        </div>

        <div class="stat-card">
            <div class="label">This Year</div>
            <div class="value">${{ number_format($thisYearTotal, 2) }}</div>
            <div class="sub">{{ $year }} total</div>
        </div>

        <div class="stat-card">
            <div class="label">Total Paid</div>
            <div class="value">${{ number_format($totalPaid, 2) }}</div>
            <div class="sub">All time</div>
        </div>

        <div class="stat-card">
            <div class="label">Last Payment</div>
            @if ($lastPayment)
                <div class="value">${{ number_format($lastPayment->amount, 2) }}</div>
                <div class="sub">{{ $lastPayment->paid_on->format('M d, Y') }}</div>
            @else
                <div class="value">—</div>
                <div class="sub">No payments yet</div>
            @endif
        </div>
    </div>

    {{-- Filter bar --}}
    <form method="GET" action="{{ route('tenant.rent') }}" class="filter-bar">
        <label>Year:</label>
        <select name="year" onchange="this.form.submit()">
            <option value="{{ date('Y') }}" {{ $year == date('Y') ? 'selected' : '' }}>
                {{ date('Y') }}
            </option>
            @foreach ($years as $y)
                @if ($y != date('Y'))
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                @endif
            @endforeach
        </select>

        @if ($year != date('Y'))
            <a href="{{ route('tenant.rent') }}" class="btn-reset">✕ Reset</a>
        @endif
    </form>

    {{-- Payment history --}}
    <div class="panel">
        <h3>Payment History — {{ $year }} ({{ $payments->total() }})</h3>

        @if ($payments->isEmpty())
            <div class="empty">
                <div class="empty-icon">💳</div>
                <div>No payments found for {{ $year }}.</div>
                <div style="margin-top:14px;">
                    <a href="{{ route('tenant.pay') }}" class="btn-pay">💳 Make a payment</a>
                </div>
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Method</th>
                        <th>Amount</th>
                        <th style="text-align:right;">Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payments as $p)
                        <tr>
                            <td>
                                <span class="badge {{ $p->status }}">
                                    {{ ucfirst($p->status) }}
                                </span>
                            </td>
                            <td>{{ $p->paid_on->format('M d, Y') }}</td>
                            <td>{{ $p->category }}</td>
                            <td style="color:#6b7280;">
                                {{ ucfirst(str_replace('_', ' ', $p->method)) }}
                            </td>
                            <td class="amount-cell">${{ number_format($p->amount, 2) }}</td>
                            <td style="text-align:right;">
                                <a href="{{ route('tenant.receipts.download', $p) }}" class="btn-receipt" title="Download receipt">
                                    ⬇ PDF
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="margin-top:20px;">
                {{ $payments->links() }}
            </div>
        @endif
    </div>

@endsection