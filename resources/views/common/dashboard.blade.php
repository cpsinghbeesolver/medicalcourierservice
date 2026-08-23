<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Courier - Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
        }

        .header {
            background: #fff;
            padding: 20px 40px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            color: #333;
            font-size: 24px;
        }

        .header .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .header .user-name {
            color: #666;
            font-size: 14px;
        }

        .btn-logout {
            padding: 8px 20px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-logout:hover {
            background: #c82333;
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .welcome-card {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .welcome-card h2 {
            color: #333;
            margin-bottom: 10px;
        }

        .welcome-card p {
            color: #666;
            line-height: 1.6;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .card h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 18px;
        }

        .card p {
            color: #666;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .card .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
        }

        .card .btn:hover {
            background: #45a049;
        }

        .token-card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .token-card h3 {
            color: #333;
            margin-bottom: 15px;
        }

        .token-display {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            word-break: break-all;
            font-family: monospace;
            font-size: 12px;
            margin-bottom: 15px;
        }

        .btn-copy {
            padding: 8px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-copy:hover {
            background: #0056b3;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .stat-card .number {
            font-size: 32px;
            font-weight: bold;
            color: #4CAF50;
            margin-bottom: 5px;
        }

        .stat-card .label {
            color: #666;
            font-size: 14px;
        }

        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .alert.warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }

        .alert.info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Medical Courier Dashboard</h1>
        <div class="user-info">
            <span class="user-name" id="userName">Loading...</span>
            <button class="btn-logout" onclick="logout()">Logout</button>
        </div>
    </div>

    <div class="container">
        <div class="welcome-card">
            <h2 id="welcomeMessage">Welcome!</h2>
            <p>You are successfully logged in to the Medical Courier API system.</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="number">47</div>
                <div class="label">Total Endpoints</div>
            </div>
            <div class="stat-card">
                <div class="number">2</div>
                <div class="label">API Systems</div>
            </div>
            <div class="stat-card">
                <div class="number">16</div>
                <div class="label">Mobile Endpoints</div>
            </div>
            <div class="stat-card">
                <div class="number">31</div>
                <div class="label">Web Endpoints</div>
            </div>
        </div>

        <div class="cards-grid">
            <div class="card">
                <h3>Swagger Documentation</h3>
                <p>Interactive API documentation for testing all endpoints</p>
                <a href="/api/documentation" target="_blank" class="btn">Open Swagger</a>
            </div>

            <div class="card">
                <h3>API Overview</h3>
                <p>Complete overview of all available APIs and features</p>
                <a href="/api-docs" target="_blank" class="btn">View Overview</a>
            </div>

            <div class="card">
                <h3>Web API</h3>
                <p>Admin and dispatcher management endpoints</p>
                <a href="/api/documentation#/Authentication" target="_blank" class="btn">View Endpoints</a>
            </div>

            <div class="card">
                <h3>Mobile API</h3>
                <p>Driver mobile application endpoints</p>
                <a href="/api/documentation#/Mobile%20-%20Authentication" target="_blank" class="btn">View Endpoints</a>
            </div>
        </div>

        <div class="token-card">
            <h3>Your API Token</h3>
            <div class="token-display" id="tokenDisplay">Loading...</div>
            <button class="btn-copy" onclick="copyToken()">Copy Token</button>
        </div>

        <div class="alert info" style="margin-top: 30px;">
            <strong>How to use your token:</strong><br>
            1. Copy the token above<br>
            2. Open Swagger documentation<br>
            3. Click the "Authorize" button<br>
            4. Paste your token (without "Bearer" prefix)<br>
            5. Test any endpoint!
        </div>
    </div>

    <script>
        // Check if user is logged in
        // const token = localStorage.getItem('api_token');
        const token = "{{ session('web_token') }}";
        const userData = localStorage.getItem('user_data');

        if (!token || !userData) {
            window.location.href = '/';
        }

        // Display user info
        try {
            const user = JSON.parse(userData);
            document.getElementById('userName').textContent = user.name || user.email;
            document.getElementById('welcomeMessage').textContent = `Welcome, ${user.name}!`;
            document.getElementById('tokenDisplay').textContent = token;
        } catch (e) {
            console.error('Error parsing user data:', e);
            document.getElementById('userName').textContent = 'User';
            document.getElementById('tokenDisplay').textContent = token || 'No token found';
        }

        // Logout function
        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                // Call logout API
                fetch('/api/v1/logout', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                }).finally(() => {
                    // Clear local storage
                    localStorage.removeItem('api_token');
                    localStorage.removeItem('user_data');
                    // Redirect to login
                    window.location.href = '/';
                });
            }
        }

        // Copy token function
        function copyToken() {
            const tokenText = document.getElementById('tokenDisplay').textContent;
            navigator.clipboard.writeText(tokenText).then(() => {
                const btn = document.querySelector('.btn-copy');
                const originalText = btn.textContent;
                btn.textContent = 'Copied!';
                btn.style.background = '#28a745';
                setTimeout(() => {
                    btn.textContent = originalText;
                    btn.style.background = '#007bff';
                }, 2000);
            }).catch(err => {
                alert('Failed to copy token');
            });
        }
    </script>
</body>
</html>
