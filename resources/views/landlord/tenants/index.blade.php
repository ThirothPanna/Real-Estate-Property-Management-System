@extends('layouts.app')

@section('title', 'Tenants – NEKJOUL IMANAGE')

@section('content')

    @if (session('status'))
        <div style="background:#dcfce7; color:#16a34a; padding:12px 16px; border-radius:10px; margin-bottom:16px; font-size:14px;">
            {{ session('status') }}
        </div>
    @endif

    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:16px; flex-wrap:wrap; margin-bottom:24px;">
        <div>
            <h1 style="font-size:24px; font-weight:700; margin-bottom:4px;">Tenants</h1>
            <p style="color:#6b7280; font-size:14px;">{{ $activeCount }} active tenant(s) across your properties.</p>
        </div>

        <a href="{{ route('landlord.tenants.invite') }}" class="btn btn-primary">➕ Invite Tenant</a>
    </div>

    @if ($tenancies->isEmpty())
        <div class="panel">
            <div class="empty">
                <div style="font-size:52px; margin-bottom:16px;">👥</div>
                <div style="font-size:16px; font-weight:600; color:#111827; margin-bottom:6px;">No tenants yet</div>
                <div style="margin-bottom:20px;">Start by inviting your first tenant.</div>
                <a href="{{ route('landlord.tenants.invite') }}" class="btn btn-primary" style="display:inline-flex;">
                    ➕ Invite Your First Tenant
                </a>
            </div>
        </div>
    @else
        <div class="panel" style="padding:0; overflow:hidden;">
            <table style="width:100%; border-collapse:collapse; font-size:14px;">
                <thead>
                    <tr>
                        <th style="text-align:left; color:#6b7280; font-weight:600; font-size:12px; text-transform:uppercase; letter-spacing:.4px; padding:14px 18px; border-bottom:1px solid #e5e7eb; background:#f9fafb;">Tenant</th>
                        <th style="text-align:left; color:#6b7280; font-weight:600; font-size:12px; text-transform:uppercase; letter-spacing:.4px; padding:14px 18px; border-bottom:1px solid #e5e7eb; background:#f9fafb;">Property</th>
                        <th style="text-align:left; color:#6b7280; font-weight:600; font-size:12px; text-transform:uppercase; letter-spacing:.4px; padding:14px 18px; border-bottom:1px solid #e5e7eb; background:#f9fafb;">Lease Period</th>
                        <th style="text-align:left; color:#6b7280; font-weight:600; font-size:12px; text-transform:uppercase; letter-spacing:.4px; padding:14px 18px; border-bottom:1px solid #e5e7eb; background:#f9fafb;">Rent</th>
                        <th style="text-align:left; color:#6b7280; font-weight:600; font-size:12px; text-transform:uppercase; letter-spacing:.4px; padding:14px 18px; border-bottom:1px solid #e5e7eb; background:#f9fafb;">Status</th>
                        <th style="text-align:right; padding:14px 18px; border-bottom:1px solid #e5e7eb; background:#f9fafb;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tenancies as $t)
                        <tr style="border-bottom:1px solid #f3f4f6;">
                            <td style="padding:16px 18px;">
                                <div style="display:flex; align-items:center; gap:12px;">
                                    <img src="{{ $t->tenant->avatar_url }}" alt=""
                                         style="width:40px; height:40px; border-radius:50%; object-fit:cover;">
                                    <div>
                                        <div style="font-weight:600; color:#111827;">{{ $t->tenant->name }}</div>
                                        <div style="font-size:12px; color:#6b7280;">{{ $t->tenant->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding:16px 18px; color:#111827;">
                                @if ($t->property)
                                    <div style="font-weight:600;">{{ $t->property->name }}</div>
                                    <div style="font-size:12px; color:#6b7280;">{{ $t->property->city }}</div>
                                @else
                                    <span style="color:#9ca3af;">—</span>
                                @endif
                            </td>
                            <td style="padding:16px 18px; font-size:13px; color:#6b7280;">
                                {{ $t->lease_start->format('M d, Y') }}<br>
                                → {{ $t->lease_end->format('M d, Y') }}
                            </td>
                            <td style="padding:16px 18px; font-weight:700; color:#16a34a;">
                                ${{ number_format($t->rent_amount, 2) }}
                                <span style="font-size:11px; font-weight:500; color:#6b7280;">/mo</span>
                            </td>
                            <td style="padding:16px 18px;">
                                <span style="padding:4px 10px; border-radius:20px; font-size:12px; font-weight:600; {{ $t->status_badge }}">
                                    {{ ucfirst($t->status) }}
                                </span>
                            </td>
                            <td style="padding:16px 18px; text-align:right;">
                                <a href="{{ route('landlord.tenants.show', $t) }}"
                                   style="font-size:13px; font-weight:600; color:#16a34a; text-decoration:none;">View →</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top:24px;">
            {{ $tenancies->links() }}
        </div>
    @endif

@endsection