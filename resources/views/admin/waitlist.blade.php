<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waitlist Submissions - Relia Track Admin</title>
    <link rel="stylesheet" href="/assets/css/bootstrap.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    @include('common.layout')

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Waitlist Submissions</h2>
                    <div>
                        <button class="btn btn-primary" onclick="loadStatistics()">
                            <i class="bi bi-graph-up"></i> View Statistics
                        </button>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <label>Status</label>
                                <select class="form-select" id="statusFilter">
                                    <option value="">All Statuses</option>
                                    <option value="pending">Pending</option>
                                    <option value="contacted">Contacted</option>
                                    <option value="converted">Converted</option>
                                    <option value="declined">Declined</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Search</label>
                                <input type="text" class="form-control" id="searchInput" placeholder="Search by name, email, or company...">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button class="btn btn-primary w-100" onclick="loadSubmissions()">Apply Filters</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div id="statisticsCards" class="row mb-4" style="display: none;">
                    <!-- Statistics will be loaded here -->
                </div>

                <!-- Submissions Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Company</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Status</th>
                                        <th>Submitted</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="submissionsTableBody">
                                    <tr>
                                        <td colspan="8" class="text-center">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div id="pagination" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Details Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Submission Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detailsContent">
                    <!-- Details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/bootstrap.bundle.js"></script>
    <script>
        const API_BASE = '/api/v1';
        let currentPage = 1;

        // Get auth token from localStorage (adjust based on your auth implementation)
        function getAuthToken() {
            return localStorage.getItem('auth_token');
        }

        // Load submissions
        async function loadSubmissions(page = 1) {
            currentPage = page;
            const status = document.getElementById('statusFilter').value;
            const search = document.getElementById('searchInput').value;

            let url = `${API_BASE}/waitlist?page=${page}`;
            if (status) url += `&status=${status}`;
            if (search) url += `&search=${search}`;

            try {
                const response = await fetch(url, {
                    headers: {
                        'Authorization': `Bearer ${getAuthToken()}`,
                        'Accept': 'application/json',
                    }
                });

                const result = await response.json();

                if (result.success) {
                    displaySubmissions(result.data.data);
                    displayPagination(result.data);
                }
            } catch (error) {
                console.error('Error loading submissions:', error);
                alert('Failed to load submissions');
            }
        }

        // Display submissions
        function displaySubmissions(submissions) {
            const tbody = document.getElementById('submissionsTableBody');

            if (submissions.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center">No submissions found</td></tr>';
                return;
            }

            tbody.innerHTML = submissions.map(sub => `
                <tr>
                    <td>${sub.id}</td>
                    <td>${sub.name}</td>
                    <td>${sub.company_name || '-'}</td>
                    <td>${sub.email}</td>
                    <td>${sub.phone}</td>
                    <td><span class="badge bg-${getStatusColor(sub.status)}">${sub.status}</span></td>
                    <td>${new Date(sub.created_at).toLocaleDateString()}</td>
                    <td>
                        <button class="btn btn-sm btn-info" onclick="viewDetails(${sub.id})">View</button>
                        <button class="btn btn-sm btn-success" onclick="updateStatus(${sub.id}, 'contacted')">Mark Contacted</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteSubmission(${sub.id})">Delete</button>
                    </td>
                </tr>
            `).join('');
        }

        // Get status badge color
        function getStatusColor(status) {
            const colors = {
                'pending': 'warning',
                'contacted': 'info',
                'converted': 'success',
                'declined': 'secondary'
            };
            return colors[status] || 'secondary';
        }

        // Display pagination
        function displayPagination(data) {
            const pagination = document.getElementById('pagination');
            let html = '<nav><ul class="pagination">';

            if (data.prev_page_url) {
                html += `<li class="page-item"><a class="page-link" href="#" onclick="loadSubmissions(${data.current_page - 1}); return false;">Previous</a></li>`;
            }

            for (let i = 1; i <= data.last_page; i++) {
                html += `<li class="page-item ${i === data.current_page ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="loadSubmissions(${i}); return false;">${i}</a>
                </li>`;
            }

            if (data.next_page_url) {
                html += `<li class="page-item"><a class="page-link" href="#" onclick="loadSubmissions(${data.current_page + 1}); return false;">Next</a></li>`;
            }

            html += '</ul></nav>';
            pagination.innerHTML = html;
        }

        // View submission details
        async function viewDetails(id) {
            try {
                const response = await fetch(`${API_BASE}/waitlist/${id}`, {
                    headers: {
                        'Authorization': `Bearer ${getAuthToken()}`,
                        'Accept': 'application/json',
                    }
                });

                const result = await response.json();

                if (result.success) {
                    const sub = result.data;
                    document.getElementById('detailsContent').innerHTML = `
                        <div class="row">
                            <div class="col-md-6"><strong>Name:</strong> ${sub.name}</div>
                            <div class="col-md-6"><strong>Email:</strong> ${sub.email}</div>
                            <div class="col-md-6"><strong>Company:</strong> ${sub.company_name || '-'}</div>
                            <div class="col-md-6"><strong>Phone:</strong> ${sub.phone}</div>
                            <div class="col-md-6"><strong>Status:</strong> <span class="badge bg-${getStatusColor(sub.status)}">${sub.status}</span></div>
                            <div class="col-md-6"><strong>Submitted:</strong> ${new Date(sub.created_at).toLocaleString()}</div>
                            <div class="col-12 mt-3"><strong>Message:</strong><br>${sub.message || 'No message'}</div>
                            <div class="col-12 mt-3"><strong>Notes:</strong><br>${sub.notes || 'No notes'}</div>
                            <div class="col-md-6"><strong>IP Address:</strong> ${sub.ip_address || '-'}</div>
                            <div class="col-md-6"><strong>Contacted At:</strong> ${sub.contacted_at ? new Date(sub.contacted_at).toLocaleString() : '-'}</div>
                        </div>
                    `;

                    const modal = new bootstrap.Modal(document.getElementById('detailsModal'));
                    modal.show();
                }
            } catch (error) {
                console.error('Error loading details:', error);
                alert('Failed to load submission details');
            }
        }

        // Update submission status
        async function updateStatus(id, status) {
            if (!confirm(`Are you sure you want to mark this submission as ${status}?`)) return;

            try {
                const response = await fetch(`${API_BASE}/waitlist/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': `Bearer ${getAuthToken()}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ status })
                });

                const result = await response.json();

                if (result.success) {
                    alert('Status updated successfully');
                    loadSubmissions(currentPage);
                }
            } catch (error) {
                console.error('Error updating status:', error);
                alert('Failed to update status');
            }
        }

        // Delete submission
        async function deleteSubmission(id) {
            if (!confirm('Are you sure you want to delete this submission?')) return;

            try {
                const response = await fetch(`${API_BASE}/waitlist/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': `Bearer ${getAuthToken()}`,
                        'Accept': 'application/json',
                    }
                });

                const result = await response.json();

                if (result.success) {
                    alert('Submission deleted successfully');
                    loadSubmissions(currentPage);
                }
            } catch (error) {
                console.error('Error deleting submission:', error);
                alert('Failed to delete submission');
            }
        }

        // Load statistics
        async function loadStatistics() {
            try {
                const response = await fetch(`${API_BASE}/waitlist/statistics`, {
                    headers: {
                        'Authorization': `Bearer ${getAuthToken()}`,
                        'Accept': 'application/json',
                    }
                });

                const result = await response.json();

                if (result.success) {
                    const stats = result.data;
                    const statsDiv = document.getElementById('statisticsCards');
                    statsDiv.style.display = 'flex';
                    statsDiv.innerHTML = `
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h3>${stats.total}</h3>
                                    <p>Total Submissions</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h3>${stats.pending}</h3>
                                    <p>Pending</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h3>${stats.contacted}</h3>
                                    <p>Contacted</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h3>${stats.converted}</h3>
                                    <p>Converted</p>
                                </div>
                            </div>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error loading statistics:', error);
            }
        }

        // Load submissions on page load
        document.addEventListener('DOMContentLoaded', () => {
            loadSubmissions();
        });
    </script>
</body>
</html>
