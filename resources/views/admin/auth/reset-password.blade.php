<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Reset Password - PT Lovina North Bali</title>
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
            margin-bottom: 28px;
            line-height: 1.5;
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

        /* Prevent browser native reveal button (Edge/IE) */
        input::-ms-reveal,
        input::-ms-clear {
            display: none;
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
            transition: color 0.2s;
        }

        .toggle-password-btn:hover {
            color: var(--primary-navy);
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
            transition: background-color 0.2s;
        }

        .btn-primary:hover {
            background-color: #152C6F;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            margin-top: 24px;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: var(--primary-navy);
            text-decoration: underline;
        }

        .footer-copyright {
            margin-top: 32px;
            font-size: 12px;
            color: var(--text-muted);
        }
    </style>
</head>
<body>
    <div class="login-card" id="admin-reset-password-card">
        <div class="brand-header">
            <img src="{{ asset('images/black logo lovina.png') }}" alt="PT LOVINA NORTH BALI REAL ESTATE AGENCY" class="brand-logo-img">
        </div>

        <h1 class="login-heading">Set New Password</h1>
        <p class="login-subheading">Please enter your new password to access your admin account.</p>

        @if ($errors->any())
            <div style="background-color: #FEE2E2; border: 1px solid #FCA5A5; color: #991B1B; padding: 12px 16px; border-radius: 8px; font-size: 14px; margin-bottom: 24px; text-align: left; display: flex; align-items: flex-start; gap: 8px;" id="reset-error-alert">
                <i data-lucide="alert-triangle" style="width: 18px; height: 18px; color: #991B1B; flex-shrink: 0; margin-top: 2px;"></i>
                <div style="flex: 1;">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        <form action="{{ route('admin.password.update') }}" method="POST" id="adminResetPasswordForm">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <div class="input-wrapper">
                    <span class="input-icon">
                        <i data-lucide="mail" style="width: 18px; height: 18px;"></i>
                    </span>
                    <input type="email" name="email" id="email" class="form-input" value="{{ old('email', $email) }}" placeholder="Enter your email" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">New Password</label>
                <div class="input-wrapper">
                    <span class="input-icon">
                        <i data-lucide="lock" style="width: 18px; height: 18px;"></i>
                    </span>
                    <input type="password" name="password" id="password" class="form-input form-input-password" placeholder="Minimum 8 characters" required>
                    <button type="button" id="togglePasswordBtn" class="toggle-password-btn" aria-label="Toggle password visibility">
                        <i data-lucide="eye-off" style="width: 18px; height: 18px;"></i>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                <div class="input-wrapper">
                    <span class="input-icon">
                        <i data-lucide="lock" style="width: 18px; height: 18px;"></i>
                    </span>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-input form-input-password" placeholder="Confirm your new password" required>
                    <button type="button" id="toggleConfirmPasswordBtn" class="toggle-password-btn" aria-label="Toggle password confirmation visibility">
                        <i data-lucide="eye-off" style="width: 18px; height: 18px;"></i>
                    </button>
                </div>
            </div>

            <button type="submit" id="btn-reset-password" class="btn-primary">
                Reset Password <i data-lucide="check" style="width: 18px; height: 18px; color: #FFFFFF;"></i>
            </button>
        </form>

        <div>
            <a href="{{ route('admin.login') }}" class="back-link" id="link-back-to-login">
                <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
                <span>Back to Login</span>
            </a>
        </div>

        <div class="footer-copyright">
            &copy; {{ date('Y') }} PT Lovina North Bali Real Estate Agency. All rights reserved.
        </div>
    </div>

    <script>
        lucide.createIcons();

        // Password visibility toggles
        const togglePasswordBtn = document.getElementById('togglePasswordBtn');
        const passwordInput = document.getElementById('password');
        if (togglePasswordBtn && passwordInput) {
            togglePasswordBtn.addEventListener('click', function () {
                const isPassword = passwordInput.getAttribute('type') === 'password';
                passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                const iconName = isPassword ? 'eye' : 'eye-off';
                togglePasswordBtn.innerHTML = `<i data-lucide="${iconName}" style="width: 18px; height: 18px;"></i>`;
                if (window.lucide && typeof window.lucide.createIcons === 'function') {
                    window.lucide.createIcons({
                        root: togglePasswordBtn
                    });
                }
            });
        }

        const toggleConfirmPasswordBtn = document.getElementById('toggleConfirmPasswordBtn');
        const confirmPasswordInput = document.getElementById('password_confirmation');
        if (toggleConfirmPasswordBtn && confirmPasswordInput) {
            toggleConfirmPasswordBtn.addEventListener('click', function () {
                const isPassword = confirmPasswordInput.getAttribute('type') === 'password';
                confirmPasswordInput.setAttribute('type', isPassword ? 'text' : 'password');
                const iconName = isPassword ? 'eye' : 'eye-off';
                toggleConfirmPasswordBtn.innerHTML = `<i data-lucide="${iconName}" style="width: 18px; height: 18px;"></i>`;
                if (window.lucide && typeof window.lucide.createIcons === 'function') {
                    window.lucide.createIcons({
                        root: toggleConfirmPasswordBtn
                    });
                }
            });
        }
    </script>
</body>
</html>
