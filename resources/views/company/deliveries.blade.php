@extends('common.layout')

@section('title', 'Job Management')
@section('page-title', 'Job Management')

@section('styles')
<style>
    .filters-bar {
        background: #ffffff;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        margin-bottom: 25px;
        display: flex;
        gap: 15px;
        align-items: center;
        flex-wrap: wrap;
    }

    .filter-group {
        flex: 1;
        min-width: 200px;
    }

    .filter-group label {
        display: block;
        font-size: 13px;
        color: #7f8c8d;
        margin-bottom: 6px;
        font-weight: 500;
    }

    .filter-group select,
    .filter-group input {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
    }

    .filter-group select:focus,
    .filter-group input:focus {
        border-color: #a8b456;
    }

    .btn-filter {
        padding: 10px 24px;
        background: #2c3e50;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        margin-top: 24px;
    }

    .btn-filter:hover {
        background: #1a252f;
    }

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
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
        display: inline-block;
        white-space: nowrap;
    }

    .badge.pending {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }

    .badge.assigned {
        background: #d1ecf1;
        color: #0c5460;
        border: 1px solid #bee5eb;
    }

    .badge.in_transit,
    .badge.in-transit {
        background: #cfe2ff;
        color: #084298;
        border: 1px solid #9ec5fe;
    }

    .badge.picked_up,
    .badge.picked-up {
        background: #d3d3ff;
        color: #3d3d8e;
        border: 1px solid #b8b8ff;
    }

    .badge.delivered {
        background: #d1e7dd;
        color: #0f5132;
        border: 1px solid #badbcc;
    }
    .badge.accepted {
        background: #bdffe1;
        color: #0f5132;
        border: 1px solid #8fe6bf;
    }

    .badge.cancelled,
    .badge.failed {
        background: #f8d7da;
        color: #842029;
        border: 1px solid #f5c2c7;
    }

    .badge.low {
        background: #d1ecf1;
        color: #0c5460;
    }

    .badge.normal {
        background: #d1e7dd;
        color: #0f5132;
    }

    .badge.high {
        background: #fff3cd;
        color: #856404;
    }

    .badge.urgent {
        background: #f8d7da;
        color: #842029;
    }


    /* Statistics Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: #a6b64a38;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .stat-card h4 {
        font-size: 14px;
        font-weight: 500;
        color: #000000;
        margin-bottom: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-card .stat-value {
        font-size: 36px;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 8px;
    }

    .stat-card .stat-description {
        font-size: 13px;
        color: #6f8283;
    }

    /* Pagination Styles */
    .pagination-container {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        padding: 20px;
        border-top: 1px solid #f0f0f0;
        flex-wrap: wrap;
    }

    .pagination-info {
        font-size: 13px;
        color: #7f8c8d;
        margin-right: 15px;
        display: none;
    }

    .pagination-controls {
        display: flex;
        gap: 4px;
        align-items: center;
    }

    .pagination-btn {
        padding: 8px 12px;
        border: 1px solid #e0e0e0;
        background: white;
        color: #2c3e50;
        border-radius: 4px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .pagination-btn:hover:not(:disabled) {
        border-color: #2c3e50;
        background: #f8f9fa;
        color: #2c3e50;
    }

    .pagination-btn:disabled {
        color: #bdc3c7;
        border-color: #e0e0e0;
        cursor: not-allowed;
        background: #f8f9fa;
    }

    .pagination-btn.active {
        background: #2c3e50;
        color: white;
        border-color: #2c3e50;
    }

    .pagination-btn.active:hover {
        background: #34495e;
        border-color: #34495e;
    }

     .search-bar {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    padding: 8px 14px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    font-size: 14px;
    outline: none;
        gap: 8px;
}

   .search-bar input{
    width: 100%;
    padding: 0px !important;
    border: none !important;
    border-radius: 0px !important;
    outline: none;
}

</style>
@endsection

@section('content')
<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card" id="totalJobsCard">
        <h4>Total Jobs</h4>
        <div class="stat-value" id="totalJobs">0</div>
        <div class="stat-description">Total number of jobs created</div>
    </div>
    <div class="stat-card active" id="activeJobsCard">
        <h4>Active Jobs</h4>
        <div class="stat-value" id="activeJobs">0</div>
        <div class="stat-description">Assigned, Picked Up & In Transit</div>
    </div>
    <div class="stat-card pending" id="pendingJobsCard">
        <h4>Pending Assignment</h4>
        <div class="stat-value" id="pendingJobs">0</div>
        <div class="stat-description">Unassigned Jobs</div>
    </div>
    <div class="stat-card delivered" id="deliveredTodayCard">
        <h4>Delivered Today</h4>
        <div class="stat-value" id="deliveredToday">0</div>
        <div class="stat-description">Successfully completed deliveries</div>
    </div>
    <div class="stat-card transit" id="inTransitCard">
        <h4>In Transit</h4>
        <div class="stat-value" id="inTransit">0</div>
        <div class="stat-description">Specimens currently being transported</div>
    </div>
</div>

<!-- Filters -->
<div class="filters-bar">
    <div class="filter-group">
        <label>Status</label>
        <select id="deliveriesFilterStatus">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="assigned">Assigned</option>
            <option value="in_transit">In Transit</option>
            <option value="picked_up">Picked Up</option>
            <option value="delivered">Delivered</option>
            <option value="failed">Failed</option>
            <option value="cancelled">Cancelled</option>
        </select>
    </div>
    <div class="filter-group">
        <label>Priority</label>
        <select id="deliveriesFilterPriority">
            <option value="">All Priority</option>
            <option value="low">Low</option>
            <option value="normal">Normal</option>
            <option value="high">High</option>
            <option value="urgent">Urgent</option>
        </select>
    </div>
    <div class="filter-group">
        <label>Search</label>
       <div class="search-bar">
         <i class="fas fa-search"></i>
        <input type="text" id="deliveriesSearchInput" placeholder="Search by delivery number...">
       </div>
    </div>
    <!-- <div class="filter-group">
        <label>Rows per page</label>
        <select id="deliveriesPageSize">
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="20">20</option>
            <option value="50">50</option>
        </select>
    </div> -->
    <!-- <button class="btn-filter" onclick="applyFilters()">Apply Filters</button> -->
</div>

<!-- Data Table -->
<div class="data-card">
    <div class="data-card-header">
        <h3>All Jobs</h3>
        <!-- <button class="btn-create" onclick="window.location.href='/company/dashboard/create-job'">
            <i class="fas fa-plus"></i> Create New Job
        </button> -->
    </div>
    <div class="table-container">
        <table class="data-table" id="deliveriesTable">
            <thead>
                <tr>
                    <th>Sr No.</th>
                    <th>Job Number</th>
                    <!-- <th>Pickup Location</th> -->
                    <!-- <th>Delivery Location</th> -->
                    <th>Driver</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <!-- <th>Distance</th> -->
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="9" style="text-align: center; padding: 60px;">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="pagination-container">
        <div class="pagination-info">
            <span id="deliveriesPageInfo">Showing 0 of 0</span>
        </div>
        <div class="pagination-controls">
            <button class="pagination-btn" id="deliveriesPrevBtn" type="button">← Previous</button>
            <div id="deliveriesPageNumbers" style="display: flex; gap: 4px;"></div>
            <button class="pagination-btn" id="deliveriesNextBtn" type="button">Next →</button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    (function() {
        const token = '{{ session("web_token") }}';
        let currentDeliveries = [];
        let currentPage = 1;
        let itemsPerPage = 10;
        let lastPage = 1;
        let totalDeliveries = 0;

        const elements = {
            status: document.getElementById('deliveriesFilterStatus'),
            priority: document.getElementById('deliveriesFilterPriority'),
            search: document.getElementById('deliveriesSearchInput'),
            // pageSize: document.getElementById('deliveriesPageSize'),
            pageSize: itemsPerPage,
            tableBody: document.querySelector('#deliveriesTable tbody'),
            pageInfo: document.getElementById('deliveriesPageInfo'),
            pageNumbers: document.getElementById('deliveriesPageNumbers'),
            prevBtn: document.getElementById('deliveriesPrevBtn'),
            nextBtn: document.getElementById('deliveriesNextBtn')
        };

        async function loadStatistics() {
            try {
                const response = await fetch('/api/v1/deliveries', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data.success && data.data) {
                    updateStatistics(data.data.deliveries || []);
                }
            } catch (error) {
                console.error('Error loading delivery statistics:', error);
            }
        }

        async function loadDeliveries(page = 1) {
            currentPage = page;
            const params = new URLSearchParams();
            params.set('page', page);
            params.set('per_page', itemsPerPage);
            const urlParams = new URLSearchParams(window.location.search);
            const get_status = urlParams.get('status');
            if(get_status){
                $('#deliveriesFilterStatus').val(get_status);
                params.set('status', get_status);
            }
            if (elements.status.value) {
                params.set('status', elements.status.value);
            }
            if (elements.priority.value) {
                params.set('priority', elements.priority.value);
            }
            if (elements.search.value.trim()) {
                params.set('search', elements.search.value.trim());
            }

            elements.tableBody.innerHTML = '<tr><td colspan="9" style="text-align: center; padding: 60px; color: #7f8c8d;">Loading...</td></tr>';

            try {
                const response = await fetch(`/api/v1/deliveries?${params.toString()}`, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data.success && data.data) {
                    currentDeliveries = data.data.deliveries || [];
                    totalDeliveries = data.data.pagination?.total ?? currentDeliveries.length;
                    lastPage = data.data.pagination?.last_page ?? 1;
                    displayDeliveries();
                    updatePagination();
                } else {
                    currentDeliveries = [];
                    totalDeliveries = 0;
                    lastPage = 1;
                    renderEmptyState();
                }
            } catch (error) {
                console.error('Error loading deliveries:', error);
                elements.tableBody.innerHTML = '<tr><td colspan="9" style="text-align: center; padding: 60px; color: #e74c3c;">Unable to load jobs. Refresh the page or try again later.</td></tr>';
            }
        }

        function updateStatistics(deliveries) {
            document.getElementById('totalJobs').textContent = deliveries.length;
            document.getElementById('activeJobs').textContent = deliveries.filter(d => ['assigned', 'picked_up', 'in_transit'].includes(d.status)).length;
            document.getElementById('pendingJobs').textContent = deliveries.filter(d => d.status === 'pending').length;

            const today = new Date().toISOString().split('T')[0];
            document.getElementById('deliveredToday').textContent = deliveries.filter(d => d.status === 'delivered' && d.delivery?.actual_time && new Date(d.delivery.actual_time).toISOString().split('T')[0] === today).length;
            document.getElementById('inTransit').textContent = deliveries.filter(d => ['in_transit', 'picked_up'].includes(d.status)).length;
        }

        function formatStatus(status) {
            const statusMap = {
                'pending': 'Pending',
                'assigned': 'Assigned',
                'in_transit': 'In Transit',
                'picked_up': 'Picked Up',
                'delivered': 'Delivered',
                'cancelled': 'Cancelled',
                'failed': 'Failed'
            };
            return statusMap[status] || status || 'Unknown';
        }

        function getStatusClass(status) {
            return String(status).replace('_', '-');
        }

        function displayDeliveries() {
            if (!currentDeliveries.length) {
                renderEmptyState();
                return;
            }

            elements.tableBody.innerHTML = currentDeliveries.map((delivery, index) => {
                const rowNumber = (currentPage - 1) * itemsPerPage + index + 1;
                const priorityText = String(delivery.priority || 'normal');

                return `
                    <tr data-id="${delivery.id}">
                        <td>${rowNumber}</td>
                        <td><strong>${delivery.delivery_number || 'N/A'}</strong></td>
                        <td>${delivery.driver ? delivery.driver.name : '<span style="color: #95a5a6;">Not Assigned</span>'}</td>
                        <td><span class="badge ${getStatusClass(delivery.status)}">${formatStatus(delivery.status)}</span></td>
                        <td><span class="badge ${priorityText}">${priorityText.charAt(0).toUpperCase() + priorityText.slice(1)}</span></td>
                        <td style="white-space: nowrap;">
                            <button class="btn-action" type="button" data-action="view" data-delivery-id="${delivery.id}">
                                <i class="fas fa-eye"></i> View
                            </button>
                            ${delivery.status === 'pending' || delivery.status === 'assigned' ? `
                            <button class="btn-action edit" type="button" data-action="edit" data-delivery-id="${delivery.id}">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            ` : ''}
                            ${delivery.status === 'pending' ? `
                            <button class="btn-action delete" type="button" data-action="delete" data-delivery-id="${delivery.id}">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                            ` : ''}
                        </td>
                    </tr>
                `;
            }).join('');

            attachActionButtons();
            updatePageInfo();
        }

        function renderEmptyState() {
            elements.tableBody.innerHTML = '<tr><td colspan="9" style="text-align: center; padding: 60px; color: #7f8c8d;">No jobs found</td></tr>';
            updatePageInfo();
        }

        function attachActionButtons() {
            elements.tableBody.querySelectorAll('button[data-action]').forEach(button => {
                const id = button.dataset.deliveryId;
                const action = button.dataset.action;
                button.onclick = null;
                if (action === 'view') {
                    button.addEventListener('click', () => window.location.href = `/company/dashboard/deliveries/${id}`);
                } 
                else if (action === 'edit') {
                    button.addEventListener('click', () => window.location.href = `/company/dashboard/edit-deliveries/${id}`);
                } 
                else if (action === 'delete') {
                    button.addEventListener('click', () => deleteDelivery(id));
                }
            });
        }

        function updatePageInfo() {
            const start = currentDeliveries.length === 0 ? 0 : (currentPage - 1) * itemsPerPage + 1;
            const end = currentDeliveries.length === 0 ? 0 : (currentPage - 1) * itemsPerPage + currentDeliveries.length;
            elements.pageInfo.textContent = `Showing ${start}-${end} of ${totalDeliveries}`;
        }

        function updatePagination() {
            elements.prevBtn.disabled = currentPage === 1;
            elements.nextBtn.disabled = currentPage >= lastPage || lastPage === 0;
            elements.pageNumbers.innerHTML = '';

            if (lastPage <= 1) {
                return;
            }

            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(lastPage, currentPage + 2);

            if (startPage > 1) {
                appendPageButton(1);
                if (startPage > 2) appendEllipsis();
            }

            for (let i = startPage; i <= endPage; i++) {
                appendPageButton(i, i === currentPage);
            }

            if (endPage < lastPage) {
                if (endPage < lastPage - 1) appendEllipsis();
                appendPageButton(lastPage);
            }
        }

        function appendPageButton(page, active = false) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = `pagination-btn ${active ? 'active' : ''}`;
            btn.textContent = page;
            btn.addEventListener('click', () => loadDeliveries(page));
            elements.pageNumbers.appendChild(btn);
        }

        function appendEllipsis() {
            const dots = document.createElement('span');
            dots.textContent = '...';
            dots.style.color = '#7f8c8d';
            dots.style.padding = '0 4px';
            elements.pageNumbers.appendChild(dots);
        }

        function applyFilters() {
            loadDeliveries(1);
        }

        function nextPage() {
            if (currentPage < lastPage) {
                loadDeliveries(currentPage + 1);
            }
        }

        function previousPage() {
            if (currentPage > 1) {
                loadDeliveries(currentPage - 1);
            }
        }

        function deleteDelivery(id) {
            showConfirmDialog('Are you sure you want to delete this job?', 'warning', 'Confirm Delete', async function() {
                try {
                    const response = await fetch(`/api/v1/deliveries/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        }
                    });
                    const data = await response.json();
                    if (data.success) {
                        showDialog('Job deleted successfully!', 'success');
                        loadDeliveries(currentPage);
                        loadStatistics();
                    } else {
                        showDialog('Failed to delete job: ' + data.message, 'error');
                    }
                } catch (error) {
                    showDialog('Error deleting job', 'error');
                    console.error(error);
                }
            });
        }

        elements.status.addEventListener('change', applyFilters);
        elements.priority.addEventListener('change', applyFilters);
        elements.search.addEventListener('keyup', applyFilters);
        // elements.pageSize.addEventListener('change', function() {
        //     itemsPerPage = Number(this.value) || 10;
        //     loadDeliveries(1);
        // });
        elements.prevBtn.addEventListener('click', previousPage);
        elements.nextBtn.addEventListener('click', nextPage);

        loadDeliveries(1);
        loadStatistics();


        const current_company_id = '{{ auth()->id() }}';
        document.addEventListener('DOMContentLoaded', () => {
            window.Echo.private('deliveries')
                .listen('DeliveryStatusUpdated', (e) => {
                    console.log('DeliveryStatusUpdated', e);
                    if(current_company_id  == e.created_by){
                        $('#deliveriesTable tbody tr').each(function() {
                            let delivery_id = $(this).attr('data-id');
                            if (delivery_id == e.delivery_id) {
                                let status_class = getStatusClass(e.status);
                                let format_status = formatStatus(e.status);
                                $(this).find('td:eq(3)').html('<span class="badge '+status_class+'">'+format_status+'</span>');
                            }
                        });
                    }
                });
        });
    })();
</script>
@endsection
