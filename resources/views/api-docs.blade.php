<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Courier - API Documentation</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            color: #333;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }

        .header h1 {
            font-size: 42px;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 18px;
            opacity: 0.9;
        }

        .container {
            max-width: 1200px;
            margin: -30px auto 40px;
            padding: 0 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .stat-card h2 {
            font-size: 48px;
            color: #667eea;
            margin-bottom: 10px;
        }

        .stat-card p {
            color: #666;
            font-size: 16px;
        }

        .api-section {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .api-section h2 {
            font-size: 28px;
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .api-section h2:before {
            content: "";
            display: inline-block;
            width: 4px;
            height: 28px;
            background: #667eea;
            margin-right: 15px;
        }

        .endpoint-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .endpoint {
            padding: 15px 20px;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 4px solid #667eea;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .endpoint .method {
            font-weight: bold;
            padding: 4px 12px;
            border-radius: 5px;
            font-size: 12px;
            margin-right: 10px;
        }

        .method.get { background: #28a745; color: white; }
        .method.post { background: #007bff; color: white; }
        .method.put { background: #ffc107; color: #333; }
        .method.delete { background: #dc3545; color: white; }

        .endpoint .path {
            flex: 1;
            font-family: 'Courier New', monospace;
            font-size: 13px;
        }

        .btn {
            display: inline-block;
            padding: 15px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: transform 0.2s;
            margin: 10px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary {
            background: #6c757d;
        }

        .action-buttons {
            text-align: center;
            padding: 40px 0;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .feature-card {
            padding: 25px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .feature-card h3 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 18px;
        }

        .feature-card ul {
            list-style: none;
            padding-left: 0;
        }

        .feature-card li {
            padding: 5px 0;
            color: #666;
            font-size: 14px;
        }

        .feature-card li:before {
            content: "✓ ";
            color: #28a745;
            font-weight: bold;
        }

        .credentials {
            background: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 10px;
            padding: 25px;
            margin: 30px 0;
        }

        .credentials h3 {
            color: #856404;
            margin-bottom: 15px;
        }

        .credentials .cred-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .credentials code {
            background: #f8f9fa;
            padding: 4px 8px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            color: #e83e8c;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🚑 Medical Courier API</h1>
        <p>Complete REST API Documentation - Web & Mobile</p>
    </div>

    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <h2>47</h2>
                <p>Total Endpoints</p>
            </div>
            <div class="stat-card">
                <h2>2</h2>
                <p>API Systems</p>
            </div>
            <div class="stat-card">
                <h2>8</h2>
                <p>Controllers</p>
            </div>
            <div class="stat-card">
                <h2>100%</h2>
                <p>Complete</p>
            </div>
        </div>

        <div class="action-buttons">
            <a href="/api/documentation" class="btn" target="_blank">📚 Open Swagger UI</a>
            <a href="/" class="btn btn-secondary">🔐 Back to Login</a>
        </div>

        <div class="api-section">
            <h2>🌐 Web/Admin API</h2>
            <p><strong>Base URL:</strong> <code>http://localhost:8000/api/v1/</code></p>
            <p><strong>Purpose:</strong> Admin dashboard and management system</p>
            <p><strong>Total Endpoints:</strong> 31</p>

            <div class="features">
                <div class="feature-card">
                    <h3>Authentication (7)</h3>
                    <ul>
                        <li>User registration</li>
                        <li>Login/Logout</li>
                        <li>Password reset</li>
                        <li>Profile management</li>
                    </ul>
                </div>
                <div class="feature-card">
                    <h3>Deliveries (11)</h3>
                    <ul>
                        <li>Full CRUD operations</li>
                        <li>Assign drivers</li>
                        <li>Track deliveries</li>
                        <li>Delivery actions</li>
                    </ul>
                </div>
                <div class="feature-card">
                    <h3>Driver Management (5)</h3>
                    <ul>
                        <li>Driver profiles CRUD</li>
                        <li>Statistics</li>
                        <li>Location tracking</li>
                        <li>Availability</li>
                    </ul>
                </div>
                <div class="feature-card">
                    <h3>Activity Logs (2)</h3>
                    <ul>
                        <li>System activity logs</li>
                        <li>User activity logs</li>
                        <li>Audit trails</li>
                    </ul>
                </div>
            </div>

            <h3 style="margin-top: 30px; margin-bottom: 15px;">Key Endpoints:</h3>
            <div class="endpoint-grid">
                <div class="endpoint">
                    <span class="method post">POST</span>
                    <span class="path">/api/v1/login</span>
                </div>
                <div class="endpoint">
                    <span class="method get">GET</span>
                    <span class="path">/api/v1/deliveries</span>
                </div>
                <div class="endpoint">
                    <span class="method post">POST</span>
                    <span class="path">/api/v1/deliveries</span>
                </div>
                <div class="endpoint">
                    <span class="method post">POST</span>
                    <span class="path">/api/v1/deliveries/{id}/assign</span>
                </div>
                <div class="endpoint">
                    <span class="method get">GET</span>
                    <span class="path">/api/v1/driver-profiles</span>
                </div>
                <div class="endpoint">
                    <span class="method get">GET</span>
                    <span class="path">/api/v1/activity-logs</span>
                </div>
            </div>
        </div>

        <div class="api-section">
            <h2>📱 Mobile API</h2>
            <p><strong>Base URL:</strong> <code>http://172.16.123.158:8000/api/mobile/v1/</code></p>
            <p><strong>Purpose:</strong> Driver mobile application</p>
            <p><strong>Total Endpoints:</strong> 16</p>

            <div class="features">
                <div class="feature-card">
                    <h3>Authentication (4)</h3>
                    <ul>
                        <li>Mobile login</li>
                        <li>Profile management</li>
                        <li>Password change</li>
                        <li>Device tracking</li>
                    </ul>
                </div>
                <div class="feature-card">
                    <h3>Deliveries (7)</h3>
                    <ul>
                        <li>View assigned deliveries</li>
                        <li>Pagination & filtering</li>
                        <li>Start/Pickup/Complete</li>
                        <li>Digital signatures</li>
                    </ul>
                </div>
                <div class="feature-card">
                    <h3>Driver Features (3)</h3>
                    <ul>
                        <li>GPS location updates</li>
                        <li>Availability status</li>
                        <li>Performance statistics</li>
                        <li>Daily metrics</li>
                    </ul>
                </div>
                <div class="feature-card">
                    <h3>Advanced Features</h3>
                    <ul>
                        <li>Dynamic pagination</li>
                        <li>Photo proof upload</li>
                        <li>Real-time tracking</li>
                        <li>Activity logging</li>
                    </ul>
                </div>
            </div>

            <h3 style="margin-top: 30px; margin-bottom: 15px;">Key Endpoints:</h3>
            <div class="endpoint-grid">
                <div class="endpoint">
                    <span class="method post">POST</span>
                    <span class="path">/api/mobile/v1/login</span>
                </div>
                <div class="endpoint">
                    <span class="method get">GET</span>
                    <span class="path">/api/mobile/v1/deliveries</span>
                </div>
                <div class="endpoint">
                    <span class="method post">POST</span>
                    <span class="path">/api/mobile/v1/deliveries/{id}/start</span>
                </div>
                <div class="endpoint">
                    <span class="method post">POST</span>
                    <span class="path">/api/mobile/v1/deliveries/{id}/pickup</span>
                </div>
                <div class="endpoint">
                    <span class="method post">POST</span>
                    <span class="path">/api/mobile/v1/driver/location</span>
                </div>
                <div class="endpoint">
                    <span class="method get">GET</span>
                    <span class="path">/api/mobile/v1/driver/statistics</span>
                </div>
            </div>
        </div>

        <div class="credentials">
            <h3>🔑 Test Credentials</h3>
            <div class="cred-item">
                <strong>Admin (Web API):</strong><br>
                Email: <code>admin@medcourier.com</code><br>
                Password: <code>password123</code>
            </div>
            <div class="cred-item">
                <strong>Dispatcher (Web API):</strong><br>
                Email: <code>dispatcher@medcourier.com</code><br>
                Password: <code>password123</code>
            </div>
            <div class="cred-item">
                <strong>Driver (Mobile API):</strong><br>
                Email: <code>john.driver@medcourier.com</code><br>
                Password: <code>password123</code>
            </div>
        </div>

        <div class="api-section">
            <h2>🔐 Security</h2>
            <p><strong>Authentication:</strong> Laravel Sanctum (Bearer Token)</p>
            <p><strong>Authorization:</strong> Role-Based Access Control (RBAC)</p>

            <h3 style="margin-top: 20px; margin-bottom: 10px;">How to Use:</h3>
            <ol style="line-height: 2;">
                <li>Login via <code>/api/v1/login</code> or <code>/api/mobile/v1/login</code></li>
                <li>Copy the token from the response</li>
                <li>Include in request header: <code>Authorization: Bearer {token}</code></li>
                <li>All protected endpoints require this header</li>
            </ol>
        </div>

        <div class="api-section">
            <h2>📖 Documentation Links</h2>
            <div class="endpoint-grid">
                <a href="/api/documentation" target="_blank" class="endpoint" style="text-decoration: none; cursor: pointer;">
                    <span style="font-size: 24px;">📚</span>
                    <span class="path">Swagger Interactive Docs</span>
                </a>
                <a href="/api/documentation#/Mobile%20-%20Authentication" target="_blank" class="endpoint" style="text-decoration: none; cursor: pointer;">
                    <span style="font-size: 24px;">📱</span>
                    <span class="path">Mobile API Docs</span>
                </a>
                <a href="/api/documentation#/Authentication" target="_blank" class="endpoint" style="text-decoration: none; cursor: pointer;">
                    <span style="font-size: 24px;">🌐</span>
                    <span class="path">Web API Docs</span>
                </a>
            </div>
        </div>

        <div class="action-buttons">
            <a href="/api/documentation" class="btn">📚 Open Swagger Documentation</a>
            <a href="/" class="btn btn-secondary">🔐 Back to Login</a>
        </div>
    </div>

    <div style="text-align: center; padding: 40px; color: #666;">
        <p>Medical Courier Services API v1.0.0</p>
        <p>© 2026 - All Rights Reserved</p>
    </div>
</body>
</html>
