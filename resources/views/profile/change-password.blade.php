@extends('common.layout')

@section('title', 'Change Password')
@section('page-title', 'Change Password')

@section('styles')
<style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            color: #2c3e50;
        }

        .password-container {
            max-width: 700px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .password-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        .password-header {
            background: #2c3e50;
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }

        .password-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: #a8b456;
        }

        .password-header .icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .password-header h2 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .password-header p {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.8);
            margin: 0;
        }

        .form-section {
            padding: 40px;
        }

        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            font-size: 14px;
            display: block;
        }

        .form-control {
            width: 100%;
            padding: 12px 45px 12px 16px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            font-size: 14px;
            color: #2c3e50;
            transition: all 0.3s;
            font-family: 'Inter', sans-serif;
        }

        .form-control:focus {
            outline: none;
            border-color: #a8b456;
            box-shadow: 0 0 0 3px rgba(168, 180, 86, 0.1);
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        .input-wrapper {
            position: relative;
            margin-bottom: 20px;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 40px;
            cursor: pointer;
            color: #6c757d;
            font-size: 18px;
            transition: color 0.3s;
        }

        .password-toggle:hover {
            color: #2c3e50;
        }

        .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: 13px;
            margin-top: 6px;
        }

        .password-requirements {
            background: #f8f9fa;
            border-left: 4px solid #a8b456;
            padding: 20px;
            margin: 25px 0;
            border-radius: 8px;
        }

        .password-requirements strong {
            color: #2c3e50;
            font-size: 15px;
        }

        .password-requirements ul {
            margin: 15px 0 0;
            padding-left: 25px;
            list-style: none;
        }

        .password-requirements li {
            margin: 10px 0;
            color: #6c757d;
            font-size: 14px;
            padding-left: 5px;
        }

        .password-requirements li.valid {
            color: #28a745;
            font-weight: 600;
        }

        .password-requirements li.invalid {
            color: #dc3545;
            font-weight: 600;
        }

        .security-tip {
            background: #fff8e6;
            border-left: 4px solid #ffc107;
            padding: 20px;
            margin-top: 25px;
            border-radius: 8px;
        }

        .security-tip strong {
            color: #856404;
            font-size: 15px;
        }

        .security-tip p {
            margin: 8px 0 0;
            color: #856404;
            font-size: 14px;
            line-height: 1.6;
        }

        .alert {
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .alert-success {
            background: #d1e7dd;
            color: #0f5132;
            border: 1px solid #badbcc;
        }

        .alert-danger {
            background: #f8d7da;
            color: #842029;
            border: 1px solid #f5c2c7;
        }

        .btn-close {
            background: transparent;
            border: none;
            font-size: 20px;
            cursor: pointer;
            opacity: 0.5;
            transition: opacity 0.3s;
        }

        .btn-close:hover {
            opacity: 1;
        }

        /* .loading-spinner {
            display: none;
            text-align: center;
            padding: 30px;
        } */

        .spinner-border {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #a8b456;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* .loading-spinner p {
            margin-top: 15px;
            color: #6c757d;
            font-size: 14px;
        } */

        .btn-danger {
            padding: 12px 28px;
            background: #2c3e50;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-danger:hover {
            background: #1a252f;
        }

        .btn-secondary {
            padding: 12px 28px;
            background: white;
            color: #2c3e50;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-secondary:hover {
            background: #f8f9fa;
            border-color: #2c3e50;
        }

        .d-flex {
            display: flex;
        }

        .gap-2 {
            gap: 12px;
        }

        .mt-4 {
            margin-top: 24px;
        }

        .mb-3 {
            margin-bottom: 20px;
        }

        .password-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .password-wrapper input {
            flex: 1;
            padding-right: 35px;
        }

        .eye-icon {
            position: absolute;
            right: 9px;
            top: 39px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            height: 18px;
            color: #7f8c8d;
            transition: color 0.3s ease;
        }

        .eye-icon:hover {
            color: #2c3e50;
        }

        @media (max-width: 768px) {
            .password-header {
                padding: 30px 20px;
            }

            .form-section {
                padding: 30px 20px;
            }

            .d-flex {
                flex-direction: column;
            }
        }
    </style>
@endsection 
@section('content')
    <div class="password-card">
        <!-- <div class="password-header">
            <div class="icon">🔒</div>
            <h2>Change Password</h2>
            <p>Keep your account secure</p>
        </div> -->

        <div class="form-section">
            <!-- Alert Messages -->
            <div id="alertContainer"></div>

            <!-- Loading Spinner -->
            <!-- <div class="loading-spinner" id="loadingSpinner">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p>Changing password...</p>
            </div> -->

            <!-- Change Password Form -->
            <form id="changePasswordForm">
                <div class="input-wrapper">
                    <label for="current_password" class="form-label">Current Password *</label>
                    <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Current password" required>
                    <svg class="eye-icon" id="togglePasswordNew" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                    <div class="invalid-feedback" id="current_password-error"></div>
                </div>

                <div class="input-wrapper">
                    <label for="new_password" class="form-label">New Password *</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" placeholder="New password" required>
                    <svg class="eye-icon" id="togglePasswordNewA" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                    <div class="invalid-feedback" id="new_password-error"></div>
                </div>

                <div class="input-wrapper">
                    <label for="new_password_confirmation" class="form-label">Confirm New Password *</label>
                    <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" placeholder="Confirm new password" required>
                    <svg class="eye-icon" id="togglePasswordNewConfirm" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                    <div class="invalid-feedback" id="new_password_confirmation-error"></div>
                </div>

                <div class="password-requirements">
                    <strong>Password Requirements:</strong>
                    <ul id="passwordRequirements">
                        <li id="req-length">At least 8 characters</li>
                        <li id="req-different">Different from current password</li>
                        <li id="req-match">Passwords match</li>
                    </ul>
                </div>

                <div class="security-tip">
                    <strong>💡 Security Tip</strong>
                    <p>Use a strong, unique password that includes a mix of uppercase and lowercase letters, numbers, and special characters. Avoid reusing passwords from other accounts.</p>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn-danger">
                        🔑 Change Password
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <!-- <script src="/assets/js/bootstrap.bundle.js"></script> -->
    <script>
        const user_role_id = $('#user_role_id').val();
        const API_BASE = '/api/v1';

        // Get auth token
        function getAuthToken() {
            // return localStorage.getItem('api_token');
            return "{{ session('web_token') }}";
        }

        // Toggle password visibility
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(`${fieldId}-icon`);

            if (field.type === 'password') {
                field.type = 'text';
                icon.textContent = '🙈';
            } else {
                field.type = 'password';
                icon.textContent = '👁️';
            }
        }

        // Show alert
        function showAlert(message, type = 'success') {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            document.getElementById('alertContainer').innerHTML = alertHtml;

            // Auto dismiss after 5 seconds
            setTimeout(() => {
                const alert = document.querySelector('.alert');
                if (alert) {
                    alert.classList.remove('show');
                    setTimeout(() => alert.remove(), 150);
                }
            }, 5000);
        }

        // Show field error
        function showFieldError(fieldName, message) {
            const field = document.getElementById(fieldName);
            const errorDiv = document.getElementById(`${fieldName}-error`);
            if (field && errorDiv) {
                field.classList.add('is-invalid');
                errorDiv.textContent = message;
            }
        }

        // Clear field errors
        function clearFieldErrors() {
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        }

        // Validate password requirements
        function validatePassword() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('new_password_confirmation').value;
            const currentPassword = document.getElementById('current_password').value;

            // Check length
            const lengthReq = document.getElementById('req-length');
            if (newPassword.length >= 8) {
                lengthReq.classList.add('valid');
                lengthReq.classList.remove('invalid');
                lengthReq.innerHTML = '✓ At least 8 characters';
            } else {
                lengthReq.classList.add('invalid');
                lengthReq.classList.remove('valid');
                lengthReq.innerHTML = '✗ At least 8 characters';
            }

            // Check if different from current
            const differentReq = document.getElementById('req-different');
            if (currentPassword && newPassword && newPassword !== currentPassword) {
                differentReq.classList.add('valid');
                differentReq.classList.remove('invalid');
                differentReq.innerHTML = '✓ Different from current password';
            } else if (newPassword && currentPassword && newPassword === currentPassword) {
                differentReq.classList.add('invalid');
                differentReq.classList.remove('valid');
                differentReq.innerHTML = '✗ Different from current password';
            } else {
                differentReq.classList.remove('valid', 'invalid');
                differentReq.innerHTML = 'Different from current password';
            }

            // Check if passwords match
            const matchReq = document.getElementById('req-match');
            if (newPassword && confirmPassword && newPassword === confirmPassword) {
                matchReq.classList.add('valid');
                matchReq.classList.remove('invalid');
                matchReq.innerHTML = '✓ Passwords match';
            } else if (confirmPassword && newPassword !== confirmPassword) {
                matchReq.classList.add('invalid');
                matchReq.classList.remove('valid');
                matchReq.innerHTML = '✗ Passwords match';
            } else {
                matchReq.classList.remove('valid', 'invalid');
                matchReq.innerHTML = 'Passwords match';
            }
        }

        // Add event listeners for real-time validation
        document.getElementById('new_password').addEventListener('input', validatePassword);
        document.getElementById('new_password_confirmation').addEventListener('input', validatePassword);
        document.getElementById('current_password').addEventListener('input', validatePassword);

        // Handle form submission
        document.getElementById('changePasswordForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            clearFieldErrors();

            // const loadingSpinner = document.getElementById('loadingSpinner');
            // loadingSpinner.style.display = 'block';
            $('.loading-spinner').show();

            const formData = {
                current_password: document.getElementById('current_password').value,
                new_password: document.getElementById('new_password').value,
                new_password_confirmation: document.getElementById('new_password_confirmation').value
            };

            try {
                const response = await fetch(`${API_BASE}/change-password`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${getAuthToken()}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const result = await response.json();

                // loadingSpinner.style.display = 'none';
                $('.loading-spinner').hide();

                if (result.success) {
                    showAlert(result.message || 'Password changed successfully! You will receive a confirmation email.', 'success');

                    // Clear form
                    document.getElementById('changePasswordForm').reset();

                    // Reset validation indicators
                    document.querySelectorAll('#passwordRequirements li').forEach(li => {
                        li.classList.remove('valid', 'invalid');
                    });

                    // Redirect to profile after 3 seconds
                    setTimeout(() => {
                        $('.logout-btn').trigger('click');
                        // window.location.href = '/logout';   
                    }, 3000);
                } else {
                    if (result.errors) {
                        Object.keys(result.errors).forEach(field => {
                            showFieldError(field, result.errors[field][0]);
                            //showAlert(result.message || 'Incorrect password', 'danger');
                        });
                    }else if(result.message){
                        showAlert(result.message, 'danger');
                    }else{
                        showAlert('Incorrect password', 'danger');
                    }
                }
            } catch (error) {
                // loadingSpinner.style.display = 'none';
                $('.loading-spinner').hide();
                console.error('Error changing password:', error);
                showAlert('Error changing password. Please try again.', 'danger');
            }
        });

        // Check authentication on page load
        document.addEventListener('DOMContentLoaded', () => {
            if (!getAuthToken()) {
                //window.location.href = '/login';
            }
        });
    </script>
    <script>
        function setupPasswordToggle(passwordInputId, toggleIconId) {
            const passwordInput = document.getElementById(passwordInputId);
            const toggleIcon = document.getElementById(toggleIconId);

            toggleIcon.addEventListener('click', function() {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    // Show eye-slash icon
                    toggleIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
                } else {
                    passwordInput.type = 'password';
                    // Show eye icon
                    toggleIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
                }
            });
        }

        // Setup toggle for both password fields
        setupPasswordToggle('current_password', 'togglePasswordNew');
        setupPasswordToggle('new_password', 'togglePasswordNewA');
        setupPasswordToggle('new_password_confirmation', 'togglePasswordNewConfirm');
    </script>
@endsection
