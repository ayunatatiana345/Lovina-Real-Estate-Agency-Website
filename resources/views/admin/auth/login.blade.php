<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin Login - PT Lovina North Bali</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <script src="https://unpkg.com/lucide@0.428.0/dist/umd/lucide.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');

        :root {
            --primary-navy: #1E3A8A;
            --medium-blue: #5A86C5;
            --light-blue: #D6E6F7;
            --light-bg: #F0F4F9;
            --white: #FFFFFF;
            --border: #E5E7EB;
            --text-primary: #1F2937;
            --text-secondary: #4B5563;
            --text-muted: #6B7280;
            --font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: var(--light-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px;
            font-family: var(--font-family);
            color: var(--text-primary);
            margin: 0;
            -webkit-font-smoothing: antialiased;
        }

        .login-card {
            background-color: var(--white);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.06);
            width: 100%;
            max-width: 440px;
            padding: 40px;
            text-align: center;
            border: 1px solid var(--border);
        }

        .brand-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .brand-logo-img {
            height: 160px;
            max-height: 160px;
            width: auto;
            max-width: 100%;
            object-fit: contain;
            margin-bottom: 0;
        }

        .login-heading {
            font-size: 26px;
            font-weight: 700;
            color: var(--primary-navy);
            margin-bottom: 6px;
        }

        .login-subheading {
            font-size: 14px;
            color: var(--text-muted);
            margin-bottom: 32px;
        }

        .form-group {
            text-align: left;
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 6px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 12px;
            color: #94A3B8;
            display: flex;
            align-items: center;
        }

        .form-input {
            width: 100%;
            padding: 12px 14px 12px 42px;
            border: 1px solid #CBD5E1;
            border-radius: 8px;
            font-family: inherit;
            font-size: 14px;
            color: var(--text-primary);
            background-color: var(--white);
            box-sizing: border-box;
            outline: none;
        }

        .form-input:focus {
            border-color: var(--primary-navy);
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
        }

        .form-input-password {
            padding-right: 42px;
        }

        .toggle-password-btn {
            position: absolute;
            right: 14px;
            top: 12px;
            background: none;
            border: none;
            cursor: pointer;
            color: #94A3B8;
            display: flex;
            align-items: center;
            padding: 0;
        }

        .btn-primary {
            width: 100%;
            padding: 14px;
            background-color: var(--primary-navy);
            color: var(--white);
            border: none;
            border-radius: 8px;
            font-family: inherit;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary:hover {
            background-color: #152C6F;
        }

        .footer-copyright {
            margin-top: 32px;
            font-size: 12px;
            color: var(--text-muted);
        }
    </style>
</head>
<body>
    <div class="login-card" id="admin-login-card">
        <div class="brand-header">
            <img src="{{ asset('images/black logo lovina.png') }}" alt="PT LOVINA NORTH BALI REAL ESTATE AGENCY" class="brand-logo-img">
        </div>

        <h1 class="login-heading">Admin Login</h1>
        <p class="login-subheading">Please sign in to access your admin dashboard</p>

        @if (session('status'))
            <div style="background-color: #DEF7EC; border: 1px solid #BCF0DA; color: #03543F; padding: 12px 16px; border-radius: 8px; font-size: 14px; margin-bottom: 24px; text-align: left; display: flex; align-items: center; gap: 8px;" id="login-status-alert">
                <i data-lucide="check-circle" style="width: 18px; height: 18px; color: #0E9F6E;"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if($errors->has('login'))
            <div style="background-color: #FEE2E2; border: 1px solid #FCA5A5; color: #991B1B; padding: 12px 16px; border-radius: 8px; font-size: 14px; margin-bottom: 24px; text-align: left; display: flex; align-items: center; gap: 8px;" id="login-error-alert">
                <i data-lucide="alert-triangle" style="width: 18px; height: 18px; color: #991B1B;"></i>
                <span>{{ $errors->first('login') }}</span>
            </div>
        @endif

        <form action="{{ route('admin.login.submit') }}" method="POST" id="adminLoginForm">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <div class="input-wrapper">
                    <span class="input-icon">
                        <i data-lucide="mail" style="width: 18px; height: 18px;"></i>
                    </span>
                    <input type="email" name="email" id="email" class="form-input" value="{{ old('email') }}" placeholder="Enter your email" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <div class="input-wrapper">
                    <span class="input-icon">
                        <i data-lucide="lock" style="width: 18px; height: 18px;"></i>
                    </span>
                    <input type="password" name="password" id="password" class="form-input form-input-password" placeholder="••••••••" required>
                    <button type="button" id="togglePasswordBtn" class="toggle-password-btn" aria-label="Toggle password visibility">
                        <i data-lucide="eye" style="width: 18px; height: 18px;"></i>
                    </button>
                </div>
            </div>

            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 28px; font-size: 14px;">
                <label style="display: flex; align-items: center; gap: 8px; color: var(--text-secondary); cursor: pointer;">
                    <input type="checkbox" name="remember" id="remember" style="width: 16px; height: 16px; accent-color: var(--primary-navy);">
                    <span>Remember Me</span>
                </label>
                <a href="{{ route('admin.password.request') }}" id="link-forgot-password" style="color: var(--primary-navy); text-decoration: none; font-weight: 500; font-size: 14px; transition: color 0.2s;" onmouseover="this.style.color='#152C6F'; this.style.textDecoration='underline';" onmouseout="this.style.color='var(--primary-navy)'; this.style.textDecoration='none';">
                    Forgot Password?
                </a>
            </div>

            <button type="submit" id="btn-admin-login" class="btn-primary">
                Login <i data-lucide="log-in" style="width: 18px; height: 18px; color: #FFFFFF;"></i>
            </button>
        </form>

        <div class="footer-copyright">
            &copy; {{ date('Y') }} PT Lovina North Bali Real Estate Agency. All rights reserved.
        </div>
    </div>

    <script src="{{ asset('js/admin.js') }}"></script>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>

