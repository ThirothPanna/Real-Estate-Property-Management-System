<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt #{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }

        body {
            font-family: 'DejaVu Sans', 'Helvetica', sans-serif;
            font-size: 13px;
            color: #111827;
            background: #fff;
            padding: 40px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 3px solid #22c55e;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .brand-name {
            font-size: 24px;
            font-weight: 800;
            color: #111827;
            letter-spacing: 1px;
        }
        .brand-tag {
            font-size: 11px;
            color: #6b7280;
            margin-top: 4px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        .brand-logo {
            width: 42px;
            height: 42px;
            background: #22c55e;
            border-radius: 10px;
            display: inline-block;
            vertical-align: middle;
            margin-right: 12px;
            text-align: center;
            line-height: 42px;
            color: #fff;
            font-size: 22px;
            font-weight: 800;
        }
        .header-right { text-align: right; }
        .receipt-label {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .receipt-id {
            font-size: 16px;
            font-weight: 700;
            color: #111827;
            margin-top: 4px;
        }
        .receipt-date {
            font-size: 12px;
            color: #6b7280;
            margin-top: 4px;
        }

        .status-banner {
            background: #dcfce7;
            border-left: 4px solid #16a34a;
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .status-title {
            font-size: 16px;
            font-weight: 700;
            color: #166534;
            margin-bottom: 2px;
        }
        .status-sub {
            font-size: 12px;
            color: #15803d;
        }

        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 30px;
            table-layout: fixed;
            border-spacing: 20px 0;
        }
        .info-block {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            background: #f9fafb;
            padding: 16px 20px;
            border-radius: 10px;
        }
        .info-block .heading {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        .info-block .name {
            font-size: 14px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 2px;
        }
        .info-block .line {
            font-size: 12px;
            color: #4b5563;
            margin-top: 2px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        .items-table th {
            background: #f3f4f6;
            text-align: left;
            padding: 12px 14px;
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .5px;
            border-bottom: 2px solid #e5e7eb;
        }
        .items-table td {
            padding: 14px;
            font-size: 13px;
            color: #111827;
            border-bottom: 1px solid #f3f4f6;
        }
        .items-table td.right { text-align: right; }
        .items-table th.right { text-align: right; }

        .total-block {
            float: right;
            width: 260px;
            margin-top: 10px;
        }
        .total-row {
            display: table;
            width: 100%;
            padding: 8px 0;
        }
        .total-row .k {
            display: table-cell;
            font-size: 13px;
            color: #6b7280;
        }
        .total-row .v {
            display: table-cell;
            text-align: right;
            font-size: 13px;
            color: #111827;
            font-weight: 600;
        }
        .total-final {
            border-top: 2px solid #22c55e;
            margin-top: 8px;
            padding-top: 12px;
        }
        .total-final .k {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
        }
        .total-final .v {
            font-size: 20px;
            font-weight: 800;
            color: #16a34a;
        }

        .footer {
            clear: both;
            margin-top: 60px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
        }
        .footer p {
            font-size: 11px;
            color: #9ca3af;
            line-height: 1.6;
        }
        .footer .brand {
            font-weight: 700;
            color: #6b7280;
        }
    </style>
</head>
<body>

    <div class="header">
        <div>
            <div class="brand-name">
                <span class="brand-logo">N</span>NEKJOUL IMANAGE
            </div>
            <div class="brand-tag">Rental Management</div>
        </div>

        <div class="header-right">
            <div class="receipt-label">Receipt</div>
            <div class="receipt-id">#{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</div>
            <div class="receipt-date">Issued {{ now()->format('M d, Y · H:i') }}</div>
        </div>
    </div>

    @if ($payment->status === 'completed')
        <div class="status-banner">
            <div class="status-title">✓ Payment Successful</div>
            <div class="status-sub">This payment has been recorded and processed.</div>
        </div>
    @elseif ($payment->status === 'pending')
        <div class="status-banner" style="background:#fef3c7; border-left-color:#d97706;">
            <div class="status-title" style="color:#92400e;">⏳ Payment Pending</div>
            <div class="status-sub" style="color:#b45309;">This payment is awaiting confirmation.</div>
        </div>
    @else
        <div class="status-banner" style="background:#fee2e2; border-left-color:#dc2626;">
            <div class="status-title" style="color:#991b1b;">✕ Payment Failed</div>
            <div class="status-sub" style="color:#b91c1c;">This payment did not go through.</div>
        </div>
    @endif

    <div class="info-grid">
        <div class="info-block">
            <div class="heading">Billed To</div>
            <div class="name">{{ $user->name }}</div>
            <div class="line">{{ $user->email }}</div>
            @if (!empty($user->phone))
                <div class="line">{{ $user->phone }}</div>
            @endif
        </div>

        <div class="info-block">
            <div class="heading">Payment Details</div>
            <div class="name">{{ $payment->paid_on->format('F d, Y') }}</div>
            <div class="line">Method: {{ ucfirst(str_replace('_', ' ', $payment->method)) }}</div>
            @if ($payment->card_last4)
                <div class="line">Card: •••• •••• •••• {{ $payment->card_last4 }}</div>
            @endif
        </div>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Description</th>
                <th>Category</th>
                <th>Status</th>
                <th class="right">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>{{ $payment->category }} Payment</strong><br>
                    <span style="color:#6b7280; font-size:12px;">
                        Payment for {{ $payment->paid_on->format('F Y') }}
                    </span>
                </td>
                <td>{{ $payment->category }}</td>
                <td>{{ ucfirst($payment->status) }}</td>
                <td class="right">${{ number_format($payment->amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="total-block">
        <div class="total-row">
            <div class="k">Subtotal</div>
            <div class="v">${{ number_format($payment->amount, 2) }}</div>
        </div>
        <div class="total-row">
            <div class="k">Tax</div>
            <div class="v">$0.00</div>
        </div>
        <div class="total-row total-final">
            <div class="k">Total Paid</div>
            <div class="v">${{ number_format($payment->amount, 2) }}</div>
        </div>
    </div>

    <div class="footer">
        <p>
            Thank you for your payment. Please keep this receipt for your records.<br>
            <span class="brand">NEKJOUL IMANAGE</span> · Rental Management System<br>
            Generated on {{ now()->format('M d, Y \a\t H:i') }}
        </p>
    </div>

</body>
</html>