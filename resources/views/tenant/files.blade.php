@extends('layouts.app')

@section('title', 'File Manager – NEKJOUL IMANAGE')

@section('content')
    <style>
        .files-header { display:flex; justify-content:space-between; align-items:flex-start; gap:16px; flex-wrap:wrap; margin-bottom:24px; }
        .files-title { font-size:24px; font-weight:700; margin-bottom:4px; }
        .files-subtitle { color:#6b7280; font-size:14px; }
        .files-panel { background:#fff; border-radius:14px; padding:22px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,.05); }
        .files-panel h2 { font-size:16px; font-weight:700; margin-bottom:16px; }
        .upload-form { display:flex; align-items:center; gap:12px; flex-wrap:wrap; }
        .upload-form input[type=file] { flex:1; min-width:220px; padding:9px; border:1px solid #e5e7eb; border-radius:8px; font-size:13px; }
        .files-button { display:inline-flex; justify-content:center; align-items:center; gap:6px; padding:9px 14px; border:1px solid #e5e7eb; border-radius:8px; background:#fff; color:#374151; text-decoration:none; font-size:13px; font-weight:600; cursor:pointer; }
        .files-button:hover { background:#f0fdf4; color:#16a34a; border-color:#bbf7d0; }
        .files-button.primary { background:#16a34a; border-color:#16a34a; color:#fff; }
        .files-button.primary:hover { background:#15803d; }
        .files-list { display:flex; flex-direction:column; }
        .file-row { display:flex; align-items:center; justify-content:space-between; gap:16px; padding:14px 0; border-top:1px solid #f3f4f6; }
        .file-info { min-width:0; }
        .file-name { display:block; overflow-wrap:anywhere; font-size:14px; font-weight:600; color:#111827; }
        .file-meta { color:#6b7280; font-size:12px; margin-top:4px; }
        .file-actions { display:flex; gap:8px; flex-shrink:0; }
        .file-empty { color:#6b7280; text-align:center; padding:24px 10px; font-size:14px; }
        .files-notice { padding:12px 16px; border-radius:9px; margin-bottom:16px; font-size:14px; }
        .files-notice.success { background:#dcfce7; color:#166534; }
        .files-notice.error { background:#fee2e2; color:#991b1b; }
        @media (max-width:600px) {
            .file-row { align-items:flex-start; flex-direction:column; }
            .file-actions { flex-wrap:wrap; }
        }
    </style>

    @if (session('status'))
        <div class="files-notice success">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="files-notice error">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="files-header">
        <div>
            <h1 class="files-title">File Manager</h1>
            <p class="files-subtitle">Upload and manage your lease agreements, receipts, and documents.</p>
        </div>
    </div>

    <section class="files-panel">
        <h2>Upload a document</h2>
        <form class="upload-form" method="POST" action="{{ route('tenant.documents.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required>
            <button class="files-button primary" type="submit">Upload file</button>
        </form>
        <p class="file-meta">PDF, JPG, PNG, DOC, or DOCX. Maximum file size: 10 MB.</p>
    </section>

    <section class="files-panel">
        <h2>My documents</h2>
        <div class="files-list">
            @forelse ($documents as $document)
                <div class="file-row">
                    <div class="file-info">
                        <a class="file-name" href="{{ route('tenant.documents.view', $document) }}">{{ $document->original_name }}</a>
                        <div class="file-meta">{{ $document->readable_size }} | Uploaded {{ $document->created_at->format('M d, Y') }}</div>
                    </div>
                    <div class="file-actions">
                        <a class="files-button" href="{{ route('tenant.documents.view', $document) }}">View</a>
                        <a class="files-button" href="{{ route('tenant.documents.download', $document) }}">Download</a>
                        <form method="POST" action="{{ route('tenant.documents.destroy', $document) }}" onsubmit="return confirm('Delete this document?')">
                            @csrf
                            @method('DELETE')
                            <button class="files-button" type="submit">Delete</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="file-empty">You haven't uploaded any documents yet.</p>
            @endforelse
        </div>
    </section>

    <section class="files-panel">
        <h2>Landlord documents</h2>
        <div class="files-list">
            @forelse ($sharedDocuments as $document)
                <div class="file-row">
                    <div class="file-info">
                        <a class="file-name" href="{{ route('tenant.shared-documents.view', $document) }}">{{ $document->title }}</a>
                        <div class="file-meta">{{ $document->original_name }} | {{ $document->readable_size }} | Shared {{ $document->created_at->format('M d, Y') }}</div>
                    </div>
                    <div class="file-actions">
                        <a class="files-button" href="{{ route('tenant.shared-documents.view', $document) }}">View</a>
                        <a class="files-button" href="{{ route('tenant.shared-documents.download', $document) }}">Download</a>
                    </div>
                </div>
            @empty
                <p class="file-empty">Your landlord hasn't shared any documents yet.</p>
            @endforelse
        </div>
    </section>

    <section class="files-panel">
        <h2>Lease agreements</h2>
        <div class="files-list">
            @forelse ($leases as $lease)
                <div class="file-row">
                    <div class="file-info">
                        <a class="file-name" href="{{ route('tenant.leases.view', $lease) }}">{{ $lease->title }}</a>
                        <div class="file-meta">{{ $lease->original_name }} | {{ $lease->readable_size }} | Added {{ $lease->created_at->format('M d, Y') }}</div>
                    </div>
                    <div class="file-actions">
                        <a class="files-button" href="{{ route('tenant.leases.view', $lease) }}">View</a>
                        <a class="files-button" href="{{ route('tenant.leases.download', $lease) }}">Download</a>
                    </div>
                </div>
            @empty
                <p class="file-empty">No lease agreements are available yet.</p>
            @endforelse
        </div>
    </section>

    <section class="files-panel">
        <h2>Payment receipts</h2>
        <div class="files-list">
            @forelse ($payments as $payment)
                <div class="file-row">
                    <div class="file-info">
                        <a class="file-name" href="{{ route('tenant.receipts.view', $payment) }}">Receipt #{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }} | {{ $payment->category }}</a>
                        <div class="file-meta">${{ number_format((float) $payment->amount, 2) }} | Paid {{ $payment->paid_on->format('M d, Y') }}</div>
                    </div>
                    <div class="file-actions">
                        <a class="files-button" href="{{ route('tenant.receipts.view', $payment) }}">View receipt</a>
                        <a class="files-button" href="{{ route('tenant.receipts.download', $payment) }}">Download</a>
                    </div>
                </div>
            @empty
                <p class="file-empty">No completed payments have receipts yet.</p>
            @endforelse
        </div>
    </section>
@endsection
