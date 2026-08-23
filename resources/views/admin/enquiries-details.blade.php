@extends('common.layout')
@section('title', 'Enquiry Details')
@section('page-title', 'Enquiry Details')
@section('content')
<div class="profile-container" id="driverCard">
    <!-- Personal & Contact Info -->
        <div class="profile-section">
            <div class="section-header">
                <i class="fas fa-user"></i>
                <h3>Personal & Contact Info</h3>
            </div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Full Name</div>
                    <div class="info-value"><strong>{{ $submission->name ?? 'N/A' }}</strong></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Company Name</div>
                    <div class="info-value">{{ $submission->company_name ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Contact Number</div>
                    <div class="info-value">{{ $submission->phone ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email Address</div>
                    <div class="info-value">{{ $submission->email ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Message</div>
                    <div class="info-value">{{ $submission->message ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Plan</div>
                    <div class="info-value">{{ $submission->plan->display_name ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Status</div>
                    <div class="info-value">{{ $submission->status ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Contacted At</div>
                    <div class="info-value">{{ $submission->created_at ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Notes</div>
                    <div class="info-value">{{ $submission->notes ?? 'N/A' }}</div>
                </div>
            </div>
            <!-- Actions -->
            <div class="profile-actions">
                <button class="btn-cancel" onclick="window.location.href='/admin/dashboard/enquiries'">Cancel</button>
                @if($submission->status === 'converted')
                    <a class="btn-edit disabled" style="pointer-events: none; opacity: 0.6;">
                        Accepted as Admin
                    </a>
                @else
                    <a class="btn-edit" onclick="confirmGenerate('{{ route('dashboard.generate-credentials', $submission->id) }}')">
                        Accept as Admin
                    </a>
                @endif
                <!-- <a class="btn-edit" onclick="confirmGenerate('{{ route('dashboard.generate-credentials', $submission->id) }}')">Accept as Admin</a> -->
            </div>
            <div class="profile-actions info-label">
                <br><b>Note: </b><span>User will be notified via email once credentials are generated.</span>
            </div>
        </div>
</div>
@endsection
@section('scripts')
<script>
    // const token = localStorage.getItem('api_token');

</script>
@endsection