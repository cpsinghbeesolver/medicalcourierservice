@extends('common.layout')

@section('title', 'Add Hospital')
@section('page-title', 'Add Hospital')

@section('content')

    <a href="{{ route('dashboard.hospitals') }}" class="back-button">
        <i class="fas fa-arrow-left"></i> Back to Hospitals
    </a>

    <div class="form-container">
        <form method="POST" action="{{ route('dashboard.hospitals.store') }}">
            @csrf
            @include('admin.hospitals.hospitals-form')

            <div class="form-actions">
                <a href="{{ route('dashboard.hospitals') }}" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-submit">Save Hospital</button>
            </div>
        </form>
    </div>

@endsection
