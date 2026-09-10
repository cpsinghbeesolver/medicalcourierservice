@extends('common.layout')

@section('title', 'Company Management')
@section('page-title', 'Companies')

@section('content')

    <div class="filters-bar">
        <form method="GET" action="{{ route('dashboard.companies') }}" style="display:flex; gap:15px; width:100%; align-items:flex-end;">
            <div class="filter-group">
                <label>Search</label>
                <input type="text" name="search" placeholder="Search by email or company name..." value="{{ request('search') }}">
            </div>
            <div>
                <button type="submit" class="btn-action">Search</button>
            </div>
        </form>
    </div>

    <div class="data-card">
        <div class="data-card-header">
            <h3>All Companies</h3>
            <a href="{{ route('dashboard.companies.create') }}" class="btn-create">
                <i class="fas fa-plus"></i> Add New Company
            </a>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Contact Name</th>
                        <th>Company</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($companies as $company)
                        <tr>
                            <td>{{ $company->name ?? 'N/A' }}</td>
                            <td>{{ $company->tenant->name ?? 'N/A' }}</td>
                            <td>{{ $company->email }}</td>
                            <td>{{ $company->phone ?? 'N/A' }}</td>
                            <td>
                                <span class="badge {{ $company->status == 'active' ? 'active' : 'inactive' }}">
                                    {{ ucfirst($company->status) }}
                                </span>
                            </td>
                            <td>{{ $company->created_at->format('n/j/Y') }}</td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('dashboard.companies.show', $company->id) }}" class="btn-action">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('dashboard.companies.edit', $company->id) }}" class="btn-action edit">
                                        <i class="fas fa-pen"></i> Edit
                                    </a>
                                    <form action="{{ route('dashboard.companies.destroy', $company->id) }}" method="POST"
                                          style="display:inline;"
                                          onsubmit="return confirm('Delete this company? This removes the account but keeps historical records.');">
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
                            <td colspan="8" style="text-align:center; color:#7f8c8d; padding:30px;">
                                No companies found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $companies->links() }}

@endsection
