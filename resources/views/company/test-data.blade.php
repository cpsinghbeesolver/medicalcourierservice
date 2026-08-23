@extends('common.layout')

@section('title', 'Test Data Loading')
@section('page-title', 'Test Data Loading')

@section('content')
<div style="padding: 30px; max-width: 1200px;">
    <div style="background: #fff; padding: 30px; border-radius: 8px; margin-bottom: 20px;">
        <h3 style="margin-bottom: 20px;">API Token Status</h3>
        <div id="tokenStatus" style="padding: 15px; background: #f5f5f5; border-radius: 4px; margin-bottom: 20px;">
            Checking token...
        </div>

        <h3 style="margin-bottom: 20px;">Locations API Response</h3>
        <div id="locationsStatus" style="padding: 15px; background: #f5f5f5; border-radius: 4px; margin-bottom: 20px;">
            Loading locations...
        </div>

        <h3 style="margin-bottom: 20px;">Pickup Locations</h3>
        <div id="pickupList" style="padding: 15px; background: #f5f5f5; border-radius: 4px; margin-bottom: 20px;">
            Loading...
        </div>

        <h3 style="margin-bottom: 20px;">Delivery Locations</h3>
        <div id="deliveryList" style="padding: 15px; background: #f5f5f5; border-radius: 4px; margin-bottom: 20px;">
            Loading...
        </div>

        <h3 style="margin-bottom: 20px;">Drivers</h3>
        <div id="driversList" style="padding: 15px; background: #f5f5f5; border-radius: 4px;">
            Loading...
        </div>
    </div>
</div>

<script>
    // Check token
    const token = localStorage.getItem('api_token');
    const tokenStatusDiv = document.getElementById('tokenStatus');

    if (token) {
        tokenStatusDiv.innerHTML = `<strong style="color: green;">✓ Token Found:</strong> ${token.substring(0, 20)}...`;
    } else {
        tokenStatusDiv.innerHTML = '<strong style="color: red;">✗ No Token Found!</strong><br>Please login first at <a href="/">http://localhost:8000</a>';
    }

    // Test locations API
    async function testLocations() {
        if (!token) {
            document.getElementById('locationsStatus').innerHTML = '<strong style="color: red;">Cannot test - No token</strong>';
            document.getElementById('pickupList').innerHTML = '<strong style="color: red;">Cannot load - No token</strong>';
            document.getElementById('deliveryList').innerHTML = '<strong style="color: red;">Cannot load - No token</strong>';
            return;
        }

        try {
            const response = await fetch('/api/v1/locations', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            document.getElementById('locationsStatus').innerHTML = `
                <strong style="color: green;">✓ API Response:</strong><br>
                Status: ${response.status}<br>
                Success: ${data.success}<br>
                Message: ${data.message}
            `;

            if (data.success && data.data) {
                // Pickup locations
                const pickupHtml = data.data.pickup_locations.map((loc, i) =>
                    `${i + 1}. <strong>${loc.name}</strong> - ${loc.full_address}`
                ).join('<br>');
                document.getElementById('pickupList').innerHTML = pickupHtml || 'No pickup locations found';

                // Delivery locations
                const deliveryHtml = data.data.delivery_locations.map((loc, i) =>
                    `${i + 1}. <strong>${loc.name}</strong> - ${loc.full_address}`
                ).join('<br>');
                document.getElementById('deliveryList').innerHTML = deliveryHtml || 'No delivery locations found';
            } else {
                document.getElementById('pickupList').innerHTML = '<strong style="color: red;">Error loading locations</strong>';
                document.getElementById('deliveryList').innerHTML = '<strong style="color: red;">Error loading locations</strong>';
            }
        } catch (error) {
            document.getElementById('locationsStatus').innerHTML = `<strong style="color: red;">✗ Error:</strong> ${error.message}`;
            console.error('Error:', error);
        }
    }

    // Test drivers API
    async function testDrivers() {
        if (!token) {
            document.getElementById('driversList').innerHTML = '<strong style="color: red;">Cannot load - No token</strong>';
            return;
        }

        try {
            const response = await fetch('/api/v1/driver-profiles?availability_status=available', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success && data.data && data.data.profiles) {
                const driversHtml = data.data.profiles.map((profile, i) =>
                    `${i + 1}. <strong>${profile.user.name}</strong> - ${profile.vehicle_type || 'Standard'} (${profile.availability_status})`
                ).join('<br>');
                document.getElementById('driversList').innerHTML = driversHtml || 'No drivers found';
            } else {
                document.getElementById('driversList').innerHTML = '<strong style="color: red;">No drivers found</strong>';
            }
        } catch (error) {
            document.getElementById('driversList').innerHTML = `<strong style="color: red;">Error:</strong> ${error.message}`;
            console.error('Error:', error);
        }
    }

    // Run tests
    if (token) {
        testLocations();
        testDrivers();
    }
</script>
@endsection
