@extends('common.layout')

@section('title', 'Company Details')
@section('page-title', 'Company Details')

@section('content')

    <a href="{{ route('dashboard.companies') }}" class="back-button">
        <i class="fas fa-arrow-left"></i> Back to Companies
    </a>

    <div class="profile-container">
        <div class="profile-section">
            <div class="section-header" style="justify-content: space-between;">
                <h3><i class="fas fa-building"></i> {{ $company->tenant->name ?? 'N/A' }}</h3>
                <a href="{{ route('dashboard.companies.edit', $company->id) }}" class="btn-edit" style="text-decoration:none; display:inline-block;">
                    <i class="fas fa-pen"></i> Edit
                </a>
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Contact Name</span>
                    <span class="info-value">{{ $company->name ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email</span>
                    <span class="info-value">{{ $company->email }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Phone</span>
                    <span class="info-value">{{ $company->phone ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Company URL</span>
                    <span class="info-value">{{ $company->tenant->subdomain ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Status</span>
                    <span class="badge {{ $company->status == 'active' ? 'active' : 'inactive' }}">
                        {{ ucfirst($company->status) }}
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Created</span>
                    <span class="info-value">{{ $company->created_at->format('M j, Y') }}</span>
                </div>
            </div>
        </div>
    </div>

@endsection
