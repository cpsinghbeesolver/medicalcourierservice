@extends('common.layout')

@section('title', 'Driver Management')
@section('page-title', 'Driver Management')

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
        min-width: 250px;
    }

    .filter-group label {
        display: block;
        font-size: 13px;
        color: #7f8c8d;
        margin-bottom: 6px;
        font-weight: 500;
    }

    .filter-group input {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
    }

    .filter-group input:focus {
        border-color: #a8b456;
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
        text-decoration: none;
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

    .data-table tbody tr {
        cursor: pointer;
        transition: background 0.2s;
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

    .badge.available {
        background: #d1e7dd;
        color: #0f5132;
    }

    .badge.busy {
        background: #fff3cd;
        color: #856404;
    }

    .badge.offline,
    .badge.off_duty {
        background: #f8d7da;
        color: #842029;
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
<!-- Filters -->
 <div class="filters-bar">
    <div class="filter-group">
        <label>Availibity Status</label>
        <select id="filterAvailibityStatus" onchange="filterAvailibitystatus()">
            <option value="">Select</option>
            <option value="available">Available</option>
            <option value="busy">Busy</option>
            <option value="off_duty">Off Duty</option>        
        </select>
    </div>
    <div class="filter-group">
        <label>Status</label>
        <select id="filterStatus" onchange="filterStatus()">
            <option value="">Select</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="suspended">Suspended</option>
        </select>
    </div>
    <div class="filter-group">

        <label>Search</label>
         <div class="search-bar">
            <i class="fas fa-search"></i>
        <input type="text" id="searchInput" onkeyup="filterDrivers()" placeholder="Search by name, email, vehicle...">
    </div></div>
    <!-- <button class="btn-filter" onclick="applyFilters()">Apply Filters</button> -->
</div>
<!-- <div class="filters-bar">
    <div class="filter-group">
        <label>Search Drivers</label>
        <input type="text" id="searchInput" placeholder="Search by name, email, vehicle..." onkeyup="filterDrivers()">
    </div>
</div> -->

<!-- Data Table -->
<div class="data-card">
    <div class="data-card-header">
        <h3>All Drivers</h3>
        <!-- <a href="/company/dashboard/drivers/create" class="btn-create">
            <i class="fas fa-plus"></i> Add New Driver
        </a> -->
    </div>
    <div class="table-container">
        <table class="data-table" id="driversTable">
            <thead>
                <tr>
                    <th>Sr No.</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Availability</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="10" style="text-align: center; padding: 60px;">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="pagination-container">
        <div class="pagination-info">
            <span id="pageInfo">Showing 0 of 0</span>
        </div>
        <div class="pagination-controls">
            <button class="pagination-btn" id="prevBtn" onclick="previousPage()">← Previous</button>
            <div id="pageNumbers" style="display: flex; gap: 4px;"></div>
            <button class="pagination-btn" id="nextBtn" onclick="nextPage()">Next →</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const token = '{{ session("web_token") }}';
    let currentDrivers = [];
    let currentPage = 1;
    let totalPages = 1;
    let totalDrivers = 0;
    const itemsPerPage = 10;
    let currentFilters = {
        availability_status: '',
        status: '',
        search: ''
    };

    async function loadDrivers(page = 1) {
        try {
            currentPage = page;
            const params = new URLSearchParams({
                page: page,
                per_page: itemsPerPage
            });
            const urlParams = new URLSearchParams(window.location.search);
            const get_status = urlParams.get('status');
            if(get_status){
                $('#filterAvailibityStatus').val(get_status);
                params.set('availability_status', get_status);
            }
            if (currentFilters.availability_status) {
                params.set('availability_status', currentFilters.availability_status);
            }
            if (currentFilters.status) {
                params.set('status', currentFilters.status);
            }
            if (currentFilters.search) {
                params.set('search', currentFilters.search);
            }

            const response = await fetch(`/api/v1/driver-profiles?${params.toString()}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                currentDrivers = data.data.profiles || [];
                totalDrivers = data.data.pagination?.total ?? currentDrivers.length;
                totalPages = data.data.pagination?.last_page ?? 1;
                currentPage = data.data.pagination?.current_page ?? currentPage;
                renderDrivers(currentDrivers);
                updatePagination();
            } else {
                console.error('Failed to load drivers', data);
            }
        } catch (error) {
            console.error('Error loading drivers:', error);
        }
    }

    function renderDrivers(drivers) {
        const tbody = document.querySelector('#driversTable tbody');
        const start = (currentPage - 1) * itemsPerPage;

        if (!drivers || drivers.length === 0) {
            tbody.innerHTML = '<tr><td colspan="10" style="text-align: center; padding: 60px; color: #7f8c8d;">No drivers found</td></tr>';
            updatePageInfo();
            return;
        }

        tbody.innerHTML = drivers.map((driver, index) => `
            <tr data-id="${driver.id}" onclick="viewDriver(${driver.id})">
                <td>${start + index + 1}</td>
                <td><strong>${driver.user ? driver.user.name : 'N/A'}</strong></td>
                <td>${driver.user ? driver.user.email : 'N/A'}</td>
                <td>${driver.user ? driver.user.phone : 'N/A'}</td>
                <td><span class="badge ${driver.availability_status || 'offline'}">${driver.availability_status ? driver.availability_status.replace(/_/g, ' ').replace(/\b\w/g, char => char.toUpperCase()): 'OFFLINE'}</span></td>
                <td onclick="event.stopPropagation()">
                    <button class="btn-action" onclick="viewDriver(${driver.id})">
                        <i class="fas fa-eye"></i> View
                    </button>
                    <button class="btn-action edit" type="button" data-action="edit" onclick="editDriver(${driver.id})">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                </td>
            </tr>
        `).join('');

        updatePageInfo();
    }

    function updatePageInfo() {
        const start = totalDrivers === 0 ? 0 : (currentPage - 1) * itemsPerPage + 1;
        const end = Math.min(currentPage * itemsPerPage, totalDrivers);
        document.getElementById('pageInfo').textContent = `Showing ${start}-${end} of ${totalDrivers}`;
    }

    function updatePagination() {
        const pageNumbersDiv = document.getElementById('pageNumbers');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');

        prevBtn.disabled = currentPage === 1;
        nextBtn.disabled = currentPage === totalPages || totalPages === 0;

        pageNumbersDiv.innerHTML = '';
        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(totalPages, currentPage + 2);

        if (startPage > 1) {
            const btn = document.createElement('button');
            btn.className = 'pagination-btn';
            btn.textContent = '1';
            btn.onclick = () => loadDrivers(1);
            pageNumbersDiv.appendChild(btn);

            if (startPage > 2) {
                const dots = document.createElement('span');
                dots.textContent = '...';
                dots.style.color = '#7f8c8d';
                dots.style.padding = '0 4px';
                pageNumbersDiv.appendChild(dots);
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            const btn = document.createElement('button');
            btn.className = `pagination-btn ${i === currentPage ? 'active' : ''}`;
            btn.textContent = i;
            btn.onclick = () => loadDrivers(i);
            pageNumbersDiv.appendChild(btn);
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                const dots = document.createElement('span');
                dots.textContent = '...';
                dots.style.color = '#7f8c8d';
                dots.style.padding = '0 4px';
                pageNumbersDiv.appendChild(dots);
            }
            const btn = document.createElement('button');
            btn.className = 'pagination-btn';
            btn.textContent = totalPages;
            btn.onclick = () => loadDrivers(totalPages);
            pageNumbersDiv.appendChild(btn);
        }
    }

    function nextPage() {
        if (currentPage < totalPages) {
            loadDrivers(currentPage + 1);
        }
    }

    function previousPage() {
        if (currentPage > 1) {
            loadDrivers(currentPage - 1);
        }
    }

    function filterAvailibitystatus() {
        currentFilters.availability_status = document.getElementById('filterAvailibityStatus').value.toLowerCase();
        loadDrivers(1);
    }

    function filterStatus() {
        currentFilters.status = document.getElementById('filterStatus').value.toLowerCase();
        loadDrivers(1);
    }

    function filterDrivers() {
        currentFilters.search = document.getElementById('searchInput').value.toLowerCase();
        loadDrivers(1);
    }

    document.getElementById('searchInput').addEventListener('keyup', filterDrivers);

    function viewDriver(id) {
        window.location.href = `/company/dashboard/drivers/${id}`;
    }

    function editDriver(id) {
        window.location.href = `/company/dashboard/drivers/${id}/edit`;
    }

    loadDrivers();

    const current_company_id = '{{ auth()->id() }}';
    document.addEventListener('DOMContentLoaded', () => {
        Echo.channel('drivers')
            .subscribed(() => {
                console.log('Subscribed to drivers channel');
            })
            .listen('DriverStatusUpdated', (e) => {
                console.log('DriverStatusUpdated', e);
                if(current_company_id  == e.driver.created_by){
                    $('#driversTable tbody tr').each(function() {
                        let driver_id = $(this).attr('data-id');
                        // console.log('email', email);
                        if (driver_id == e.driver.id) {
                            $(this).find('td:eq(4)').html('<span class="badge '+e.driver.availability_status+'">'+e.driver.availability_status+'</span>');
                            console.log('Found driver');
                        }
                    });
                }
            });
    });
</script>
@endsection
