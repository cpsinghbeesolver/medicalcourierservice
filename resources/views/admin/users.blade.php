@extends('common.layout')

@section('title', 'User Management')
@section('page-title', 'User Management')

@section('styles')
<style>
    .data-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        overflow: hidden;
    }

    .data-card-header {
        padding: 20px 25px;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .data-card-header h3 {
        font-size: 18px;
        font-weight: 600;
        color: #2c3e50;
    }

    .btn-create {
        padding: 10px 20px;
        background: #a8b456;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-create:hover {
        background: #96a048;
    }

    .table-container {
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table thead {
        background: #f8f9fa;
    }

    .data-table th {
        padding: 14px 20px;
        text-align: left;
        font-size: 13px;
        font-weight: 600;
        color: #2c3e50;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .data-table td {
        padding: 16px 20px;
        border-bottom: 1px solid #f0f0f0;
        font-size: 14px;
        color: #2c3e50;
    }

    .data-table tbody tr:hover {
        background: #fafafa;
    }

    .badge {
        padding: 5px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
    }

    .badge.admin {
        background: #e8d5ff;
        color: #6b21a8;
    }

    .badge.driver {
        background: #cfe2ff;
        color: #084298;
    }

    .badge.coordinator {
        background: #d1ecf1;
        color: #0c5460;
    }

    .badge.active {
        background: #d1e7dd;
        color: #0f5132;
    }

    .badge.inactive,
    .badge.suspended {
        background: #f8d7da;
        color: #842029;
    }

    /* Modal */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }

    .modal.show {
        display: flex;
    }

    .modal-content {
        background: white;
        border-radius: 12px;
        max-width: 500px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-header {
        padding: 20px 25px;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h3 {
        font-size: 18px;
        font-weight: 600;
        color: #2c3e50;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #7f8c8d;
    }

    .modal-body {
        padding: 25px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-size: 13px;
        color: #2c3e50;
        margin-bottom: 6px;
        font-weight: 500;
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
    }

    .form-group input:focus,
    .form-group select:focus {
        border-color: #a8b456;
    }

    .modal-footer {
        padding: 20px 25px;
        border-top: 1px solid #f0f0f0;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .btn-cancel {
        padding: 10px 20px;
        background: #e0e0e0;
        color: #2c3e50;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
    }

    .btn-submit {
        padding: 10px 20px;
        background: #a8b456;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
    }

    .info-message {
        background: #d1ecf1;
        color: #0c5460;
        padding: 20px;
        border-radius: 8px;
        border-left: 4px solid #0c5460;
        margin: 20px;
        font-size: 14px;
        line-height: 1.6;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .btn-action {
        padding: 6px 12px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 12px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-edit {
        background: #cfe2ff;
        color: #084298;
    }

    .btn-edit:hover {
        background: #b6d4fe;
    }

    .btn-delete {
        background: #f8d7da;
        color: #842029;
    }

    .btn-delete:hover {
        background: #f1aeb5;
    }

    .loading-message {
        text-align: center;
        padding: 60px;
        color: #7f8c8d;
    }
</style>
@endsection

@section('content')
<!-- Data Table -->
<div class="data-card">
    <div class="data-card-header">
        <h3>All Users</h3>
        <button class="btn-create" onclick="showCreateModal()">
            <i class="fas fa-plus"></i> Add New User
        </button>
    </div>

    <div class="table-container">
        <table class="data-table" id="usersTable">
            <thead>
                <tr>
                    <th>Sr No.</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="usersTableBody">
                <tr>
                    <td colspan="7" class="loading-message">
                        <i class="fas fa-spinner fa-spin" style="font-size: 24px; color: #a8b456;"></i>
                        <p style="margin-top: 10px;">Loading users...</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Create/Edit Modal -->
<div id="userModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Create New User</h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <form id="userForm">
            <input type="hidden" id="userId" name="user_id">
            <input type="hidden" id="isEdit" name="is_edit" value="false">
            <div class="modal-body">
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" id="userName" name="name" required placeholder="John Doe">
                </div>
                <div class="form-group">
                    <label>Email Address *</label>
                    <input type="email" id="userEmail" name="email" required placeholder="john@example.com">
                </div>
                <div class="form-group" id="passwordGroup">
                    <label>Password *</label>
                    <input type="password" id="userPassword" name="password" placeholder="Minimum 8 characters">
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="tel" id="userPhone" name="phone" placeholder="+1234567890">
                </div>
                <div class="form-group">
                    <label>Role *</label>
                    <select id="userRole" name="role" required>
                        <option value="coordinator">Coordinator</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status *</label>
                    <select id="userStatus" name="status" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn-submit" id="submitBtn">Create User</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const token = '{{ session("web_token") }}';
    let allUsers = [];

    // Load users on page load
    async function loadUsers() {
        try {
            const response = await fetch('/api/v1/users', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (result.success && result.data) {
                allUsers = result.data.users || result.data;
                // Filter out drivers - only show admin and coordinator
                const filteredUsers = allUsers.filter(user => user.role !== 'driver');
                displayUsers(filteredUsers);
            } else {
                throw new Error('Failed to load users');
            }
        } catch (error) {
            console.error('Error loading users:', error);
            document.getElementById('usersTableBody').innerHTML = `
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px; color: #e74c3c;">
                        <i class="fas fa-exclamation-triangle" style="font-size: 24px;"></i>
                        <p style="margin-top: 10px;">Error loading users</p>
                    </td>
                </tr>
            `;
        }
    }

    function displayUsers(users) {
        const tbody = document.getElementById('usersTableBody');

        if (!users || users.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px; color: #7f8c8d;">
                        No users found. Click "Add New User" to create one.
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = users.map((user, index) => `
            <tr>
                <td>${index + 1}</td>
                <td>${user.name || 'N/A'}</td>
                <td>${user.email || 'N/A'}</td>
                <td>${user.phone || 'N/A'}</td>
                <td><span class="badge ${user.role || 'driver'}">${user.role || 'N/A'}</span></td>
                <td><span class="badge ${user.status || 'active'}">${user.status || 'active'}</span></td>
                <td>${user.created_at ? new Date(user.created_at).toLocaleDateString() : 'N/A'}</td>
                <td>
                    <div class="action-buttons">
                        <button class="btn-action btn-edit" onclick="showEditModal(${user.id})" title="Edit User">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn-action btn-delete" onclick="deleteUser(${user.id}, '${user.name}')" title="Delete User">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    function showCreateModal() {
        document.getElementById('modalTitle').textContent = 'Create New User';
        document.getElementById('submitBtn').textContent = 'Create User';
        document.getElementById('isEdit').value = 'false';
        document.getElementById('userId').value = '';
        document.getElementById('userPassword').required = true;
        document.getElementById('passwordGroup').querySelector('label').textContent = 'Password *';
        document.getElementById('userForm').reset();
        document.getElementById('userModal').classList.add('show');
    }

    function showEditModal(userId) {
        const user = allUsers.find(u => u.id === userId);
        if (!user) {
            showDialog('User not found', 'error');
            return;
        }

        document.getElementById('modalTitle').textContent = 'Edit User';
        document.getElementById('submitBtn').textContent = 'Update User';
        document.getElementById('isEdit').value = 'true';
        document.getElementById('userId').value = user.id;
        document.getElementById('userName').value = user.name || '';
        document.getElementById('userEmail').value = user.email || '';
        document.getElementById('userPhone').value = user.phone || '';
        document.getElementById('userRole').value = user.role || 'driver';
        document.getElementById('userStatus').value = user.status || 'active';
        document.getElementById('userPassword').value = '';
        document.getElementById('userPassword').required = false;
        document.getElementById('passwordGroup').querySelector('label').textContent = 'Password (leave blank to keep current)';
        document.getElementById('userModal').classList.add('show');
    }

    function closeModal() {
        document.getElementById('userModal').classList.remove('show');
        document.getElementById('userForm').reset();
    }

    async function deleteUser(userId, userName) {
        if (!confirm(`Are you sure you want to delete user "${userName}"? This action cannot be undone.`)) {
            return;
        }

        try {
            const response = await fetch(`/api/v1/users/${userId}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (result.success) {
                showDialog('User deleted successfully!', 'success');
                loadUsers(); // Reload users
            } else {
                showDialog('Error: ' + (result.message || 'Failed to delete user'), 'error');
            }
        } catch (error) {
            console.error('Error deleting user:', error);
            showDialog('Error deleting user', 'error');
        }
    }

    document.getElementById('userForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const isEdit = formData.get('is_edit') === 'true';
        const userId = formData.get('user_id');

        const data = {
            name: formData.get('name'),
            email: formData.get('email'),
            phone: formData.get('phone'),
            role: formData.get('role'),
            status: formData.get('status')
        };

        // Only include password if provided
        const password = formData.get('password');
        if (password) {
            data.password = password;
            data.password_confirmation = password;
        }

        try {
            let response;
            if (isEdit) {
                // Update existing user
                response = await fetch(`/api/v1/users/${userId}`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
            } else {
                // Create new user
                if (!password) {
                    showDialog('Password is required for new users', 'error');
                    return;
                }
                response = await fetch('/api/v1/users', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
            }

            const result = await response.json();

            if (result.success) {
                showDialog(`User ${isEdit ? 'updated' : 'created'} successfully!`, 'success');
                closeModal();
                loadUsers(); // Reload users
            } else {
                showDialog('Error: ' + (result.message || `Failed to ${isEdit ? 'update' : 'create'} user`), 'error');
            }
        } catch (error) {
            console.error('Error submitting form:', error);
            showDialog(`Error ${isEdit ? 'updating' : 'creating'} user`, 'error');
        }
    });

    // Load users when page loads
    loadUsers();
</script>
@endsection
