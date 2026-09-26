@extends('layouts.app')

@section('title', $tenancy->tenant->name . ' – NEKJOUL IMANAGE')

@section('content')

    @if (session('status'))
        <div style="background:#dcfce7; color:#16a34a; padding:12px 16px; border-radius:10px; margin-bottom:16px; font-size:14px;">
            {{ session('status') }}
        </div>
    @endif

    @if (session('temp_password'))
        <div style="background:#fef3c7; border-left:4px solid #d97706; padding:16px 20px; border-radius:10px; margin-bottom:16px;">
            <div style="font-weight:700; color:#92400e; margin-bottom:6px;">⚠ Temporary Password</div>
            <div style="font-size:13px; color:#78350f; margin-bottom:10px;">
                Share this with the tenant so they can log in. Ask them to change it in Settings.
            </div>
            <div style="display:flex; align-items:center; gap:10px;">
                <code style="background:#fff; padding:10px 14px; border-radius:8px; font-family:monospace; font-size:15px; font-weight:700; color:#111827; border:1px solid #fcd34d;">
                    {{ session('temp_password') }}
                </code>
            </div>
        </div>
    @endif

    <div style="max-width:900px; margin:0 auto;">

        <a href="{{ route('landlord.tenants.index') }}" style="font-size:13px; color:#6b7280; text-decoration:none; display:inline-block; margin-bottom:16px;">← Back to Tenants</a>

        {{-- Tenant hero --}}
        <div class="panel" style="display:flex; align-items:center; gap:20px; flex-wrap:wrap;">
            <img src="{{ $tenancy->tenant->avatar_url }}" alt=""
                 style="width:80px; height:80px; border-radius:50%; object-fit:cover;">

            <div style="flex:1; min-width:200px;">
                <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                    <h1 style="font-size:22px; font-weight:700; margin:0;">{{ $tenancy->tenant->name }}</h1>
                    <span style="padding:4px 10px; border-radius:20px; font-size:12px; font-weight:600; {{ $tenancy->status_badge }}">
                        {{ ucfirst($tenancy->status) }}
                    </span>
                </div>
                <div style="color:#6b7280; font-size:14px; margin-top:6px;">{{ $tenancy->tenant->email }}</div>
                @if ($tenancy->tenant->phone)
                    <div style="color:#6b7280; font-size:14px;">{{ $tenancy->tenant->phone }}</div>
                @endif
            </div>
        </div>

        {{-- Lease details --}}
        <div class="panel">
            <h3>Lease Details</h3>

            <div style="display:grid; grid-template-columns:repeat(2, 1fr); gap:20px; margin-top:16px;">
                <div>
                    <div style="font-size:12px; color:#6b7280; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">Property</div>
                    <div style="font-size:15px; font-weight:600; color:#111827;">
                        <a href="{{ route('landlord.properties.show', $tenancy->property) }}"
                           style="color:#16a34a; text-decoration:none;">
                            {{ $tenancy->property->name }}
                        </a>
                    </div>
                </div>

                <div>
                    <div style="font-size:12px; color:#6b7280; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">Address</div>
                    <div style="font-size:14px; color:#374151;">{{ $tenancy->property->full_address }}</div>
                </div>

                <div>
                    <div style="font-size:12px; color:#6b7280; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">Lease Period</div>
                    <div style="font-size:14px; color:#374151;">
                        {{ $tenancy->lease_start->format('M d, Y') }} – {{ $tenancy->lease_end->format('M d, Y') }}
                    </div>
                </div>

                <div>
                    <div style="font-size:12px; color:#6b7280; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">Monthly Rent</div>
                    <div style="font-size:18px; font-weight:800; color:#16a34a;">${{ number_format($tenancy->rent_amount, 2) }}</div>
                </div>

                @if ($tenancy->security_deposit)
                    <div>
                        <div style="font-size:12px; color:#6b7280; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">Security Deposit</div>
                        <div style="font-size:14px; color:#374151;">${{ number_format($tenancy->security_deposit, 2) }}</div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Recent payments --}}
        <div class="panel">
            <h3>Recent Payments</h3>

            @if ($recentPayments->isEmpty())
                <div class="empty">No payments yet.</div>
            @else
                <table style="width:100%; border-collapse:collapse; font-size:14px;">
                    <thead>
                        <tr>
                            <th style="text-align:left; color:#6b7280; font-weight:600; font-size:12px; text-transform:uppercase; padding:10px 8px; border-bottom:1px solid #e5e7eb;">Date</th>
                            <th style="text-align:left; color:#6b7280; font-weight:600; font-size:12px; text-transform:uppercase; padding:10px 8px; border-bottom:1px solid #e5e7eb;">Category</th>
                            <th style="text-align:left; color:#6b7280; font-weight:600; font-size:12px; text-transform:uppercase; padding:10px 8px; border-bottom:1px solid #e5e7eb;">Amount</th>
                            <th style="text-align:left; color:#6b7280; font-weight:600; font-size:12px; text-transform:uppercase; padding:10px 8px; border-bottom:1px solid #e5e7eb;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentPayments as $p)
                            <tr>
                                <td style="padding:12px 8px; color:#111827;">{{ $p->paid_on->format('M d, Y') }}</td>
                                <td style="padding:12px 8px; color:#6b7280;">{{ $p->category }}</td>
                                <td style="padding:12px 8px; font-weight:600; color:#111827;">${{ number_format($p->amount, 2) }}</td>
                                <td style="padding:12px 8px;">
                                    <span style="padding:4px 10px; border-radius:20px; font-size:12px; font-weight:600;
                                        @if($p->status === 'completed') background:#dcfce7; color:#16a34a;
                                        @elseif($p->status === 'pending') background:#fef3c7; color:#d97706;
                                        @else background:#fee2e2; color:#dc2626;
                                        @endif">
                                        {{ ucfirst($p->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- Recent requests --}}
        <div class="panel">
            <h3>Recent Requests</h3>

            @if ($recentRequests->isEmpty())
                <div class="empty">No requests yet.</div>
            @else
                @foreach ($recentRequests as $r)
                    <div style="display:flex; justify-content:space-between; align-items:center; padding:12px 0; border-bottom:1px solid #f3f4f6;">
                        <div>
                            <div style="font-weight:600; color:#111827;">{{ $r->title }}</div>
                            <div style="font-size:12px; color:#6b7280; margin-top:2px;">
                                {{ $r->created_at->diffForHumans() }}
                            </div>
                        </div>
                        <span style="padding:4px 10px; border-radius:20px; font-size:12px; font-weight:600;
                            @if($r->status === 'pending') background:#fef3c7; color:#d97706;
                            @elseif($r->status === 'in_progress') background:#dbeafe; color:#2563eb;
                            @else background:#dcfce7; color:#16a34a;
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $r->status)) }}
                        </span>
                    </div>
                @endforeach
            @endif
        </div>

        {{-- Actions --}}
        <div style="display:flex; justify-content:flex-end; gap:12px; margin-bottom:40px;">
            <a href="{{ route('landlord.tenants.edit', $tenancy) }}" class="btn btn-outline">Edit Tenancy</a>

            @if ($tenancy->status === 'active')
                <form method="POST" action="{{ route('landlord.tenants.destroy', $tenancy) }}"
                      onsubmit="return confirm('End this tenancy? The property will become available again.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn" style="background:#fef2f2; color:#dc2626; border:1px solid #fecaca;">
                        End Tenancy
                    </button>
                </form>
            @endif
        </div>

    </div>

@endsection