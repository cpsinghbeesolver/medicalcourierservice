@extends('common.layout')

@section('title', 'Tenant Management')
@section('page-title', 'Tenant Management')

@section('styles')
@endsection

@section('content')
<!-- Filters -->
<div class="filters-bar">
    <div class="filter-group">
        <label>Search Tenant</label>
        <input type="text" id="searchInput" placeholder="Search by name" onkeyup="filterDrivers()">
    </adiv>
</div>

<!-- Data Table -->
<div class="data-card">
    <div class="data-card-header">
        <h3>All Tenants</h3>
    </div>
    <div class="table-container">
        <table class="data-table" id="driversTable">
            <thead>
                <tr>
                    <th>Sr No.</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @if($tenants)
                    @foreach($tenants as $tenant)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $tenant->name }}</td>
                            <td>
                                @if($tenant->status === 'trial')
                                    <span class="badge active">Trial</span>
                                @elseif($tenant->status === 'active')
                                    <span class="badge coordinator">Active</span>
                                @elseif($tenant->status === 'suspended')
                                    <span class="badge inactive">Suspended</span>
                                @elseif($tenant->status === 'cancelled')
                                    <span class="badge inactive">Cancelled</span>
                                @endif
                            </td>
                            <td>{{ $tenant->created_at }}</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-action btn-edit" onclick="window.location.href='{{ route('dashboard.tenants-details', $tenant->id) }}'">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 60px;">No tenants found.</td>
                    </tr>
                @endif
                
            </tbody>
            
        </table>
    </div>
</div>
{{ $tenants->links() }}
@endsection