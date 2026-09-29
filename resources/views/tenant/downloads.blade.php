@extends('layouts.app')

@section('title', 'Downloads – NEKJOUL IMANAGE')

@section('content')
    <style>
        .downloads-header { margin-bottom:24px; }
        .downloads-title { font-size:24px; font-weight:700; margin-bottom:4px; }
        .downloads-subtitle { color:#6b7280; font-size:14px; }
        .downloads-panel { background:#fff; border-radius:14px; padding:22px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,.05); }
        .downloads-panel h2 { font-size:16px; font-weight:700; margin-bottom:12px; }
        .downloads-list { display:flex; flex-direction:column; }
        .download-row { display:flex; align-items:center; justify-content:space-between; gap:16px; padding:14px 0; border-top:1px solid #f3f4f6; }
        .download-info { min-width:0; }
        .download-name { display:block; overflow-wrap:anywhere; font-size:14px; font-weight:600; color:#111827; }
        .download-meta { color:#6b7280; font-size:12px; margin-top:4px; }
        .download-link { display:inline-flex; align-items:center; justify-content:center; flex-shrink:0; padding:8px 12px; border:1px solid #e5e7eb; border-radius:8px; color:#374151; background:#fff; text-decoration:none; font-size:13px; font-weight:600; }
        .download-link:hover { color:#16a34a; background:#f0fdf4; border-color:#bbf7d0; }
        .download-actions { display:flex; gap:8px; flex-shrink:0; }
        .download-empty { color:#6b7280; text-align:center; padding:20px 10px; font-size:14px; }
        @media (max-width:600px) { .download-row { align-items:flex-start; flex-direction:column; } }
    </style>

    <header class="downloads-header">
        <h1 class="downloads-title">Downloads</h1>
        <p class="downloads-subtitle">Download your payment receipts, lease agreements, and uploaded documents.</p>
    </header>

    <section class="downloads-panel">
        <h2>Payment receipts</h2>
        <div class="downloads-list">
            @forelse ($payments as $payment)
                <div class="download-row">
                    <div class="download-info">
                        <a class="download-name" href="{{ route('tenant.receipts.view', $payment) }}">Receipt #{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }} — {{ $payment->category }}</a>
                        <div class="download-meta">${{ number_format((float) $payment->amount, 2) }} | Paid {{ $payment->paid_on->format('M d, Y') }}</div>
                    </div>
                    <div class="download-actions">
                        <a class="download-link" href="{{ route('tenant.receipts.view', $payment) }}">View PDF</a>
                        <a class="download-link" href="{{ route('tenant.receipts.download', $payment) }}">Download</a>
                    </div>
                </div>
            @empty
                <p class="download-empty">No completed payments are available for download.</p>
            @endforelse
        </div>
    </section>

    <section class="downloads-panel">
        <h2>Lease agreements</h2>
        <div class="downloads-list">
            @forelse ($leases as $lease)
                <div class="download-row">
                    <div class="download-info">
                        <a class="download-name" href="{{ route('tenant.leases.view', $lease) }}">{{ $lease->title }}</a>
                        <div class="download-meta">{{ $lease->original_name }} | {{ $lease->readable_size }} | Added {{ $lease->created_at->format('M d, Y') }}</div>
                    </div>
                    <div class="download-actions">
                        <a class="download-link" href="{{ route('tenant.leases.view', $lease) }}">View</a>
                        <a class="download-link" href="{{ route('tenant.leases.download', $lease) }}">Download</a>
                    </div>
                </div>
            @empty
                <p class="download-empty">No lease agreements are available for download.</p>
            @endforelse
        </div>
    </section>

    <section class="downloads-panel">
        <h2>My documents</h2>
        <div class="downloads-list">
            @forelse ($documents as $document)
                <div class="download-row">
                    <div class="download-info">
                        <a class="download-name" href="{{ route('tenant.documents.view', $document) }}">{{ $document->original_name }}</a>
                        <div class="download-meta">{{ $document->readable_size }} | Uploaded {{ $document->created_at->format('M d, Y') }}</div>
                    </div>
                    <div class="download-actions">
                        <a class="download-link" href="{{ route('tenant.documents.view', $document) }}">View</a>
                        <a class="download-link" href="{{ route('tenant.documents.download', $document) }}">Download</a>
                    </div>
                </div>
            @empty
                <p class="download-empty">You haven't uploaded any documents yet.</p>
            @endforelse
        </div>
    </section>
@endsection
