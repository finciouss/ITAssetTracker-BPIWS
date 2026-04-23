<!DOCTYPE html>
<html lang="en" style="zoom: 0.8;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — IT Asset Tracker BAUER</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('img/bauer-logo.jpeg') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        *, *::before, *::after { box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(145deg, #020617 0%, #1e293b 45%, #334155 75%, #475569 100%);
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }

        /* Background decorations */
        body::before, body::after {
            content: '';
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
        }
        body::before {
            width: 600px; height: 600px;
            top: -200px; right: -150px;
            background: radial-gradient(circle, rgba(148,163,184,.15) 0%, transparent 70%);
        }
        body::after {
            width: 400px; height: 400px;
            bottom: -100px; left: -100px;
            background: radial-gradient(circle, rgba(203,213,225,.1) 0%, transparent 70%);
        }

        .login-wrap {
            display: flex;
            width: 100%;
            max-width: 860px;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 30px 80px rgba(0,0,0,.5);
            position: relative;
            z-index: 1;
        }

        /* Left panel */
        .login-panel-left {
            flex: 1;
            background: linear-gradient(160deg, #1e293b, #64748b);
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: white;
        }
        @media (max-width: 600px) { .login-panel-left { display: none; } }
        .login-panel-left .brand { display: flex; align-items: center; gap: .75rem; }
        .login-panel-left .brand img { height: 42px; border-radius: 8px; }
        .login-panel-left .brand-name { font-weight: 800; font-size: 1.4rem; line-height: 1.1; }
        .login-panel-left .brand-sub  { font-size: .78rem; opacity: .7; margin-top: 2px; font-style: italic; }
        .login-panel-left .tagline { font-size: 2rem; font-weight: 800; line-height: 1.2; letter-spacing: -1px; }
        .login-panel-left .tagline span { opacity: .6; font-weight: 400; }
        .login-panel-left .footer-note { font-size: .75rem; opacity: .5; }

        /* Right panel */
        .login-panel-right {
            width: 380px;
            flex-shrink: 0;
            background: #fff;
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        @media (max-width: 600px) { .login-panel-right { width: 100%; border-radius: 24px; } }

        .login-title { font-size: 1.5rem; font-weight: 800; color: #0f172a; letter-spacing: -.5px; margin-bottom: .25rem; }
        .login-sub   { font-size: .875rem; color: #64748b; margin-bottom: 2rem; }

        .form-label {
            font-size: .75rem;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: .4px;
            margin-bottom: .4rem;
        }
        .input-wrap { position: relative; }
        .input-wrap .input-icon {
            position: absolute;
            left: .85rem; top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1rem;
            pointer-events: none;
        }
        .input-wrap input {
            width: 100%;
            padding: .65rem .9rem .65rem 2.5rem;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: .9rem;
            font-family: 'Inter', sans-serif;
            transition: all .2s;
            background: #f8fafc;
            color: #0f172a;
        }
        .input-wrap input:focus {
            outline: none;
            border-color: #475569;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(71,85,105,.15);
        }

        .error-box {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 10px;
            color: #b91c1c;
            font-size: .8rem;
            padding: .65rem .9rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .btn-login {
            width: 100%;
            padding: .75rem;
            background: linear-gradient(135deg, #475569, #334155);
            border: none;
            border-radius: 10px;
            color: #fff;
            font-weight: 700;
            font-size: .95rem;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all .2s;
            box-shadow: 0 4px 16px rgba(51,65,85,.3);
            letter-spacing: .1px;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(51,65,85,.4);
            background: linear-gradient(135deg, #334155, #1e293b);
        }
        .btn-login:active { transform: translateY(0); }

        .login-footer { margin-top: 2rem; font-size: .72rem; color: #94a3b8; text-align: center; }

        /* Stats decorative */
        .stat-items { display: flex; flex-direction: column; gap: .75rem; }
        .stat-item { display: flex; align-items: center; gap: .75rem; }
        .stat-icon { width: 38px; height: 38px; background: rgba(255,255,255,.15); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0; }
        .stat-label { font-size: .8rem; opacity: .85; font-weight: 500; }
    </style>
</head>
<body>
    <div class="login-wrap">
        {{-- Left Panel --}}
        <div class="login-panel-left">
            <div class="brand">
                <img src="{{ asset('img/bauer-logo.jpeg') }}" alt="BAUER">
                <div>
                    <div class="brand-name">IT Department</div>
                    <div class="brand-sub">Internal Management Access</div>
                </div>
            </div>

            <div>
                <div class="tagline">IT Asset Tracker<br><span>Management</span><br>System.</div>
            </div>

            <div class="stat-items">
                <div class="stat-item">
                    <div class="stat-icon"><i class="bi bi-pc-display"></i></div>
                    <div class="stat-label">Manage IT Assets Lifecycle</div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon"><i class="bi bi-people"></i></div>
                    <div class="stat-label">Track Employee Allocations</div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon"><i class="bi bi-clock-history"></i></div>
                    <div class="stat-label">Detailed Audit & Maintenance Logs</div>
                </div>
            </div>

            <div class="footer-note">PT. BAUER Pratama Indonesia &copy; {{ date('Y') }}</div>
        </div>

        {{-- Right Panel --}}
        <div class="login-panel-right">
            <div class="login-title">Selamat Datang</div>
            <div class="login-sub">Masuk untuk melanjutkan ke sistem</div>

            @if($errors->any())
            <div class="error-box">
                <i class="bi bi-exclamation-triangle-fill"></i>
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <div class="input-wrap">
                        <i class="bi bi-envelope input-icon"></i>
                        <input type="email" name="email" value="{{ old('email') }}"
                               placeholder="Masukkan email" required autofocus>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <div class="input-wrap">
                        <i class="bi bi-lock input-icon"></i>
                        <input type="password" name="password"
                               placeholder="Masukkan password" required>
                    </div>
                </div>
                <button type="submit" class="btn-login">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Masuk ke Sistem
                </button>
            </form>

            <div class="login-footer">
                Akses terbatas untuk departemen IT PT. BAUER Pratama Indonesia
            </div>
        </div>
    </div>
</body>
</html>
