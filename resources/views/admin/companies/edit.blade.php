@extends('common.layout')

@section('title', 'Edit Company')
@section('page-title', 'Edit Company')

@section('content')

    <a href="{{ route('dashboard.companies') }}" class="back-button">
        <i class="fas fa-arrow-left"></i> Back to Companies
    </a>

    <div class="form-container">
        <form method="POST" action="{{ route('dashboard.companies.update', $company->id) }}">
            @csrf
            @method('PUT')
            @include('admin.companies.form')

            <div class="form-actions">
                <a href="{{ route('dashboard.companies') }}" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-submit">Update Company</button>
            </div>
        </form>
    </div>

@endsection
