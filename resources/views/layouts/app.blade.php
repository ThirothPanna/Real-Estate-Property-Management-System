<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NEKJOUL IMANAGE')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI', system-ui, sans-serif; }
        body { background:#f4f6f8; color:#1f2937; }

        /* ============ SIDEBAR ============ */
        .sidebar {
            position:fixed; top:0; left:0; height:100vh; width:78px;
            background:#fff; border-right:1px solid #e5e7eb;
            display:flex; flex-direction:column; align-items:center;
            padding:20px 0; transition:width .35s cubic-bezier(.4,0,.2,1);
            overflow:hidden; z-index:100;
        }
        .sidebar:hover, .sidebar.pinned { width:250px; box-shadow:4px 0 24px rgba(0,0,0,.08); }
        .logo { display:flex; align-items:center; gap:12px; margin-bottom:32px; padding-left:14px; width:100%; white-space:nowrap; }
        .logo-icon { color:#22c55e; flex-shrink:0; }
        .logo-text {
            font-size:15px; font-weight:700; color:#111827;
            opacity:0; transition:opacity .25s .1s;
            letter-spacing:.5px; white-space:nowrap;
        }
        .sidebar:hover .logo-text, .sidebar.pinned .logo-text { opacity:1; }

        .nav { width:100%; display:flex; flex-direction:column; gap:4px; padding:0 10px; }
        .nav-item {
            display:flex; align-items:center; gap:16px; padding:12px 14px;
            border-radius:10px; cursor:pointer; white-space:nowrap;
            color:#4b5563; transition:background .2s, color .2s, transform .2s;
            position:relative; text-decoration:none;
        }
        .nav-item:hover { background:#f0fdf4; color:#16a34a; transform:translateX(4px); }
        .nav-item.active { background:#dcfce7; color:#16a34a; font-weight:600; }
        .nav-item.active::before {
            content:''; position:absolute; left:-10px; top:50%; transform:translateY(-50%);
            width:4px; height:24px; background:#22c55e; border-radius:0 4px 4px 0;
        }
        .nav-icon { flex-shrink:0; display:flex; }
        .nav-label { opacity:0; transition:opacity .25s .1s; font-size:14px; }
        .sidebar:hover .nav-label, .sidebar.pinned .nav-label { opacity:1; }

        .sidebar-bottom { margin-top:auto; width:100%; padding:0 10px; }
        .app-badge { display:flex; align-items:center; gap:14px; padding:14px; border-radius:12px; background:#f0fdf4; cursor:pointer; white-space:nowrap; }
        .app-badge-text { opacity:0; transition:opacity .25s .1s; font-size:12px; }
        .app-badge-text b { display:block; font-size:13px; color:#111827; }
        .app-badge-text span { color:#6b7280; }
        .sidebar:hover .app-badge-text, .sidebar.pinned .app-badge-text { opacity:1; }

        /* ============ MAIN / TOPBAR ============ */
        .main { margin-left:78px; min-height:100vh; transition:margin-left .35s cubic-bezier(.4,0,.2,1); }
        body.sidebar-pinned .main { margin-left:250px; }

        .topbar {
            display:flex; align-items:center; justify-content:space-between;
            padding:16px 32px; background:#fff; border-bottom:1px solid #e5e7eb;
            position:sticky; top:0; z-index:50;
        }
        .topbar-left { display:flex; align-items:center; gap:10px; }
        .topbar-right { display:flex; align-items:center; gap:10px; }

        .icon-btn {
            cursor:pointer; color:#374151; position:relative;
            width:40px; height:40px; border-radius:10px;
            display:flex; align-items:center; justify-content:center;
            background:none; border:none; transition:background .2s, color .2s;
            text-decoration:none;
        }
        .icon-btn:hover { background:#f0fdf4; color:#16a34a; }

        .bell .dot {
            position:absolute; top:6px; right:6px; background:#ef4444; color:#fff;
            font-size:10px; width:16px; height:16px; border-radius:50%;
            display:flex; align-items:center; justify-content:center;
            font-weight:700;
        }

        /* ============ TOPBAR DROPDOWNS ============ */
        .dropdown-wrap { position:relative; }
        .dropdown {
            display:none; position:absolute; top:100%; right:0; margin-top:8px;
            background:#fff; border:1px solid #e5e7eb; border-radius:12px;
            box-shadow:0 12px 32px rgba(0,0,0,.12); width:320px; z-index:200; overflow:hidden;
        }
        .dropdown.open { display:block; }
        .dropdown-header {
            padding:16px 20px; border-bottom:1px solid #e5e7eb;
            font-weight:600; font-size:15px; color:#111827;
            display:flex; justify-content:space-between; align-items:center; white-space:nowrap;
        }
        .dropdown-header a { font-size:13px; color:#3f9c3a; text-decoration:none; font-weight:500; }
        .dropdown-header a:hover { text-decoration:underline; }
        .dropdown-body { max-height:340px; overflow-y:auto; }
        .dropdown-empty { padding:32px 20px; text-align:center; color:#9ca3af; font-size:14px; }
        .dropdown-item {
            display:flex; align-items:flex-start; gap:12px; padding:14px 20px;
            cursor:pointer; text-decoration:none; color:#111827;
            border-bottom:1px solid #f3f4f6; transition:background .15s;
        }
        .dropdown-item:last-child { border-bottom:none; }
        .dropdown-item:hover { background:#f0fdf4; }
        .dropdown-item-title { font-size:14px; font-weight:600; margin-bottom:2px; }
        .dropdown-item-text { font-size:13px; color:#6b7280; line-height:1.4; }
        .dropdown-item-icon { color:#22c55e; flex-shrink:0; margin-top:2px; }

        /* ============ PROFILE PILL ============ */
        .profile {
            display:flex; align-items:center; gap:12px; padding:8px 16px;
            border:1px solid #e5e7eb; border-radius:30px; cursor:pointer;
            position:relative; user-select:none; margin-left:8px;
        }
        .profile-name { font-size:14px; font-weight:600; color:#111827; }

        .avatar {
            width:36px; height:36px; border-radius:50%;
            background:#22c55e; color:#fff;
            display:flex; align-items:center; justify-content:center;
            font-weight:600; font-size:14px;
            overflow:hidden;
            flex-shrink:0;
        }
        .avatar img {
            width:100%; height:100%;
            object-fit:cover;
            display:block;
        }

        .user-menu {
            display:none; position:absolute; right:0; top:calc(100% + 8px);
            background:#fff; border:1px solid #e5e7eb; border-radius:14px;
            box-shadow:0 12px 32px rgba(0,0,0,.12);
            width:340px; padding:24px 0; z-index:200;
        }
        .user-menu.open { display:block; }
        .user-menu-header { display:flex; align-items:center; gap:16px; padding:0 24px 20px; }

        .user-menu-avatar {
            width:56px; height:56px; border-radius:50%;
            background:#34d399; color:#fff; flex-shrink:0;
            display:flex; align-items:center; justify-content:center;
            font-weight:600; font-size:20px;
            overflow:hidden;
        }
        .user-menu-avatar img {
            width:100%; height:100%;
            object-fit:cover;
            display:block;
        }

        .user-menu-info { display:flex; flex-direction:column; gap:2px; }
        .user-menu-role { font-size:13px; color:#9ca3af; }
        .user-menu-name { font-size:17px; font-weight:600; color:#111827; }
        .user-menu-email { font-size:14px; color:#3f9c3a; }

        .user-menu-settings {
            display:block;
            margin:8px 24px 18px;
            padding:10px 20px;
            border:1px solid #d1d5db;
            background:#fff;
            color:#111827;
            border-radius:8px;
            font-size:14px;
            font-weight:600;
            cursor:pointer;
            text-align:center;
            text-decoration:none;
        }
        .user-menu-settings:hover { background:#f9fafb; }

        .user-menu-divider { height:1px; background:#e5e7eb; margin:0 24px; }
        .user-menu-item {
            display:flex; align-items:center; gap:16px;
            padding:16px 24px; font-size:15px; color:#111827;
            cursor:pointer; text-decoration:none; background:none;
            border:none; width:100%; text-align:left;
        }
        .user-menu-item:hover { background:#f9fafb; }
        .user-menu-item svg { flex-shrink:0; color:#111827; }

        /* ============ CONTENT ============ */
        .content { padding:28px 32px; display:block; }
        .welcome { margin-bottom:24px; }
        .welcome h1 { font-size:24px; margin-bottom:4px; }
        .welcome p { color:#6b7280; font-size:14px; }

        .stats { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:24px; }
        .stat { background:#fff; border-radius:14px; padding:18px; box-shadow:0 1px 3px rgba(0,0,0,.05); transition:transform .2s, box-shadow .2s; }
        .stat:hover { transform:translateY(-4px); box-shadow:0 8px 20px rgba(0,0,0,.08); }
        .stat .label { font-size:12px; color:#6b7280; text-transform:uppercase; letter-spacing:.5px; }
        .stat .value { font-size:22px; font-weight:700; margin:6px 0 2px; color:#d1d5db; }
        .stat .trend { font-size:12px; color:#d1d5db; }

        .quick-actions { display:flex; gap:12px; margin-bottom:24px; flex-wrap:wrap; }
        .btn { padding:10px 18px; border-radius:10px; border:none; cursor:pointer; font-size:14px; font-weight:600; display:flex; align-items:center; gap:8px; transition:transform .15s, opacity .15s; text-decoration:none; }
        .btn:hover { transform:translateY(-2px); opacity:.92; }
        .btn-primary { background:#22c55e; color:#fff; }
        .btn-outline { background:#fff; color:#374151; border:1px solid #e5e7eb; }

        .panel { background:#fff; border-radius:16px; padding:24px; box-shadow:0 1px 3px rgba(0,0,0,.05); margin-bottom:24px; }
        .panel h3 { font-size:16px; margin-bottom:16px; }
        .panel .empty { text-align:center; color:#9ca3af; padding:32px 0; font-size:14px; }

        table { width:100%; border-collapse:collapse; font-size:14px; }
        th { text-align:left; color:#6b7280; font-weight:600; font-size:12px; text-transform:uppercase; letter-spacing:.4px; padding:10px 8px; border-bottom:1px solid #e5e7eb; }
        td { padding:12px 8px; border-bottom:1px solid #f3f4f6; color:#9ca3af; }

        .lease-upload { border:2px dashed #e5e7eb; border-radius:12px; padding:40px 20px; text-align:center; color:#9ca3af; font-size:14px; cursor:pointer; transition:border .2s, background .2s; }
        .lease-upload:hover { border-color:#22c55e; background:#f0fdf4; }

        @media (max-width:1100px) {
            .stats { grid-template-columns:repeat(2,1fr); }
        }
    </style>
</head>
<body>

{{-- ============ SIDEBAR ============ --}}
<aside class="sidebar" id="sidebar">
    <div class="logo">
        <div class="logo-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        </div>
        <span class="logo-text">NEKJOUL IMANAGE</span>
    </div>

    <nav class="nav">
        @if (auth()->user()->isLandlord())
            {{-- ========== LANDLORD SIDEBAR ========== --}}
            <a href="{{ route('landlord.dashboard') }}" class="nav-item {{ request()->routeIs('landlord.dashboard') ? 'active' : '' }}">
                <span class="nav-icon">▦</span><span class="nav-label">Dashboard</span>
            </a>
            <a href="{{ route('landlord.properties.index') }}" class="nav-item {{ request()->routeIs('landlord.properties.*') ? 'active' : '' }}">
                <span class="nav-icon">🏠</span><span class="nav-label">Properties</span>
            </a>
            <a href="{{ route('landlord.tenants.index') }}" class="nav-item {{ request()->routeIs('landlord.tenants.*') ? 'active' : '' }}">
                <span class="nav-icon">👥</span><span class="nav-label">Tenants</span>
            </a>
            <a href="#" class="nav-item"><span class="nav-icon">✂</span><span class="nav-label">Requests</span></a>
            <a href="#" class="nav-item"><span class="nav-icon">💳</span><span class="nav-label">Payments</span></a>
            <a href="#" class="nav-item"><span class="nav-icon">📊</span><span class="nav-label">Reports</span></a>
            <a href="#" class="nav-item"><span class="nav-icon">🗀</span><span class="nav-label">Documents</span></a>
        @else
            {{-- ========== TENANT SIDEBAR ========== --}}
            <a href="{{ route('tenant.dashboard') }}" class="nav-item {{ request()->routeIs('tenant.dashboard') ? 'active' : '' }}">
                <span class="nav-icon">▦</span><span class="nav-label">Dashboard</span>
            </a>
            <a href="{{ route('tenant.rent') }}" class="nav-item {{ request()->routeIs('tenant.rent') ? 'active' : '' }}">
                <span class="nav-icon">＄</span><span class="nav-label">Rent</span>
            </a>
            <a href="{{ route('tenant.requests') }}" class="nav-item {{ request()->routeIs('tenant.requests') ? 'active' : '' }}">
                <span class="nav-icon">✂</span><span class="nav-label">Requests</span>
            </a>
            <a href="{{ route('tenant.utilities') }}" class="nav-item {{ request()->routeIs('tenant.utilities') ? 'active' : '' }}">
                <span class="nav-icon">⚲</span><span class="nav-label">Utility Providers</span>
            </a>
            <a href="{{ route('tenant.applications') }}" class="nav-item {{ request()->routeIs('tenant.applications') ? 'active' : '' }}">
                <span class="nav-icon">▤</span><span class="nav-label">Applications</span>
            </a>
            <a href="{{ route('tenant.files') }}" class="nav-item {{ request()->routeIs('tenant.files') ? 'active' : '' }}">
                <span class="nav-icon">🗀</span><span class="nav-label">File Manager</span>
            </a>
            <a href="{{ route('tenant.downloads') }}" class="nav-item {{ request()->routeIs('tenant.downloads') ? 'active' : '' }}">
                <span class="nav-icon">☁</span><span class="nav-label">Downloads</span>
            </a>
        @endif
    </nav>

    <div class="sidebar-bottom">
        <div class="app-badge">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><rect x="6" y="2" width="12" height="20" rx="2"/></svg>
            <div class="app-badge-text"><b>Download Mobile App</b><span>App is available on iOS / Android</span></div>
        </div>
    </div>
</aside>

{{-- ============ MAIN ============ --}}
<div class="main">

    <header class="topbar">
        <div class="topbar-left">

            {{-- MENU BUTTON --}}
            <button class="icon-btn" id="menuToggle" title="Toggle sidebar">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>

            {{-- BELL --}}
            <a href="{{ route('tenant.notifications') }}" class="icon-btn bell" title="Notifications">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                @if ($unreadCount > 0)
                    <span class="dot">{{ $unreadCount }}</span>
                @endif
            </a>
        </div>

        <div class="topbar-right">

            {{-- HOME --}}
            <a href="{{ route(auth()->user()->dashboardRoute()) }}" class="icon-btn" title="Home">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
            </a>

            {{-- CHAT --}}
            <div class="dropdown-wrap">
                <button class="icon-btn" id="chatToggle" title="Support chat">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                </button>
                <div class="dropdown" id="chatDropdown">
                    <div class="dropdown-header"><span>Support</span></div>
                    <div class="dropdown-body">
                        <a href="mailto:support@nekjoul.com" class="dropdown-item">
                            <span class="dropdown-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            </span>
                            <div>
                                <div class="dropdown-item-title">Email Support</div>
                                <div class="dropdown-item-text">support@nekjoul.com</div>
                            </div>
                        </a>
                        <a href="#" class="dropdown-item">
                            <span class="dropdown-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                            </span>
                            <div>
                                <div class="dropdown-item-title">Live Chat</div>
                                <div class="dropdown-item-text">Mon–Fri, 9am–5pm</div>
                            </div>
                        </a>
                        <a href="#" class="dropdown-item">
                            <span class="dropdown-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                            </span>
                            <div>
                                <div class="dropdown-item-title">Browse FAQs</div>
                                <div class="dropdown-item-text">Answers to common questions</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            {{-- HELP --}}
            <div class="dropdown-wrap">
                <button class="icon-btn" id="helpToggle" title="Help">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
                        <line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                </button>
                <div class="dropdown" id="helpDropdown">
                    <div class="dropdown-header"><span>Help &amp; Resources</span></div>
                    <div class="dropdown-body">
                        <a href="#" class="dropdown-item">
                            <span class="dropdown-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                            </span>
                            <div>
                                <div class="dropdown-item-title">Getting started guide</div>
                                <div class="dropdown-item-text">Learn the basics in 5 minutes</div>
                            </div>
                        </a>
                        <a href="#" class="dropdown-item">
                            <span class="dropdown-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                            </span>
                            <div>
                                <div class="dropdown-item-title">Help center</div>
                                <div class="dropdown-item-text">Search articles &amp; tutorials</div>
                            </div>
                        </a>
                        <a href="#" class="dropdown-item">
                            <span class="dropdown-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            </span>
                            <div>
                                <div class="dropdown-item-title">Book a demo</div>
                                <div class="dropdown-item-text">Talk to our team</div>
                            </div>
                        </a>
                        <a href="#" class="dropdown-item">
                            <span class="dropdown-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg>
                            </span>
                            <div>
                                <div class="dropdown-item-title">What's new</div>
                                <div class="dropdown-item-text">Latest updates &amp; features</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            {{-- PROFILE --}}
            <div class="profile" id="profilePill">
                <span class="profile-name">{{ auth()->user()->name }}</span>
                <div class="avatar">
                    <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}">
                </div>

                <div class="user-menu" id="userMenu">
                    <div class="user-menu-header">
                        <div class="user-menu-avatar">
                            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}">
                        </div>
                        <div class="user-menu-info">
                            <span class="user-menu-role">{{ auth()->user()->isLandlord() ? 'Landlord' : 'Tenant' }}</span>
                            <span class="user-menu-name">{{ auth()->user()->name }}</span>
                            <span class="user-menu-email">{{ auth()->user()->email }}</span>
                        </div>
                    </div>

                    @if (auth()->user()->isLandlord())
                        <a href="#" class="user-menu-settings">Settings</a>
                    @else
                        <a href="{{ route('tenant.settings') }}" class="user-menu-settings">Settings</a>
                    @endif

                    <div class="user-menu-divider"></div>

                    <a href="{{ route('login') }}" class="user-menu-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <line x1="19" y1="8" x2="19" y2="14"/>
                            <line x1="22" y1="11" x2="16" y2="11"/>
                        </svg>
                        <span>Add another account</span>
                    </a>

                    <div class="user-menu-divider"></div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="user-menu-item">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                <polyline points="16 17 21 12 16 7"/>
                                <line x1="21" y1="12" x2="9" y2="12"/>
                            </svg>
                            <span>Log out</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <div class="content">
        @yield('content')
    </div>
</div>

<script>
    // Sidebar toggle
    const sidebar = document.getElementById('sidebar');
    const menuToggle = document.getElementById('menuToggle');
    menuToggle.addEventListener('click', () => {
        sidebar.classList.toggle('pinned');
        document.body.classList.toggle('sidebar-pinned');
    });

    // Generic dropdown helper
    function bindDropdown(btnId, menuId) {
        const btn  = document.getElementById(btnId);
        const menu = document.getElementById(menuId);
        if (!btn || !menu) return;

        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            document.querySelectorAll('.dropdown.open, .user-menu.open').forEach(el => {
                if (el !== menu) el.classList.remove('open');
            });
            menu.classList.toggle('open');
        });
    }

    bindDropdown('chatToggle', 'chatDropdown');
    bindDropdown('helpToggle', 'helpDropdown');

    // Profile pill
    const pill = document.getElementById('profilePill');
    const userMenu = document.getElementById('userMenu');

    pill.addEventListener('click', (e) => {
        if (e.target.closest('.user-menu')) return;
        e.stopPropagation();
        document.querySelectorAll('.dropdown.open').forEach(el => el.classList.remove('open'));
        userMenu.classList.toggle('open');
    });

    // Click outside closes everything
    document.addEventListener('click', (e) => {
        document.querySelectorAll('.dropdown.open, .user-menu.open').forEach(el => {
            if (!el.parentElement.contains(e.target)) {
                el.classList.remove('open');
            }
        });
    });
</script>

</body>
</html>