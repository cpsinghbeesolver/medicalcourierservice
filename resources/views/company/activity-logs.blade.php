@extends('common.layout')

@section('title', 'Driver Activity Log')
@section('page-title', 'Driver Activity Log')

@section('styles')
<!-- Google Maps API - Get your FREE key at: https://console.cloud.google.com/google/maps-apis/start -->
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&libraries=geometry"></script>
<style>
    .filter-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        padding: 30px;
        max-width: 800px;
    }

    .filter-form {
        display: flex;
        gap: 20px;
        align-items: flex-end;
    }

    .form-group {
        flex: 1;
    }

    .form-group label {
        display: block;
        font-size: 13px;
        color: #2c3e50;
        margin-bottom: 8px;
        font-weight: 500;
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 11px 14px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
        transition: border-color 0.3s;
    }

    .form-group input:focus,
    .form-group select:focus {
        border-color: #a8b456;
    }

    .btn-done {
        padding: 11px 30px;
        background: #a8b456;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        transition: background 0.3s;
        white-space: nowrap;
    }

    .btn-done:hover {
        background: #96a048;
    }

    .results-section {
        margin-top: 30px;
    }

    .items-count {
        font-size: 16px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 20px;
    }

    .delivery-card {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 25px;
    }

    .delivery-from {
        display: flex;
        align-items: start;
        gap: 8px;
        margin-bottom: 15px;
    }

    .delivery-from i {
        color: #a8b456;
        margin-top: 3px;
    }

    .from-label {
        font-size: 12px;
        color: #7f8c8d;
        margin-bottom: 4px;
    }

    .from-address {
        font-size: 14px;
        color: #2c3e50;
        font-weight: 500;
    }

    .delivery-times {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        padding-top: 15px;
        border-top: 1px solid #e0e0e0;
    }

    .time-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .time-item i {
        color: #7f8c8d;
    }

    .time-label {
        font-size: 12px;
        color: #7f8c8d;
        margin-right: 8px;
    }

    .time-value {
        font-size: 14px;
        color: #2c3e50;
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 30px 0 15px;
    }

    .section-header i {
        font-size: 16px;
    }

    .section-header.delivered i {
        color: #0f5132;
    }

    .section-header.not-delivered i {
        color: #842029;
    }

    .section-header.info i {
        color: #0c5460;
    }

    .section-header.map i {
        color: #2c3e50;
    }

    .section-header h3 {
        font-size: 15px;
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
    }

    .item-card {
        background: #ffffff;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 15px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .item-card:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border-color: #a8b456;
    }

    .item-card.not-delivered {
        border-color: #f8d7da;
        background: #fff5f5;
    }

    .item-card.active {
        border-color: #a8b456;
        box-shadow: 0 2px 8px rgba(168, 180, 86, 0.2);
    }

    .item-more-details {
        display: none;
        margin-top: 25px;
        padding-top: 20px;
        border-top: 1px solid #e0e0e0;
    }

    .item-more-details.show {
        display: block;
    }

    .item-more-details .section-header {
        margin: 20px 0 15px;
    }

    .item-more-details .info-grid {
        margin-bottom: 20px;
    }

    .item-more-details .map-container {
        margin-bottom: 0;
    }

    .item-badge {
        display: inline-block;
        padding: 4px 12px;
        background: #2c3e50;
        color: white;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 15px;
    }

    .item-details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    .detail-row {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .detail-label {
        font-size: 12px;
        color: #7f8c8d;
    }

    .detail-value {
        font-size: 14px;
        color: #2c3e50;
        font-weight: 500;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        background: #ffffff;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 25px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .info-label {
        font-size: 12px;
        color: #7f8c8d;
    }

    .info-value {
        font-size: 14px;
        color: #2c3e50;
        font-weight: 500;
    }

    .map-container {
        background: #e9ecef;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        height: 400px;
        overflow: hidden;
    }

    .map-canvas {
        width: 100%;
        height: 100%;
    }

    .no-results {
        text-align: center;
        padding: 40px;
        color: #7f8c8d;
    }
    .small-text{
        font-size: 12px;
        color: #7f8c8d;
    }

    .calendar-input-wrapper {
    position: relative;
}

.calendar-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #a8b456;
    font-size: 16px;
    pointer-events: none;
}

