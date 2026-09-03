<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="api-token" content="{{ session('web_token') }}">
    <title>@yield('title', 'Admin Dashboard') - {{ env('APP_NAME') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/libphonenumber-js@1.11.13/bundle/libphonenumber-max.js"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    <script src="{{ asset('assets/js/jquery-4.0.0.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>
    <link rel="icon" type="image/png" href="/assets/img/fav.png">
    <link rel="stylesheet" href="{{ asset('assets/css/flatpickr.min.css') }}">
    <script src="{{ asset('assets/js/flatpickr.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/just-validate@latest/dist/just-validate.production.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .sidebar-menu .job-management-menu {
            max-height: 0;
            overflow: hidden;
            opacity: 0;
            margin: 0;
            padding: 0 0 0 24px;
            list-style: none;
            transition: max-height 0.3s ease, opacity 0.3s ease, padding 0.3s ease;
        }

        .sidebar-menu .job-management-menu.open {
            max-height: 220px;
            opacity: 1;
            padding-top: 6px;
            padding-bottom: 6px;
        }

        .sidebar-menu .job-management-menu li {
            margin: 4px 0;
        }

        .sidebar-menu .job-management-menu a {
            display: block;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 13px;
            text-decoration: none;
        }


        .sidebar-menu .submenu-toggle {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .sidebar-menu .submenu-caret {
            font-size: 12px;
            transition: transform 0.3s ease;
        }

        .sidebar-menu .submenu-toggle.open .submenu-caret {
            transform: rotate(90deg);
        }

        .notification-wrapper {
            position: relative;
            display: inline-flex;
            align-items: center;
        }

        .notification-icon {
            position: relative;
            width: 44px;
            height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: #f8f9ff;
            color: #4f46e5;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .notification-icon:hover {
            background: #ececff;
        }

        .notification-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 10px;
            height: 10px;
            background: #ef4444;
            border: 2px solid #fff;
            border-radius: 50%;
        }

        .notification-dropdown {
            position: absolute;
            right: 97px;
            top: calc(100% + 10px);
            width: 320px;
            max-width: 100%;
            background: #fff;
            border: 1px solid rgba(15, 23, 42, 0.08);
            box-shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
            border-radius: 18px;
            z-index: 1100;
            display: none;
            top: 64px;
        }

        .notification-dropdown.show {
            display: block;
        }

        .notification-dropdown .notification-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 18px;
            border-bottom: 1px solid #e5e7eb;
        }

        .notification-dropdown .notification-header span {
            font-weight: 700;
            font-size: 14px;
        }

        .notification-dropdown .notification-header button {
            border: none;
            background: transparent;
            color: #4f46e5;
            font-size: 13px;
            cursor: pointer;
        }

        .notification-list {
            max-height: 300px;
            overflow-y: auto;
        }

        .notification-item {
            cursor: pointer;
            display: flex;
            gap: 12px;
            padding: 14px 18px;
            border-bottom: 1px solid #f3f4f6;
        }
        .notification-item:hover {
            background: #f8ffcf38 !important;
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-item.unread {
            background: #eef2ff;
        }

        .notification-item-icon {
            flex-shrink: 0;
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #eef2ff;
            color: #4f46e5;
        }

        .notification-item-text p {
            margin: 0 0 6px;
            font-size: 14px;
            line-height: 1.4;
            color: #111827;
        }

        .notification-item-text .notification-time {
            font-size: 12px;
            color: #6b7280;
        }
        .fa-xmark{
            cursor: pointer;
            display: none;
        }
    </style>
    <style>
        /* Ensure horizontal padding for form controls across the app */
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="tel"],
        input[type="url"],
        input[type="number"],
        input[type="date"],
        input[type="datetime-local"],
        textarea {
            padding-left: 8px !important;
            padding-right: 8px !important;
        }
    </style>
    <style>
        /* Calendar icon for date/time inputs (uses inline SVG) */
        input[type="date"],
        input[type="datetime-local"],
        input[id*="date"],
        input[name*="date"],
        input[id*="dob"],
        input[name*="dob"],
        input[id*="expiry"],
        input[name*="expiry"],
        input[id*="time"],
        input[name*="time"] {
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><rect x='3' y='4' width='18' height='18' rx='2' ry='2'></rect><line x='16' y='2' x='16' y='6'></line><line x='8' y='2' x='8' y='6'></line><line x='3' y='10' x='21' y='10'></line></svg>");
            background-repeat: no-repeat;
            background-position: right 10px center;
            padding-right: 36px !important;
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="layout">
        {{auth()->user()->isAdmin();}}
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-logo">
                <div class="">
                    @if(auth()->user()->isAdmin())
                        <a href="">
                    @elseif(auth()->user()->isDispatcher())
                        <a href="/company/dashboard">
                    @elseif(auth()->user()->isHospital())
                        <a href="/hospital/dashboard">
                    @endif
                    <img src="/assets/img/logo.png" class="logo-image" alt="{{ env('APP_NAME') }} Logo"></a>
                </div>
            </div>

            <ul class="sidebar-menu">
                
                
                @if(auth()->user()->isAdmin())
                    <li><a href="/admin/dashboard" class="{{ (request()->is('dashboard') && !request()->is('/admin/dashboard/*')) || request()->is('/admin/dashboard/create-job') || request()->is('/admin/dashboard/drivers/create') ? 'active' : '' }}">
                        <i class="fas fa-th-large"></i> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="/admin/dashboard/enquiries" class="{{ request()->is('/admin/dashboard/enquiries*') ? 'active' : '' }}">
                            <i class="fas fa-briefcase"></i> Enquiries
                        </a>
                    </li>
                    
                @endif
                
                @if(auth()->user()->isDispatcher())
                    <li><a href="/company/dashboard" class="{{ (request()->is('company/dashboard') && !request()->is('company/dashboard/*')) ? 'active' : '' }}">
                        <i class="fas fa-th-large"></i> Dashboard
                    </a></li>
                    <li>
                        <a href="#" class="submenu-toggle {{ request()->is('company/dashboard/deliveries*') || request()->is('company/dashboard/edit-deliveries*') || request()->is('company/dashboard/specimen-types*') || request()->is('company/dashboard/temperature-requirement*') || request()->is('company/dashboard/vehicle-requirement*') || request()->is('company/dashboard/create-job') ? 'active open' : '' }}">
                            <span><i class="fas fa-briefcase"></i> Job Management</span>
                            <i class="fas fa-chevron-right submenu-caret"></i>
                        </a>
                        <ul class="job-management-menu {{ request()->is('company/dashboard/deliveries*') || request()->is('company/dashboard/edit-deliveries*') || request()->is('company/dashboard/specimen-types*') || request()->is('company/dashboard/temperature-requirement*') || request()->is('company/dashboard/vehicle-requirement*') || request()->is('company/dashboard/create-job') ? 'open' : '' }}">
                            <li>
                                <a href="/company/dashboard/deliveries" class="{{ request()->is('company/dashboard/deliveries*') || request()->is('company/dashboard/edit-deliveries*') || request()->is('company/dashboard/create-job') ? 'active' : '' }}">
                                <i class="fas fa-briefcase"></i> Job Management
                                </a>
                            </li>
                            <li>
                                <a href="/company/dashboard/specimen-types" class="{{ request()->is('company/dashboard/specimen-types*') ? 'active' : '' }}">
                                <i class="fas fa-book-medical"></i> Specimen Types
                                </a>
                            </li>
                            <li>
                                <a href="/company/dashboard/temperature-requirement" class="{{ request()->is('company/dashboard/temperature-requirement*') ? 'active' : '' }}">
                                    <i class="fas fa-temperature-full"></i> Temperature Requirement
                                </a>
                            </li>
                            <li>
                                <a href="/company/dashboard/vehicle-requirement" class="{{ request()->is('company/dashboard/vehicle-requirement*') ? 'active' : '' }}">
                                    <i class="fas fa-car-on"></i> Vehicle Requirement
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li><a href="/company/dashboard/drivers" class="{{ request()->is('company/dashboard/drivers') || (request()->is('company/dashboard/drivers/*') || request()->is('company/dashboard/drivers/create')) ? 'active' : '' }}">
                        <i class="fas fa-user-tie"></i> Driver Management
                    </a></li>
                    <li><a href="/company/dashboard/activity-logs" class="{{ request()->is('company/dashboard/activity-logs*') ? 'active' : '' }}">
                        <i class="fas fa-file-lines"></i> Audit
                    </a></li>
                    <li><a href="/company/dashboard/maps" class="{{ request()->is('company/dashboard/maps*') ? 'active' : '' }}">
                        <i class="fas fa-map-marker-alt"></i> Maps
                    </a></li>
                @endif
                @if(auth()->user()->isAdmin())
                    <li><a href="/admin/dashboard/tenants" class="{{ request()->is('dashboard/tenants*') ? 'active' : '' }}">
                        <i class="fa-solid fa-building-user"></i> Tenant Management
                    </a></li>
                    <li><a href="/admin/dashboard/users" class="{{ request()->is('dashboard/users*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i> User Management
                    </a></li>
                @endif
                
            </ul>
        </aside>

        <!-- Main Content -->
         
        <main class="main-content">
            <!-- Top Header -->
            <header class="top-header">
                @if(auth()->user()->isDispatcher())
                <div class="header-search">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInputHeader" placeholder="Search" autocomplete="off">
                    <i class="fas fa-xmark"></i>
                    <div id="searchResults" class="search-results">
                        <div class="search-item">
                            <a href="/company/users/1">
                                <span>John Smith</span>
                            </a>
                        </div>
                        <div class="search-item">
                            <a href="/company/users/1">
                                <span>Steve Smith</span>
                            </a>
                        </div>
                        <div class="search-item">
                            <a href="/company/users/1">
                                <span>George ABc</span>
                            </a>
                        </div>

                    </div>
                </div>
                @endif
                <div class="header-actions">
                    @if(auth()->user()->isDispatcher())
                        @if(request()->is('company/dashboard/drivers/create'))
                            <button class="btn-create-driver-active" disabled onclick="window.location.href='/company/dashboard/drivers/create'">
                                Create Driver Profile <i class="fas fa-plus"></i>
                            </button>
                        @else
                            <button class="btn-create-driver" onclick="window.location.href='/company/dashboard/drivers/create'">
                                Create Driver Profile <i class="fas fa-plus"></i>
                            </button>
                        @endif
                    <button class="btn-create-job" onclick="window.location.href='/company/dashboard/create-job'">
                        Create Job <i class="fas fa-plus"></i>
                    </button>
                    @endif
                    @if(auth()->user()->isDispatcher())
                    <div class="notification-wrapper">
                        <div class="notification-icon" id="notificationIcon">
                            <i class="fas fa-bell"></i>
                            <div class="badge-div">
                                @if(auth()->user()->hasUnreadNotification())
                                    <span class="notification-badge"></span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="notification-dropdown" id="notificationDropdown">
                        <div class="notification-header">
                            <span>Notifications</span>
                            <button type="button" id="markAllReadBtn">Mark all as read</button>
                        </div>
                        <div class="notification-list">
                            <div class="notification-item"><span>Loading...</span></div>
                            
                        </div>
                    </div>
                    <div class="divider"></div>
                    <div class="user-menu">
                        <div class="user-avatar" id="userAvatar" onclick="toggleUserDropdown()">
                            <span class="user-avatar-text" id="userAvatarText">A</span>
                        </div>
                        <div class="user-dropdown" id="userDropdown" onclick="toggleUserDropdown()">
                            @if(auth()->user()->isDispatcher())
                                <a href="/company/profile/edit"><i class="fas fa-user-edit"></i> Edit Profile</a>
                                <a href="/company/profile/change-password"><i class="fas fa-key"></i> Change Password</a>
                            @endif
                            @if(auth()->user()->isHospital())
                                <a href="/hospital/profile/edit"><i class="fas fa-user-edit"></i> Edit Profile</a>
                                <a href="/hospital/profile/change-password"><i class="fas fa-key"></i> Change Password</a>
                            @endif
                            @if(auth()->user()->isAdmin())
                                <a href="/admin/profile/edit"><i class="fas fa-user-edit"></i> Edit Profile</a>
                                <a href="/admin/profile/change-password"><i class="fas fa-key"></i> Change Password</a>
                            @endif
                            <a href="{{ route('logout') }}" class="logout-btn" onclick="event.preventDefault(); localStorage.removeItem('fcm_token'); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt"></i> Log out
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                        <div class="searchDropdown" id="searchDropdown">
                            <span>Created deliveries</span>
                            <span>Pickup confirmed for delivery DLV-6A225E05AE95C</span>
                            <span>Pickup confirmed for delivery DLV-6A225E05AE95C</span>
                            <span>Created deliveries</span>
                            <span>Created deliveries</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="content">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @elseif(session('error'))
                    <div class="alert alert-error">
                        {{ session('error') }}
                    </div>
                 @elseif(session('warning'))
                    <div class="alert alert-warning">
                        {{ session('warning') }}
                    </div>    
                @endif
                <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
                @yield('content')
                <input type="hidden" id="user_role_id" value="{{ auth()->user()->role_id }}" />
            </div>
        </main>
    </div>

    <!-- Dialog Box -->
    <div class="dialog-overlay" id="dialogOverlay">
        <div class="dialog-box">
            <div class="dialog-header">
                <div class="dialog-icon" id="dialogIcon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="dialog-title" id="dialogTitle">Success</div>
            </div>
            <div class="dialog-message" id="dialogMessage">
                Operation completed successfully!
            </div>
            <div class="dialog-actions">
                <button class="dialog-btn primary" id="dialogOkBtn">OK</button>
            </div>
        </div>
    </div>

    <div class="loading-spinner">
        <i class="fas fa-spinner fa-spin" style="font-size: 32px; color: #a8b456;"></i>
        <p style="margin-top: 15px;" class="loading-text"></p>
    </div>
    
    <script src="{{ asset('assets/js/validation.js') }}"></script>
    <script>
        $('#searchInputHeader').on('keyup', function() {
            var query = $(this).val();
            var token = "{{ session('web_token') }}";
            if(query.length === 0) {
                $('#searchResults').hide();
                $('.fa-xmark').hide();
                return;
            }else{
                $('.fa-xmark').show();
            }
            $.ajax({
                url: '/api/v1/search-names',
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                },
                data: { query: query },
                success: function(response) {
                    console.log(response);
                    $('#searchResults').show();
                    //resonse is an array of user objects with id and name properties
                    var resultsHtml = '';
                    if(response.data.length === 0) {
                        resultsHtml = '<div class="search-item"><span>No results found</span></div>';
                    }else{
                        response.data.forEach(function(user) {
                            if(user.type === 'Driver') {
                                resultsHtml += '<div class="search-item"><a href="/company/dashboard/drivers/' + user.id + '"><span>' + user.title + '</span></a><span class="small">'+ user.type +'</span></div>';
                            }else{  
                                resultsHtml += '<div class="search-item"><a href="/company/dashboard/deliveries/' + user.id + '"><span>' + user.title + '</span></a><span class="small">'+ user.type +'</span></div>';
                            }
                        });
                    }
                    $('#searchResults').html(resultsHtml);
                }
            });
        }); 
        

        // Load user data and profile photo
        async function loadUserProfile() {
            const token = "{{ session('web_token') }}";
            
            // if (!token) {
            //     window.location.href = '/';
            //     return;
            // }

            try {
                // Fetch fresh user data from API
                const response = await fetch('/api/v1/me', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    const result = await response.json();
                    if (result.success && result.data) {
                        const user = result.data;

                        // Update localStorage with fresh data
                        localStorage.setItem('user_data', JSON.stringify(user));

                        // Update avatar
                        updateUserAvatar(user);
                    }
                } else {
                    // Fallback to localStorage if API fails
                    const userData = localStorage.getItem('user_data');
                    if (userData) {
                        const user = JSON.parse(userData);
                        updateUserAvatar(user);
                    }
                }
            } catch (error) {
                console.error('Error loading user profile:', error);
                // Fallback to localStorage
                const userData = localStorage.getItem('user_data');
                if (userData) {
                    try {
                        const user = JSON.parse(userData);
                        updateUserAvatar(user);
                    } catch (e) {
                        console.error('Error parsing user data:', e);
                    }
                }
            }
        }

        function updateUserAvatar(user) {
            const userAvatar = document.getElementById('userAvatar');
            const userAvatarText = document.getElementById('userAvatarText');

            if (user.profile_photo) {
                // Show profile photo
                userAvatar.innerHTML = `<img src="/storage/${user.profile_photo}" alt="${user.name}">`;
            } else {
                // Show initials
                const initial = (user.name || user.email).charAt(0).toUpperCase();
                userAvatar.innerHTML = `<span class="user-avatar-text">${initial}</span>`;
            }
        }

        // Load user profile on page load
        loadUserProfile();

        // Toggle user dropdown
        function toggleUserDropdown() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('show');
            const notificationDropdown = document.getElementById('notificationDropdown');
            if (notificationDropdown) {
                notificationDropdown.classList.remove('show');
            }
        }

        function toggleNotificationDropdown() {
            const dropdown = document.getElementById('notificationDropdown');
            const userDropdown = document.getElementById('userDropdown');
            if (dropdown) {
                if(!dropdown.classList.contains('show')) {
                    const token = document.querySelector('meta[name="api-token"]').getAttribute('content');
                    $.ajax({
                        url: '/api/mobile/v1/get-notification',
                        method: 'GET',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        },
                        data: { page: 1,per_page: 15,type: 'web' },
                        success: function(response) {
                            console.log(response);
                            const notificationList = document.querySelector('.notification-list');
                            if(notificationList) {
                                let resultsHtml = '';
                                if(response.notifications.data.length === 0) {
                                    resultsHtml = '<div class="notification-item"><span>No notifications found</span></div>';
                                }else{
                                    response.notifications.data.forEach(function(notification) {
                                        const isUnread = !notification.is_read;
                                        resultsHtml += `
                                            <div class="notification-item ${isUnread ? 'unread' : ''}" data-id="${notification.id || '#'}" data-delivery-id="${notification.data.delivery_id || '#'}" data-user-id="${notification.data.user_id || '#'}">
                                                <div class="notification-item-icon"><i class="fas fa-bell"></i></div>
                                                <div class="notification-item-text">
                                                    <p>${notification.title}</p>
                                                    <span class="notification-time">${new Date(notification.created_at).toLocaleString()}</span>
                                                </div>
                                            </div>
                                        `;
                                    });
                                }
                                notificationList.innerHTML = resultsHtml;
                            }
                        }
                    });
                } 
                dropdown.classList.toggle('show');
            }
            if (userDropdown) {
                userDropdown.classList.remove('show');
            }
        }

        

        function closeDropdowns(event) {
            const userMenu = document.querySelector('.user-menu');
            const notificationWrapper = document.querySelector('.notification-wrapper');
            const userDropdown = document.getElementById('userDropdown');
            const notificationDropdown = document.getElementById('notificationDropdown');
            const searchInputHeader = document.getElementById('searchInputHeader');

            if (userDropdown && (!userMenu || !userMenu.contains(event.target))) {
                userDropdown.classList.remove('show');
            }
            if (notificationDropdown && (!notificationWrapper || !notificationWrapper.contains(event.target))) {
                notificationDropdown.classList.remove('show');
            }
            if(!searchInputHeader.contains(event.target)){
                $('#searchResults').hide();
            }
        }

        document.addEventListener('click', closeDropdowns);

        // Dialog functions
        function showDialog(message, type = 'info', title = '', onOk = null) {
            const overlay = document.getElementById('dialogOverlay');
            const iconEl = document.getElementById('dialogIcon');
            const titleEl = document.getElementById('dialogTitle');
            const messageEl = document.getElementById('dialogMessage');
            const actionsEl = document.querySelector('.dialog-actions');

            // Set icon and title based on type
            const icons = {
                'success': '<i class="fas fa-check-circle"></i>',
                'error': '<i class="fas fa-times-circle"></i>',
                'warning': '<i class="fas fa-exclamation-triangle"></i>',
                'info': '<i class="fas fa-info-circle"></i>'
            };

            const titles = {
                'success': 'Success',
                'error': 'Error',
                'warning': 'Warning',
                'info': 'Information'
            };

            iconEl.className = 'dialog-icon ' + type;
            iconEl.innerHTML = icons[type] || icons.info;
            titleEl.textContent = title || titles[type] || titles.info;
            messageEl.innerHTML = message;

            // Reset to single OK button
            actionsEl.innerHTML = '<button class="dialog-btn primary" id="dialogOkBtn">OK</button>';

            // Show dialog
            overlay.classList.add('show');

            // Handle OK button click
            document.getElementById('dialogOkBtn').onclick = function() {
                overlay.classList.remove('show');
                if (onOk) onOk();
            };

            // Close on overlay click
            overlay.onclick = function(e) {
                if (e.target === overlay) {
                    overlay.classList.remove('show');
                    if (onOk) onOk();
                }
            };
        }

        function showConfirmDialog(message, type = 'warning', title = '', onConfirm = null, onCancel = null) {
            const overlay = document.getElementById('dialogOverlay');
            const iconEl = document.getElementById('dialogIcon');
            const titleEl = document.getElementById('dialogTitle');
            const messageEl = document.getElementById('dialogMessage');
            const actionsEl = document.querySelector('.dialog-actions');

            const icons = {
                'success': '<i class="fas fa-check-circle"></i>',
                'error': '<i class="fas fa-times-circle"></i>',
                'warning': '<i class="fas fa-exclamation-triangle"></i>',
                'info': '<i class="fas fa-info-circle"></i>'
            };

            const titles = {
                'success': 'Success',
                'error': 'Error',
                'warning': 'Warning',
                'info': 'Confirm'
            };

            iconEl.className = 'dialog-icon ' + type;
            iconEl.innerHTML = icons[type] || icons.warning;
            titleEl.textContent = title || titles[type] || titles.warning;
            messageEl.textContent = message;

            // Create confirm and cancel buttons
            actionsEl.innerHTML = `
                <button class="dialog-btn secondary" id="dialogCancelBtn">Cancel</button>
                <button class="dialog-btn primary" id="dialogConfirmBtn">Confirm</button>
            `;

            overlay.classList.add('show');

            document.getElementById('dialogConfirmBtn').onclick = function() {
                overlay.classList.remove('show');
                if (onConfirm) onConfirm();
            };

            document.getElementById('dialogCancelBtn').onclick = function() {
                overlay.classList.remove('show');
                if (onCancel) onCancel();
            };

            overlay.onclick = function(e) {
                if (e.target === overlay) {
                    overlay.classList.remove('show');
                    if (onCancel) onCancel();
                }
            };
        }

        // Logout function
        function logout() {
            showConfirmDialog('Are you sure you want to logout?', 'warning', 'Confirm Logout', function() {
                const token = localStorage.getItem('api_token');
                fetch('/api/v1/logout', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                }).finally(() => {
                    localStorage.removeItem('api_token');
                    localStorage.removeItem('user_data');
                    localStorage.removeItem('verify_email');
                    window.location.href = '/company/login';
                });
            });
            return false;
        }

        function show_load_spinner(hide_class,text='',hide_type='id'){
            $('.loading-spinner .loading-text').html(text);
            $('.loading-spinner').show();
            if(hide_type === 'id') {
                $('#'+hide_class).css('opacity','0.3');
            } else {
                $('.'+hide_class).css('opacity','0.3');
            }
        }
        function hide_load_spinner(hide_class,hide_type='id'){
            $('.loading-spinner .loading-text').html('');
            $('.loading-spinner').hide();
            if(hide_type === 'id') {
                $('#'+hide_class).css('opacity','1');
            } else {
                $('.'+hide_class).css('opacity','1');
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const jobManagementToggle = document.querySelector('.submenu-toggle');
            const jobManagementMenu = document.querySelector('.job-management-menu');
            const notificationIcon = document.getElementById('notificationIcon');
            const notificationDropdown = document.getElementById('notificationDropdown');
            const markAllReadBtn = document.getElementById('markAllReadBtn');
            const notificationItem = document.getElementsByClassName('notification-item');
            

            if (jobManagementToggle && jobManagementMenu) {
                jobManagementToggle.addEventListener('click', function (e) {
                    e.preventDefault();
                    jobManagementToggle.classList.toggle('open');
                    jobManagementMenu.classList.toggle('open');
                });
            }

            if (notificationIcon && notificationDropdown) {
                notificationIcon.addEventListener('click', function (e) {
                    e.stopPropagation();
                    toggleNotificationDropdown();
                });
            }

            if (markAllReadBtn) {
                markAllReadBtn.addEventListener('click', function (e) {
                    var token = "{{ session('web_token') }}";
                    e.stopPropagation();
                    document.querySelectorAll('.notification-item.unread').forEach(function (item) {
                        item.classList.remove('unread');
                    });
                    this.textContent = 'All read';

                    $.ajax({
                        url: `/api/mobile/v1/notifications/read-all`,
                        method: 'GET',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        },
                        data: { type: 'web' },
                        success: function(response) {
                            $('.badge-div').html('');
                        }
                    });
                });
            }
            $(document).on('click', '.notification-item', function (e) {
                e.stopPropagation();
                const notificationId = $(this).data('id');
                var token = "{{ session('web_token') }}";
                //Mark as read
                $.ajax({
                    url: `/api/mobile/v1/notifications/${notificationId}/read`,
                    method: 'PUT',
                    context:this,
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        console.log('Notification marked as read:', response);
                        $(this).removeClass('unread');
                    }
                });

                const deliveryId = $(this).data('delivery-id');
                const userId = $(this).data('user-id');

                if (deliveryId && deliveryId !== '#') {
                    window.location.href = `/company/dashboard/deliveries/${deliveryId}`;
                } else if (userId && userId !== '#') {
                    window.location.href = `/company/dashboard/drivers/${userId}`;
                }
            });

            const current_company_id = '{{ auth()->id() }}';
            
            document.addEventListener('DOMContentLoaded', () => {
                window.Echo.private('deliveries')
                    .listen('DeliveryStatusUpdated', (e) => {
                        console.log('DeliveryStatusUpdated', e);
                        if(current_company_id  == e.created_by){
                            $('#notificationIcon .badge-div').html('<span class="notification-badge"></span>');
                        }
                    });
            });

            $(document).on('input', '.numbers-only', function () {
                $(this).val($(this).val().replace(/\D/g, ''));
            });

            $(document).on('input', '.letters-only', function () {
                $(this).val($(this).val().replace(/[^a-zA-Z\s]/g, ''));
            });

            

        });
        function isValidEmail(email) {
            const regex = /^[^\s@]+@[^\s@]+\.[a-zA-Z]{2,}$/;
            return regex.test(email);
        }

        $("body").on("keyup", ".validatePhone", function() {
            const phone = $(this).val();

            // USA phone validation regex
            const regex = /^(\+1\s?)?(\([0-9]{3}\)|[0-9]{3})[-.\s]?[0-9]{3}[-.\s]?[0-9]{4}$/;
            if (!regex.test(phone)) {
                $(this).addClass('invalid-phone');
            } else {
                $(this).removeClass('invalid-phone');
            }
        });

        function validatePhone() {

            const phone = document.getElementById('phone').value.trim();
            const country = document
                .getElementById('country_code')
                .selectedOptions[0]
                .dataset.country;
                
            if (!phone) {
                // error.textContent = 'Phone number is required.';
                return false;
            }
            try {
                const phoneNumber = libphonenumber.parsePhoneNumberFromString(
                    phone,
                    country
                );

                if (!phoneNumber || !phoneNumber.isValid()) {
                    // error.textContent = 'Invalid phone number.';
                    return false;
                }

                return true;

            } catch (e) {
                // error.textContent = 'Invalid phone number.';
                return false;
            }
        }


        const hipaaFile = document.getElementById('hipaa_file');
        if (hipaaFile) {
            hipaaFile.addEventListener('change', function () {
                const fileNameInput = document.getElementById('hipaa_file_name');

                if (fileNameInput) {
                    if (this.files && this.files.length > 0) {
                        fileNameInput.value = this.files[0].name;
                    } else {
                        fileNameInput.value = '';
                    }
                }
            });
        }

        const bloodborneFile = document.getElementById('bloodborne_file');
        if (bloodborneFile) {
            bloodborneFile.addEventListener('change', function () {
                const fileNameInput = document.getElementById('bloodborne_file_name');

                if (fileNameInput) {
                    if (this.files && this.files.length > 0) {
                        fileNameInput.value = this.files[0].name;
                    } else {
                        fileNameInput.value = '';
                    }
                }
            });
        }

        $('.fa-xmark').click(function(){
            $('#searchInputHeader').val('');
            $('#searchInputHeader').focus();
            $('#searchResults').hide();
            $(this).hide();
        });
        
        //add hospital
        $('#addHospitalModal').on('submit', function(e) {
            e.preventDefault();
            if (document.querySelector('#add_hospital .just-validate-error-label')) {
                hide_load_spinner();
                return false;
            }
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());
            
            $.ajax({
                url: '/api/v1/add-hospital',
                type: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                },
                contentType: 'application/json',
                data: JSON.stringify(data),

                success: function (result) {
                    hide_load_spinner();
                    if (result.success) {
                        showDialog('Hospital added successfully!', 'success');
                        $('#add_hospital')[0].reset();
                    } 
                    
                },
                error: function (result) {
                    hide_load_spinner();
                    showDialog(result.responseJSON.message, 'error');
                }
            });
        });

        function initHospitalSearch(){
            $('.search-hospital').each(function () {
                const input = $(this);
                if (input.data('hospitalAutocompleteInitialized')) return;

                input.data('hospitalAutocompleteInitialized', true);
                const results = input.siblings('.hospital-autocomplete-results');

                input.on('input', function () {
                    const search = input.val().trim();
                    input.siblings('.hospital-id').val('');
                    results.empty().hide();

                    if (search.length < 2) return;

                    $.ajax({
                        url: '/api/v1/search-hospitals',
                        type: 'GET',
                        dataType: 'json',
                        data: { search: search },
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        },
                        success: function (response) {
                            if (!input.is(':focus') || input.val().trim() !== search) return;

                            response.data.forEach(function (hospital) {
                                $('<button>', {
                                    type: 'button',
                                    class: 'hospital-autocomplete-option',
                                    text: hospital.name
                                }).data('hospital', hospital).appendTo(results);
                            });

                            results.toggle(response.data.length > 0);
                        }
                    });
                });

                results.on('click', '.hospital-autocomplete-option', function () {
                    const hospital = $(this).data('hospital');
                    input.val(hospital.name);
                    input.siblings('.hospital-id').val(hospital.id);
                    results.empty().hide();
                });

                input.on('blur', function () {
                    setTimeout(function () { results.hide(); }, 150);
                });
            });
        }

         
        $('body').on('click', '.dropoff_type', function() {
            var value = $(this).val();
            if(value == 'hospital'){
                $(this).parents('.item-card').find('.search-hospital').prop('disabled',false);
                $(this).parents('.item-card').find('.type_address').hide();
                // $(this).parents('.item-card').find('.type_address input').each(function(){
                //     $(this).val('');
                // });
            }else{
                $(this).parents('.item-card').find('.search-hospital').prop('disabled',true);
                $(this).parents('.item-card').find('.type_address').show();
                // $(this).parents('.item-card').find('.hospital-id').val('');
                // $(this).parents('.item-card').find('.search-hospital').val('');
            }
        });

        function datTimeFormat(datetime = ''){
            const date = new Date(datetime);
            const formatted = date.toLocaleString('en-IN', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            });
            return formatted;
        }
        
    </script>

    @yield('scripts')
</body>
</html>
