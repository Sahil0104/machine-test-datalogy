<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'User Management')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:        #0d0f14;
            --surface:   #161921;
            --border:    #252a35;
            --accent:    #6c63ff;
            --accent2:   #a78bfa;
            --text:      #e2e8f0;
            --muted:     #64748b;
            --danger:    #f43f5e;
            --success:   #10b981;
            --warning:   #f59e0b;
            --sidebar-w: 240px;
        }

        body {
            font-family: 'Manrope', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        /* ── Sidebar ── */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            z-index: 100;
        }
        .sidebar-brand {
            padding: 28px 24px 20px;
            border-bottom: 1px solid var(--border);
        }
        .sidebar-brand h1 {
            font-family: 'Sora', sans-serif;
            font-size: 1.25rem;
            color: var(--accent2);
            letter-spacing: -.5px;
        }
        .sidebar-brand span { color: var(--muted); font-size: .75rem; font-weight: 400; display: block; margin-top: 2px; }
        .sidebar-nav { padding: 16px 12px; flex: 1; }
        .nav-label { font-size: .65rem; text-transform: uppercase; letter-spacing: 1.5px; color: var(--muted); padding: 8px 12px 6px; }
        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px; border-radius: 8px;
            color: var(--muted); font-size: .9rem; font-weight: 500;
            text-decoration: none;
            transition: all .2s;
            margin-bottom: 2px;
        }
        .nav-item:hover, .nav-item.active {
            background: rgba(108,99,255,.12);
            color: var(--accent2);
        }
        .nav-item svg { width: 16px; height: 16px; flex-shrink: 0; }
        .sidebar-footer {
            padding: 16px 12px;
            border-top: 1px solid var(--border);
        }
        .user-info {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px; border-radius: 8px;
            margin-bottom: 8px;
            background: rgba(255,255,255,.03);
        }
        .user-avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            display: flex; align-items: center; justify-content: center;
            font-size: .75rem; font-weight: 700; color: #fff; flex-shrink: 0;
        }
        .user-name { font-size: .83rem; font-weight: 600; }
        .user-email { font-size: .72rem; color: var(--muted); }

        /* ── Main ── */
        .main {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            display: flex; flex-direction: column;
        }
        .topbar {
            height: 60px;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center;
            padding: 0 28px;
            gap: 12px;
        }
        .topbar-title { font-family: 'Sora', sans-serif; font-size: 1.05rem; flex: 1; }
        .content { padding: 28px; flex: 1; }

        /* ── Cards ── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 24px;
        }
        .card-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 20px;
        }
        .card-title { font-size: 1rem; font-weight: 600; }

        /* ── Stat card ── */
        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 24px;
            display: flex; align-items: center; gap: 18px;
        }
        .stat-icon {
            width: 52px; height: 52px; border-radius: 12px;
            background: rgba(108,99,255,.15);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .stat-icon svg { color: var(--accent2); width: 24px; height: 24px; }
        .stat-num { font-family: 'Sora', sans-serif; font-size: 2rem; font-weight: 800; line-height: 1; }
        .stat-label { color: var(--muted); font-size: .85rem; margin-top: 4px; }

        /* ── Buttons ── */
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 9px 18px; border-radius: 8px;
            font-family: inherit; font-size: .875rem; font-weight: 600;
            cursor: pointer; border: none; transition: all .2s;
            text-decoration: none;
        }
        .btn-primary { background: var(--accent); color: #fff; }
        .btn-primary:hover { background: #5b52e0; }
        .btn-danger { background: rgba(244,63,94,.15); color: var(--danger); border: 1px solid rgba(244,63,94,.2); }
        .btn-danger:hover { background: rgba(244,63,94,.25); }
        .btn-edit { background: rgba(167,139,250,.12); color: var(--accent2); border: 1px solid rgba(167,139,250,.2); }
        .btn-edit:hover { background: rgba(167,139,250,.22); }
        .btn-sm { padding: 6px 12px; font-size: .8rem; }
        .btn-outline { background: transparent; border: 1px solid var(--border); color: var(--muted); }
        .btn-outline:hover { border-color: var(--accent); color: var(--accent2); }

        /* ── Forms ── */
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: .83rem; font-weight: 600; color: var(--muted); margin-bottom: 6px; letter-spacing: .3px; }
        .form-control {
            width: 100%; padding: 10px 14px;
            background: rgba(255,255,255,.04);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text); font-family: inherit; font-size: .9rem;
            transition: border-color .2s, box-shadow .2s;
            outline: none;
        }
        .form-control:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(108,99,255,.15); }
        .form-control.error { border-color: var(--danger); }
        label.error { color: var(--danger); font-size: .78rem; margin-top: 4px; display: block; }

        /* ── Alerts ── */
        .alert {
            padding: 12px 16px; border-radius: 8px;
            font-size: .875rem; margin-bottom: 18px;
            display: flex; align-items: flex-start; gap: 10px;
        }
        .alert-danger  { background: rgba(244,63,94,.1);  border: 1px solid rgba(244,63,94,.2);  color: #fca5a5; }
        .alert-success { background: rgba(16,185,129,.1); border: 1px solid rgba(16,185,129,.2); color: #6ee7b7; }

        /* ── Modal ── */
        .modal-backdrop {
            position: fixed; inset: 0;
            background: rgba(0,0,0,.6);
            display: none; align-items: center; justify-content: center;
            z-index: 999;
        }
        .modal-backdrop.show { display: flex; }
        .modal-box {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            width: 100%; max-width: 480px;
            padding: 28px;
            animation: modalIn .2s ease;
        }
        @keyframes modalIn { from { opacity:0; transform:translateY(-16px); } to { opacity:1; transform:none; } }
        .modal-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 22px;
        }
        .modal-title { font-family: 'Sora', sans-serif; font-size: 1.1rem; }
        .modal-close { background: none; border: none; color: var(--muted); cursor: pointer; font-size: 1.3rem; line-height: 1; }
        .modal-close:hover { color: var(--text); }
        .modal-footer { display: flex; justify-content: flex-end; gap: 10px; margin-top: 22px; }

        /* ── DataTable custom ── */
        .dataTables_wrapper { font-family: 'Manrope', sans-serif; }
        table.dataTable { border-collapse: collapse !important; width: 100% !important; }
        table.dataTable thead th {
            background: rgba(255,255,255,.03);
            color: var(--muted); font-size: .78rem; text-transform: uppercase;
            letter-spacing: .8px; padding: 12px 14px; border-bottom: 1px solid var(--border) !important;
            border-top: none !important;
        }
        table.dataTable tbody tr { border-bottom: 1px solid var(--border); }
        table.dataTable tbody td { padding: 13px 14px; font-size: .88rem; color: var(--text); border: none !important; }
        table.dataTable tbody tr:hover td { background: rgba(255,255,255,.025); }
        .dataTables_filter input, .dataTables_length select {
            background: var(--surface) !important;
            border: 1px solid var(--border) !important;
            color: var(--text) !important;
            border-radius: 6px;
            padding: 6px 10px;
            font-family: inherit;
            outline: none;
        }
        .dataTables_filter input:focus { border-color: var(--accent) !important; }
        .dataTables_filter label, .dataTables_length label,
        .dataTables_info, .dataTables_paginate { color: var(--muted) !important; font-size: .83rem !important; }
        .dataTables_paginate .paginate_button {
            color: var(--muted) !important;
            border: none !important;
            border-radius: 6px !important;
            padding: 4px 10px !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--accent) !important;
            color: #fff !important;
        }
        .dataTables_wrapper .dataTables_paginate span .paginate_button:hover,
        .dataTables_wrapper .dataTables_paginate a.paginate_button:hover,
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.current) {
            background: rgba(108,99,255,.15) !important;
            color: var(--accent2) !important;
            border: none !important;
            background-image: none !important;
        }

        /* ── Logout form ── */
        .logout-form { display: inline; }
        .logout-btn {
            background: none; border: none;
            color: var(--muted); font-size: .9rem;
            font-family: inherit; font-weight: 500;
            cursor: pointer; display: flex; align-items: center; gap: 10px;
            padding: 10px 12px; border-radius: 8px; width: 100%;
            transition: all .2s;
        }
        .logout-btn:hover { background: rgba(244,63,94,.1); color: var(--danger); }
    </style>
    @stack('styles')
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand">
            <h1>Datalogy User Panel</h1>
        {{-- <span>Management System</span> --}}
    </div>
    <nav class="sidebar-nav">
        <div class="nav-label">Menu</div>
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Dashboard
        </a>
        <a href="{{ route('users.index') }}" class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Users
        </a>
    </nav>
    <div class="sidebar-footer">
        @if(session('user_name'))
        <div class="user-info">
            <div class="user-avatar">{{ strtoupper(substr(session('user_name'), 0, 2)) }}</div>
            <div>
                <div class="user-name">{{ session('user_name') }}</div>
                <div class="user-email">{{ session('user_email') }}</div>
            </div>
        </div>
        @endif
        <form action="{{ route('logout') }}" method="POST" class="logout-form">
            @csrf
            <button type="submit" class="logout-btn">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Logout
            </button>
        </form>
    </div>
</div>

<div class="main">
    <div class="topbar">
        <span class="topbar-title">@yield('page-title', 'Dashboard')</span>
    </div>
    <div class="content">
        @yield('content')
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
@stack('scripts')
</body>
</html>
