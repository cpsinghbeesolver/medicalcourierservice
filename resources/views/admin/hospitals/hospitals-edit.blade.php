@extends('common.layout')

@section('title', 'Edit Hospital')
@section('page-title', 'Edit Hospital')

@section('content')

    <a href="{{ route('dashboard.hospitals') }}" class="back-button">
        <i class="fas fa-arrow-left"></i> Back to Hospitals
    </a>

    <div class="form-container">
        <form method="POST" action="{{ route('dashboard.hospitals.update', $hospital->id) }}">
            @csrf
            @method('PUT')
            @include('admin.hospitals.hospitals-form')

            <div class="form-actions">
                <a href="{{ route('dashboard.hospitals') }}" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-submit">Update Hospital</button>
            </div>
        </form>
    </div>

@endsection
