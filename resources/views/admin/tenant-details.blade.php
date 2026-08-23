@extends('common.layout')
@section('title', 'Tenant Details')
@section('page-title', 'Tenant Details')
@section('content')
<div class="profile-container" id="tenantCard">
    <!-- Personal & Contact Info -->
    <form id="updateTenantForm" method="POST" action="{{ route('dashboard.update-tenant', $tenant->id) }}" enctype="multipart/form-data">
        @csrf
        <div class="profile-section filter-group">
            <div class="section-header">
                <i class="fas fa-user"></i>
                <h3>Update Tenant</h3>
            </div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Name</div>
                    <input type="text" name="name" id="nameInput" value="{{ $tenant->name ?? 'N/A' }}" class="info-value" placeholder="Enter tenant name">
                </div>
            </div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Status</div>
                    <select name="status" id="statusInput" class="info-value">
                        <option value="">Select Status</option>
                        <option value="trial" {{ $tenant->status === 'trial' ? 'selected' : '' }}>Trial</option>
                        <option value="active" {{ $tenant->status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="suspended" {{ $tenant->status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                        <option value="cancelled" {{ $tenant->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
            </div>
                <!-- <div class="info-item">
                    <div class="info-value">{{ $tenant->status ?? 'N/A' }}</div>
                </div> -->
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Logo</div>
                    <div class="info-value">
                        @if($tenant->logo_path)
                            <img src="{{ asset('' . $tenant->logo_path) }}"  alt="Tenant Logo" style="max-width: 150px; max-height: 150px;">
                        @else
                            N/A
                        @endif
                        <input type="file" name="logo" id="logoInput" class="info-value">
                    </div>
                </div>
            </div>
            <!-- Actions -->
            <div class="profile-actions">
                <!-- <button class="btn-cancel" onclick="{{ route('dashboard.tenants') }}">Cancel</button> -->
                <a href="{{ route('dashboard.tenants') }}" class="btn-cancel">Cancel</a>
                <button class="btn-edit" type="submit">Update</button>
            </div>
        </div>
    </form>
</div>
@endsection
@section('scripts')
<script>
    // const token = localStorage.getItem('api_token');

</script>
@endsection