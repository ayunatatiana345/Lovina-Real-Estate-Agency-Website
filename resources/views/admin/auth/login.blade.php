<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - PT Lovina North Bali</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <script src="https://unpkg.com/lucide@0.428.0/dist/umd/lucide.min.js"></script>
    <style>
        body {
            background-color: #F0F4F9;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px;
            font-family: 'Poppins', sans-serif;
        }
        .login-card {
            background-color: #FFFFFF;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.06);
            width: 100%;
            max-width: 440px;
            padding: 40px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-card" id="admin-login-card">
        <div style="margin-bottom: 24px;">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#1E3A8A" stroke-width="2" style="margin: 0 auto 12px auto;">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            <div style="font-size: 16px; font-weight: 700; color: #1E3A8A;">PT LOVINA NORTH BALI</div>
            <div style="font-size: 12px; color: #64748B;">REAL ESTATE AGENCY</div>
        </div>

        <h1 style="font-size: 26px; font-weight: 700; color: #0F172A; margin-bottom: 6px;">Admin Login</h1>
        <p style="font-size: 14px; color: #64748B; margin-bottom: 32px;">Please sign in to access your admin dashboard</p>

        @if($errors->has('login'))
            <div style="background-color: #FEE2E2; border: 1px solid #FCA5A5; color: #991B1B; padding: 12px 16px; border-radius: 8px; font-size: 14px; margin-bottom: 24px; text-align: left; display: flex; align-items: center; gap: 8px;" id="login-error-alert">
                <i data-lucide="alert-triangle" style="width: 18px; height: 18px; color: #991B1B;"></i>
                <span>{{ $errors->first('login') }}</span>
            </div>
        @endif

        <form action="{{ route('admin.login.submit') }}" method="POST" id="adminLoginForm">
            @csrf

            <div style="text-align: left; margin-bottom: 20px;">
                <label for="email" style="display: block; font-size: 14px; font-weight: 600; color: #334155; margin-bottom: 6px;">Email Address</label>
                <div style="position: relative;">
                    <span style="position: absolute; left: 14px; top: 12px; color: #94A3B8; display: flex; align-items: center;">
                        <i data-lucide="mail" style="width: 18px; height: 18px; color: #94A3B8;"></i>
                    </span>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" style="width: 100%; padding: 12px 14px 12px 42px; border: 1px solid #CBD5E1; border-radius: 8px; font-family: inherit; font-size: 14px;" placeholder="Enter your email" required autofocus>
                </div>
            </div>

            <div style="text-align: left; margin-bottom: 20px;">
                <label for="password" style="display: block; font-size: 14px; font-weight: 600; color: #334155; margin-bottom: 6px;">Password</label>
                <div style="position: relative;">
                    <span style="position: absolute; left: 14px; top: 12px; color: #94A3B8; display: flex; align-items: center;">
                        <i data-lucide="lock" style="width: 18px; height: 18px; color: #94A3B8;"></i>
                    </span>
                    <input type="password" name="password" id="password" style="width: 100%; padding: 12px 42px 12px 42px; border: 1px solid #CBD5E1; border-radius: 8px; font-family: inherit; font-size: 14px;" placeholder="••••••••" required>
                    <button type="button" id="togglePasswordBtn" style="position: absolute; right: 14px; top: 12px; background: none; border: none; cursor: pointer; color: #94A3B8; display: flex; align-items: center;">
                        <i data-lucide="eye" style="width: 18px; height: 18px; color: #94A3B8;"></i>
                    </button>
                </div>
            </div>

            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 28px; font-size: 14px;">
                <label style="display: flex; align-items: center; gap: 8px; color: #475569; cursor: pointer;">
                    <input type="checkbox" name="remember" id="remember" style="width: 16px; height: 16px;">
                    <span>Remember Me</span>
                </label>
            </div>

            <button type="submit" id="btn-admin-login" style="width: 100%; padding: 14px; background-color: #2563EB; color: #FFFFFF; border: none; border-radius: 8px; font-family: inherit; font-size: 16px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 8px;">
                Login <i data-lucide="log-in" style="width: 18px; height: 18px; color: #FFFFFF;"></i>
            </button>
        </form>

        <div style="margin-top: 32px; font-size: 12px; color: #94A3B8;">
            &copy; {{ date('Y') }} PT Lovina North Bali Real Estate Agency. All rights reserved.
        </div>
    </div>

    <script src="{{ asset('js/admin.js') }}"></script>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
