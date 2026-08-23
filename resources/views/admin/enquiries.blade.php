@extends('common.layout')

@section('title', 'Enquiry Management')
@section('page-title', 'Enquiry Management')

@section('styles')
@endsection

@section('content')
<!-- Filters -->
<div class="filters-bar">
    <div class="filter-group">
        <label>Search Enquiry</label>
        <input type="text" id="searchInput" placeholder="Search by name, email, vehicle..." onkeyup="filterDrivers()">
    </adiv>
</div>

<!-- Data Table -->
<div class="data-card">
    <div class="data-card-header">
        <h3>All Enquiries</h3>
    </div>
    <div class="table-container">
        <table class="data-table" id="driversTable">
            <thead>
                <tr>
                    <th>Sr No.</th>
                    <th>Name</th>
                    <th>Company Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Plan</th>
                    <th>Status</th>
                    <th>Contacted At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @if($submissions)
                    @foreach($submissions as $submission)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $submission->name }}</td>
                            <td>{{ $submission->company_name }}</td>
                            <td>{{ $submission->phone }}</td>
                            <td>{{ $submission->email }}</td>
                            <td>{{ $submission->plan->name ?? 'N/A' }}</td>
                            <td>
                                @if($submission->status === 'contacted')
                                    <span class="badge active">Contacted</span>
                                @elseif($submission->status === 'converted')
                                    <span class="badge coordinator">Converted</span>
                                @elseif($submission->status === 'pending')
                                    <span class="badge inactive">Pending</span>
                                @elseif($submission->status === 'declined')
                                    <span class="badge inactive">Declined</span>
                                @endif
                            </td>
                            <td>{{ $submission->created_at }}</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-action btn-edit" onclick="window.location.href='{{ route('dashboard.enquiries-details', $submission->id) }}'">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    @if($submission->status != 'converted')
                                    <button class="btn-action btn-delete" onclick="confirmDecline('{{ route('dashboard.reject-enquiry', $submission->id) }}')" title="Decline Enquiry">
                                        <i class="fas fa-times"></i> Decline
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 60px;">No enquiries found.</td>
                    </tr>
                @endif
                
            </tbody>
            
        </table>
    </div>
</div>
{{ $submissions->links() }}
@endsection