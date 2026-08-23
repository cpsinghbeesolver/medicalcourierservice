@extends('common.layout')

@section('title', 'Job Details')
@section('page-title', 'Job Details')

@section('styles')
<style>
    

    .detail-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        padding: 30px;
        margin-bottom: 25px;
    }

    .detail-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 20px;
        border-bottom: 2px solid #f0f0f0;
        margin-bottom: 30px;
    }

    .detail-header h3 {
        font-size: 20px;
        font-weight: 600;
        color: #2c3e50;
    }

    .detail-actions {
        display: flex;
        gap: 10px;
    }

    .btn-action-primary {
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

    .btn-action-primary:hover {
        background: #96a048;
    }

    .btn-action-danger {
        padding: 10px 20px;
        background: #e74c3c;
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

    .btn-action-danger:hover {
        background: #c0392b;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        margin-bottom: 30px;
    }

    .info-section h4 {
        font-size: 16px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f0f0f0;
    }

    .info-row {
        display: flex;
        padding: 12px 0;
        border-bottom: 1px solid #f8f9fa;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        flex: 0 0 140px;
        font-size: 14px;
        color: #7f8c8d;
        font-weight: 500;
    }

    .info-value {
        flex: 1;
        font-size: 14px;
        color: #2c3e50;
        font-weight: 400;
    }

    .badge {
        padding: 5px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
        display: inline-block;
    }

    .badge.pending {
        background: #fff3cd;
        color: #856404;
    }

    .badge.assigned {
        background: #d1ecf1;
        color: #0c5460;
    }

    .badge.in_transit,
    .badge.picked_up {
        background: #cfe2ff;
        color: #084298;
    }

    .badge.delivered {
        background: #d1e7dd;
        color: #0f5132;
    }

    .badge.cancelled,
    .badge.failed {
        background: #f8d7da;
        color: #842029;
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

    .instructions-box {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        border-left: 4px solid #a8b456;
        margin-top: 20px;
    }

    .instructions-box h4 {
        font-size: 14px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 10px;
    }

    .instructions-box p {
        font-size: 14px;
        color: #2c3e50;
        line-height: 1.6;
        margin: 0;
    }

    .loading-state {
        text-align: center;
        padding: 60px;
        color: #7f8c8d;
    }

    .full-width {
        grid-column: 1 / -1;
    }

    .items-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    .items-table th,
    .items-table td {
        padding: 12px 10px;
        border-bottom: 1px solid #f0f0f0;
        text-align: left;
        vertical-align: top;
    }

    .items-table th {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #7f8c8d;
        background: #f8f9fa;
    }

    .verification-list {
        display: grid;
        gap: 12px;
        margin-top: 10px;
    }

    .verification-card {
        padding: 16px;
        background: #fcfcfc;
        border: 1px solid #e9ecef;
        border-radius: 8px;
    }

    .empty-state {
        text-align: center;
        padding: 24px;
        border: 1px dashed #e0e0e0;
        border-radius: 8px;
        color: #7f8c8d;
        background: #f8f9fa;
        margin-top: 10px;
    }

    /* Driver Selection Modal */
    .assign-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 10000;
        align-items: center;
        justify-content: center;
    }

    .assign-modal.show {
        display: flex;
    }

    .assign-modal-content {
        background: white;
        border-radius: 12px;
        width: 90%;
        max-width: 600px;
        max-height: 80vh;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .assign-modal-header {
        padding: 25px 30px;
        border-bottom: 1px solid #e0e0e0;
    }

    .assign-modal-header h3 {
        font-size: 20px;
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
    }

    .assign-modal-body {
        padding: 20px 30px;
        overflow-y: auto;
        flex: 1;
    }

    .driver-search {
        margin-bottom: 20px;
    }

    .driver-search input {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
    }

    .driver-search input:focus {
        border-color: #a8b456;
    }

    .driver-item {
        padding: 15px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        margin-bottom: 12px;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .driver-item:hover {
        border-color: #a8b456;
        background: #f8faf5;
    }

    .driver-item.selected {
        border-color: #a8b456;
        background: #f8faf5;
    }

    .driver-avatar-small {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #a8b456;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 18px;
        flex-shrink: 0;
    }

    .driver-item-info {
        flex: 1;
    }

    .driver-item-name {
        font-size: 15px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 4px;
    }

    .driver-item-status {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        color: #7f8c8d;
    }

    .driver-item-status .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .driver-item-status.available .dot {
        background: #10B981;
    }

    .driver-item-status.busy .dot {
        background: #F59E0B;
    }

    .driver-item-status.off_duty .dot {
        background: #6B7280;
    }

    .assign-modal-footer {
        padding: 20px 30px;
        border-top: 1px solid #e0e0e0;
        display: flex;
        gap: 10px;
        justify-content: flex-end;
    }

    .btn-modal {
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-modal-cancel {
        background: #e9ecef;
        color: #2c3e50;
    }

    .btn-modal-cancel:hover {
        background: #dee2e6;
    }

    .btn-modal-assign {
        background: #a8b456;
        color: white;
    }

    .btn-modal-assign:hover {
        background: #96a048;
    }

    .btn-modal-assign:disabled {
        background: #d1d5db;
        cursor: not-allowed;
    }

    .no-drivers {
        text-align: center;
        padding: 40px;
        color: #7f8c8d;
    }
    .signature_image{
        width: 250px;
    }
</style>
@endsection

@section('content')
<a href="/company/dashboard/deliveries" class="back-button">
    <i class="fas fa-arrow-left"></i> Back to Jobs
</a>

<div class="detail-card" id="deliveryCard">
    <div class="loading-state">
        <i class="fas fa-spinner fa-spin" style="font-size: 32px; color: #a8b456;"></i>
        <p style="margin-top: 15px;">Loading job details...</p>
    </div>
</div>

<!-- Assign Driver Modal -->
<div class="assign-modal" id="assignModal">
    <div class="assign-modal-content">
        <div class="assign-modal-header">
            <h3>Assign Driver</h3>
        </div>
        <div class="assign-modal-body">
            <div class="driver-search">
                <input type="text" id="driverSearch" placeholder="Search drivers by name...">
            </div>
            <div id="driversList"></div>
        </div>
        <div class="assign-modal-footer">
            <button class="btn-modal btn-modal-cancel" onclick="closeAssignModal()">Cancel</button>
            <button class="btn-modal btn-modal-assign" id="btnAssignDriver" onclick="confirmAssignDriver()" disabled>Assign Driver</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // const token = localStorage.getItem('api_token');
    const token = '{{ session("web_token") }}';
    const deliveryId = {{ $id }};

    async function loadDeliveryDetails() {
        try {
            const response = await fetch(`/api/v1/deliveries/${deliveryId}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            console.log('API Response:', data);

            if (data.success && data.data) {
                // const delivery = data.data.delivery || data.data;
                const delivery = data.data;
                // console.log('Delivery object:', data);
                displayDelivery(delivery);
                if(delivery.status == 'pending' ||  delivery.status == 'assigned'){
                    $('.assign-driver').show();
                }else{
                    $('.assign-driver').hide();
                }
            } else {
                throw new Error(data.message || 'Failed to load delivery data');
            }
        } catch (error) {
            console.error('Error loading delivery details:', error);
            document.getElementById('deliveryCard').innerHTML = `
                <div class="loading-state">
                    <i class="fas fa-exclamation-triangle" style="font-size: 32px; color: #e74c3c;"></i>
                    <p style="margin-top: 15px; color: #e74c3c;">Error loading job details</p>
                    <p style="margin-top: 10px; color: #7f8c8d; font-size: 13px;">${error.message}</p>
                </div>
            `;
        }
    }

    function escapeHtml(value, fallback = 'N/A') {
        if (value === null || value === undefined || value === '') {
            return fallback;
        }

        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function formatValue(value, fallback = 'N/A') {
        if (value === null || value === undefined || value === '') {
            return fallback;
        }

        return escapeHtml(value);
    }
    function formatValueString(value) {
        if (!value) return 'N/A';

        return value
            .replace(/_/g, ' ')
            .replace(/^./, char => char.toUpperCase());
    }

    function formatBoolean(value) {
        return value ? 'Yes' : 'No';
    }

    function formatDate(value) {
        if (!value) return 'N/A';

        const date = new Date(value);

        return isNaN(date.getTime())
            ? 'N/A'
            : date.toLocaleString('en-US', {
                timeZone: 'UTC',
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true,
            });
    }

    function formatLocation(location) {
        if (!location) return 'N/A';
        const latitude = location.latitude ?? location.lat;
        const longitude = location.longitude ?? location.lng;

        if (latitude === null || latitude === undefined || latitude === '' || longitude === null || longitude === undefined || longitude === '') {
            return 'N/A';
        }

        return `${latitude}, ${longitude}`;
    }

    function renderItems(items = []) {
        if (!Array.isArray(items) || items.length === 0) {
            return '<div class="empty-state">No items were attached to this delivery.</div>';
        }

        return `
            <div class="info-grid">
                ${items.map(item => `
                    <div class="verification-card">
                        <div class="info-row">
                            <div class="info-label">Item:</div>
                            <div class="info-value"><strong>${formatValue(item.item_name || item.description || item.item_type || 'Unnamed item')}</strong></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Specimen:</div>
                            <div class="info-value">${formatValue(item.specimen_name || item.specimen_type)}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Specimen ID:</div>
                            <div class="info-value">${formatValue(item.barcode)}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Drop Off Location:</div>
                            <div class="info-value">${formatValue(item.dropoff_name)}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Drop Off Address:</div>
                            <div class="info-value">${formatValue(item.dropoff_address)}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Drop Off Phone:</div>
                            <div class="info-value">${formatValue(item.dropoff_phone)}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Drop Off Contact Person:</div>
                            <div class="info-value">${formatValue(item.dropoff_contact_person)}</div>
                        </div>
                        
                        <div class="info-row">
                            <div class="info-label">Temperature Requirement:</div>
                            <div class="info-value">${formatValue(item.temperature_requirement_name || item.temperature_requirement)}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Handling:</div>
                            <div class="info-value">${item.requires_special_handling ? '<span class="badge urgent">Requires special handling</span>' : formatValue(item.handling_instructions || 'Standard handling')}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Description:</div>
                            <div class="info-value">${formatValue(item.description)}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Status:</div>
                            <div class="info-value">${formatValue(item.status)}</div>
                        </div>
                        ${item.signature_image ? `
                        <div class="info-row">
                            <div class="info-label">Signature Image:</div>
                            <div class="info-value"><img class="signature_image" src="${item.signature_image}" /></div>
                        </div>
                        ` : ''}
                        ${item.barcode ? `
                            <div class="info-row">
                                <div class="info-label">Barcode:</div>
                                <div class="info-value">${item.barcode}</div>
                            </div>
                        ` : ''}
                    </div>
                `).join('')}
            </div>
        `;
    }

    function renderVerifications(verifications = []) {
        if (!Array.isArray(verifications) || verifications.length === 0) {
            return '<div class="empty-state">No verification records available yet.</div>';
        }

        return `
            <div class="verification-list">
                ${verifications.map(verification => `
                    <div class="verification-card">
                        <div class="info-row">
                            <div class="info-label">Type:</div>
                            <div class="info-value">${formatValue(verification.verification_type)}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Recipient:</div>
                            <div class="info-value">${formatValue(verification.recipient_name)}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Verified At:</div>
                            <div class="info-value">${formatDate(verification.verified_at)}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Location:</div>
                            <div class="info-value">${formatLocation(verification.location)}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Notes:</div>
                            <div class="info-value">${formatValue(verification.notes)}</div>
                        </div>
                    </div>
                `).join('')}
            </div>
        `;
    }

    function displayDelivery(delivery) {
        const card = document.getElementById('deliveryCard');
        const pickup = delivery.pickup || {};
        const dropoff = delivery.delivery || {};
        const items = Array.isArray(delivery.items) ? delivery.items : [];
        const verifications = Array.isArray(delivery.verifications) ? delivery.verifications : [];
        const vehicleRequirement = delivery.vehicle_requirement?.name || (delivery.required_vehicle_type ? `ID ${delivery.required_vehicle_type}` : null);

        card.innerHTML = `
            <div class="detail-header">
                <h3>${formatValue(delivery.delivery_number, 'Delivery')}</h3>
                <div class="detail-actions">
                    <button onclick="assignDriver()" class="btn-action-primary assign-driver">
                        <i class="fas fa-user-plus"></i> Assign Driver
                    </button>
                    ${
                        delivery.status === 'cancelled'
                            ? `
                            <button onclick="resumeDelivery(${delivery.id})" class="btn-action-primary">
                                <i class="fas fa-play-circle"></i> Resume Delivery
                            </button>
                            `
                            : delivery.status !== 'delivered'
                            ? `
                            <button onclick="cancelDelivery(${delivery.id})" class="btn-action-danger">
                                <i class="fas fa-times-circle"></i> Cancel Job
                            </button>
                            `
                            : ''
                    }
                </div>
            </div>

            <div class="info-grid">
                <div class="info-section">
                    <h4>Basic Information</h4>
                    <div class="info-row">
                        <div class="info-label">Job Number:</div>
                        <div class="info-value"><strong>${formatValue(delivery.delivery_number, 'N/A')}</strong></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Job Title / Reference:</div>
                        <div class="info-value">${formatValue(pickup.name)}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Status:</div>
                        <div class="info-value">
                            <span class="badge ${delivery.status || 'pending'}">${formatValue(delivery.status ? delivery.status.replace('_', ' ') : 'N/A')}</span>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Priority:</div>
                        <div class="info-value">
                            <span class="badge ${delivery.priority || 'normal'}">${formatValue(delivery.priority || 'N/A')}</span>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Urgency Level:</div>
                        <div class="info-value">${formatValueString(delivery.urgency_level)}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Vehicle Requirement:</div>
                        <div class="info-value">${formatValue(vehicleRequirement)}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Containers / Bags:</div>
                        <div class="info-value">${formatValue(delivery.container_count)}</div>
                    </div>
                </div>

                <div class="info-section">
                    <h4>Driver Information</h4>
                    <div class="info-row">
                        <div class="info-label">Driver Name:</div>
                        <div class="info-value">${delivery.driver ? formatValue(delivery.driver.name, 'Not Assigned') : '<span style="color: #95a5a6;">Not Assigned</span>'}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Phone:</div>
                        <div class="info-value">${delivery.driver ? formatValue(delivery.driver.phone) : 'N/A'}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Email:</div>
                        <div class="info-value">${delivery.driver ? formatValue(delivery.driver.email) : 'N/A'}</div>
                    </div>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-section">
                    <h4>Schedule & Time Window</h4>
                    <div class="info-row">
                        <div class="info-label">Pickup Window Start:</div>
                        <div class="info-value">${formatDate(delivery.scheduled_time_window_start)}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Delivery Deadline:</div>
                        <div class="info-value">${formatDate(delivery.scheduled_time_window_end)}</div>
                    </div>
                </div>
                <div class="info-section">
                    <h4>Pickup Location</h4>
                    <div class="info-row">
                        <div class="info-label">Name:</div>
                        <div class="info-value">${formatValue(pickup.name)}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Address:</div>
                        <div class="info-value">${formatValue(pickup.address)}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Phone:</div>
                        <div class="info-value">${formatValue(pickup.phone)}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Scheduled:</div>
                        <div class="info-value">${formatDate(pickup.scheduled_time)}</div>
                    </div>
                    <!--div class="info-row">
                        <div class="info-label">Actual:</div>
                        <div class="info-value">${formatDate(pickup.actual_time)}</div>
                    </div-->
                </div>
            </div>


            <div class="info-grid">
                <div class="info-section full-width">
                    <h4>Delivery Items</h4>
                    ${renderItems(items)} 
                </div>
            </div>

            <div class="info-grid">
                <div class="info-section full-width">
                    <h4>Delivery Notes & Instructions</h4>
                    <div class="info-row">
                        <div class="info-label">Special Instructions:</div>
                        <div class="info-value">${formatValue(delivery.special_instructions)}</div>
                    </div>
                </div>
            </div>


            <!--div class="info-grid">
                <div class="info-section full-width">
                    <h4>Verification Records</h4>
                    ${renderVerifications(verifications)}
                </div>
            </div-->
        `;
    }

    let availableDrivers = [];
    let selectedDriverId = null;

    async function assignDriver() {
        // Show modal
        document.getElementById('assignModal').classList.add('show');

        // Load available drivers
        await loadAvailableDrivers();
    }

    async function loadAvailableDrivers() {
        const driversList = document.getElementById('driversList');
        driversList.innerHTML = '<div style="text-align: center; padding: 20px; color: #7f8c8d;"><i class="fas fa-spinner fa-spin"></i> Loading drivers...</div>';

        try {
            // Fetch all users with driver role
            const response = await fetch('/api/v1/users?role_id=4&per_page=100', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success && data.data && data.data.users) {
                availableDrivers = data.data.users;
                displayDriversList(availableDrivers);
            } else {
                driversList.innerHTML = '<div class="no-drivers"><i class="fas fa-user-times" style="font-size: 32px; margin-bottom: 10px; opacity: 0.3;"></i><p>No drivers available</p></div>';
            }
        } catch (error) {
            console.error('Error loading drivers:', error);
            driversList.innerHTML = '<div class="no-drivers" style="color: #e74c3c;"><i class="fas fa-exclamation-triangle" style="font-size: 32px; margin-bottom: 10px;"></i><p>Error loading drivers</p></div>';
        }
    }

    function displayDriversList(drivers) {
        const driversList = document.getElementById('driversList');

        if (drivers.length === 0) {
            driversList.innerHTML = '<div class="no-drivers"><i class="fas fa-search" style="font-size: 32px; margin-bottom: 10px; opacity: 0.3;"></i><p>No drivers found</p></div>';
            return;
        }

        driversList.innerHTML = drivers.map(driver => {
            const status = driver.driver_profile?.availability_status || 'off_duty';
            const statusText = status.replace('_', ' ').toUpperCase();

            return `
                <div class="driver-item" data-driver-id="${driver.id}" onclick="selectDriver(${driver.id})">
                    <div class="driver-avatar-small">${driver.name.charAt(0).toUpperCase()}</div>
                    <div class="driver-item-info">
                        <div class="driver-item-name">${driver.name}</div>
                        <div class="driver-item-status ${status}">
                            <span class="dot"></span>
                            ${statusText}
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    function selectDriver(driverId) {
        // Remove selected class from all items
        document.querySelectorAll('.driver-item').forEach(item => {
            item.classList.remove('selected');
        });

        // Add selected class to clicked item
        const selectedItem = document.querySelector(`[data-driver-id="${driverId}"]`);
        if (selectedItem) {
            selectedItem.classList.add('selected');
        }

        // Store selected driver ID
        selectedDriverId = driverId;

        // Enable assign button
        document.getElementById('btnAssignDriver').disabled = false;
    }

    async function confirmAssignDriver() {
        if (!selectedDriverId) {
            showDialog('Please select a driver', 'warning');
            return;
        }

        const btnAssign = document.getElementById('btnAssignDriver');
        btnAssign.disabled = true;
        btnAssign.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Assigning...';

        try {
            const response = await fetch(`/api/v1/deliveries/${deliveryId}/assign`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    driver_id: selectedDriverId
                })
            });

            const data = await response.json();

            if (data.success) {
                closeAssignModal();
                showDialog('Driver assigned successfully!', 'success', '', () => {
                    loadDeliveryDetails();
                });
            } else {
                closeAssignModal();
                showDialog(data.message || 'Failed to assign driver', 'error');
                btnAssign.disabled = false;
                btnAssign.textContent = 'Assign Driver';
            }
        } catch (error) {
            console.error('Error assigning driver:', error);
            showDialog('Error assigning driver', 'error');
            btnAssign.disabled = false;
            btnAssign.textContent = 'Assign Driver';
        }
    }

    function closeAssignModal() {
        document.getElementById('assignModal').classList.remove('show');
        selectedDriverId = null;
        document.getElementById('btnAssignDriver').disabled = true;
        document.getElementById('btnAssignDriver').textContent = 'Assign Driver';
        document.getElementById('driverSearch').value = '';
    }

    // Driver search functionality
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('driverSearch');
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                const filteredDrivers = availableDrivers.filter(driver =>
                    driver.name.toLowerCase().includes(searchTerm)
                );
                displayDriversList(filteredDrivers);
            });
        }

        // Close modal when clicking outside
        const modal = document.getElementById('assignModal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeAssignModal();
                }
            });
        }
    });

    function cancelDelivery() {
        showConfirmDialog('Are you sure you want to cancel this job?', 'warning', 'Confirm Cancel', function() {
            fetch(`/api/v1/deliveries/${deliveryId}/cancel`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            }).then(response => response.json())
              .then(data => {
                  if (data.success) {
                      showDialog('Job cancelled successfully', 'success');
                      loadDeliveryDetails();
                  } else {
                      showDialog('Failed to cancel job: ' + data.message, 'error');
                  }
              })
              .catch(error => {
                  showDialog('Error cancelling job', 'error');
                  console.error(error);
              });
        });
    }

    function resumeDelivery() {
        showConfirmDialog('Are you sure you want to cancel this job?', 'warning', 'Confirm Cancel', function() {
            fetch(`/api/v1/deliveries/${deliveryId}/resume`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            }).then(response => response.json())
              .then(data => {
                  if (data.success) {
                      showDialog('Job resumed successfully', 'success');
                      loadDeliveryDetails();
                  } else {
                      showDialog('Failed to cancel job: ' + data.message, 'error');
                  }
              })
              .catch(error => {
                  showDialog('Error cancelling job', 'error');
                  console.error(error);
              });
        });
    }
    

    loadDeliveryDetails();
</script>
@endsection
