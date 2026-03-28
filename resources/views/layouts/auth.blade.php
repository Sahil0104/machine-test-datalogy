<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'User Management')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:      #0d0f14;
            --surface: #161921;
            --border:  #252a35;
            --accent:  #6c63ff;
            --accent2: #a78bfa;
            --text:    #e2e8f0;
            --muted:   #64748b;
            --danger:  #f43f5e;
            --success: #10b981;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* background decoration */
        body::before {
            content: '';
            position: fixed;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(108,99,255,.12) 0%, transparent 70%);
            top: -150px; left: -150px;
            pointer-events: none;
        }
        body::after {
            content: '';
            position: fixed;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(167,139,250,.08) 0%, transparent 70%);
            bottom: -100px; right: -100px;
            pointer-events: none;
        }

        .auth-wrapper {
            width: 100%; max-width: 420px;
            padding: 16px;
            position: relative; z-index: 1;
        }

        .auth-logo {
            text-align: center;
            margin-bottom: 28px;
        }
        .auth-logo h1 {
            font-family: 'Syne', sans-serif;
            font-size: 1.6rem;
            color: var(--accent2);
        }
        .auth-logo p { color: var(--muted); font-size: .875rem; margin-top: 4px; }

        .auth-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 32px;
        }

        .auth-card h2 {
            font-family: 'Syne', sans-serif;
            font-size: 1.3rem;
            margin-bottom: 6px;
        }
        .auth-card .subtitle {
            color: var(--muted); font-size: .875rem;
            margin-bottom: 24px;
        }

        .form-group { margin-bottom: 16px; }
        .form-label {
            display: block; font-size: .8rem; font-weight: 600;
            color: var(--muted); margin-bottom: 6px; letter-spacing: .3px;
        }
        .form-control {
            width: 100%; padding: 11px 14px;
            background: rgba(255,255,255,.04);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text); font-family: inherit; font-size: .9rem;
            transition: border-color .2s, box-shadow .2s;
            outline: none;
        }
        .form-control:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(108,99,255,.15); }
        .form-control.error { border-color: var(--danger); }
        label.error { color: var(--danger); font-size: .76rem; margin-top: 4px; display: block; }

        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

        .btn-block {
            width: 100%; padding: 12px;
            background: var(--accent);
            color: #fff; font-family: inherit; font-size: .95rem; font-weight: 600;
            border: none; border-radius: 8px; cursor: pointer;
            transition: background .2s, transform .1s;
            margin-top: 4px;
        }
        .btn-block:hover { background: #5b52e0; }
        .btn-block:active { transform: scale(.99); }

        .auth-footer {
            text-align: center;
            margin-top: 20px;
            color: var(--muted); font-size: .875rem;
        }
        .auth-footer a { color: var(--accent2); text-decoration: none; font-weight: 600; }
        .auth-footer a:hover { text-decoration: underline; }

        .alert {
            padding: 11px 14px; border-radius: 8px;
            font-size: .85rem; margin-bottom: 18px;
        }
        .alert-danger  { background: rgba(244,63,94,.1);  border: 1px solid rgba(244,63,94,.2);  color: #fca5a5; }
        .alert-success { background: rgba(16,185,129,.1); border: 1px solid rgba(16,185,129,.2); color: #6ee7b7; }
    </style>
    @stack('styles')
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-logo">
            <h1>UserPanel</h1>
            <p>User Management System</p>
        </div>
        @yield('content')
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    @stack('scripts')
</body>
</html>
