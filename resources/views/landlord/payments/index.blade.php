@extends('layouts.app')

@section('title', 'Payments – NEKJOUL IMANAGE')

@section('content')

    <style>
        .pay-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 22px;
        }
        .pay-header h1 { font-size: 24px; font-weight: 700; margin-bottom: 4px; }
        .pay-header p  { color: #6b7280; font-size: 14px; }

        .pay-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 20px;
        }
        .pay-stat {
            background: #fff;
            border-radius: 14px;
            padding: 18px;
            box-shadow: 0 1px 3px rgba(0,0,0,.05);
        }
        .pay-stat .label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .5px;
        }
        .pay-stat .value {
            font-size: 24px;
            font-weight: 700;
            margin-top: 6px;
        }
        .pay-stat .value.green { color: #16a34a; }
        .pay-stat .value.amber { color: #d97706; }
        .pay-stat .value.gray  { color: #111827; }

        .pay-tabs {
            display: flex;
            gap: 6px;
            margin-bottom: 16px;
            border-bottom: 1px solid #e5e7eb;
        }
        .pay-tab {
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
        .pay-tab:hover { color: #16a34a; }
        .pay-tab.active { color: #16a34a; border-color: #22c55e; }
        .pay-tab .badge {
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 20px;
            background: #f3f4f6;
            color: #6b7280;
            font-weight: 700;
        }
        .pay-tab.active .badge { background: #dcfce7; color: #16a34a; }

        .pay-filters {
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
        .pay-filters input[type="text"] {
            flex: 1;
            min-width: 200px;
            padding: 9px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 13px;
            outline: none;
        }
        .pay-filters input[type="text"]:focus { border-color: #22c55e; }
        .pay-filters select {
            padding: 9px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 13px;
            background: #fff;
            cursor: pointer;
        }
        .pay-filters button {
            padding: 9px 18px;
            background: #22c55e;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }
        .pay-filters button:hover { background: #16a34a; }
        .pay-filters a.clear {
            padding: 9px 14px;
            font-size: 13px;
            color: #6b7280;
            text-decoration: none;
        }

        .period-toggle {
            display: inline-flex;
            background: #f3f4f6;
            border-radius: 10px;
            padding: 4px;
            gap: 4px;
        }
        .period-toggle a {
            padding: 6px 14px;
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            text-decoration: none;
            border-radius: 6px;
        }
        .period-toggle a:hover { color: #16a34a; }
        .period-toggle a.active {
            background: #fff;
            color: #16a34a;
            box-shadow: 0 1px 3px rgba(0,0,0,.08);
        }

        .pay-table-wrap {
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
            vertical-align: middle;
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
        .badge.completed { background: #dcfce7; color: #16a34a; }
        .badge.pending   { background: #fef3c7; color: #d97706; }
        .badge.failed    { background: #fee2e2; color: #dc2626; }

        .amount-cell {
            font-weight: 700;
            color: #111827;
        }

        .btn-receipt {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            background: #fff;
            font-size: 12px;
            font-weight: 600;
            color: #374151;
            text-decoration: none;
        }
        .btn-receipt:hover {
            background: #f0fdf4;
            color: #16a34a;
            border-color: #bbf7d0;
        }

        .empty {
            text-align: center;
            color: #9ca3af;
            padding: 60px 20px;
            font-size: 14px;
        }
        .empty-icon { font-size: 48px; margin-bottom: 12px; }

        @media (max-width: 900px) {
            .pay-stats { grid-template-columns: 1fr; }
        }
    </style>

    @if (session('status'))
        <div style="background:#dcfce7; color:#16a34a; padding:12px 16px; border-radius:10px; margin-bottom:16px; font-size:14px;">
            {{ session('status') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="pay-header">
        <div>
            <h1>Payments</h1>
            <p>{{ $counts['all'] }} total payments · {{ $counts['completed'] }} completed · {{ $counts['pending'] }} pending</p>
        </div>

        <div class="period-toggle">
            <a href="{{ route('landlord.payments.index', array_merge(request()->except('period','page'), ['period' => 'day'])) }}"
               class="{{ $period === 'day' ? 'active' : '' }}">Today</a>
            <a href="{{ route('landlord.payments.index', array_merge(request()->except('period','page'), ['period' => 'month'])) }}"
               class="{{ $period === 'month' ? 'active' : '' }}">Month</a>
            <a href="{{ route('landlord.payments.index', array_merge(request()->except('period','page'), ['period' => 'year'])) }}"
               class="{{ $period === 'year' ? 'active' : '' }}">Year</a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="pay-stats">
        <div class="pay-stat">
            <div class="label">Total Collected</div>
            <div class="value green">${{ number_format($totals['completed'], 2) }}</div>
        </div>
        <div class="pay-stat">
            <div class="label">Pending</div>
            <div class="value amber">${{ number_format($totals['pending'], 2) }}</div>
        </div>
        <div class="pay-stat">
            <div class="label">Total Processed</div>
            <div class="value gray">${{ number_format($totals['all'], 2) }}</div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="pay-tabs">
        <a href="{{ route('landlord.payments.index', array_merge(request()->except('tab','page'), ['tab' => 'all'])) }}"
           class="pay-tab {{ $tab === 'all' ? 'active' : '' }}">
            All <span class="badge">{{ $counts['all'] }}</span>
        </a>
        <a href="{{ route('landlord.payments.index', array_merge(request()->except('tab','page'), ['tab' => 'completed'])) }}"
           class="pay-tab {{ $tab === 'completed' ? 'active' : '' }}">
            Completed <span class="badge">{{ $counts['completed'] }}</span>
        </a>
        <a href="{{ route('landlord.payments.index', array_merge(request()->except('tab','page'), ['tab' => 'pending'])) }}"
           class="pay-tab {{ $tab === 'pending' ? 'active' : '' }}">
            Pending <span class="badge">{{ $counts['pending'] }}</span>
        </a>
        <a href="{{ route('landlord.payments.index', array_merge(request()->except('tab','page'), ['tab' => 'failed'])) }}"
           class="pay-tab {{ $tab === 'failed' ? 'active' : '' }}">
            Failed <span class="badge">{{ $counts['failed'] }}</span>
        </a>
    </div>

    {{-- Search + Property filter --}}
    <form method="GET" action="{{ route('landlord.payments.index') }}" class="pay-filters">
        <input type="hidden" name="tab" value="{{ $tab }}">
        <input type="hidden" name="period" value="{{ $period }}">

        <input type="text" name="search" value="{{ $search }}" placeholder="Search by tenant name or email…">

        <select name="property_id">
            <option value="">All Properties</option>
            @foreach ($properties as $p)
                <option value="{{ $p->id }}" {{ $propertyId == $p->id ? 'selected' : '' }}>
                    {{ $p->name }}
                </option>
            @endforeach
        </select>

        <button type="submit">Filter</button>

        @if ($search || $propertyId)
            <a href="{{ route('landlord.payments.index', ['tab' => $tab, 'period' => $period]) }}" class="clear">✕ Clear</a>
        @endif
    </form>

    {{-- Table --}}
    @if ($payments->isEmpty())
        <div class="pay-table-wrap">
            <div class="empty">
                <div class="empty-icon">💳</div>
                <div>No payments found.</div>
            </div>
        </div>
    @else
        <div class="pay-table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Tenant</th>
                        <th>Property</th>
                        <th>Date</th>
                        <th>Method</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th style="text-align:right;">Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payments as $p)
                        @php
                            $tenancy = $tenantPropertyMap[$p->user_id] ?? null;
                        @endphp
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <img src="{{ $p->user->avatar_url }}" alt=""
                                         style="width:36px; height:36px; border-radius:50%; object-fit:cover;">
                                    <div>
                                        <div style="font-weight:600;">{{ $p->user->name }}</div>
                                        <div style="font-size:12px; color:#6b7280;">{{ $p->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="font-size:13px; color:#374151;">
                                @if ($tenancy && $tenancy->property)
                                    {{ $tenancy->property->name }}
                                @else
                                    <span style="color:#9ca3af;">—</span>
                                @endif
                            </td>
                            <td style="font-size:13px; color:#6b7280;">
                                {{ $p->paid_on->format('M d, Y') }}
                            </td>
                            <td style="font-size:13px; color:#6b7280;">
                                {{ ucfirst(str_replace('_', ' ', $p->method)) }}
                                @if ($p->card_last4)
                                    <div style="font-size:11px; color:#9ca3af;">•••• {{ $p->card_last4 }}</div>
                                @endif
                            </td>
                            <td class="amount-cell">${{ number_format($p->amount, 2) }}</td>
                            <td>
                                <span class="badge {{ $p->status }}">
                                    {{ ucfirst($p->status) }}
                                </span>
                            </td>
                            <td style="text-align:right;">
                                <a href="{{ route('landlord.payments.receipt.view', $p) }}" class="btn-receipt">View PDF</a>
                                <a href="{{ route('landlord.payments.receipt.download', $p) }}" class="btn-receipt">Download</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top:20px;">
            {{ $payments->links() }}
        </div>
    @endif

@endsection