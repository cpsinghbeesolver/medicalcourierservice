@extends('common.layout')

@section('title', 'Hospital Management')
@section('page-title', 'Hospitals')

@section('content')

    <div class="filters-bar">
        <form method="GET" action="{{ route('dashboard.hospitals') }}" style="display:flex; gap:15px; width:100%; align-items:flex-end;">
            <div class="filter-group">
                <label>Search</label>
                <input type="text" name="search" placeholder="Search hospitals..." value="{{ request('search') }}">
            </div>
            <div>
                <button type="submit" class="btn-action">Search</button>
            </div>
        </form>
    </div>

    <div class="data-card">
        <div class="data-card-header">
            <h3>All Hospitals</h3>
            <a href="{{ route('dashboard.hospitals.create') }}" class="btn-create">
                <i class="fas fa-plus"></i> Add New Hospital
            </a>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Registration No.</th>
                        <th>Contact Person</th>
                        <th>Phone</th>
                        <th>City</th>
                        <th>State</th>
                        <th>Company</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($hospitals as $hospital)
                        <tr>
                            <td>{{ $hospital->name }}</td>
                            <td>{{ $hospital->registration_number }}</td>
                            <td>{{ $hospital->contact_person }}</td>
                            <td>{{ $hospital->phone }}</td>
                            <td>{{ $hospital->city ?: 'N/A' }}</td>
                            <td>{{ $hospital->state ?: 'N/A' }}</td>
                            <td>{{ $hospital->createdByUser->name ?? 'N/A' }}</td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('dashboard.hospitals.show', $hospital->id) }}" class="btn-action">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('dashboard.hospitals.edit', $hospital->id) }}" class="btn-action edit">
                                        <i class="fas fa-pen"></i> Edit
                                    </a>
                                    <form action="{{ route('dashboard.hospitals.destroy', $hospital->id) }}" method="POST"
                                          style="display:inline;"
                                          onsubmit="return confirm('Delete this hospital?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-delete">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align:center; color:#7f8c8d; padding:30px;">
                                No hospitals found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $hospitals->links() }}

@endsection
