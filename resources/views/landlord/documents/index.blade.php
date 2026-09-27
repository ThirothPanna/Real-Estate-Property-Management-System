@extends('layouts.app')

@section('title', 'Documents – NEKJOUL IMANAGE')

@section('content')

    <style>
        .doc-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }
        .doc-header h1 { font-size: 24px; font-weight: 700; margin-bottom: 4px; }
        .doc-header p  { color: #6b7280; font-size: 14px; }

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

        /* Documents grid */
        .doc-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 16px;
        }
        .doc-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 20px;
            transition: transform .2s, box-shadow .2s, border-color .2s;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        .doc-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 24px rgba(0,0,0,.08);
            border-color: #bbf7d0;
        }
        .doc-card-head {
            display: flex;
            gap: 14px;
            align-items: flex-start;
        }
        .doc-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }
        .doc-icon.pdf   { background: #fee2e2; }
        .doc-icon.image { background: #dbeafe; }
        .doc-icon.doc   { background: #e0e7ff; }
        .doc-icon.file  { background: #f3f4f6; }

        .doc-title {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 4px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .doc-meta {
            font-size: 12px;
            color: #6b7280;
        }
        .doc-property {
            display: inline-block;
            padding: 3px 10px;
            background: #f0fdf4;
            color: #16a34a;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            margin-top: 6px;
        }
        .doc-property.all {
            background: #dbeafe;
            color: #2563eb;
        }

        .doc-actions {
            display: flex;
            gap: 8px;
            margin-top: auto;
        }
        .doc-actions a,
        .doc-actions button {
            flex: 1;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #374151;
            text-decoration: none;
            text-align: center;
            transition: background .15s, color .15s, border-color .15s;
        }
        .doc-actions a:hover {
            background: #f0fdf4;
            color: #16a34a;
            border-color: #bbf7d0;
        }
        .doc-actions button:hover {
            background: #fef2f2;
            color: #dc2626;
            border-color: #fecaca;
        }

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

    {{-- Header --}}
    <div class="doc-header">
        <div>
            <h1>Documents</h1>
            <p>Share documents with your tenants. They'll see them in their File Manager.</p>
        </div>

        <button class="btn-new" onclick="openUploadModal()">
            📎 Upload Document
        </button>
    </div>

    {{-- Grid --}}
    @if ($documents->isEmpty())
        <div class="empty">
            <div class="empty-icon">📁</div>
            <div>No documents uploaded yet.</div>
            <div style="margin-top:16px;">
                <button class="btn-new" onclick="openUploadModal()">
                    📎 Upload your first document
                </button>
            </div>
        </div>
    @else
        <div class="doc-grid">
            @foreach ($documents as $doc)
                <div class="doc-card">
                    <div class="doc-card-head">
                        <div class="doc-icon {{ $doc->icon }}">
                            @if ($doc->icon === 'pdf') 📄
                            @elseif ($doc->icon === 'image') 🖼️
                            @elseif ($doc->icon === 'doc') 📝
                            @else 📎
                            @endif
                        </div>
                        <div style="flex:1; min-width:0;">
                            <div class="doc-title" title="{{ $doc->title }}">{{ $doc->title }}</div>
                            <div class="doc-meta">
                                {{ $doc->readable_size }} · {{ $doc->created_at->diffForHumans() }}
                            </div>
                            @if ($doc->property)
                                <div class="doc-property">🏠 {{ $doc->property->name }}</div>
                            @else
                                <div class="doc-property all">📢 All properties</div>
                            @endif
                        </div>
                    </div>

                    <div class="doc-actions">
                        <a href="{{ $doc->url }}" target="_blank" download="{{ $doc->original_name }}">⬇ Download</a>

                        <form method="POST" action="{{ route('landlord.documents.destroy', $doc) }}"
                              onsubmit="return confirm('Delete this document?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="width:100%;">✕ Delete</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div style="margin-top:20px;">
            {{ $documents->links() }}
        </div>
    @endif

    {{-- Upload Modal --}}
    <div class="modal-overlay" id="uploadModal">
        <div class="modal-box">
            <div class="modal-title">Upload Document</div>
            <div class="modal-sub">Tenants will receive a notification when you upload.</div>

            <form method="POST" action="{{ route('landlord.documents.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="modal-field">
                    <label>Title</label>
                    <input type="text" name="title" required maxlength="255"
                           value="{{ old('title') }}"
                           placeholder="e.g. Building Fire Safety Rules">
                </div>

                <div class="modal-field">
                    <label>File</label>
                    <input type="file" name="document" required
                           accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                    <p style="font-size:12px; color:#9ca3af; margin-top:6px;">
                        PDF, JPG, PNG, DOC, DOCX — max 10 MB
                    </p>
                </div>

                <div class="modal-field">
                    <label>Assign to Property</label>
                    <select name="property_id">
                        <option value="">All my properties</option>
                        @foreach ($properties as $p)
                            <option value="{{ $p->id }}" {{ old('property_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeUploadModal()">Cancel</button>
                    <button type="submit" class="btn-send">Upload</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openUploadModal() {
            document.getElementById('uploadModal').classList.add('open');
        }
        function closeUploadModal() {
            document.getElementById('uploadModal').classList.remove('open');
        }
        document.getElementById('uploadModal').addEventListener('click', function (e) {
            if (e.target === this) closeUploadModal();
        });

        @if ($errors->any())
            openUploadModal();
        @endif
    </script>

@endsection