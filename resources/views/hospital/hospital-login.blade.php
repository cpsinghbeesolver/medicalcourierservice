<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ env('APP_NAME') }} - Login</title>
    <link rel="icon" type="image/png" href="/assets/img/fav.png">
    <script src="https://cdn.jsdelivr.net/npm/just-validate@latest/dist/just-validate.production.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            width: 100%;
            overflow: hidden;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-wrapper {
            display: flex;
            flex-direction: row;
            width: 100%;
            height: 100vh;
            padding: 23px;
            overflow: hidden;    
        }

        .left-section {
            flex: 0 0 50%;
            width: 50%;
            background-color: #f0f0f0;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .left-section img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            border-radius: 13px;
        }

        .right-section {
            flex: 0 0 50%;
            width: 50%;
            background: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
            overflow-y: auto;
        }

        .login-container {
            width: 100%;
            max-width: 500px;
        }

        .logo {
            margin-bottom: 50px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-icon {
            width: 150px;
            height: 150px;
            position: relative;
            flex-shrink: 0;
            margin: 0px auto;
        }

        .logo-icon img {
            width: 100%;
            height: 100%;
        }

        .logo-text h1 {
            font-size: 22px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 2px;
        }

        .logo-text h1 .highlight {
            color: #d4a259;
        }

        .logo-text p {
            font-size: 9px;
            color: #95a5a6;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 400;
        }

        .login-header {
            margin-bottom: 40px;
        }

        .login-header h2 {
            font-size: 30px;
            color: #2c3e50;
            margin-bottom: 8px;
            font-weight: 700;
        }

        .login-header p {
            font-size: 13px;
            color: #7f8c8d;
            line-height: 1.5;
            font-weight: 400;
        }

        .form-group {
            margin-bottom: 30px;
        }

        .form-group label {
            display: block;
            color: #7f8c8d;
            font-size: 13px;
            font-weight: 400;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 7px;
            border: none;
            border-bottom: 1px solid #e0e0e0;
            font-size: 15px;
            color: #2c3e50;
            background: transparent;
            transition: border-color 0.3s ease;
            font-weight: 400;
        }

        .form-group input::placeholder {
            color: #bdc3c7;
        }

        .form-group input:focus {
            outline: none;
            border-bottom-color: #2c3e50;
        }

        .forgot-password {
            text-align: right;
            margin-bottom: 35px;
        }

        .forgot-password a {
            color: #d4a259;
            text-decoration: none;
            font-size: 13px;
            font-weight: 400;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            background: #2c3e50;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s ease;
            letter-spacing: 0.3px;
        }

        .btn-login:hover {
            background: #34495e;
        }

        .btn-login:disabled {
            background: #95a5a6;
            cursor: not-allowed;
        }

        .message {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .message.info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .password-wrapper {
            position: relative;
            display: grid;
            align-items: center;
            width: 100%;
        }

        .password-wrapper input {
            flex: 1;
            padding-right: 32px;
        }

        .eye-icon {
            position: absolute;
            right: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            height: 18px;
            top: 11px;
            color: #7f8c8d;
            transition: color 0.3s ease;
        }

        .eye-icon:hover {
            color: #2c3e50;
        }
        .just-validate-error-label {
            font-size: 13px !important;
            line-height: 1.4;
        }
        .loading-spinner{
            display: none;  
            text-align: center;
            padding: 60px;
            color: #7f8c8d;
            position: fixed;
            top: 40%;
            left: 50%;
        }

        @media (max-width: 968px) {
            .login-wrapper {
                flex-direction: column;
            }

            .left-section {
                display: none;
            }

            .right-section {
                width: 100%;
                flex: 1;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="left-section">
            <img src="/images/tablet.png" alt="Medical Courier">
        </div>
        <div class="loading-spinner">
            <i class="fas fa-spinner fa-spin" style="font-size: 32px; color: #a8b456;"></i>
            <p style="margin-top: 15px;" class="loading-text"></p>
        </div>
        <div class="right-section">
            <div class="login-container">
                <div class="logo">
                    <div class="logo-icon">
                        <img src="/images/logo_new.png" alt="ReliaTrack Logo">
                    </div>
                </div>

                <div class="login-header">
                    <h2>Hospital Login</h2>
                    <p>Access your secure portal with your credentials.</p>
                </div>

                <div id="message">
                    @if(session('error'))
                        <div class="message error">{{ session('error') }}</div>
                    @endif

                    @if(session('success'))
                        <div class="message success">{{ session('success') }}</div>
                    @endif
                </div>

                <form id="loginForm" method="POST" action="{{ route('hospital-login') }}" autocomplete="off">
                    @csrf
                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <input type="email" maxlength="254" id="email" name="email" placeholder="Please enter email" required autocomplete="off">
                    </div>

                    <!-- <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div> -->
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="password-wrapper">
                            <input type="password" maxlength="128" id="hospital-password" name="password" required placeholder="Please enter password" autocomplete="new-password">
                            <svg class="eye-icon" id="togglePassword" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                        </div>
                    </div>

                    <!-- <div class="forgot-password">
                        <a href="#">Forgot password?</a>
                    </div> -->
                    <!-- <input type="hidden" name="device_name" value="web"> -->
                    <button type="submit" class="btn-login" id="loginBtn">Login</button>
                </form>
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/js/validation.js') }}"></script>
     <script>
        function setupPasswordToggle(passwordInputId, toggleIconId) {
            const passwordInput = document.getElementById(passwordInputId);
            const toggleIcon = document.getElementById(toggleIconId);

            if (!passwordInput || !toggleIcon) {
                return;
            }

            toggleIcon.addEventListener('click', function() {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    toggleIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
                } else {
                    passwordInput.type = 'password';
                    toggleIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
                }
            });
        }

        setupPasswordToggle('hospital-password', 'togglePassword');
    </script>
</body>
</html>