.calendar-input-wrapper input[type="text"] {
    padding-left: 38px !important;
}
</style>
@endsection

@section('content')
<div class="filter-card">
    <form class="filter-form" id="filterForm">
        <div class="form-group">
            <label>Driver Name</label>
            <select id="driverName">
                <option value="">Select driver</option>
            </select>
        </div>
         <div class="form-group">
                  <label>Choose Date & Time <span class="small-text">(optional)</span></label>
                    <div class="calendar-input-wrapper">
                       <i class="fas fa-calendar-alt calendar-icon"></i>
            
            <input type="datetime-local" id="dateTime" placeholder="Select date and time">
        </div></div>
        <button type="submit" class="btn-done">Submit</button>
        <button type="button" class="btn-done" id="clearButton">Clear</button>
    </form>

    <div class="results-section" id="resultsSection" style="display: none;">
        <div id="resultsContent"></div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // const token = localStorage.getItem('api_token');
    const token = '{{ session("web_token") }}';

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
                const select = document.getElementById('driverName');
                data.data.profiles.forEach(profile => {
                    if (profile.user) {
                        const option = document.createElement('option');
                        option.value = profile.user.id;
                        option.textContent = profile.user.name;
                        select.appendChild(option);
                    }
                });
            }
        } catch (error) {
            console.error('Error loading drivers:', error);
        }
    }

    document.getElementById('filterForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const driverId = document.getElementById('driverName').value;
        const dateTime = document.getElementById('dateTime').value;

        if (!driverId && !dateTime) {
            showDialog('Please select driver or choose date & time', 'warning');
            return;
        }

        await searchDeliveries(driverId, dateTime);
    });

    async function searchDeliveries(driverId, dateTime) {
        show_load_spinner('content', 'Loading deliveries...','class');
        try {
            // Build query parameters
            let url = '/api/v1/deliveries?per_page=100';

            if (driverId) {
                url += `&driver_id=${driverId}`;
            }

            if (dateTime) {
                const date = new Date(dateTime).toISOString().split('T')[0];
                url += `&from_date=${date}&to_date=${date}`;
            }

            const response = await fetch(url, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            const resultsSection = document.getElementById('resultsSection');
            const resultsContent = document.getElementById('resultsContent');

            if (data.success && data.data && data.data.deliveries) {
                const deliveries = data.data.deliveries;

                resultsSection.style.display = 'block';

                if (deliveries.length === 0) {
                    resultsContent.innerHTML = '<div class="no-results">No deliveries found for the selected criteria</div>';
                    hide_load_spinner('content','class');
                    return;
                }

                resultsContent.innerHTML = `
                    <div class="items-count">${deliveries.length} Item${deliveries.length > 1 ? 's' : ''}</div>
                    ${deliveries.map(delivery => renderDelivery(delivery)).join('')}
                `;
            } else {
                resultsSection.style.display = 'block';
                resultsContent.innerHTML = '<div class="no-results">No deliveries available</div>';
            }
            hide_load_spinner('content','class');
        } catch (error) {
            hide_load_spinner('content','class');
            console.error('Error loading deliveries:', error);
            const resultsSection = document.getElementById('resultsSection');
            const resultsContent = document.getElementById('resultsContent');

            resultsSection.style.display = 'block';
            resultsContent.innerHTML = '<div class="no-results" style="color: #e74c3c;">Error loading deliveries</div>';
        }
    }

    function renderDelivery(delivery) {
        //console.log(delivery);
        const items = delivery.items || [];
        const deliveredItems = items.filter(item => item.status === 'delivered');
        const notDeliveredItems = items.filter(item => item.status !== 'delivered');
        const deliveryId = delivery.id;

        return `
            <div class="delivery-card">
                <div class="delivery-from">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <div class="from-label">From</div>
                        <div class="from-address">${delivery.pickup.address || 'N/A'}</div>
                    </div>
                </div>
                <div class="delivery-times">
                    <div class="time-item">
                        <i class="far fa-calendar"></i>
                        <span class="time-label">Pickup</span>
                        <span class="time-value">${delivery.pickup.scheduled_time ? new Date(delivery.pickup.scheduled_time).toLocaleString() : 'N/A'}</span>
                    </div>
                    <div class="time-item">
                        <i class="far fa-calendar"></i>
                        <span class="time-label">Delivery</span>
                        <span class="time-value">${delivery.delivery.scheduled_time ? new Date(delivery.delivery.scheduled_time).toLocaleString() : 'N/A'}</span>
                    </div>
                </div>
            </div>

            ${deliveredItems.length > 0 ? `
                <div class="section-header delivered">
                    <i class="fas fa-check-circle"></i>
                    <h3>Item Delivered</h3>
                </div>
                ${deliveredItems.map((item, index) => `
                    <div class="item-card" onclick="toggleItemDetails('item-${deliveryId}-${index}')">
                        <div class="item-badge">Item ${index + 1}</div>
                        <div class="item-details">
                            <div class="detail-row">
                                <span class="detail-label">Name</span>
                                <span class="detail-value">${item.description || item.specimen_type || 'N/A'}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Specimen Type</span>
                                <span class="detail-value">${item.specimen_name || 'N/A'}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Accession/Specimen ID</span>
                                <span class="detail-value">${item.barcode || item.item_code || 'N/A'}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Drop off location</span>
                                <span class="detail-value">${delivery.delivery.address || 'N/A'}</span>
                            </div>
                        </div>

                        <div class="item-more-details" id="details-item-${deliveryId}-${index}">
                            <div class="section-header info">
                                <i class="fas fa-info-circle"></i>
                                <h3>More Info</h3>
                            </div>
                            <div class="info-grid">
                                <div class="info-item">
                                    <span class="info-label">Urgency Level</span>
                                    <span class="info-value">${delivery.priority || 'N/A'}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Temperature Requirement</span>
                                    <span class="info-value">${item.temperature_requirement || 'N/A'}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Vehicle Requirements</span>
                                    <span class="info-value">${delivery.vehicle_requirements || 'N/A'}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Number of containers or bags</span>
                                    <span class="info-value">${item.quantity || 1}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Temperature Reading</span>
                                    <span class="info-value">N/A</span>
                                </div>
                            </div>

                            <div class="section-header map">
                                <i class="fas fa-map-marker-alt"></i>
                                <h3>Map View</h3>
                            </div>
                            <div class="map-container">
                                <div class="map-canvas" id="map-${deliveryId}-${index}"
                                     data-pickup-lat="${delivery.pickup.location.latitude || ''}"
                                     data-pickup-lng="${delivery.pickup.location.longitude || ''}"
                                     data-delivery-lat="${delivery.delivery.location.latitude || ''}"
                                     data-delivery-lng="${delivery.delivery.location.longitude || ''}"
                                     data-pickup-address="${delivery.pickup.address || ''}"
                                     data-delivery-address="${delivery.delivery.address || ''}">
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('')}
            ` : ''}

            ${notDeliveredItems.length > 0 ? `
                <div class="section-header not-delivered">
                    <i class="fas fa-times-circle"></i>
                    <h3>Item not Delivered</h3>
                </div>
                ${notDeliveredItems.map((item, index) => {
                    const itemIndex = deliveredItems.length + index;
                    return `
                    <div class="item-card not-delivered" onclick="toggleItemDetails('item-${deliveryId}-${itemIndex}')">
                        <div class="item-badge">Item ${itemIndex + 1}</div>
                        <div class="item-details">
                            <div class="detail-row">
                                <span class="detail-label">Name</span>
                                <span class="detail-value">${item.description || item.specimen_type || 'N/A'}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Specimen Type</span>
                                <span class="detail-value">${item.specimen_name || 'N/A'}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Accession/Specimen ID</span>
                                <span class="detail-value">${item.barcode || item.item_code || 'N/A'}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Drop off location</span>
                                <span class="detail-value">${delivery.delivery.address || 'N/A'}</span>
                            </div>
                        </div>

                        <div class="item-more-details" id="details-item-${deliveryId}-${itemIndex}">
                            <div class="section-header info">
                                <i class="fas fa-info-circle"></i>
                                <h3>More Info</h3>
                            </div>
                            <div class="info-grid">
                                <div class="info-item">
                                    <span class="info-label">Urgency Level</span>
                                    <span class="info-value">${delivery.priority || 'N/A'}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Temperature Requirement</span>
                                    <span class="info-value">${item.temperature_requirement || 'N/A'}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Vehicle Requirements</span>
                                    <span class="info-value">${delivery.vehicle_requirements || 'N/A'}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Number of containers or bags</span>
                                    <span class="info-value">${item.quantity || 1}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Temperature Reading</span>
                                    <span class="info-value">N/A</span>
                                </div>
                            </div>

                            <div class="section-header map">
                                <i class="fas fa-map-marker-alt"></i>
                                <h3>Map View</h3>
                            </div>
                            <div class="map-container">
                                <div class="map-canvas" id="map-${deliveryId}-${itemIndex}"
                                     data-pickup-lat="${delivery.pickup.location.latitude || ''}"
                                     data-pickup-lng="${delivery.pickup.location.longitude || ''}"
                                     data-delivery-lat="${delivery.delivery.location.latitude || ''}"
                                     data-delivery-lng="${delivery.delivery.location.longitude || ''}"
                                     data-pickup-address="${delivery.pickup.address || ''}"
                                     data-delivery-address="${delivery.delivery.address || ''}">
                                </div>
                            </div>
                        </div>
                    </div>
                `}).join('')}
            ` : ''}
        `;
    }

    function toggleItemDetails(itemId) {
        return;
        const detailsSection = document.getElementById(`details-${itemId}`);
        const allItemCards = document.querySelectorAll('.item-card');
        const allDetails = document.querySelectorAll('.item-more-details');

        // Check if this item's details are already shown
        const isCurrentlyShown = detailsSection.classList.contains('show');

        // Hide all details and remove active class from all items
        allDetails.forEach(detail => detail.classList.remove('show'));
        allItemCards.forEach(card => card.classList.remove('active'));

        // If it wasn't shown, show it now
        if (!isCurrentlyShown) {
            event.currentTarget.classList.add('active');
            detailsSection.classList.add('show');

            // Initialize map after showing details
            setTimeout(() => {
                const mapCanvas = detailsSection.querySelector('.map-canvas');
                if (mapCanvas && !mapCanvas.dataset.initialized) {
                    initializeMap(mapCanvas);
                }
            }, 100);
        }
    }

    function initializeMap(mapElement) {
        const pickupLat = parseFloat(mapElement.dataset.pickupLat);
        const pickupLng = parseFloat(mapElement.dataset.pickupLng);
        const deliveryLat = parseFloat(mapElement.dataset.deliveryLat);
        const deliveryLng = parseFloat(mapElement.dataset.deliveryLng);
        const pickupAddress = mapElement.dataset.pickupAddress;
        const deliveryAddress = mapElement.dataset.deliveryAddress;

        // Check if coordinates are valid
        if (!pickupLat || !pickupLng || !deliveryLat || !deliveryLng) {
            mapElement.innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #7f8c8d;"><i class="fas fa-map-marker-alt" style="margin-right: 10px;"></i> Location coordinates not available</div>';
            return;
        }

        // Mark as initialized
        mapElement.dataset.initialized = 'true';

        // Create map centered between pickup and delivery
        const centerLat = (pickupLat + deliveryLat) / 2;
        const centerLng = (pickupLng + deliveryLng) / 2;

        const map = new google.maps.Map(mapElement, {
            zoom: 12,
            center: { lat: centerLat, lng: centerLng },
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

        // Pickup marker (Green - Source)
        const pickupMarker = new google.maps.Marker({
            position: { lat: pickupLat, lng: pickupLng },
            map: map,
            title: 'Pickup Location',
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 12,
                fillColor: '#10B981',
                fillOpacity: 1,
                strokeColor: '#FFFFFF',
                strokeWeight: 3
            },
            label: {
                text: 'P',
                color: '#FFFFFF',
                fontSize: '12px',
                fontWeight: 'bold'
            }
        });

        // Delivery marker (Red - Destination)
        const deliveryMarker = new google.maps.Marker({
            position: { lat: deliveryLat, lng: deliveryLng },
            map: map,
            title: 'Delivery Location',
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 12,
                fillColor: '#EF4444',
                fillOpacity: 1,
                strokeColor: '#FFFFFF',
                strokeWeight: 3
            },
            label: {
                text: 'D',
                color: '#FFFFFF',
                fontSize: '12px',
                fontWeight: 'bold'
            }
        });

        // Pickup Info Window
        const pickupInfoWindow = new google.maps.InfoWindow({
            content: `
                <div style="padding: 10px; max-width: 250px;">
                    <h4 style="margin: 0 0 8px 0; color: #10B981; font-size: 14px; font-weight: 600;">
                        <i class="fas fa-map-marker-alt"></i> Pickup Location
                    </h4>
                    <p style="margin: 0; font-size: 13px; color: #2c3e50;">${pickupAddress}</p>
                </div>
            `
        });

        // Delivery Info Window
        const deliveryInfoWindow = new google.maps.InfoWindow({
            content: `
                <div style="padding: 10px; max-width: 250px;">
                    <h4 style="margin: 0 0 8px 0; color: #EF4444; font-size: 14px; font-weight: 600;">
                        <i class="fas fa-flag-checkered"></i> Delivery Location
                    </h4>
                    <p style="margin: 0; font-size: 13px; color: #2c3e50;">${deliveryAddress}</p>
                </div>
            `
        });

        // Add click listeners for markers
        pickupMarker.addListener('click', () => {
            deliveryInfoWindow.close();
            pickupInfoWindow.open(map, pickupMarker);
        });

        deliveryMarker.addListener('click', () => {
            pickupInfoWindow.close();
            deliveryInfoWindow.open(map, deliveryMarker);
        });

        // Draw route line between pickup and delivery (Uber-style)
        const routePath = new google.maps.Polyline({
            path: [
                { lat: pickupLat, lng: pickupLng },
                { lat: deliveryLat, lng: deliveryLng }
            ],
            geodesic: true,
            strokeColor: '#3B82F6',
            strokeOpacity: 0.8,
            strokeWeight: 4,
            map: map
        });

        // Fit bounds to show both markers
        const bounds = new google.maps.LatLngBounds();
        bounds.extend({ lat: pickupLat, lng: pickupLng });
        bounds.extend({ lat: deliveryLat, lng: deliveryLng });
        map.fitBounds(bounds);

        // Add padding to bounds
        google.maps.event.addListenerOnce(map, 'bounds_changed', () => {
            const currentZoom = map.getZoom();
            if (currentZoom > 15) {
                map.setZoom(15);
            }
        });
    }

    loadDrivers();

    $('#clearButton').click(function(){
        if(confirm('Are you sure you want to clear the filters?')) {
            $('#driverName').val('');
            $('#dateTime').val('');
            $('#resultsSection').hide();
            $('#resultsContent').html('');
        }
    });
</script>
@endsection
