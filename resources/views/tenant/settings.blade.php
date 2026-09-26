@extends('layouts.app')

@section('title', 'Settings – NEKJOUL IMANAGE')

@section('content')

    <style>
        .settings-wrap { max-width:720px; margin:0 auto; }

        .page-header { margin-bottom:28px; }
        .page-h1 { font-size:24px; font-weight:700; margin-bottom:4px; }
        .page-sub { color:#6b7280; font-size:14px; }

        .settings-panel {
            background:#fff; border-radius:16px;
            padding:28px 32px; margin-bottom:24px;
            box-shadow:0 1px 3px rgba(0,0,0,.05);
        }
        .settings-panel h2 {
            font-size:18px; font-weight:700; margin-bottom:6px;
            color:#111827;
        }
        .settings-panel .panel-sub {
            font-size:13px; color:#6b7280; margin-bottom:22px;
        }

        /* ============ AVATAR UPLOADER ============ */
        .avatar-uploader {
            display:flex; align-items:center; gap:24px;
            margin-bottom:8px;
        }
        .avatar-preview-wrap {
            position:relative;
            width:110px; height:110px; border-radius:50%;
            background:#f3f4f6;
            overflow:hidden;
            border:3px solid #22c55e;
            flex-shrink:0;
        }
        .avatar-preview-wrap img {
            width:100%; height:100%; object-fit:cover; display:block;
        }
        .avatar-camera {
            position:absolute; bottom:0; right:0;
            width:34px; height:34px; border-radius:50%;
            background:#22c55e; color:#fff;
            display:flex; align-items:center; justify-content:center;
            border:2px solid #fff;
            cursor:pointer;
        }
        .avatar-camera:hover { background:#16a34a; }

        .avatar-info { flex:1; }
        .avatar-info h4 { font-size:15px; font-weight:700; color:#111827; margin-bottom:4px; }
        .avatar-info p  { font-size:12px; color:#6b7280; margin-bottom:12px; }

        .btn-change {
            background:#22c55e; color:#fff; border:none;
            padding:9px 18px; border-radius:8px;
            font-size:13px; font-weight:600; cursor:pointer;
            transition:transform .15s, opacity .15s;
        }
        .btn-change:hover { transform:translateY(-2px); opacity:.94; }

        .btn-remove {
            background:#fff; color:#dc2626; border:1px solid #fecaca;
            padding:9px 18px; border-radius:8px;
            font-size:13px; font-weight:600; cursor:pointer;
            margin-left:8px;
            transition:background .15s;
        }
        .btn-remove:hover { background:#fef2f2; }

        /* ============ FORM ============ */
        .form-row {
            display:grid; grid-template-columns:1fr 1fr; gap:20px;
            margin-bottom:18px;
        }
        .form-row.full { grid-template-columns:1fr; }

        .field { display:flex; flex-direction:column; gap:6px; }
        .field label {
            font-size:12px; font-weight:600; color:#6b7280;
            text-transform:uppercase; letter-spacing:.4px;
        }
        .field input {
            width:100%; padding:12px 14px;
            border:1px solid #e5e7eb; border-radius:10px;
            font-size:14px; font-weight:500; color:#111827;
            outline:none; font-family:inherit;
            transition:border .15s, background .15s;
        }
        .field input:focus { border-color:#22c55e; }
        .field input.is-error { border-color:#ef4444; background:#fef2f2; }
        .field .err {
            font-size:12px; color:#ef4444; min-height:16px;
        }

        .form-actions {
            display:flex; justify-content:flex-end;
            margin-top:24px; gap:12px;
        }
        .btn-save {
            background:#22c55e; color:#fff; border:none;
            padding:11px 24px; border-radius:10px;
            font-size:14px; font-weight:600; cursor:pointer;
            transition:transform .15s, opacity .15s;
        }
        .btn-save:hover { transform:translateY(-2px); opacity:.94; }

        .success-banner {
            background:#dcfce7; color:#16a34a;
            padding:12px 16px; border-radius:10px;
            margin-bottom:20px; font-size:14px;
            display:flex; align-items:center; gap:8px;
        }

        @media (max-width: 640px) {
            .form-row { grid-template-columns:1fr; }
            .avatar-uploader { flex-direction:column; text-align:center; }
        }
    </style>

    <div class="settings-wrap">

        <div class="page-header">
            <div class="page-h1">Settings</div>
            <div class="page-sub">Manage your account details, photo, and password.</div>
        </div>

        @if (session('status'))
            <div class="success-banner">
                <span>✓</span> {{ session('status') }}
            </div>
        @endif

        {{-- ============ AVATAR PANEL ============ --}}
        <div class="settings-panel">
            <h2>Profile Photo</h2>
            <div class="panel-sub">Upload a photo — JPG, PNG, or WEBP. Max 2 MB.</div>

            <div class="avatar-uploader">
                <div class="avatar-preview-wrap">
                    <img id="avatarPreview" src="{{ $user->avatar_url }}" alt="Avatar">
                    <label for="avatarInput" class="avatar-camera" title="Change photo">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                            <circle cx="12" cy="13" r="4"/>
                        </svg>
                    </label>
                </div>

                <div class="avatar-info">
                    <h4>Your Profile Picture</h4>
                    <p>Your photo appears in the topbar and on the settings page.</p>

                    <form method="POST" action="{{ route('tenant.settings.avatar') }}" enctype="multipart/form-data" id="avatarForm">
                        @csrf
                        <input type="file" name="avatar" id="avatarInput" accept="image/*" style="display:none;">
                        <button type="button" class="btn-change" onclick="document.getElementById('avatarInput').click()">
                            Change Photo
                        </button>
                    </form>

                    @error('avatar')
                        <div style="font-size:12px; color:#ef4444; margin-top:8px;">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- ============ PROFILE PANEL ============ --}}
        <div class="settings-panel">
            <h2>Profile Information</h2>
            <div class="panel-sub">Update your name, email, and phone number.</div>

            <form method="POST" action="{{ route('tenant.settings.profile') }}">
                @csrf
                @method('patch')

                <div class="form-row">
                    <div class="field">
                        <label>Full Name</label>
                        <input type="text" name="name"
                               value="{{ old('name', $user->name) }}"
                               class="{{ $errors->has('name') ? 'is-error' : '' }}"
                               placeholder="Enter your name">
                        <div class="err">@error('name') {{ $message }} @enderror</div>
                    </div>

                    <div class="field">
                        <label>Email</label>
                        <input type="email" name="email"
                               value="{{ old('email', $user->email) }}"
                               class="{{ $errors->has('email') ? 'is-error' : '' }}"
                               placeholder="Enter your email">
                        <div class="err">@error('email') {{ $message }} @enderror</div>
                    </div>
                </div>

                <div class="form-row full">
                    <div class="field">
                        <label>Phone</label>
                        <input type="tel" name="phone"
                               value="{{ old('phone', $user->phone ?? '') }}"
                               class="{{ $errors->has('phone') ? 'is-error' : '' }}"
                               placeholder="Enter your phone number">
                        <div class="err">@error('phone') {{ $message }} @enderror</div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-save">Save Changes</button>
                </div>
            </form>
        </div>

        {{-- ============ PASSWORD PANEL ============ --}}
        <div class="settings-panel">
            <h2>Change Password</h2>
            <div class="panel-sub">Choose a strong password with at least 8 characters.</div>

            <form method="POST" action="{{ route('tenant.settings.password') }}">
                @csrf
                @method('patch')

                <div class="form-row full">
                    <div class="field">
                        <label>Current Password</label>
                        <input type="password" name="current_password"
                               class="{{ $errors->has('current_password') ? 'is-error' : '' }}"
                               placeholder="Enter current password">
                        <div class="err">@error('current_password') {{ $message }} @enderror</div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="field">
                        <label>New Password</label>
                        <input type="password" name="password"
                               class="{{ $errors->has('password') ? 'is-error' : '' }}"
                               placeholder="Enter new password">
                        <div class="err">@error('password') {{ $message }} @enderror</div>
                    </div>

                    <div class="field">
                        <label>Confirm Password</label>
                        <input type="password" name="password_confirmation"
                               placeholder="Repeat new password">
                        <div class="err"></div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-save">Update Password</button>
                </div>
            </form>
        </div>

    </div>

    <script>
        // Preview the newly selected avatar instantly
        document.getElementById('avatarInput').addEventListener('change', function () {
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('avatarPreview').src = e.target.result;
                };
                reader.readAsDataURL(this.files[0]);

                // Auto-submit the upload form
                setTimeout(function () {
                    document.getElementById('avatarForm').submit();
                }, 400);
            }
        });
    </script>

@endsection