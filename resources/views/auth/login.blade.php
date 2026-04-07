<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LIS - Sign In</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; min-height: 100vh; display: flex; }
        .login-left {
            flex: 1;
            background: linear-gradient(135deg, #1a2a3a 0%, #2c3e50 50%, #34495e 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: #fff;
            padding: 40px;
            position: relative;
            overflow: hidden;
        }
        .login-left::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(52,152,219,0.08) 0%, transparent 70%);
            animation: float 15s infinite;
        }
        @keyframes float {
            0%, 100% { transform: translate(0,0); }
            50% { transform: translate(30px, 30px); }
        }
        .hospital-icon {
            width: 100px;
            height: 100px;
            border: 3px solid rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
            position: relative;
            z-index: 1;
        }
        .hospital-icon i { font-size: 42px; color: rgba(255,255,255,0.7); }
        .hospital-name-ar {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
            text-align: center;
        }
        .hospital-name-en {
            font-size: 16px;
            color: rgba(255,255,255,0.7);
            margin-bottom: 30px;
            position: relative;
            z-index: 1;
        }
        .hospital-desc {
            font-size: 14px;
            color: rgba(255,255,255,0.5);
            text-align: center;
            line-height: 1.6;
            position: relative;
            z-index: 1;
            max-width: 400px;
        }
        .login-right {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            background: #fff;
            position: relative;
        }
        .lang-switch {
            position: absolute;
            top: 20px;
            right: 20px;
        }
        .lang-switch a {
            color: #666;
            text-decoration: none;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .lang-switch a:hover { color: #333; }
        .login-form {
            width: 100%;
            max-width: 400px;
        }
        .login-form h2 {
            font-size: 28px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 6px;
        }
        .login-form .subtitle {
            color: #999;
            font-size: 14px;
            margin-bottom: 35px;
        }
        .login-form .form-group {
            margin-bottom: 20px;
        }
        .login-form .input-wrapper {
            position: relative;
            background: #f0f4f8;
            border-radius: 8px;
            display: flex;
            align-items: center;
            border: 2px solid transparent;
            transition: all 0.2s;
        }
        .login-form .input-wrapper:focus-within {
            border-color: #3498db;
            background: #fff;
        }
        .login-form .input-wrapper .input-icon {
            padding: 0 14px;
            color: #aaa;
            font-size: 14px;
        }
        .login-form .input-wrapper input {
            flex: 1;
            border: none;
            background: transparent;
            padding: 14px 14px 14px 0;
            font-size: 14px;
            outline: none;
            color: #333;
        }
        .login-form .input-wrapper input::placeholder { color: #aaa; }
        .login-form .form-check {
            margin-bottom: 24px;
        }
        .login-form .form-check-label {
            font-size: 13px;
            color: #666;
        }
        .forgot-link {
            font-size: 13px;
            color: #e74c3c;
            text-decoration: none;
            font-weight: 600;
        }
        .forgot-link:hover { color: #c0392b; }
        .btn-login {
            width: 100%;
            padding: 14px;
            background: #3498db;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s;
        }
        .btn-login:hover {
            background: #2980b9;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(52,152,219,0.3);
        }
        .login-footer {
            margin-top: 50px;
            text-align: center;
            color: #aaa;
            font-size: 12px;
        }
        @media (max-width: 768px) {
            body { flex-direction: column; }
            .login-left { min-height: 200px; padding: 30px; }
            .hospital-name-ar { font-size: 24px; }
            .hospital-desc { display: none; }
        }
    </style>
</head>
<body>
    <div class="login-left">
        <div class="hospital-icon">
            <i class="fas fa-hospital"></i>
        </div>
        <div class="hospital-name-ar">مستشفى الشيخ البدري الجامعي</div>
        <div class="hospital-name-en">Sheikh Albadri University Hospital</div>
        <div class="hospital-desc">
            Modern Medical Laboratory Information System for<br>
            accelerating performance and improving healthcare quality.
        </div>
    </div>

    <div class="login-right">
        <div class="lang-switch">
            @if(app()->getLocale() == 'en')
                <a href="{{ route('lang.switch', 'ar') }}"><i class="fas fa-globe"></i> العربية</a>
            @else
                <a href="{{ route('lang.switch', 'en') }}"><i class="fas fa-globe"></i> English</a>
            @endif
        </div>

        <div class="login-form">
            <h2>Sign In</h2>
            <p class="subtitle">Enter your credentials to continue</p>

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" placeholder="admin@lis.com" value="{{ old('email') }}" required autofocus>
                    </div>
                    @error('email') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-key"></i></span>
                        <input type="password" name="password" placeholder="••••••" required>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember">Remember Me</label>
                    </div>
                    <a href="#" class="forgot-link">Forgot Password?</a>
                </div>

                <button type="submit" class="btn-login">
                    Login <i class="fas fa-sign-in-alt"></i>
                </button>
            </form>

            <div class="login-footer">
                &copy; {{ date('Y') }} Sheikh Albadri University Hospital.<br>
                All rights reserved.
            </div>
        </div>
    </div>
</body>
</html>
