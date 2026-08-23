<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReliaTrack - Email Verification</title>
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

        .verify-wrapper {
            display: flex;
            flex-direction: row;
            width: 100%;
            height: 100vh;
        }

        .left-section {
            flex: 0 0 50%;
            width: 50%;
            height: 100vh;
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
        }

        .right-section {
            flex: 0 0 50%;
            width: 50%;
            height: 100vh;
            background: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
            overflow-y: auto;
        }

        .verify-container {
            width: 100%;
            max-width: 500px;
        }

        .back-link {
            display: flex;
            align-items: center;
            color: #7f8c8d;
            text-decoration: none;
            font-size: 13px;
            margin-bottom: 40px;
            font-weight: 400;
        }

        .back-link:hover {
            color: #2c3e50;
        }

        .back-link::before {
            content: '←';
            margin-right: 8px;
            font-size: 16px;
        }

        .logo {
            margin-bottom: 50px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            position: relative;
            flex-shrink: 0;
        }

        .logo-icon svg {
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

        .verify-header {
            margin-bottom: 10px;
        }

        .verify-header h2 {
            font-size: 28px;
            color: #2c3e50;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .verify-header p {
            font-size: 14px;
            color: #7f8c8d;
            line-height: 1.5;
            font-weight: 400;
            margin-bottom: 5px;
        }

        .email-display {
            font-size: 14px;
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 35px;
        }

        .code-inputs {
            display: flex;
            gap: 12px;
            margin-bottom: 35px;
            justify-content: space-between;
        }

        .code-input {
            width: 60px;
            height: 60px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            text-align: center;
            font-size: 24px;
            font-weight: 600;
            color: #2c3e50;
            transition: border-color 0.3s ease;
        }

        .code-input:focus {
            outline: none;
            border-color: #2c3e50;
        }

        .btn-verify {
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
            margin-bottom: 20px;
        }

        .btn-verify:hover {
            background: #34495e;
        }

        .btn-verify:disabled {
            background: #95a5a6;
            cursor: not-allowed;
        }

        .resend-section {
            text-align: center;
            font-size: 13px;
            color: #7f8c8d;
        }

        .resend-section a {
            color: #d4a259;
            text-decoration: none;
            font-weight: 500;
        }

        .resend-section a:hover {
            text-decoration: underline;
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

        @media (max-width: 968px) {
            .verify-wrapper {
                flex-direction: column;
            }

            .left-section {
                display: none;
            }

            .right-section {
                width: 100%;
                flex: 1;
            }

            .code-inputs {
                gap: 8px;
            }

            .code-input {
                width: 50px;
                height: 50px;
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="verify-wrapper">
        <div class="left-section">
            <img src="/images/tablet.png" alt="Medical Courier">
        </div>

        <div class="right-section">
            <div class="verify-container">
                <a href="/" class="back-link">BACK</a>

                <div class="logo">
                    <div class="logo-icon">
                        <img src="/images/Logo.svg" alt="ReliaTrack Logo">
                    </div>
                </div>

                <div class="verify-header">
                    <h2>Please Check your email</h2>
                    <p>We've sent a code to</p>
                    <div class="email-display" id="userEmail">user@example.com</div>
                </div>

                <div id="message"></div>

                <form id="verifyForm" action="{{ route('submit-verify') }}" method="POST">
                    
                    <div class="code-inputs">
                        <input type="text" class="code-input" maxlength="1" id="code1" placeholder="0" required>
                        <input type="text" class="code-input" maxlength="1" id="code2" placeholder="0" required>
                        <input type="text" class="code-input" maxlength="1" id="code3" placeholder="0" required>
                        <input type="text" class="code-input" maxlength="1" id="code4" placeholder="0" required>
                        <input type="text" class="code-input" maxlength="1" id="code5" placeholder="0" required>
                        <input type="text" class="code-input" maxlength="1" id="code6" placeholder="0" required>
                    </div>
                    <input type="hidden" name="email" id="userEmailId" value="">
                    <button type="submit" class="btn-verify" id="verifyBtn">Verification</button>
                    @csrf 
                </form>

                <div class="resend-section">
                    Didn't receive the code? <a href="#" id="resendLink">Resend code</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Get email from URL parameter or localStorage
        const urlParams = new URLSearchParams(window.location.search);
        const email = urlParams.get('email') || localStorage.getItem('verify_email') || '';

        if (!email) {
            window.location.href = '/';
        }

        document.getElementById('userEmail').textContent = email;
        document.getElementById('userEmailId').value = email;
        // Auto-focus first input
        document.getElementById('code1').focus();

        // Handle input navigation
        const inputs = document.querySelectorAll('.code-input');
        inputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                if (e.target.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });

            // Only allow numbers
            input.addEventListener('keypress', (e) => {
                if (!/[0-9]/.test(e.key)) {
                    e.preventDefault();
                }
            });
        });

        // Handle form submission
        document.getElementById('verifyForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const code = Array.from(inputs).map(input => input.value).join('');

            if (code.length !== 6) {
                showMessage('Please enter all 6 digits', 'error');
                return;
            }

            const verifyBtn = document.getElementById('verifyBtn');
            verifyBtn.disabled = true;
            verifyBtn.textContent = 'Verifying...';
            showMessage('Verifying code...', 'info');

            try {
                const response = await fetch('/api/v1/verify-code', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        email: email,
                        code: code
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    localStorage.setItem('api_token', data.data.token);
                    localStorage.setItem('user_data', JSON.stringify(data.data.user));
                    localStorage.removeItem('verify_email');

                    showMessage('Email verified successfully! Redirecting...', 'success');
                    
                    //submit form
                    document.getElementById('verifyForm').submit();

                    // setTimeout(() => {
                    //     window.location.href = '/dashboard';
                    // }, 1500);

                } else {
                    showMessage(data.message || 'Invalid verification code', 'error');
                    verifyBtn.disabled = false;
                    verifyBtn.textContent = 'Verification';
                    // Clear inputs
                    inputs.forEach(input => input.value = '');
                    inputs[0].focus();
                }
            } catch (error) {
                showMessage('Connection error. Please try again.', 'error');
                verifyBtn.disabled = false;
                verifyBtn.textContent = 'Verification';
            }
        });

        // Handle resend code
        document.getElementById('resendLink').addEventListener('click', async (e) => {
            e.preventDefault();

            showMessage('Sending new code...', 'info');

            try {
                const response = await fetch('/api/v1/resend-verification-code', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        email: email
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    showMessage('New verification code sent to your email!', 'success');
                } else {
                    showMessage(data.message || 'Failed to resend code', 'error');
                }
            } catch (error) {
                showMessage('Connection error. Please try again.', 'error');
            }
        });

        function showMessage(text, type) {
            const messageDiv = document.getElementById('message');
            messageDiv.innerHTML = `<div class="message ${type}">${text}</div>`;
        }
    </script>
</body>
</html>
