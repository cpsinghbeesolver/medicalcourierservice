@extends('common.layout')

@section('title', 'Hospital Details')
@section('page-title', 'Hospital Details')

@section('content')

    <a href="{{ route('dashboard.hospitals') }}" class="back-button">
        <i class="fas fa-arrow-left"></i> Back to Hospitals
    </a>

    <div class="profile-container">

        <div class="profile-section">
            <div class="section-header" style="justify-content: space-between;">
                <h3><i class="fas fa-hospital"></i> {{ $hospital->name }}</h3>
                <a href="{{ route('dashboard.hospitals.edit', $hospital->id) }}" class="btn-edit" style="text-decoration:none; display:inline-block;">
                    <i class="fas fa-pen"></i> Edit
                </a>
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Hospital ID</span>
                    <span class="info-value">{{ $hospital->hospital_id }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Registration Number</span>
                    <span class="info-value">{{ $hospital->registration_number }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Contact Person</span>
                    <span class="info-value">{{ $hospital->contact_person }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Phone</span>
                    <span class="info-value">{{ $hospital->phone }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Coordinates</span>
                    <span class="info-value">{{ $hospital->latitude ?? '—' }}, {{ $hospital->longitude ?? '—' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Company</span>
                    <span class="info-value">{{ $hospital->createdByUser->name ?? 'N/A' }}</span>
                </div>
            </div>

            <div class="subsection">
                <div class="subsection-title">Address</div>
                <div class="info-value">
                    {{ $hospital->address }}, {{ $hospital->city }}, {{ $hospital->state }} {{ $hospital->zip }}, {{ $hospital->country }}
                </div>
            </div>
        </div>

        <div class="data-card">
            <div class="data-card-header">
                <h3>Delivery Items</h3>
            </div>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($hospital->items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->name ?? $item->id }}</td>
                                <td>
                                    <span class="badge {{ strtolower($item->status ?? '') }}">
                                        {{ $item->status ?? '-' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align:center; color:#7f8c8d; padding:30px;">
                                    No delivery items linked to this hospital.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

@endsection
