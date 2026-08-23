@extends('common.layout')

@section('title', 'Live Driver Tracking')
@section('page-title', 'Maps - Live Driver Tracking')

@section('styles')
<!-- Google Maps API - Get your FREE key at: https://console.cloud.google.com/google/maps-apis/start -->
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&libraries=marker&libraries=geometry&loading=async"></script>
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
    .gm-ui-hover-effect{
        width: 24px !important;
        height: 22px !important;
        top: 3px;
        right: 2px;
    }
    .gm-ui-hover-effect>span{
        margin: 0px !important;
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
                    <option value="{{ $driver['id'] }}">{{ $driver['title'] }} - {{ ucfirst(str_replace('_', ' ', $driver['availability_status'])) }}</option> 
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
                    <span style="font-size: 12px; color: #7f8c8d;">Pending Deliveries:</span>
                    <span style="font-size: 13px; font-weight: 600; color: #2c3e50;" id="pendingDeliveries">0</span>
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

        <!-- <div class="map-legend">
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
        </div> -->
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
    let newDriverMarker = null;
    let driverMarkers = {};

    function initDriver(){
        //drivers markers on map
        const drivers = @json($drivers);
        drivers.forEach(driver => {
            // console.log(driver);
            //if(driver.availability_status == 'available'){
                //if(driver.current_latitude != null && driver.current_longitude != null){
                    console.log(driver.title);
                    var item = {
                        driver_id: driver.id,
                        driver_lat: driver.current_latitude,
                        driver_long: driver.current_longitude,
                        driver_name: driver.title
                    };
                    if(driver.availability_status == 'available'){
                        changeLocation(item);
                    }
                    listenSocket(driver.id);
                    disconnectSocket(driver.id);
                //}
            //}
        });
    }

    //Listen socket
    let driverLocationChannel = null;
    let driverDisconnectChannel = null;
    
    window.live_driver_id = null;
    window.live_driver_lat = null;
    window.live_driver_long = null;
    window.live_driver_name = null;
    window.isSingleDriverMode = false;
    window.avatar = null;
    window.live_drivers_locations = [];
    window.authUserId = {{ auth()->id() }};

    function disconnectSocket(driver_id){
        // console.log('driver-disconnected');
        const channelName = 'driver-disconnected.'+driver_id;
        // Prevent duplicate subscription
        Echo.leave(channelName);

        driverDisconnectChannel = Echo.private(channelName);

        driverDisconnectChannel
            .subscribed(() => {
                console.log('Subscribed to:', channelName);
            })
            .error((error) => {
                console.error('Channel error:', error);
            })
            .listenForWhisper('get-driver-disconnected', (e) => {
                console.log('Disconnect Socket Response:', e);
                window.live_drivers_locations =
                window.live_drivers_locations.filter(
                    d => Number(d.driver_id) !== Number(e.driver_id)
                );

                clearDriverMarker(e.driver_id);
                if($('.driver-info-card').hasClass('show')){
                    $('.driver-status').addClass('off_duty');
                    $('#driverStatus').html('<span class="status-indicator"></span><span id="driverStatusText">OFF DUTY</span>');
                }
            });
    }

    function clearDriverMarker(driver_id){
        if (driverMarkers[driver_id]) {
            driverMarkers[driver_id].map = null;
        }
    }

    function listenSocket(driver_id){
        const channelName = 'driver-locations.' + window.authUserId+'.'+driver_id;
        // Prevent duplicate subscription
        Echo.leave(channelName);

        driverLocationChannel = Echo.private(channelName);

        driverLocationChannel
            .subscribed(() => {
                console.log('Subscribed to:', channelName);
                console.log('Pusher state:', Echo.connector.pusher.connection.state);
            })
            .error((error) => {
                console.error('Channel error:', error);
            })
            .listenForWhisper('driver-location-updated', (e) => {

                console.log('Socket Response:', e);

                window.live_driver_id = e.driver_id;
                window.live_driver_lat = parseFloat(e.latitude);
                window.live_driver_long = parseFloat(e.longitude);
                window.live_driver_name = e.driver_name;

                const driver = {
                    driver_id: e.driver_id,
                    driver_lat: parseFloat(e.latitude),
                    driver_long: parseFloat(e.longitude),
                    driver_name: e.driver_name
                };

                const index = window.live_drivers_locations.findIndex(
                    d => d.driver_id == driver.driver_id
                );

                if (index === -1) {
                    window.live_drivers_locations.push(driver);
                } else {
                    window.live_drivers_locations[index] = driver;
                }

                const driverSelect = $('#driverSelect').val();

                if (driverSelect) {
                    const selectedDriver = window.live_drivers_locations.find(
                        item => item.driver_id == driverSelect
                    );
                    // alert(selectedDriver);
                    if (selectedDriver) {
                        changeLocation(selectedDriver);
                        //move map to socket driver location
                        if(selectedDriver.driver_lat != null && selectedDriver.driver_long != null){
                            const position = {
                                lat: Number(selectedDriver.driver_lat),
                                lng: Number(selectedDriver.driver_long)
                            };

                            // map.setCenter(position);
                            smoothPanTo(map, position, 2000); // same duration as marker animation
                            //map.setZoom(15);
                        }
                        
                    }
                }else{
                    window.live_drivers_locations.forEach(item => {
                        changeLocation(item);
                    });
                }

                

                console.log(
                    'Live Drivers:',
                    window.live_drivers_locations
                );
            });
    }

    // Initialize map
    async function initMap() {
        const { Map } = await google.maps.importLibrary("maps");
        const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");
        // Default center (will be updated when driver is selected)
        var defaultCenter = { lat: 30.7046, lng: 76.7179 }; 
        map = new google.maps.Map(document.getElementById('mainMap'), {
            zoom: 13,
            maxZoom: 16,
            minZoom: 12,
            mapId: "{{ config('services.google_maps.api_key') }}",
            center: defaultCenter,
            mapTypeControl: true,
            streetViewControl: false,
            fullscreenControl: true,
            zoomControl: true
        });

        google.maps.event.addListener(map, 'zoom_changed', () => {
            if (mapPanFrame) {
                cancelAnimationFrame(mapPanFrame);
                mapPanFrame = null;
            }
        });

        google.maps.event.addListener(map, 'dragstart', () => {
            if (mapPanFrame) {
                cancelAnimationFrame(mapPanFrame);
                mapPanFrame = null;
            }
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

    function intial_markers(){
        if(window.live_drivers_locations != ''){
            console.log(window.live_drivers_locations);
            for (let item of window.live_drivers_locations) {
                //newDriverMarker = null;
                changeLocation(item);
            }
        }
    }

    function clearAllMarkers() {
        Object.values(driverMarkers).forEach(marker => {
            marker.map = null;
        });

        driverMarkers = {};
    }

    // Handle driver selection
    document.getElementById('driverSelect').addEventListener('change', async (e) => {
        //await initMap();
        const driverId = e.target.value;
        clearAllMarkers();
        if (!driverId) {
            window.isSingleDriverMode = false;
            clearMap();
            document.getElementById('driverInfoCard').classList.remove('show');
            $('#activeDeliveriesSection').hide();
            $('#completedDeliveriesSection').hide();
            $('#inProgressDeliveriesSection').hide();
            $('#pendingDeliveriesSection').hide();
            document.getElementById('noDataMessage').style.display = 'block';

            // Clear refresh interval
            if (refreshInterval) {
                clearInterval(refreshInterval);
                refreshInterval = null;
            }
            // return;
            intial_markers();
            map.setZoom(13);
        }else{
            window.isSingleDriverMode = true;
            selectedDriverId = driverId;
            document.getElementById('noDataMessage').style.display = 'none';

            let profileData = await loadDriverData(driverId);
            if(window.live_drivers_locations != ''){
                for (const item of window.live_drivers_locations) {
                    if (item.driver_id == driverId) {
                        changeLocation(item);
                    }
                }
            }else if(profileData.driver_profile.availability_status == 'available' && profileData.driver_profile.current_location.latitude != 'null' &&  profileData.driver_profile.current_location.longitude != 'null'){
                var item = {
                        driver_id: profileData.id,
                        driver_lat: profileData.driver_profile.current_location.latitude,
                        driver_long: profileData.driver_profile.current_location.longitude,
                        driver_name: profileData.name
                    };
                changeLocation(item);
            }

            //set map default
            if(profileData.driver_profile.current_location.latitude != null &&  profileData.driver_profile.current_location.longitude != null){
                const position = {
                    lat: Number(profileData.driver_profile.current_location.latitude),
                    lng: Number(profileData.driver_profile.current_location.longitude)
                };

                map.setCenter(position);
                map.setZoom(15);
            }
        }
    });

    let mapPanFrame = null;
    function smoothPanTo(map, newPosition, duration = 2000) {
        if (mapPanFrame) {
            cancelAnimationFrame(mapPanFrame);
        }

        const center = map.getCenter();
        const start = { lat: center.lat(), lng: center.lng() };
        const end = newPosition;
        const startTime = performance.now();

        function easeInOutQuad(t) {
            return t < 0.5 ? 2 * t * t : -1 + (4 - 2 * t) * t;
        }

        function animate(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const eased = easeInOutQuad(progress);

            const lat = start.lat + (end.lat - start.lat) * eased;
            const lng = start.lng + (end.lng - start.lng) * eased;

            map.panTo({ lat, lng });

            if (progress < 1) {
                mapPanFrame = requestAnimationFrame(animate);
            } else {
                mapPanFrame = null;
            }
        }

        mapPanFrame = requestAnimationFrame(animate);
    }

    
    
    
    async function changeLocation(item) {
        if (!item || item.driver_lat == null || item.driver_long == null) {
            return;
        }

        const position = {
            lat: Number(item.driver_lat),
            lng: Number(item.driver_long)
        };

        const driverId = item.driver_id;

        // Create marker if it doesn't exist
        if (!driverMarkers[driverId]) {
            const pin = document.createElement("div");

            // --- PIN SHAPE STYLING ---
            pin.style.width = "32px";
            pin.style.height = "32px";
            pin.style.background = "#F59E0B";
            pin.style.border = "2px solid white";
            pin.style.borderRadius = "50% 50% 50% 0";

            // Rotate the pin teardrop and shift the sharp tip to the anchor point
            pin.style.transform = "rotate(-45deg)";
            pin.style.transformOrigin = "bottom left"; 

            pin.style.boxShadow = "0 2px 6px rgba(0,0,0,0.3)";
            pin.style.display = "flex";
            pin.style.alignItems = "center";
            pin.style.justifyContent = "center";

            // --- INNER TEXT CONTAINER (Straight text + 15px font) ---
            const label = document.createElement("span");
            label.style.transform = "rotate(45deg)"; // Counter-rotate to stay straight
            label.style.color = "black";
            label.style.fontWeight = "bold";
            label.style.fontSize = "15px";            // Set font size to 15px
            label.style.lineHeight = "1";
            label.style.display = "block";
            label.textContent = item.driver_name
                ? item.driver_name.charAt(0).toUpperCase()
                : "D";

            pin.appendChild(label);

            const marker = new google.maps.marker.AdvancedMarkerElement({
                map: map,
                position: position,
                content: pin,
                title: item.driver_name || "Driver"
            });

            const infoWindow = new google.maps.InfoWindow();

            marker.addListener("click", () => {
                infoWindow.setContent(`
                    <div style="padding: 5px;">
                        <strong>${item.driver_name || "Driver"}</strong>
                    </div>
                `);

                infoWindow.open({
                    map: map,
                    anchor: marker
                });
            });

            driverMarkers[driverId] = marker;

        } else {

            // Move existing marker
            // driverMarkers[driverId].position = position;
            animateMarker(driverMarkers[driverId], position, 2000);
        }

        function animateMarker(marker, newPosition, duration = 1500) {
            const start = marker.position;

            const startLat = start.lat;
            const startLng = start.lng;

            const endLat = newPosition.lat;
            const endLng = newPosition.lng;

            const startTime = performance.now();

            function animate(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);

                const lat = startLat + (endLat - startLat) * progress;
                const lng = startLng + (endLng - startLng) * progress;

                marker.position = { lat, lng };

                if (progress < 1) {
                    requestAnimationFrame(animate);
                }
            }

            requestAnimationFrame(animate);
        }
    }

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
            }
            return profileData.data;
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
        window.avatar = avatar.textContent;
        name.textContent = driver.name;

        const driverStatus = driver.driver_profile?.availability_status || 'off_duty';
        status.className = 'driver-status ' + driverStatus;
        statusText.textContent = driverStatus.replace('_', ' ').toUpperCase();

        infoCard.classList.add('show');
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
        document.getElementById('pendingDeliveries').textContent = pendingDeliveries.length;
        document.getElementById('inProgressCount').textContent = inProgressDeliveries.length;
        document.getElementById('completedCount').textContent = completedDeliveries.length;
        // document.getElementById('pendingCount').textContent = pendingDeliveries.length;

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
                        ${truncateText(delivery.pickup.address, 30)}
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
                        ${truncateText(delivery.pickup.address, 30)}
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
                        ${truncateText(delivery.pickup.address, 30)}
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

    // Initialize
    window.addEventListener('load', () => {
        initMap();
        setTimeout(() => {
            initDriver();
        }, 1000);
        //loadDrivers();
    });

    // Cleanup on page unload
    window.addEventListener('beforeunload', () => {
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
    });

    function focusDelivery(deliveryId) {
        return;
    }

    function truncateText(text, maxLength = 50) {
        return text && text.length > maxLength
            ? text.substring(0, maxLength) + '...'
            : text;
    }

    
</script>
@endsection
