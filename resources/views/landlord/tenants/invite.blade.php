@extends('layouts.app')

@section('title', 'Invite Tenant – NEKJOUL IMANAGE')

@section('content')

    <div style="max-width:700px; margin:0 auto;">

        <div style="margin-bottom:24px;">
            <a href="{{ route('landlord.tenants.index') }}" style="font-size:13px; color:#6b7280; text-decoration:none;">← Back to Tenants</a>
            <h1 style="font-size:24px; font-weight:700; margin-top:8px; margin-bottom:4px;">Invite Tenant</h1>
            <p style="color:#6b7280; font-size:14px;">Add a tenant to one of your properties.</p>
        </div>

        @if ($properties->isEmpty())
            <div class="panel" style="text-align:center; padding:40px;">
                <div style="font-size:48px; margin-bottom:12px;">🏠</div>
                <div style="font-size:16px; font-weight:600; color:#111827; margin-bottom:6px;">No properties yet</div>
                <div style="margin-bottom:20px; color:#6b7280;">Add a property first, then invite a tenant.</div>
                <a href="{{ route('landlord.properties.create') }}" class="btn btn-primary" style="display:inline-flex;">
                    ➕ Add Property
                </a>
            </div>
        @else
            <form method="POST" action="{{ route('landlord.tenants.store') }}">
                @csrf

                <div class="panel">
                    <h3>Tenant Information</h3>

                    <div>
                        <label style="display:block; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.4px; margin-bottom:6px;">Email Address *</label>
                        <input type="email" name="email" required value="{{ old('email') }}"
                               placeholder="tenant@example.com"
                               style="width:100%; padding:12px 14px; border:1px solid #e5e7eb; border-radius:10px; font-size:14px; outline:none;">
                        @error('email') <div style="color:#ef4444; font-size:12px; margin-top:4px;">{{ $message }}</div> @enderror

                        <p style="font-size:12px; color:#9ca3af; margin-top:8px;">
                            If the email isn't registered yet, we'll create an account and give you a temporary password to share.
                        </p>
                    </div>
                </div>

                <div class="panel">
                    <h3>Property &amp; Lease</h3>

                    <div style="display:grid; gap:18px;">
                        <div>
                            <label style="display:block; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.4px; margin-bottom:6px;">Property *</label>
                            <select name="property_id" required
                                    style="width:100%; padding:12px 14px; border:1px solid #e5e7eb; border-radius:10px; font-size:14px; background:#fff;">
                                <option value="">Select a property…</option>
                                @foreach ($properties as $p)
                                    <option value="{{ $p->id }}" {{ old('property_id') == $p->id ? 'selected' : '' }}>
                                        {{ $p->name }} — ${{ number_format($p->rent_amount, 2) }}/mo
                                    </option>
                                @endforeach
                            </select>
                            @error('property_id') <div style="color:#ef4444; font-size:12px; margin-top:4px;">{{ $message }}</div> @enderror
                        </div>

                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                            <div>
                                <label style="display:block; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.4px; margin-bottom:6px;">Lease Start *</label>
                                <input type="date" name="lease_start" required value="{{ old('lease_start', date('Y-m-d')) }}"
                                       style="width:100%; padding:12px 14px; border:1px solid #e5e7eb; border-radius:10px; font-size:14px;">
                                @error('lease_start') <div style="color:#ef4444; font-size:12px; margin-top:4px;">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label style="display:block; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.4px; margin-bottom:6px;">Lease End *</label>
                                <input type="date" name="lease_end" required value="{{ old('lease_end', date('Y-m-d', strtotime('+1 year'))) }}"
                                       style="width:100%; padding:12px 14px; border:1px solid #e5e7eb; border-radius:10px; font-size:14px;">
                                @error('lease_end') <div style="color:#ef4444; font-size:12px; margin-top:4px;">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                            <div>
                                <label style="display:block; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.4px; margin-bottom:6px;">Monthly Rent (USD) *</label>
                                <input type="number" name="rent_amount" step="0.01" min="0" required value="{{ old('rent_amount') }}"
                                       placeholder="1250.00"
                                       style="width:100%; padding:12px 14px; border:1px solid #e5e7eb; border-radius:10px; font-size:14px;">
                                @error('rent_amount') <div style="color:#ef4444; font-size:12px; margin-top:4px;">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label style="display:block; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.4px; margin-bottom:6px;">Security Deposit</label>
                                <input type="number" name="security_deposit" step="0.01" min="0" value="{{ old('security_deposit') }}"
                                       placeholder="Optional"
                                       style="width:100%; padding:12px 14px; border:1px solid #e5e7eb; border-radius:10px; font-size:14px;">
                                @error('security_deposit') <div style="color:#ef4444; font-size:12px; margin-top:4px;">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div style="display:flex; justify-content:flex-end; gap:12px; margin-bottom:40px;">
                    <a href="{{ route('landlord.tenants.index') }}" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-primary">Send Invite</button>
                </div>
            </form>
        @endif

    </div>

@endsection