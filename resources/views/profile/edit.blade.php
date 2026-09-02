@extends('common.layout')

@section('title', 'Edit Profile')
@section('page-title', 'Edit Profile')

@section('styles')
<style>
        

        .profile-container {
            max-width: none;
            margin: 40px auto;
            padding: 0px 20px;
        }

        .profile-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        .profile-header {
            background: #2c3e50;
            color: white;
            padding: 40px 30px;
            position: relative;
        }

        .profile-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: #a8b456;
        }

        .profile-header h2 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .profile-header p {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.8);
            margin: 0;
        }

        .profile-photo-wrapper {
            text-align: center;
            padding: 40px 0 30px;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .profile-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .profile-photo-placeholder {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: #2c3e50;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: white;
            font-weight: 700;
            border: 4px solid #a8b456;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .photo-actions {
                position: relative;
                left: 3px;
                bottom: 57px;
                background: #fff;
                padding: 2px;
                box-shadow: 0 0 5px #cfcbcb;
                border-radius: 100%;
                cursor: pointer;
                height: 29px;
                width: 29px;
        }

        .btn-upload {
            padding: 10px 24px;
            background: #a8b456;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-right: 10px;
        }

        .btn-upload:hover {
            background: #96a048;
        }

        .btn-remove {
            padding: 10px 24px;
            background: white;
            color: #dc3545;
            border: 2px solid #dc3545;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-remove:hover {
            background: #dc3545;
            color: white;
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
            padding: 12px 16px;
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

        .form-control:disabled {
            background: #f8f9fa;
            color: #6c757d;
            cursor: not-allowed;
        }

        .text-muted {
            font-size: 12px;
            color: #6c757d;
            margin-top: 4px;
        }

        .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: 13px;
            margin-top: 6px;
        }

        .form-control.is-invalid {
            border-color: #dc3545;
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
            display: none;
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #a8b456;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        #profileForm{
            margin-top: 29px;
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

        .btn-primary {
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

        .btn-primary:hover {
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

        .btn-warning {
            padding: 12px 28px;
            background: #a8b456;
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

        .btn-warning:hover {
            background: #96a048;
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

        .ms-auto {
            margin-left: auto;
        }

        .row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .col-12 {
            grid-column: 1 / -1;
        }

        .mb-3 {
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .row {
                grid-template-columns: 1fr;
            }

            .profile-header {
                padding: 30px 20px;
            }

            .form-section {
                padding: 30px 20px;
            }

            .d-flex {
                flex-direction: column;
            }

            .ms-auto {
                margin-left: 0;
            }
        }
    </style>
@endsection    
@section('content')
    <div class="profile-card">
        <!-- <div class="profile-header">
            <h2>Edit Profile</h2>
            <p>Update your account information</p>
        </div> -->

        <div class="form-section">
            <!-- Alert Messages -->
            <div id="alertContainer"></div>
            <!-- Profile Photo Section -->
            <div class="profile-photo-wrapper">
                <div class="photo-class">
                    <img id="profilePhotoPreview" class="profile-photo" style="display: none;" alt="Profile Photo">
                    <div id="profilePhotoPlaceholder" class="profile-photo-placeholder">
                        <span id="userInitials">U</span>
                    </div>
                </div>
                <div class="photo-actions">
                    <input type="file" id="profilePhotoInput" accept="image/*" style="display: none;">
                    <!-- <button type="button" class="btn-upload" onclick="document.getElementById('profilePhotoInput').click()">
                        📷 Change Photo
                    </button> -->
                    <div class="icon-class">
                        <i class="fa-solid fa-pen-to-square" onclick="document.getElementById('profilePhotoInput').click()"></i>
                        <button type="button" class="btn-remove" id="removePhotoBtn" style="display: none;">
                            🗑️ Remove
                        </button>
                    </div>
                </div>
            </div>

            

            <!-- Loading Spinner -->
            <!-- <div class="loading-spinner" id="loadingSpinner">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p>Updating profile...</p>
            </div> -->

            <!-- Profile Form -->
            <form id="profileForm">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Full Name <span class="astrik">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter full name" required>
                        <div class="invalid-feedback" id="name-error"></div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" disabled placeholder="Email cannot be changed">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control numbers-only" id="profile_phone" name="phone" placeholder="+1 (555) 123-4567">
                        <div class="invalid-feedback" id="phone-error"></div>
                    </div>

                    @if(auth()->user()->isHospital())
                        <div class="col-md-6 mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" name="address">
                            <div class="invalid-feedback" id="address-error"></div>
                        </div>
                    @else
                    <div class="col-md-6 mb-3">
                        <label for="dob" class="form-label">Date of Birth</label>
                        <div class="location-input-wrapper">
                            <i class="fas fa-calendar-alt location-icon"></i>
                            <input type="date" class="form-control" id="dob" name="dob" placeholder="YYYY-MM-DD">
                            <div class="invalid-feedback" id="dob-error"></div>
                        </div>
                    </div>

                    <div class="col-12 mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3" placeholder="Enter your full address"></textarea>
                        <div class="invalid-feedback" id="address-error"></div>
                    </div>
                    @endif
                    <div class="col-12 mb-3">
                        <label for="role" class="form-label">Role</label>
                        <input type="text" class="form-control" id="role" name="role" disabled placeholder="User role">
                    </div>
                </div>

                <div class="gap-2 mt-4">
                    <a href="/admin/profile/change-password" class="btn btn-primary" style="float:left;">
                        🔑 Change Password
                    </a>
                    <button type="submit" class="btn-submit" style="float: right;">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection    
@section('scripts')    
    <script src="/assets/js/bootstrap.bundle.js"></script>
    <script>
        const API_BASE = '/api/v1';
        let currentUser = null;
        let selectedFile = null;
        const token = "{{ session('web_token') }}";

        // Get auth token
        function getAuthToken() {
            return localStorage.getItem('api_token');
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

        // Load current user data
        async function loadUserData() {
            try {
                const response = await fetch(`${API_BASE}/me`, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (result.success) {
                    currentUser = result.data;
                    populateForm(currentUser);
                } else {
                    showAlert('Failed to load user data', 'danger');
                }
            } catch (error) {
                console.error('Error loading user data:', error);
                showAlert('Error loading user data', 'danger');
            }
        }

        // Populate form with user data
        function populateForm(user) {
            console.log('Populating form with user:', user);
            console.log('Profile photo path:', user.profile_photo);

            document.getElementById('name').value = user.name || '';
            document.getElementById('email').value = user.email || '';
            document.getElementById('profile_phone').value = user.role === 'hospital' ? user.hospital?.phone ?? '' : user.phone ?? '';
            const dob = document.getElementById('dob');
            if (dob) {
                dob.value = user.dob || '';
            }
            document.getElementById('address').value = user.address || '';
            document.getElementById('role').value = user.role ? user.role.toUpperCase() : '';

            // Set user initials
            const initials = user.name ? user.name.split(' ').map(n => n[0]).join('').toUpperCase() : 'U';
            document.getElementById('userInitials').textContent = initials;

            // Load profile photo if exists
            if (user.profile_photo) {
                const photoPreview = document.getElementById('profilePhotoPreview');
                const photoPlaceholder = document.getElementById('profilePhotoPlaceholder');
                const photoUrl = `/storage/${user.profile_photo}`;
                console.log('Loading photo from:', photoUrl);
                photoPreview.src = photoUrl;
                photoPreview.style.display = 'block';
                photoPlaceholder.style.display = 'none';
                // document.getElementById('removePhotoBtn').style.display = 'inline-block';
            } else {
                console.log('No profile photo found');
            }
        }

        // Handle profile photo selection
        document.getElementById('profilePhotoInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    showAlert('Photo size must be less than 2MB', 'danger');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    const photoPreview = document.getElementById('profilePhotoPreview');
                    const photoPlaceholder = document.getElementById('profilePhotoPlaceholder');
                    photoPreview.src = e.target.result;
                    photoPreview.style.display = 'block';
                    photoPlaceholder.style.display = 'none';
                    // document.getElementById('removePhotoBtn').style.display = 'inline-block';
                };
                reader.readAsDataURL(file);
                selectedFile = file;
            }
            setTimeout(() => {
                $('#profileForm button[type="submit"]').trigger('click');
            }, 500);
            
        });

        // Handle form submission
        document.getElementById('profileForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            // return false;
            clearFieldErrors();

            // const loadingSpinner = document.getElementById('loadingSpinner');
            // loadingSpinner.style.display = 'block';
            $('.loading-spinner').show();
            if (document.querySelector('.just-validate-error-label')) {
                hide_load_spinner();
                return false;
            }

            const formData = new FormData();
            formData.append('_method', 'PUT');
            formData.append('name', document.getElementById('name').value);
            formData.append('phone', document.getElementById('profile_phone').value);
            const dob = document.getElementById('dob');
            if (dob) {
                formData.append('dob', dob.value || '');
            }
            formData.append('address', document.getElementById('address').value);

            if (selectedFile) {
                formData.append('profile_photo', selectedFile);
            }

            try {
                const response = await fetch(`${API_BASE}/profile`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const result = await response.json();
                console.log('Profile update response:', result);

                // loadingSpinner.style.display = 'none';
                $('.loading-spinner').hide();

                if (result.success) {
                    showAlert(result.message || 'Profile updated successfully!', 'success');
                    currentUser = result.data;
                    selectedFile = null;

                    // Reload the profile data to show updated photo
                    console.log('Updated user data:', result.data);
                    populateForm(result.data);

                    // Update localStorage with new user data
                    localStorage.setItem('user_data', JSON.stringify(result.data));

                    // Clear the file input
                    document.getElementById('profilePhotoInput').value = '';
                    $('html, body').animate({ scrollTop: 0 }, 800);
                } else {
                    if (result.errors) {
                        Object.keys(result.errors).forEach(field => {
                            showFieldError(field, result.errors[field][0]);
                        });
                    }
                    showAlert(result.message || 'Failed to update profile', 'danger');
                }
            } catch (error) {
                // loadingSpinner.style.display = 'none';
                $('.loading-spinner').hide();
                console.error('Error updating profile:', error);
                showAlert('Error updating profile. Please try again.', 'danger');
            }
        });

        // Load user data on page load
        document.addEventListener('DOMContentLoaded', () => {
            if (!getAuthToken()) {
                //window.location.href = '/login';
                //return;
            }
            loadUserData();
        });
    </script>
@endsection
