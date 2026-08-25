@extends('common.layout')

@section('title', 'Driver Profile')
@section('page-title', '')

@section('styles')
<style>
    .profile-container {
        max-width: 1100px;
        margin: 0 auto;
        padding: 20px;
    }

    .loading-state {
        background: #ffffff;
        border-radius: 16px;
        padding: 40px;
        text-align: center;
        box-shadow: 0 14px 40px rgba(0,0,0,0.08);
    }

    .profile-title {
        margin-bottom: 24px;
        font-size: 28px;
        font-weight: 700;
        color: #2c3e50;
    }

    .profile-section {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    }

    .section-header {
        display: inline-block;
        align-items: center;
        gap: 12px;
        width: 50%;
    }

    .section-header h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: #2c3e50;
    }

    .section-header i {
        color: #a8b456;
        font-size: 18px;
        margin-top: 5px;
        float: left;
        margin-right: 11px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
    }

    .info-grid.two-cols {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .info-item {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 16px;
    }

    .info-label {
        font-size: 12px;
        color: #6c757d;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .info-value {
        font-size: 15px;
        color: #2c3e50;
        line-height: 1.5;
    }

    .file-link {
        color: #1d6fa5;
        text-decoration: none;
        font-weight: 600;
    }

    .profile-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 20px;
    }

    .btn-cancel,
    .btn-edit {
        border: none;
        border-radius: 10px;
        padding: 12px 22px;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-cancel {
        background: #f1f3f5;
        color: #2c3e50;
    }

    .btn-edit {
        background: #a8b456;
        color: #000;
    }
</style>
@endsection

@section('content')
<a href="/company/dashboard/drivers" class="back-button">
    <i class="fas fa-arrow-left"></i> Back to Driver Details
</a>
<div class="profile-container" id="driverCard">
    <div class="loading-state">
        <i class="fas fa-spinner fa-spin" style="font-size: 32px; color: #a8b456;"></i>
        <p style="margin-top: 15px;">Loading driver profile...</p>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const token = '{{ session("web_token") }}';
    const driverId = '{{ $id }}';
    const APP_URL = '{{ url('/') }}';

    async function loadDriverDetails() {
        try {
            const response = await fetch(`/api/v1/driver-profiles/${driverId}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success && data.data) {
                const driver = data.data;
                displayDriver(driver);
            }
        } catch (error) {
            console.error('Error loading driver details:', error);
            document.getElementById('driverCard').innerHTML = `
                <div class="loading-state">
                    <i class="fas fa-exclamation-triangle" style="font-size: 32px; color: #e74c3c;"></i>
                    <p style="margin-top: 15px; color: #e74c3c;">Error loading driver profile</p>
                </div>
            `;
        }
    }

    function displayDriver(driver) {
        const card = document.getElementById('driverCard');

        card.innerHTML = `
            <h2 class="profile-title">Driver Profile</h2>

            <div class="profile-section shadow-sm">
                <div class="section-header">
                    <i class="fas fa-user"></i>
                    <h3>Personal & Contact Info</h3>
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Full Name</div>
                        <div class="info-value">${driver.user?.name || 'N/A'}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Email Address</div>
                        <div class="info-value">${driver.user?.email || 'N/A'}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Contact Number</div>
                        <div class="info-value">${driver.user?.phone || 'N/A'}</div>
                    </div>
                </div>
                <div class="info-grid two-cols">
                    <div class="info-item">
                        <div class="info-label">Date of Birth</div>
                        <div class="info-value">${driver.date_of_birth || 'N/A'}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">User Role</div>
                        <div class="info-value">${driver.user?.role || 'N/A'}</div>
                    </div>
                </div>
            </div>

            <div class="profile-section">
                <div class="section-header">
                    <i class="fas fa-id-card"></i>
                    <h3>License & Documentation</h3>
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">License Number</div>
                        <div class="info-value">${driver.license_number || 'N/A'}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">License State</div>
                        <div class="info-value">${driver.license_state || 'N/A'}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">License Expiry</div>
                        <div class="info-value">${driver.license_expiry_date || 'N/A'}</div>
                    </div>
                </div>
                <div class="info-grid two-cols">
                    <div class="info-item">
                        <div class="info-label">License Expired</div>
                        <div class="info-value">${driver.license_expired ? 'Yes' : 'No'}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Insurance Policy Number</div>
                        <div class="info-value">${driver.insurance_policy_number || 'N/A'}</div>
                    </div>
                </div>
                <div class="info-grid two-cols">
                    <div class="info-item">
                        <div class="info-label">Insurance Expiry Date</div>
                        <div class="info-value">${driver.insurance_expiry_date || 'N/A'}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Account Status</div>
                        <div class="info-value">${driver.user?.status || 'N/A'}</div>
                    </div>
                </div>
            </div>

            <div class="profile-section">
                <div class="section-header">
                    <i class="fas fa-truck"></i>
                    <h3>Availability</h3>
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Availability</div>
                        <div class="info-value">${driver.availability_status
                        ? driver.availability_status
                            .replace(/_/g, ' ')
                            .replace(/\b\w/g, char => char.toUpperCase())
                        : 'N/A'}
                        </div>
                    </div>
                </div>
                <div class="info-grid two-cols">
                    <div class="info-item">
                        <div class="info-label">Latitude</div>
                        <div class="info-value">${driver.current_location?.latitude ?? 'N/A'}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Longitude</div>
                        <div class="info-value">${driver.current_location?.longitude ?? 'N/A'}</div>
                    </div>
                </div>
            </div>

            <div class="profile-section">
                <div class="section-header">
                    <i class="fas fa-map-marker-alt"></i>
                    <h3>Address & Emergency Contact</h3>
                </div>
                <div class="info-grid two-cols">
                    <div class="info-item">
                        <div class="info-label">Street Address</div>
                        <div class="info-value">${driver.address || 'N/A'}</div>
                    </div>
                </div>
            </div>

            <div class="profile-section">
                <div class="section-header">
                    <i class="fas fa-notes-medical"></i>
                    <h3>Medical Compliance</h3>
                </div>
                <div class="info-grid two-cols">
                    <div class="info-item">
                        <div class="info-label">HIPAA Certification Date</div>
                        <div class="info-value">${driver.hipaa_certification_date || 'N/A'}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Background Check Status</div>
                        <div class="info-value">${driver.background_check_status || 'N/A'}</div>
                    </div>
                </div>
                <div class="info-grid two-cols">
                    <div class="info-item">
                        <div class="info-label">Drug Screen Expiry</div>
                        <div class="info-value">${driver.drug_screen_expiry || 'N/A'}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Specimen Handling Cert Date</div>
                        <div class="info-value">${driver.specimen_handling_certification_date || 'N/A'}</div>
                    </div>
                </div>
                <div class="info-grid two-cols">
                    <div class="info-item">
                        <div class="info-label">Specimen Handling Confirmed</div>
                        <div class="info-value">${driver.specimen_handling_confirmed == 1 ? 'Yes' : 'No'}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Bloodborne Training Date</div>
                        <div class="info-value">${driver.bloodborne_pathogen_training_date || 'N/A'}</div>
                    </div>
                </div>
            </div>

            <div class="profile-section">
                <div class="section-header">
                    <i class="fas fa-file-alt"></i>
                    <h3>Attachments</h3>
                </div>
                <div class="info-grid two-cols">
                    <div class="info-item">
                        <div class="info-label">HIPAA File</div>
                        <div class="info-value">${driver.hipaa_certification_file ? `<a class="file-link" href="${formatFileUrl(driver.hipaa_certification_file)}" target="_blank">View HIPAA File</a>` : 'No file attached'}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Bloodborne File</div>
                        <div class="info-value">${driver.bloodborne_pathogen_file ? `<a class="file-link" href="${formatFileUrl(driver.bloodborne_pathogen_file)}" target="_blank">View Bloodborne File</a>` : 'No file attached'}</div>
                    </div>
                </div>
            </div>

            <div class="profile-actions">
                <button class="btn-edit" onclick="window.location.href='/company/dashboard/drivers/${driverId}/edit'">Edit</button>
            </div>
        `;
    }

    function formatFileUrl(path) {
        if (!path) {
            return '#';
        }

        if (path.startsWith('http://') || path.startsWith('https://')) {
            return path;
        }

        return `${APP_URL}${path}`;
    }

    loadDriverDetails();
</script>
@endsection
