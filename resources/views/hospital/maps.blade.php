@extends('common.layout')

@section('title', 'Live Driver Tracking')
@section('page-title', 'Maps - Live Driver Tracking')

@section('styles')
<!-- Google Maps API - Get your FREE key at: https://console.cloud.google.com/google/maps-apis/start -->
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&libraries=geometry"></script>
<style>
    .maps-container {
        display: flex;
        gap: 20px;
        height: calc(100vh - 200px);
    }

    .maps-sidebar {
        width: 320px;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        padding: 20px;
        overflow-y: auto;
    }

    .maps-main {
        flex: 1;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        overflow: hidden;
        position: relative;
    }

    .driver-selector {
        margin-bottom: 25px;
    }

    .driver-selector label {
        display: block;
        font-size: 13px;
        color: #2c3e50;
        margin-bottom: 8px;
        font-weight: 600;
    }

    .driver-selector select {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
        transition: border-color 0.3s;
        background: white;
    }

    .driver-selector select:focus {
        border-color: #a8b456;
    }

    .driver-info-card {
        background: #f8f9fa;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        display: none;
    }

    .driver-info-card.show {
        display: block;
    }

    .driver-avatar {
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
        margin-bottom: 10px;
    }

    .driver-name {
        font-size: 16px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 5px;
    }

    .driver-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .driver-status.active {
        background: #d1f4e0;
        color: #0f5132;
    }

    .driver-status.available {
        background: #cfe2ff;
        color: #084298;
    }

    .driver-status.busy {
        background: #fff3cd;
        color: #856404;
    }

    .driver-status.off_duty {
        background: #e9ecef;
        color: #495057;
    }

    .status-indicator {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: currentColor;
    }

    .deliveries-section {
        margin-top: 20px;
    }

    .section-title {
        font-size: 14px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-title i {
        color: #a8b456;
    }

    .delivery-list-item {
        background: #ffffff;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .delivery-list-item:hover {
        border-color: #a8b456;
        box-shadow: 0 2px 8px rgba(168, 180, 86, 0.2);
    }

    .delivery-list-item.active {
        border-color: #a8b456;
        background: #f8faf5;
    }

    .delivery-list-item.live {
        border-color: #10B981;
        background: #ecfdf5;
    }

    .delivery-number {
        font-size: 13px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 5px;
    }

    .delivery-route {
        font-size: 12px;
        color: #7f8c8d;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .delivery-route i {
        font-size: 10px;
    }

    .live-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 8px;
        background: #10B981;
        color: white;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        margin-top: 5px;
    }

    .live-badge .pulse {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: white;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.3; }
    }

    .map-canvas {
        width: 100%;
        height: 100%;
    }

    .map-legend {
        position: absolute;
        bottom: 20px;
        right: 20px;
        background: white;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        font-size: 12px;
    }

    .legend-title {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 10px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 6px;
    }

    .legend-icon {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 2px solid white;
        box-shadow: 0 1px 3px rgba(0,0,0,0.3);
    }

    .legend-icon.driver {
        background: #3B82F6;
    }

    .legend-icon.pickup {
        background: #10B981;
    }

    .legend-icon.delivery {
        background: #EF4444;
    }

    .legend-icon.completed {
        background: #6B7280;
    }

    .no-data {
        text-align: center;
        padding: 40px;
        color: #7f8c8d;
    }

    .refresh-button {
        position: absolute;
        top: 20px;
        right: 20px;
        background: white;
        border: none;
        padding: 10px 16px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 500;
        color: #2c3e50;
        transition: all 0.3s;
    }

    .refresh-button:hover {
        background: #f8f9fa;
    }

    .refresh-button i {
        font-size: 14px;
    }

    .refresh-button.refreshing i {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>
@endsection

@section('content')
<div class="maps-container">
    <!-- Sidebar -->
    <div class="maps-sidebar">
        <!-- Driver Selector -->
        <div class="driver-selector">
            <label>Select Driver</label>
            <select id="driverSelect">
                <option value="">Choose a driver...</option>
                @foreach($drivers as $driver)
                    <option value="{{ $driver['id'] }}">{{ $driver['title'] }}</option> 
                @endforeach
            </select>
        </div>

        <!-- Driver Info Card -->
        <div class="driver-info-card" id="driverInfoCard">
            <div class="driver-avatar" id="driverAvatar">D</div>
            <div class="driver-name" id="driverName">Driver Name</div>
            <div class="driver-status available" id="driverStatus">
                <span class="status-indicator"></span>
                <span id="driverStatusText">Available</span>
            </div>
            <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #e0e0e0;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span style="font-size: 12px; color: #7f8c8d;">Today's Deliveries:</span>
                    <span style="font-size: 13px; font-weight: 600; color: #2c3e50;" id="totalDeliveries">0</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span style="font-size: 12px; color: #7f8c8d;">In Progress:</span>
                    <span style="font-size: 13px; font-weight: 600; color: #10B981;" id="inProgressCount">0</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="font-size: 12px; color: #7f8c8d;">Completed:</span>
                    <span style="font-size: 13px; font-weight: 600; color: #6B7280;" id="completedCount">0</span>
                </div>
            </div>
        </div>

        <!-- In Progress Deliveries -->
        <div class="deliveries-section" id="inProgressDeliveriesSection" style="display: none;">
            <div class="section-title">
                <i class="fas fa-truck"></i>
                In Progress
            </div>
            <div id="inProgressDeliveriesList"></div>
        </div>

        <!-- Pending Deliveries -->
        <div class="deliveries-section" id="pendingDeliveriesSection" style="display: none;">
            <div class="section-title">
                <i class="fas fa-clock"></i>
                Pending / Assigned
            </div>
            <div id="pendingDeliveriesList"></div>
        </div>

        <!-- Completed Deliveries -->
        <div class="deliveries-section" id="completedDeliveriesSection" style="display: none;">
            <div class="section-title">
                <i class="fas fa-check-circle"></i>
                Completed (Today)
            </div>
            <div id="completedDeliveriesList"></div>
        </div>

        <div class="no-data" id="noDataMessage">
            <i class="fas fa-map-marked-alt" style="font-size: 48px; margin-bottom: 10px; opacity: 0.3;"></i>
            <p>Select a driver to view their location and deliveries</p>
        </div>
    </div>

    <!-- Map View -->
    <div class="maps-main">
        <button class="refresh-button" id="refreshButton" onclick="refreshDriverLocation()">
            <i class="fas fa-sync-alt"></i>
            Refresh Location
        </button>

        <div class="map-canvas" id="mainMap"></div>

        <div class="map-legend">
            <div class="legend-title">Legend</div>
            <div class="legend-item">
                <div class="legend-icon driver"></div>
                <span>Driver Location</span>
            </div>
            <div class="legend-item">
                <div class="legend-icon pickup"></div>
                <span>In Progress (Pickup)</span>
            </div>
            <div class="legend-item">
                <div class="legend-icon delivery"></div>
                <span>In Progress (Delivery)</span>
            </div>
            <div class="legend-item">
                <div class="legend-icon" style="background: #F59E0B;"></div>
                <span>Pending/Assigned</span>
            </div>
            <div class="legend-item">
                <div class="legend-icon completed"></div>
                <span>Completed</span>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // const token = localStorage.getItem('api_token');
    const token = '{{ session("web_token") }}';
    let map;
    let markers = [];
    let driverMarker = null;
    let selectedDriverId = null;
    let refreshInterval = null;

    // Initialize map
    function initMap() {
        // Default center (will be updated when driver is selected)
        const defaultCenter = { lat: 40.7128, lng: -74.0060 }; // New York

        map = new google.maps.Map(document.getElementById('mainMap'), {
            zoom: 12,
            center: defaultCenter,
            mapTypeControl: true,
            streetViewControl: false,
            fullscreenControl: true,
            zoomControl: true,
            styles: [
                {
                    featureType: "poi",
                    elementType: "labels",
                    stylers: [{ visibility: "off" }]
                }
            ]
        });
    }

    // Load drivers on page load
    async function loadDrivers() {
        try {
            const response = await fetch('/api/v1/driver-profiles?per_page=100', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success && data.data && data.data.profiles) {
                const select = document.getElementById('driverSelect');
                // console.log(data.data.profiles);
                data.data.profiles.forEach(profile => {
                    if (profile.user) {
                        const option = document.createElement('option');
                        option.value = profile.user.id;
                        option.textContent = profile.user.name;
                        option.dataset.status = profile.availability_status || 'off_duty';
                        select.appendChild(option);
                    }
                });
            }
        } catch (error) {
            console.error('Error loading drivers:', error);
        }
    }

    // Handle driver selection
    document.getElementById('driverSelect').addEventListener('change', async (e) => {
        const driverId = e.target.value;

        if (!driverId) {
            clearMap();
            document.getElementById('driverInfoCard').classList.remove('show');
            document.getElementById('activeDeliveriesSection').style.display = 'none';
            document.getElementById('completedDeliveriesSection').style.display = 'none';
            document.getElementById('noDataMessage').style.display = 'block';

            // Clear refresh interval
            if (refreshInterval) {
                clearInterval(refreshInterval);
                refreshInterval = null;
            }
            return;
        }

        selectedDriverId = driverId;
        document.getElementById('noDataMessage').style.display = 'none';

        // if(window.live_driver_id == driverId){
        if(1 == 1){
            await loadDriverData(driverId);
        }else{
            initMap();
        }

        // Start auto-refresh every 10 seconds for live tracking
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
        // refreshInterval = setInterval(() => {
        //     loadDriverData(driverId, true);
        // }, 10000);
    });

    async function loadDriverData(driverId, silent = false) {
        try {
            // Load driver profile
            const profileResponse = await fetch(`/api/v1/users/${driverId}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const profileData = await profileResponse.json();

            if (profileData.success && profileData.data) {
                const driver = profileData.data;
                updateDriverInfo(driver);
            }

            // Load driver deliveries
            const deliveriesResponse = await fetch(`/api/v1/deliveries?driver_id=${driverId}&per_page=50`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const deliveriesData = await deliveriesResponse.json();

            if (deliveriesData.success && deliveriesData.data) {
                const deliveries = deliveriesData.data.deliveries;
                updateDeliveriesList(deliveries);
                updateMap(profileData.data, deliveries);
            }

        } catch (error) {
            console.error('Error loading driver data:', error);
            if (!silent) {
                showDialog('Error loading driver data', 'error');
            }
        }
    }

    function updateDriverInfo(driver) {
        const avatar = document.getElementById('driverAvatar');
        const name = document.getElementById('driverName');
        const status = document.getElementById('driverStatus');
        const statusText = document.getElementById('driverStatusText');
        const infoCard = document.getElementById('driverInfoCard');

        avatar.textContent = driver.name.charAt(0).toUpperCase();
        name.textContent = driver.name;

        const driverStatus = driver.driver_profile?.availability_status || 'off_duty';
        status.className = 'driver-status ' + driverStatus;
        statusText.textContent = driverStatus.replace('_', ' ').toUpperCase();

        infoCard.classList.add('show');
    }

    function updateDeliveriesList(deliveries) {
        const inProgressList = document.getElementById('inProgressDeliveriesList');
        const pendingList = document.getElementById('pendingDeliveriesList');
        const completedList = document.getElementById('completedDeliveriesList');
        const inProgressSection = document.getElementById('inProgressDeliveriesSection');
        const pendingSection = document.getElementById('pendingDeliveriesSection');
        const completedSection = document.getElementById('completedDeliveriesSection');

        const today = new Date().toISOString().split('T')[0];

        // Filter deliveries into categories
        const inProgressDeliveries = deliveries.filter(d =>
            ['in_transit', 'picked_up'].includes(d.status)
        );

        const pendingDeliveries = deliveries.filter(d =>
            ['pending', 'assigned'].includes(d.status)
        );

        const completedDeliveries = deliveries.filter(d =>
            d.status === 'delivered' &&
            d.delivery_actual_time &&
            d.delivery_actual_time.startsWith(today)
        );

        // Update counts in driver info card
        const totalToday = inProgressDeliveries.length + pendingDeliveries.length + completedDeliveries.length;
        document.getElementById('totalDeliveries').textContent = totalToday;
        document.getElementById('inProgressCount').textContent = inProgressDeliveries.length;
        document.getElementById('completedCount').textContent = completedDeliveries.length;

        // Update In Progress deliveries
        if (inProgressDeliveries.length > 0) {
            inProgressSection.style.display = 'block';
            inProgressList.innerHTML = inProgressDeliveries.map(delivery => {
                const statusText = delivery.status === 'in_transit' ? 'En Route' : 'Picked Up';
                const statusColor = delivery.status === 'in_transit' ? '#3B82F6' : '#10B981';
                return `
                <div class="delivery-list-item live" data-delivery-id="${delivery.id}" onclick="focusDelivery(${delivery.id})">
                    <div class="delivery-number">${delivery.delivery_number}</div>
                    <div class="delivery-route">
                        <i class="fas fa-circle" style="color: #10B981;"></i>
                        ${delivery.pickup.city}
                        <i class="fas fa-arrow-right"></i>
                        <i class="fas fa-circle" style="color: #EF4444;"></i>
                        ${delivery.delivery.city}
                    </div>
                    <div class="live-badge" style="background: ${statusColor};">
                        <span class="pulse"></span>
                        ${statusText}
                    </div>
                </div>
            `}).join('');
        } else {
            inProgressSection.style.display = 'none';
        }

        // Update Pending/Assigned deliveries
        if (pendingDeliveries.length > 0) {
            pendingSection.style.display = 'block';
            pendingList.innerHTML = pendingDeliveries.map(delivery => {
                const statusText = delivery.status === 'assigned' ? 'Assigned' : 'Pending';
                const statusColor = delivery.status === 'assigned' ? '#F59E0B' : '#94A3B8';
                return `
                <div class="delivery-list-item" data-delivery-id="${delivery.id}" onclick="focusDelivery(${delivery.id})" style="border-color: ${statusColor};">
                    <div class="delivery-number">${delivery.delivery_number}</div>
                    <div class="delivery-route">
                        <i class="fas fa-circle" style="color: ${statusColor};"></i>
                        ${delivery.pickup.city}
                        <i class="fas fa-arrow-right"></i>
                        <i class="fas fa-circle" style="color: ${statusColor};"></i>
                        ${delivery.delivery.city}
                    </div>
                    <div style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; background: ${statusColor}; color: white; border-radius: 12px; font-size: 11px; font-weight: 600; margin-top: 5px;">
                        ${statusText}
                    </div>
                </div>
            `}).join('');
        } else {
            pendingSection.style.display = 'none';
        }

        // Update completed deliveries
        if (completedDeliveries.length > 0) {
            completedSection.style.display = 'block';
            completedList.innerHTML = completedDeliveries.map(delivery => `
                <div class="delivery-list-item" data-delivery-id="${delivery.id}" onclick="focusDelivery(${delivery.id})">
                    <div class="delivery-number">${delivery.delivery_number}</div>
                    <div class="delivery-route">
                        <i class="fas fa-circle" style="color: #6B7280;"></i>
                        ${delivery.pickup.city}
                        <i class="fas fa-arrow-right"></i>
                        <i class="fas fa-circle" style="color: #6B7280;"></i>
                        ${delivery.delivery.city}
                    </div>
                    <div style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; background: #6B7280; color: white; border-radius: 12px; font-size: 11px; font-weight: 600; margin-top: 5px;">
                        <i class="fas fa-check"></i> Completed
                    </div>
                </div>
            `).join('');
        } else {
            completedSection.style.display = 'none';
        }
    }

    function updateMap(driver, deliveries) {
        // return false; 
        // map.setZoom('2');
        // Clear existing markers
        clearMap();

        const bounds = new google.maps.LatLngBounds();
        let hasLocations = false;

        // Add driver's current location if available
        if (driver.driver_profile && driver.driver_profile.current_latitude && driver.driver_profile.current_longitude) {
            const driverLat = parseFloat(driver.driver_profile.current_latitude);
            const driverLng = parseFloat(driver.driver_profile.current_longitude);
            
            driverMarker = new google.maps.Marker({
                position: { lat: driverLat, lng: driverLng },
                map: map,
                title: `${driver.name} - Current Location`,
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 14,
                    fillColor: '#3B82F6',
                    fillOpacity: 1,
                    strokeColor: '#FFFFFF',
                    strokeWeight: 3
                },
                label: {
                    text: 'D',
                    color: '#FFFFFF',
                    fontSize: '12px',
                    fontWeight: 'bold'
                },
                zIndex: 1000
            });

            const driverInfoWindow = new google.maps.InfoWindow({
                content: `
                    <div style="padding: 10px;">
                        <h4 style="margin: 0 0 8px 0; color: #3B82F6; font-size: 14px; font-weight: 600;">
                            <i class="fas fa-user"></i> ${driver.name}
                        </h4>
                        <p style="margin: 0; font-size: 12px; color: #7f8c8d;">Current Location</p>
                        <p style="margin: 5px 0 0 0; font-size: 11px; color: #95a5a6;">
                            Updated: ${new Date().toLocaleTimeString()}
                        </p>
                    </div>
                `
            });

            driverMarker.addListener('click', () => {
                driverInfoWindow.open(map, driverMarker);
            });

            bounds.extend({ lat: driverLat, lng: driverLng });
            hasLocations = true;
        }

        // Add In Progress delivery markers (active/live)
        const inProgressDeliveries = deliveries.filter(d =>
            ['in_transit', 'picked_up'].includes(d.status)
        );
        
        inProgressDeliveries.forEach(delivery => {
            //alert(delivery.pickup.location.latitude);
            // if(driver.driver_profile ==  )
            addDeliveryMarkers(delivery, 'in_progress', bounds);
            hasLocations = true;
        });
        
        // Add Pending/Assigned delivery markers
        const pendingDeliveries = deliveries.filter(d =>
            ['pending', 'assigned'].includes(d.status)
        );

        pendingDeliveries.forEach(delivery => {
            addDeliveryMarkers(delivery, 'pending', bounds);
            hasLocations = true;
        });

        // Add completed delivery markers
        const today = new Date().toISOString().split('T')[0];
        const completedDeliveries = deliveries.filter(d =>
            d.status === 'delivered' &&
            d.delivery_actual_time &&
            d.delivery_actual_time.startsWith(today)
        );

        completedDeliveries.forEach(delivery => {
            addDeliveryMarkers(delivery, 'completed', bounds);
            hasLocations = true;
        });
        
        // Fit map to bounds
        if (hasLocations) {
            map.fitBounds(bounds);
            setTimeout(() => {
                const currentZoom = map.getZoom();
                if (currentZoom > 15) {
                    //map.setZoom(15);
                }
            }, 100);
        }
    }

    function addDeliveryMarkers(delivery, type, bounds) {
        //map.setZoom(1);
        const pickupLat = parseFloat(delivery.pickup.location.latitude);
        const pickupLng = parseFloat(delivery.pickup.location.longitude);
        const deliveryLat = parseFloat(delivery.delivery.location.latitude);
        // const deliveryLat = 30.6428;
        const deliveryLng = parseFloat(delivery.delivery.location.longitude);
        // const deliveryLng = 76.8169;
        // pickupLat = pickupLat + 100;
        // pickupLng = pickupLng + 100;

        if (!pickupLat || !pickupLng || !deliveryLat || !deliveryLng) {
            return;
        }

        // Set colors based on delivery type
        let pickupColor, deliveryColor, lineColor, statusText;

        if (type === 'in_progress') {
            pickupColor = '#10B981';  // Green
            deliveryColor = '#EF4444'; // Red
            lineColor = '#3B82F6';     // Blue
            statusText = delivery.status === 'in_transit' ? 'EN ROUTE' : 'PICKED UP';
        } else if (type === 'pending') {
            pickupColor = '#F59E0B';   // Orange
            deliveryColor = '#F59E0B'; // Orange
            lineColor = '#FCD34D';     // Light Orange
            statusText = delivery.status === 'assigned' ? 'ASSIGNED' : 'PENDING';
        } else { // completed
            pickupColor = '#6B7280';   // Gray
            deliveryColor = '#6B7280'; // Gray
            lineColor = '#9CA3AF';     // Light Gray
            statusText = 'COMPLETED';
        }

        // Pickup marker
        const pickupMarker = new google.maps.Marker({
            position: { lat: pickupLat, lng: pickupLng },
            map: map,
            title: 'Pickup: ' + delivery.pickup.address,
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 10,
                fillColor: pickupColor,
                fillOpacity: 1,
                strokeColor: '#FFFFFF',
                strokeWeight: 2
            },
            label: {
                text: 'P',
                color: '#FFFFFF',
                fontSize: '10px',
                fontWeight: 'bold'
            }
        });

        // function animateMarker(marker, oldPos, newPos, duration = 5000) {

        //     const startTime = performance.now();

        //     function animate(currentTime) {

        //         const elapsed = currentTime - startTime;
        //         const progress = Math.min(elapsed / duration, 1);

        //         const lat = oldPos.lat + (newPos.lat - oldPos.lat) * progress;
        //         const lng = oldPos.lng + (newPos.lng - oldPos.lng) * progress;
        //         console.log(lat+' '+lng);
        //         // console.log(lng);
        //         marker.setPosition({
        //             lat: lat,
        //             lng: lng
        //         });

        //         if (progress < 1) {
        //             requestAnimationFrame(animate);
        //         }
        //     }

        //     requestAnimationFrame(animate);
        // }
        // var oldPos = { lat: 30.7026120, lng: 76.7025280 };
        // var newPos = { lat: 30.6802 , lng: 76.7457 };
        // animateMarker(
        //     pickupMarker,
        //     oldPos,
        //     newPos,
        //     6000
        // );

        // Delivery marker
        const deliveryMarker = new google.maps.Marker({
            position: { lat: deliveryLat, lng: deliveryLng },
            map: map,
            title: 'Delivery: ' + delivery.delivery.address,
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 10,
                fillColor: deliveryColor,
                fillOpacity: 1,
                strokeColor: '#FFFFFF',
                strokeWeight: 2
            },
            label: {
                text: 'D',
                color: '#FFFFFF',
                fontSize: '10px',
                fontWeight: 'bold'
            }
        });

        // Route line
        const routePath = new google.maps.Polyline({
            path: [
                { lat: pickupLat, lng: pickupLng },
                { lat: deliveryLat, lng: deliveryLng }
            ],
            geodesic: true,
            strokeColor: lineColor,
            strokeOpacity: type === 'in_progress' ? 0.9 : (type === 'pending' ? 0.6 : 0.4),
            strokeWeight: type === 'in_progress' ? 4 : (type === 'pending' ? 3 : 2),
            map: map
        });
        routePath.setMap(null);

        // Info windows
        const pickupInfoWindow = new google.maps.InfoWindow({
            content: `
                <div style="padding: 10px; max-width: 250px;">
                    <h4 style="margin: 0 0 5px 0; color: ${pickupColor}; font-size: 13px; font-weight: 600;">
                        <i class="fas fa-map-marker-alt"></i> Pickup Location
                    </h4>
                    <p style="margin: 0 0 5px 0; font-size: 12px; font-weight: 600;">${delivery.delivery_number}</p>
                    <p style="margin: 0 0 8px 0; font-size: 12px; color: #2c3e50;">${delivery.pickup.address}</p>
                    <p style="margin: 0; padding: 4px 8px; background: ${pickupColor}; color: white; border-radius: 4px; font-size: 11px; font-weight: 600; display: inline-block;">${statusText}</p>
                </div>
            `
        });

        const deliveryInfoWindow = new google.maps.InfoWindow({
            content: `
                <div style="padding: 10px; max-width: 250px;">
                    <h4 style="margin: 0 0 5px 0; color: ${deliveryColor}; font-size: 13px; font-weight: 600;">
                        <i class="fas fa-flag-checkered"></i> Delivery Location
                    </h4>
                    <p style="margin: 0 0 5px 0; font-size: 12px; font-weight: 600;">${delivery.delivery_number}</p>
                    <p style="margin: 0 0 8px 0; font-size: 12px; color: #2c3e50;">${delivery.delivery.address}</p>
                    <p style="margin: 0; padding: 4px 8px; background: ${deliveryColor}; color: white; border-radius: 4px; font-size: 11px; font-weight: 600; display: inline-block;">${statusText}</p>
                </div>
            `
        });

        pickupMarker.addListener('click', () => {
            deliveryInfoWindow.close();
            pickupInfoWindow.open(map, pickupMarker);
        });

        deliveryMarker.addListener('click', () => {
            pickupInfoWindow.close();
            deliveryInfoWindow.open(map, deliveryMarker);
        });

        markers.push(pickupMarker, deliveryMarker, routePath);
        bounds.extend({ lat: pickupLat, lng: pickupLng });
        bounds.extend({ lat: deliveryLat, lng: deliveryLng });
    }

    function clearMap() {
        markers.forEach(marker => {
            if (marker.setMap) {
                marker.setMap(null);
            }
        });
        markers = [];

        if (driverMarker) {
            driverMarker.setMap(null);
            driverMarker = null;
        }
    }

    function focusDelivery(deliveryId) {
        // Highlight selected delivery in list
        document.querySelectorAll('.delivery-list-item').forEach(item => {
            item.classList.remove('active');
        });

        const selectedItem = document.querySelector(`[data-delivery-id="${deliveryId}"]`);
        if (selectedItem) {
            selectedItem.classList.add('active');
        }
    }

    async function refreshDriverLocation() {
        if (!selectedDriverId) return;

        const button = document.getElementById('refreshButton');
        button.classList.add('refreshing');

        await loadDriverData(selectedDriverId, true);

        setTimeout(() => {
            button.classList.remove('refreshing');
        }, 500);
    }

    // Initialize
    window.addEventListener('load', () => {
        initMap();
        //loadDrivers();
    });

    // Cleanup on page unload
    window.addEventListener('beforeunload', () => {
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
    });

    window.live_driver_id = null;
    window.live_driver_lat = null;
    window.live_driver_long = null;
    document.addEventListener('DOMContentLoaded', () => {
            window.Echo.private('driver-locations')
            .subscribed(() => {
                console.log('Subscribed to driver-locations');
            })
            .error((error) => {
                console.error('Channel error:', error);
            })
            .listenForWhisper('driver-location-updated', (e) => {
                window.live_driver_id = e.driver_id;
                window.live_driver_lat = e.lat;
                window.live_driver_long = e.long;
                console.log('DriverLocationUpdated', e);
                console.log('Live Driver ID:', window.live_driver_id);
            });
        });
</script>
@endsection
